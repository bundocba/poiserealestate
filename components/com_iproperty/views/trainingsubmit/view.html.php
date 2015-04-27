<?php


/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
jimport('joomla.application.component.view');
require_once JPATH_ADMINISTRATOR.'/components/com_iproperty/models/trainings.php';

class IpropertyViewTrainingSubmit extends JViewLegacy
{
    protected $params;
    protected $items;
    protected $featured;
    protected $pagination;
    protected $state;
    protected $settings;
    protected $ipauth;
    protected $ipbaseurl;    
    
    public function display($tpl = null)
    {
	$app                = JFactory::getApplication();
        $document           = JFactory::getDocument();
        $this->params       = $app->getParams();

        $cid = JRequest::getInt("cid");
	$user = JFactory::getUser();
        
        
        // Initialise variable
       
        $limit = $this->checkLimit();
               
        if($limit ==0){
            $result = $this->getRegister();
        }else{
            $result = 0;
        }
        $this->assignRef('limit'       , $limit);
        $this->assignRef('result'       , $result);
        
      

        $this->pagination	= $this->get('Pagination');
        $this->state		= $this->get('State');

        $this->settings     = ipropertyAdmin::config();
        $this->ipauth       = new ipropertyHelperAuth();        
        $this->ipbaseurl    = JURI::root(true);

        // get IP plugins
        JPluginHelper::importPlugin( 'iproperty');
        $dispatcher = JDispatcher::getInstance();       

                // create toolbar
        $dispatcher->trigger( 'onBeforeRenderToolbar', array( &$this->settings ) );

        $co_photo_width     = ($this->settings->file_photo_width) ? $this->settings->file_photo_width : '90';
        $enable_featured    = $this->settings->agent_show_featured;        

        $this->assignRef('co_photo_width'   , $co_photo_width);
        $this->assignRef('enable_featured'  , $enable_featured);
        $this->assignRef('dispatcher'       , $dispatcher);

        //Escape strings for HTML output
                $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

        $this->_prepareDocument();              
       
        parent::display($tpl);
    }

    protected function _prepareDocument()
    {
        $app            = JFactory::getApplication();
		$menus          = $app->getMenu();
		$pathway        = $app->getPathway();
		$this->params   = $app->getParams();
		$title          = null;

        $menu = $menus->getActive();
		if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', JText::_('COM_IPROPERTY_INTELLECTUAL_PROPERTY' ));
		}

        $title = (is_object($menu) && $menu->query['view'] == 'trainings') ? $menu->title : JText::_('COM_IPROPERTY_TRAININGS_TITLE');
        $this->iptitle = $title;
        if (empty($title)) {
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		$this->document->setTitle($title);

        // Set meta data according to menu params
        if ($this->params->get('menu-meta_description')) $this->document->setDescription($this->params->get('menu-meta_description'));
        if ($this->params->get('menu-meta_keywords')) $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        if ($this->params->get('robots')) $this->document->setMetadata('robots', $this->params->get('robots'));

		// Breadcrumbs
        if(is_object($menu) && $menu->query['view'] != 'trainings') {
			$pathway->addItem($this->iptitle);
		}
	}
      
    //check limit register
    protected function checkLimit(){
        
        $cid = JRequest::getInt("cid");
        
        $query = "Select count(trainingid) as quantity,t.pax from #__iproperty_training as t,#__iproperty_training_detail as td where t.id=td.trainingid and td.trainingid = ".$cid;
        $query .= " group by t.pax";
        
       
        
        $db = JFactory::getDBO();
         
        $db->setQuery($query);
        
        $result = $db->loadAssocList();
        
        if(empty($result)) $count  = 0;
        else{
        
            foreach ($result as $key => $value) {
                 
                if($value["quantity"] < $value["pax"])
                    $count = 0;
                else
                    $count = 1;
            }
        }
        
       
        return $count;
         
    }
        
