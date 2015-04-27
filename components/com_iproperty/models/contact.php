<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
jimport('joomla.application.component.model');

class IpropertyModelContact extends JModelForm
{
	protected $view_item    = 'contact';
    protected $_item        = null;
    protected $_context     = 'com_iproperty.contact';
    
    protected function populateState()
	{
		$app = JFactory::getApplication('site');

		// Load state from the request.
		$pk = $app->input->getUint('id');
		$this->setState('contact.id', $pk);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);
        
        $this->setState('layout', $app->input->getCmd('layout'));
	}
    
    public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_iproperty.contact', 'contact', array('control' => 'jform', 'load_data' => true));
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	protected function loadFormData()
	{
		$data = (array) JFactory::getApplication()->getUserState('com_iproperty.contact.data', array());
		return $data;
	}
    
    public function &getItem($pk = null)
	{
		$pk     = (!empty($pk)) ? $pk : (int) $this->getState('contact.id');
        $type   = $this->getState('layout');
        $data   = false;

		if ($this->_item === null) {
			$this->_item = array();
		}

		if (!isset($this->_item[$pk])) {

			try {                
                if($this->getState('layout') == 'agent')
                {
                    $data = ipropertyHTML::buildAgent($this->getState('contact.id'));
                }else if($this->getState('layout') == 'company'){
                    $data = ipropertyHTML::buildCompany($this->getState('contact.id')); 
                }

				$this->_item[$pk] = $data;
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

    public function sendContact($post, $contact_id)
    {
		$app  = JFactory::getApplication();

		// import IP plugins to trigger after send IP contact actions
        JPluginHelper::importPlugin('iproperty');
        $dispatcher    = JDispatcher::getInstance();

        // set site vars
        $user          = JFactory::getUser();
        $settings      = ipropertyAdmin::config();
        $admin_from     = $app->getCfg('fromname');
        $admin_email    = $app->getCfg('mailfrom');
        $site_name      = $app->getCfg('sitename');
        $date           = JHTML::_('date', 'now', JText::_('DATE_FORMAT_LC4'));
        $fulldate       = JHTML::_('date', 'now', JText::_('DATE_FORMAT_LC2'));
        $remote_addr    = $_SERVER['REMOTE_ADDR'];
        
        // set email vars
        $from_name      = $post['sender_name'];
		$from_email     = $post['sender_email'];
        $from_dphone    = ($post['sender_dphone']) ? $post['sender_dphone'] : '--N/A--';
        $from_ephone    = ($post['sender_ephone']) ? $post['sender_ephone'] : '--N/A--';
        $from_contact   = ($post['sender_preference']) ? $post['sender_preference'] : '--N/A--';
        $from_comments  = ($post['sender_requests']) ? $post['sender_requests'] : '--N/A--';        
        
        $cc             = ($post['sender_copy']) ? true : false;
		$subject        = sprintf(JText::_('COM_IPROPERTY_CONTACT_SUBJECT'), $site_name, $from_name);

        if($post['ctype'] == 'company'){
            //get company email
            $company = ipropertyHTML::buildCompany($contact_id);
            $contact_email = $company->email;
        }elseif($post['ctype'] == 'agent'){
            //get agent email
            $agent = ipropertyHTML::buildAgent($contact_id);
            $contact_email = $agent->email;
        }else{
            return false;
        }
        
        $body = sprintf(JText::_('COM_IPROPERTY_CONTACT_EMAIL'),
                JURI::base(),
                $site_name, 
                $date, 
                $from_name, 
                $from_email,
                $from_dphone,
                $from_ephone,
                $from_contact,
                $from_comments, 
                $remote_addr, 
                $fulldate);

        $sent = '';
		$mail = JFactory::getMailer();
        $mail->addRecipient( $contact_email );
        $mail->addReplyTo(array($from_email, $from_name));
        $mail->setSender( array( $admin_email, $admin_from ));
        $mail->setSubject( $subject );
        $mail->setBody( $body );
        $sent = $mail->Send();


        //if cc sender, send copy of email to sender email
        if( $cc ){
            $copySubject = JText::_('COM_IPROPERTY_COPY_OF' ).": ".$subject;

            $copyBody   = JText::_('COM_IPROPERTY_COPY_OF_MESSAGE' ).":\r\n";
            $copyBody   .= "-----------------------------------------------------------------\r\n\r\n";
            $copyBody   .= $body;

            $mail = JFactory::getMailer();
            $mail->addRecipient( $from_email );
            $mail->setSender( array( $admin_email, $admin_from ) );
            $mail->setSubject( $copySubject );
            $mail->setBody( $copyBody );
            $sent = $mail->Send();
        }
        $dispatcher->trigger( 'onAfterIpContact', array($user->get('id'), $post, $settings ) );
        return $sent;
	}
}

?>