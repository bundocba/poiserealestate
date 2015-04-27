<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

// No direct access
defined('_JEXEC') or die;

// Base this model on the backend version.
require_once JPATH_ADMINISTRATOR.'/components/com_iproperty/models/agent.php';
jimport('joomla.event.dispatcher');

class IpropertyModelAgentForm extends IpropertyModelAgent
{
	protected function populateState()
	{
		$app    = JFactory::getApplication();

		// Load state from the request.
		$pk     = $app->input->getUint('id');
		$this->setState('agent.id', $pk);

		$return = $app->input->get('return', null, 'default', 'base64');
		$this->setState('return_page', base64_decode($return));

		// Load the parameters.
		$params	= $app->getParams();
		$this->setState('params', $params);

		$this->setState('layout', $app->input->getCmd('layout'));
	}

	public function getItem($itemId = null)
	{
		// Initialise variables.
		$itemId     = (int) (!empty($itemId)) ? $itemId : $this->getState('agent.id');

		// Get a row instance.
		$table      = $this->getTable();

		// Attempt to load the row.
		$return     = $table->load($itemId);

		// Check for a table object error.
		if ($return === false && $table->getError()) {
			$this->setError($table->getError());
			return false;
		}

		$properties = $table->getProperties(1);
		$item      = JArrayHelper::toObject($properties, 'JObject');
        
        if (property_exists($item, 'params'))
		{
			$registry = new JRegistry;
			$registry->loadString($item->params);
			$item->params = $registry->toArray();
		}
        
        //$value->params = new JRegistry;
        if ($itemId) {
            $item->name = $item->fname.' '.$item->lname;
        }
        
		return $item;
	}
    
    public function checkin($pk = null)
	{
		// Only attempt to check the row in if it exists.
		if ($pk) {
			$user = JFactory::getUser();

			// Get an instance of the row to checkin.
			$table = $this->getTable();
			if (!$table->load($pk)) {
				$this->setError($table->getError());
				return false;
			}

			// Attempt to check the row in.
			if (!$table->checkin($pk)) {
				$this->setError($table->getError());
				return false;
			}
		}

		return true;
	}

	public function getReturnPage()
	{
		return base64_encode($this->getState('return_page'));
	}
    
    public function createJuser($data)
    {
        $db         = JFactory::getDbo();        
        $new        = $this->getState($this->getName().'.new'); 
        $agentid    = $this->getState($this->getName().'.id');
        $params     = JComponentHelper::getParams('com_users');
        
        // only execute this function if this is a new agent and the auto agent setting is enabled
        if($new){
            // check the agent's email address and see if they exist in the Joomla users table
            $query = $db->getQuery(true);
            $query->select('id, email, username')
                ->from('#__users')
                ->where('email = '.$db->quote($data['email']))
                ->where('block = 0');
            $db->setQuery($query, 0, 1);
            
            // if yes, assign the user to the agent profile
            if(!$result = $db->loadObject()){
                $newdata = array();
                
                // Get the groups the user should be added to after registration.
                $newdata['groups'] = array();
                
                // Add user to default new user group, Registered if not specified.                
                $newdata['groups'][]    = $params->get('new_usertype', 2);
                
                // email did not exist in users table, so we need to create a new Joomla user from the submitted agent data
                $newdata['name']        = $data['fname'].' '.$data['lname'];
                $newdata['username']    = strtolower(str_replace(' ', '', $newdata['name']));
                
                // make sure the username is unique
                while(!$this->_validateUsername($newdata['username'])){
                    $rand = $this->_genRandomString();
                    $newdata['username'] = $newdata['username'].$rand;
                }
                
                $newdata['email']       = $data['email'];
                $newdata['password']    = JUserHelper::genRandomPassword();
                // user register function to add new user to Joomla users table and send activation/registration email
                if(!$result = $this->_register($newdata)){
                    return false;
                }
            }else{
                JUserHelper::addUserToGroup($result->id, $params->get('new_usertype', 2));
            }
            
            $query = $db->getQuery(true);
            $query->update('#__iproperty_agents')
                ->set('user_id = '.(int)$result->id)
                ->where('id = '.(int)$agentid);
            $db->setQuery($query);
            if(!$db->execute()){
                $this->setError($db->getError());
                return false;
            }
            // Email and IP notification letting the agent know that they are able to edit their listings            
            if(!$this->_emailAgentNotification($result)){
                return false;
            }
        }else{
            return true;
        }
    }
    
