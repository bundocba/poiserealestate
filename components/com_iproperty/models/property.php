<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined('_JEXEC' ) or die( 'Restricted access');
jimport('joomla.application.component.model');

class IpropertyModelProperty extends JModelForm
{
	protected $view_item    = 'property';
    protected $_item        = null;
    protected $_context     = 'com_iproperty.property';

	protected function populateState()
	{
		$app = JFactory::getApplication('site');

		// Load state from the request.
		$pk = $app->input->getInt('id');
		$this->setState('property.id', $pk);

		$offset = $app->input->getUInt('limitstart');
		$this->setState('list.offset', $offset);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);
	}
    
    public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_iproperty.request', 'request', array('control' => 'jform', 'load_data' => true, 'form_type' => 'request'));
		if (empty($form)) {
			return false;
		}

		return $form;
	}
    
    public function getStfForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_iproperty.stf', 'stf', array('control' => 'jform', 'load_data' => true, 'form_type' => 'stf'));
		if (empty($form)) {
			return false;
		}

		return $form;
	}
    
    // custom loadForm method to handle multiple forms
    // handles new argument for form_type in the options array
    // request and send to friend forms
    protected function loadForm($name, $source = null, $options = array(), $clear = false, $xpath = false)
	{
		// Handle the optional arguments.
		$options['control'] = JArrayHelper::getValue($options, 'control', false);

		// Create a signature hash.
		$hash = md5($source . serialize($options));

		// Check if we can use a previously loaded form.
		if (isset($this->_forms[$hash]) && !$clear)
		{
			return $this->_forms[$hash];
		}

		// Get the form.
		JForm::addFormPath(JPATH_COMPONENT . '/models/forms');
		JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');

		try
		{
			$form = JForm::getInstance($name, $source, $options, false, $xpath);

			if (isset($options['load_data']) && $options['load_data'])
			{
				// Get the data for the form.
				$data = $this->loadFormData($options['form_type']);
			}
			else
			{
				$data = array();
			}

			// Allow for additional modification of the form, and events to be triggered.
			// We pass the data because plugins may require it.
			$this->preprocessForm($form, $data);

			// Load the data into the form after the plugins have operated.
			$form->bind($data);

		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		}

		// Store the form for later.
		$this->_forms[$hash] = $form;

		return $form;
	}

	protected function loadFormData($form_type = 'request')
	{
        $data = (array) JFactory::getApplication()->getUserState('com_iproperty.'.$form_type.'.data', array());
		return $data;
	}

	public function &getItem($pk = null)
	{
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('property.id');
        if (!$pk) return false;

		if ($this->_item === null) {
			$this->_item = array();
		}

		if (!isset($this->_item[$pk])) {

			try {
				// get property query setting id
                $where['property']['id']    = $pk;			
                $pquery                     = new ipropertyHelperQuery($this->_db);
                $pquery                     = $pquery->buildPropertyQuery($where, 'property');
                
                $this->_db->setQuery($pquery, 0, 1);
                $data = $this->_db->loadObject();
                $data = ipropertyHelperProperty::getPropertyItems(array($data), true);                

				$this->_item[$pk] = $data[0];
			}
			catch (Exception $e)
			{
				if ($e->getCode() == 404) {
					// Need to go thru the error handler to allow Redirect to work.
					JError::raiseError(404, $e->getMessage());
				}
				else {
					$this->setError($e);
					$this->_item[$pk] = false;
				}
			}
		}

		return $this->_item[$pk];
	}

	public function hit($pk = 0)
	{
        $pk = (!empty($pk)) ? $pk : (int) $this->getState('property.id');
        $db = $this->getDbo();

        $db->setQuery(
                'UPDATE #__iproperty' .
                ' SET hits = hits + 1' .
                ' WHERE id = '.(int) $pk
        );

        try
        {
            $db->execute();
        }
        catch (RuntimeException $e)
        {
            $this->setError($e->getMessage());
            return false;
        }
		return true;
	}

    public function getImages($pk = 0)
    {
        $pk = (!empty($pk)) ? $pk : (int) $this->getState('property.id');
        
        $query = $this->_db->getQuery(true);
        $query->select('*, title AS img_title, description AS img_description')
                ->from('#__iproperty_images')
                ->where('propid = '.(int)$pk)
                ->where('(type = ".jpg" OR type = ".jpeg" OR type = ".gif" OR type = ".png")')
                ->order('ordering ASC');
              
        $this->_db->setQuery($query);

  		$result = $this->_db->loadObjectList();
  		return $result;
  	}

    public function getdocs($pk = 0)
	{
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('property.id');
        
        $query = $this->_db->getQuery(true);
        $query->select('*')
                ->from('#__iproperty_images')
                ->where('propid = '.(int)$pk)
                ->where('(type != ".jpg" AND type != ".jpeg" AND type != ".gif" AND type != ".png")')
                ->order('type, ordering ASC');
        
        $this->_db->setQuery($query);

		$docs = $this->_db->loadObjectList();
		return $docs;
	} 
    
    public function getAmenities($pk = 0)
    {
        $pk = (!empty($pk)) ? $pk : (int) $this->getState('property.id');

        $query = $this->_db->getQuery(true);
        $query->select('a.*')
            ->from('#__iproperty_amenities AS a')
            ->leftJoin('#__iproperty_propmid AS pm ON pm.amen_id = a.id')
            ->where('pm.prop_id = '.(int)$pk)
            ->order('a.title ASC');

        $this->_db->setQuery($query);
        
        $amens = $this->_db->loadObjectList();
        return $amens;
    }

	public function sendTofriend($post)
    {
		$app  = JFactory::getApplication();

		// import IP plugins to trigger after send to friend actions
        JPluginHelper::importPlugin('iproperty');
        $dispatcher    = JDispatcher::getInstance();        
        
        // get path to property page where request was made
        $property_path = @$_SERVER['HTTP_REFERER'];
        if (empty($property_path) || !JURI::isInternal($property_path)) {
            $property_path = JURI::base();
        }

        // set site vars
        $user           = JFactory::getUser();
        $settings       = ipropertyAdmin::config();
        $admin_from     = $app->getCfg('fromname');
        $admin_email    = $app->getCfg('mailfrom');
        $site_name      = $app->getCfg('sitename');
        $date           = JHTML::_('date','now',JText::_('DATE_FORMAT_LC4'));
        $fulldate       = JHTML::_('date','now',JText::_('DATE_FORMAT_LC2'));
        $remote_addr    = $_SERVER['REMOTE_ADDR'];

		// set email vars
		$recipients     = explode(',', $post['recipient_email']);
		$from_name      = ($post['sender_name']) ? $post['sender_name'] : '--N/A--';
		$from_email     = ($post['sender_email']) ? $post['sender_email'] : '--N/A--'; 
        $from_comments  = ($post['comments']) ? $post['comments'] : '--N/A--';       

		$body = sprintf(JText::_('COM_IPROPERTY_STF_EMAIL'),
                JURI::base(),
                $site_name, 
                $date, 
                $from_name, 
                $from_email, 
                $from_comments, 
                $property_path, 
                $remote_addr, 
                $fulldate);

		$sento = '';
		$mail = JFactory::getMailer();
        $mail->addRecipient( $recipients );
        $mail->addReplyTo(array($from_email, $from_name));
        $mail->setSender(array( $admin_email, $admin_from));
        $mail->setSubject( sprintf(JText::_('COM_IPROPERTY_STF_SUBJECT'), $from_name) );
        $mail->setBody( stripslashes($body) );
        $sento = $mail->Send();

		if( $sento ){
			//send a confirmation email to admin
			if( $admin_email && $settings->notify_sendfriend == 1 ){
                $copySubject = JText::_('COM_IPROPERTY_COPY_OF' ).": ".sprintf(JText::_('COM_IPROPERTY_STF_SUBJECT'), $from_name);

                $copyBody   = JText::_('COM_IPROPERTY_COPY_OF_MESSAGE' ).":\r\n";
                $copyBody   .= JText::_('COM_IPROPERTY_COPY_EMAIL_1') . " " . $app->getCfg('sitename') . "\r\n";
                $copyBody   .= JText::_('COM_IPROPERTY_SENT_TO_FOLLOWING' ) . ": " . $post['recipient_email'] . "\r\n";
                $copyBody   .= "-----------------------------------------------------------------\r\n\r\n";
                $copyBody   .= $body;                

                $mail = JFactory::getMailer();
                $mail->addRecipient( $admin_email );
                $mail->setSender( array( $admin_email, $admin_from ) );
                $mail->setSubject( $copySubject );
                $mail->setBody( $copyBody );
                $mail->Send();
            }
            $dispatcher->trigger( 'onAfterSendFriend', array($user->get('id'), $post, $settings ) );
			return true;
		}else{
			return false;
		}
	}

	public function sendRequest($post)
    {
		$app  = JFactory::getApplication();

		// import IP plugins to trigger after send request actions
        JPluginHelper::importPlugin( 'iproperty');
        $dispatcher    = JDispatcher::getInstance();       
        
        // get path to property page where request was made
        $property_path = @$_SERVER['HTTP_REFERER'];
        if (empty($property_path) || !JURI::isInternal($property_path)) {
            $property_path = JURI::base();
        }

		// set site vars
        $user           = JFactory::getUser();
        $settings       = ipropertyAdmin::config();
        $admin_from     = $app->getCfg('fromname');
        $admin_email    = $app->getCfg('mailfrom');
        $site_name      = $app->getCfg('sitename');
        $date           = JHTML::_('date','now',JText::_('DATE_FORMAT_LC4'));
        $fulldate       = JHTML::_('date','now',JText::_('DATE_FORMAT_LC2'));
        $remote_addr    = $_SERVER['REMOTE_ADDR'];
        
        // set email vars        
		$from_name      = $post['sender_name'];
		$from_email     = $post['sender_email'];
        $from_dphone    = ($post['sender_dphone']) ? $post['sender_dphone'] : '-N/A-';
        $from_ephone    = ($post['sender_ephone']) ? $post['sender_ephone'] : '-N/A-';
        $from_contact   = ($post['sender_preference']) ? $post['sender_preference'] : '-N/A-';
        $from_ctime     = ($post['sender_ctime']) ? $post['sender_ctime'] : '-N/A-';
        $from_comments  = ($post['special_requests']) ? $post['special_requests'] : '-N/A-';

        $agents        = ipropertyHTML::getAvailableAgents($post['prop_id']);
        $company_email = ipropertyHTML::getCompanyEmail($post['company_id']);
        $property      = ipropertyHTML::getPropertyTitle($post['prop_id']);

        $subject        = sprintf(JText::_('COM_IPROPERTY_REQ_SUBJECT'), $site_name, $from_name);
        $cc             = ($post['copy_me']) ? $post['sender_email'] : '';
        $bcc            = $settings->form_copyadmin;
		

        //check who admin wants to send the requests to
        $recipients = array();
        switch($settings->form_recipient){
            case '0': //send to admin only
                if($admin_email) $recipients[] = $admin_email;
            break;
            case '1': //send to agent
                foreach($agents as $a){
                    if($a->email) $recipients[] = $a->email;
                }
            break;
            case '2': //send to company
                if($company_email) $recipients[] = $company_email;
            break;
            case '3': //send to agent and company
                foreach($agents as $a){
                    if($a->email) $recipients[] = $a->email;
                }
                if($company_email) $recipients[] = $company_email;
            break;
            default:
                if($admin_email) $recipients[] = $admin_email;
            break;
        }  
        
        if(empty($recipients) && !$admin_email){
            $app->enqueueMessage('No recipients or admin email found!', 'warning');
            return false;
        }
            
        
        $body = sprintf(JText::_('COM_IPROPERTY_REQ_EMAIL'),                
                JURI::base(),
                $site_name,
                $date,
                $property,
                $from_name,
                $from_email,
                $from_dphone,
                $from_ephone,                
                JText::_($from_contact),
                JText::_($from_ctime),
                $from_comments,
                $property_path,
                $remote_addr,
                $fulldate);

        $sento = '';
        $mail = JFactory::getMailer();
        $mail->addRecipient( $recipients );
        $mail->addReplyTo(array($from_email, $from_name));
        $mail->setSender( array( $admin_email, $admin_from ));
        $mail->setSubject( $subject );
        $mail->setBody( $body );
        $sento = $mail->Send();

		if( $sento ){
            
            $copySubject 	= JText::_('COM_IPROPERTY_COPY_OF' ).": ".$subject;
            //send copy to sender if requested
            if($cc){
                $copyBody 		= JText::_('COM_IPROPERTY_COPY_OF_MESSAGE' ).":";
                $copyBody 		.= "\r\n\r\n".$body;
                $recipients[]   = $cc;

                $mail = JFactory::getMailer();
                $mail->addRecipient( $cc );
                $mail->setSender( array( $admin_email, $admin_from ) );
                $mail->setSubject( $copySubject );
                $mail->setBody( $copyBody );
                $mail->Send();
            }
            //send copy to admin email
            if($bcc){                
                $copyBody 		= JText::_('COM_IPROPERTY_COPY_OF_MESSAGE' )." -- ".JText::_('COM_IPROPERTY_OTHER_RECIPIENTS' ).": ".implode(',',$recipients);
                $copyBody 		.= "\r\n\r\n".$body;

                $mail = JFactory::getMailer();
                $mail->addRecipient( $admin_email );
                $mail->setSender( array( $admin_email, $admin_from ) );
                $mail->setSubject( $copySubject );
                $mail->setBody( $copyBody );
                $mail->Send();
            }
			//uncomment the following lines to see where this email is going
            //$final_send = 'send to: '.implode(',', $recipients).'<br />cc: '. $cc . '<br />bcc: '.$bcc;
            //return $final_send;
            //Trigger plugins to perform actions after a request is made
            $dispatcher->trigger( 'onAfterPropertyRequest', array($user->get('id'), $post, $settings ) );
            return true;
		}else{
			return false;
		}
	}    
}//end class

?>