    protected function getRegister(){
        
        //training id 
        $cid = JRequest::getInt("cid");
	$user = JFactory::getUser();
        $userid = $user->id;
        $config =& JFactory::getConfig();
         $db = JFactory::getDBO();
       
        
        //check exist
        if($this->checkExist($cid,$userid)){
            if($this->updateRegister($cid,$userid) == 0)
                $register = 1;
            else
                $register = 0;
            //update data
            $query = "Update  #__iproperty_training_detail set register=".$register." where trainingid=".$cid." and userid=".$userid;
            
        }else{
            //insert data
            $query = "insert into #__iproperty_training_detail(trainingid,userid,register)values(".$cid.",".$userid.",1)";
        }
       
      
        
        
        $db->setQuery($query);
        
        $count = $db->execute();
        
        //send mail to admin
        $name_active=$config->get('fromname');
	$email_active=$config->get('mailfrom');	
        
        $email_user = $user->email;
        $body ='Hi, Admin!<br>';
        $body.='There are a agent register training course:<br>';			
        
        $body.='Best regard.';
        $this->sendMail($email_active, $name_active, $email_user, 'Register Training Course', $body );
        
        //send email to client
        $bodynew = 'Dear '.$user->name;
        $bodynew = 'You registerd successfull ';
        $bodynew = 'Best regard.';

        $this->sendMail($email_user, $name_active, $email_active, 'Register Training Course', $bodynew );
        
        return $count;
    }
    protected function sendMail( $email, $sender, $from, $subject, $body ){
		global $mainframe;
		jimport( 'joomla.mail.helper' );
		// An array of e-mail headers we do not want to allow as input
		$headers = array (	'Content-Type:',
							'MIME-Version:',
							'Content-Transfer-Encoding:',
							'bcc:',
							'cc:');

		// An array of the input fields to scan for injected headers
		$fields = array ('mailto',
						 'sender',
						 'from',
						 'subject'
						 );

		foreach ($fields as $field)
		{
			foreach ($headers as $header)
			{
				if (strpos($_POST[$field], $header) !== false)
				{
					JError::raiseError(403, '');
				}
			}
		}

		/*
		 * Free up memory
		 */
		unset ($headers, $fields);
		
		// Check for a valid to address
		$error	= false;
		if ( ! $email  || ! JMailHelper::isEmailAddress($email) )
		{
			$error	= JText::sprintf('EMAIL_INVALID', $email);
			JError::raiseWarning(0, $error );
		}

		// Check for a valid from address
		if ( ! $from || ! JMailHelper::isEmailAddress($from) )
		{
			$error	= JText::sprintf('EMAIL_INVALID', $from);
			JError::raiseWarning(0, $error );
		}
		// Build the message to send
		$msg	= JText :: _('EMAIL_MSG');

		// Clean the email data
		$subject = JMailHelper::cleanSubject($subject);
		$body	 = JMailHelper::cleanBody($body);
		$sender	 = JMailHelper::cleanAddress($sender);

		// Send the email
		
		//if ( JMail::sendMail($from, $sender, $email, $subject, $body, true) !== true )
		if ( JFactory::getMailer()->sendMail($from, $sender, $email, $subject, $body, true) !== true )
		{ 
			JError::raiseNotice( 500, 'Error' );
		}
		
	}
    protected function checkExist($cid = 0,$userid = 0){
        
        $db = JFactory::getDBO();
        $query="select id from #__iproperty_training_detail where trainingid=".$cid." and userid=".$userid." ORDER BY id DESC";
        $db->setQuery($query);
        
        $result = $db->loadAssocList();
        
        return $result;
    }
    
    protected function updateRegister($cid = 0,$userid = 0){
        
        $db = JFactory::getDBO();
        $query="select register from #__iproperty_training_detail where trainingid=".$cid." and userid=".$userid."";
        $db->setQuery($query);
        
        $result = $db->loadResult();
        //print_r($result);die;
        return $result;
    }
    
}

?>