    protected function _emailAgentNotification($result)
    {
        $config = JFactory::getConfig();
		$fromname	= $config->get('fromname');
		$mailfrom	= $config->get('mailfrom');
		$sitename	= $config->get('sitename');
		$siteurl	= JUri::root();
        $jdate      = JHTML::_('date','now',JText::_('DATE_FORMAT_LC2'));        
        
        $emailSubject = sprintf(JText::_('COM_IPROPERTY_AGENT_NOTIFY_SUBJECT'), $result->name, $sitename);
        // Include username to log in as, full name to address the email to, username to log in as, and a link to the site login page
        $emailBody = sprintf(JText::_('COM_IPROPERTY_AGENT_NOTIFY_EMAIL'), $result->name, $sitename, $siteurl, $result->username);
        $emailBody .= "\n".JText::_('COM_IPROPERTY_REMOTE_ADDR' )." ".$result->remote_addr;
        $emailBody .= "\n\n".JText::_('COM_IPROPERTY_GENERATED_BY_INTELLECTUAL_PROPERTY' )." ".$jdate;
        
        $sent = JFactory::getMailer()->sendMail($mailfrom, $fromname, $result->email, $emailSubject, $emailBody);
        if ($sent !== true) {
            $this->setError(sprintf(JText::_('COM_IPROPERTY_AGENT_NOTIFY_FAILED'), $result->email));
            return false;
        }
        return true;
    }
    
    protected function _validateUsername($value)
    {
        // Get the database object and a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Build the query.
		$query->select('COUNT(*)')
            ->from('#__users')
            ->where('username = '.$db->quote($value));
        $db->setQuery($query);
		$duplicate = (bool) $db->loadResult();
        
        if(!$duplicate) return true; // no other user with this username exists - good to go
        return false;
    }
    
    protected function _genRandomString($length = 4) 
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $string = '';    

        for ($p = 0; $p < $length; $p++) {
            $string .= $characters[mt_rand(0, strlen($characters))];
        }

        return $string;
    }
    
    protected function _register($data)
	{
        $lang = JFactory::getLanguage();
        $lang->load('com_users', JPATH_SITE);
        
        $config = JFactory::getConfig();
		$db		= $this->getDbo();
		$params = JComponentHelper::getParams('com_users');

		// Initialise the table with JUser.
		$user = new JUser;
		$useractivation = $params->get('useractivation');

		// Check if the user needs to activate their account.
		if (($useractivation == 1) || ($useractivation == 2)) {
			$data['activation'] = JApplication::getHash(JUserHelper::genRandomPassword());
			$data['block'] = 1;
		}

		// Bind the data.
		if (!$user->bind($data)) {
			$this->setError(JText::sprintf('COM_USERS_REGISTRATION_BIND_FAILED', $user->getError()));
			return false;
		}

		// Load the users plugin group.
		JPluginHelper::importPlugin('user');

		// Store the data.
		if (!$user->save()) {
			$this->setError(JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $user->getError()));
			return false;
		}

		// Compile the notification mail values.
		$data = $user->getProperties();
		$data['fromname']	= $config->get('fromname');
		$data['mailfrom']	= $config->get('mailfrom');
		$data['sitename']	= $config->get('sitename');
		$data['siteurl']	= JUri::root();

		// Handle account activation/confirmation emails.
		if ($useractivation == 2)
		{
			// Set the link to confirm the user email.
			$uri = JURI::getInstance();
			$base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
			$data['activate'] = $base.JRoute::_('index.php?option=com_users&task=registration.activate&token='.$data['activation'], false);

			$emailSubject	= JText::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);

			$emailBody = JText::sprintf(
				'COM_USERS_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY',
				$data['name'],
				$data['sitename'],
				$data['siteurl'].'index.php?option=com_users&task=registration.activate&token='.$data['activation'],
				$data['siteurl'],
				$data['username'],
				$data['password_clear']
			);
		}
		elseif ($useractivation == 1)
		{
			// Set the link to activate the user account.
			$uri = JURI::getInstance();
			$base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
			$data['activate'] = $base.JRoute::_('index.php?option=com_users&task=registration.activate&token='.$data['activation'], false);

			$emailSubject	= JText::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);

			$emailBody = JText::sprintf(
				'COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY',
				$data['name'],
				$data['sitename'],
				$data['siteurl'].'index.php?option=com_users&task=registration.activate&token='.$data['activation'],
				$data['siteurl'],
				$data['username'],
				$data['password_clear']
			);
		} else {

			$emailSubject	= JText::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);

			$emailBody = JText::sprintf(
				'COM_USERS_EMAIL_REGISTERED_BODY',
				$data['name'],
				$data['sitename'],
				$data['siteurl']
			);
		}

		// Send the registration email.
		$return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $data['email'], $emailSubject, $emailBody);
		
		//Send Notification mail to administrators
		if (($params->get('useractivation') < 2) && ($params->get('mail_to_admin') == 1)) {
			$emailSubject = JText::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);
			
			$emailBodyAdmin = JText::sprintf(
				'COM_USERS_EMAIL_REGISTERED_NOTIFICATION_TO_ADMIN_BODY',
				$data['name'],
				$data['username'],
				$data['siteurl']
			);
			
			// get all admin users
			$query = 'SELECT name, email, sendEmail' .
					' FROM #__users' .
					' WHERE sendEmail=1';
			
			$db->setQuery( $query );
			$rows = $db->loadObjectList();
			
			// Send mail to all superadministrators id
			foreach( $rows as $row )
			{
				$return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $row->email, $emailSubject, $emailBodyAdmin);
			
				// Check for an error.
				if ($return !== true) {
					$this->setError(JText::_('COM_USERS_REGISTRATION_ACTIVATION_NOTIFY_SEND_MAIL_FAILED'));
					//return false;
				}
			}
		}
		// Check for an error.
		if ($return !== true) {
			$this->setError(JText::_('COM_USERS_REGISTRATION_SEND_MAIL_FAILED'));

			// Send a system message to administrators receiving system mails
			$db = JFactory::getDBO();
			$q = "SELECT id
				FROM #__users
				WHERE block = 0
				AND sendEmail = 1";
			$db->setQuery($q);
			$sendEmail = $db->loadColumn();
			if (count($sendEmail) > 0) {
				$jdate = new JDate();
				// Build the query to add the messages
				$q = "INSERT INTO ".$db->quoteName('#__messages')." (".$db->quoteName('user_id_from').
				", ".$db->quoteName('user_id_to').", ".$db->quoteName('date_time').
				", ".$db->quoteName('subject').", ".$db->quoteName('message').") VALUES ";
				$messages = array();

				foreach ($sendEmail as $userid) {
					$messages[] = "(".$userid.", ".$userid.", '".$jdate->toSql()."', '".JText::_('COM_USERS_MAIL_SEND_FAILURE_SUBJECT')."', '".JText::sprintf('COM_USERS_MAIL_SEND_FAILURE_BODY', $return, $data['username'])."')";
				}
				$q .= implode(',', $messages);
				$db->setQuery($q);
				$db->execute();
			}
			//return false;
		}

		return $user;
	}
    
    public function deleteAgent($pks)
	{
        // Initialise variables.
		$table	= $this->getTable();
		$pks	= (array) $pks;
        $ipauth = new ipropertyHelperAuth();

        // Access checks.
		foreach ($pks as $i => $pk) {
			if ($table->load($pk)) {
				if (!$ipauth->canDeleteAgent($pk)){
					// Prune items that you can't change.
					unset($pks[$i]);
                    $this->setError(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
                    return false;
				}
			}
		}

		// Attempt to change the state of the records.
		if (!$table->delete($pks)) {
			$this->setError($table->getError());
			return false;
		}

		return true;
	}
    
    public function iconUpload($file, $id, $folder)
    {
        jimport('joomla.filesystem.file');
        
        $error = false;
        if (isset($file)) {
			// set an array of accepted mime types to check files against
			$accepted_mimetypes = array(
								'image/jpeg',
								'image/gif',
								'image/png'
								);

            $db         = JFactory::getDbo();
            $settings   = ipropertyAdmin::config();
            $path       = JPATH_SITE;
            $user       = JFactory::getUser();
            
            switch ($folder){
                case 'agents':
                    $table      = '#__iproperty_agents';
                    $imgfolder  = 'agents';
                break;
                case 'companies':
                    $table      = '#__iproperty_companies';
                    $imgfolder  = 'companies';
                break;
                default:
                    $table      = '#__iproperty_agents';
                    $imgfolder  = 'agents';
                break;
            }

            $src_file	= (isset($file['tmp_name']) ? $file['tmp_name'] : "");
            $dest_dir 	= $path.'/media/com_iproperty/'.$imgfolder.'/';
            // get the file extension_loaded
            $parts      = pathinfo($file['name']);
            $ext        = $parts['extension'];
            $vfilename  = uniqid().'.'.$ext;
            $dest_file  = $dest_dir.$vfilename;
            
            // we're going to make sure that the file's mime type is in the accepted group of mime types
			if (function_exists('mime_content_type')) {
				$mime_type = mime_content_type($src_file);
				if (strlen($mime_type) && !in_array($mime_type, $accepted_mimetypes)){
					$error = JText::_('COM_IPROPERTY_WRONG_FILETYPE' ). ' Error: 1';
					return $error;
				}
			} elseif (function_exists('finfo_file')) {
				$finfo = finfo_open(FILEINFO_MIME);
				$mime_type = finfo_file($finfo, $src_file);
				if (strlen($mime_type) && !in_array($mime_type, $accepted_mimetypes)){
					$error = JText::_('COM_IPROPERTY_WRONG_FILETYPE' ). ' Error: 2';
					return $error;
				}
			}
           
            // check if there's an existing image
            $query = $db->getQuery(true);
            $query->select('icon')
                    ->from($table)
                    ->where('id = '.(int)$id);
            
            $db->setQuery($query);
            $icon = $db->loadResult();
            
            if ($icon && $icon != 'nopic.png') JFile::delete($dest_dir.$icon); // delete the existing image

            if(filesize($src_file) > (intval($settings->maximgsize) * 1000)) {
                $error = sprintf(JText::_('COM_IPROPERTY_IMAGE_TOO_LARGE' ), (filesize($src_file)/1000).'KB', $cfg['maximgsize'].'KB', ini_get('upload_max_filesize'));
                return $error;
            }

            if (file_exists($dest_file)) {
                //$error = $dest_file;
                $error = JText::_('COM_IPROPERTY_FILE_EXISTS');
                return $error;
            }

            if(@copy($src_file,$dest_file)){
                //continue
            }else{
                $error = JText::_('COM_IPROPERTY_IMAGE_NOT_COPIED');
                return $error;
            }

            if(!$error) {
                // update the icon name in the appropriate table
                $query = $db->getQuery(true);
                $query->update($table)
                        ->set('icon = '.$db->Quote($vfilename))
                        ->where('id = '.(int)$id);
                
                $db->setQuery($query);
                $result = $db->execute();
                
                if($result) return $vfilename;
            }else{
                return $error;
            }
            return $error;
        } else {
            return JText::_('COM_IPROPERTY_NO_FILE_FOUND');
        }    
    }
}