<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/controller.php';

/**
 * Registration controller class for Users.
 *
 * @package     Joomla.Site
 * @subpackage  com_users
 * @since       1.6
 */
class fwuserControllerRegistration extends fwuserController
{
	/**
	 * Method to activate a user.
	 *
	 * @return  boolean  True on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function activate()
	{
		$user  	 = JFactory::getUser();
		$input 	 = JFactory::getApplication()->input;
		$uParams = JComponentHelper::getParams('com_users');

		// Check for admin activation. Don't allow non-super-admin to delete a super admin
		if ($uParams->get('useractivation') != 2 && $user->get('id'))
		{
			$this->setRedirect('index.php');

			return true;
		}

		// If user registration or account activation is disabled, throw a 403.
		if ($uParams->get('useractivation') == 0 || $uParams->get('allowUserRegistration') == 0)
		{
			JError::raiseError(403, JText::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'));

			return false;
		}

		$model = $this->getModel('Registration', 'UsersModel');
		$token = $input->getAlnum('token');

		// Check that the token is in a valid format.
		if ($token === null || strlen($token) !== 32)
		{
			JError::raiseError(403, JText::_('JINVALID_TOKEN'));

			return false;
		}

		// Attempt to activate the user.
		$return = $model->activate($token);

		// Check for errors.
		if ($return === false)
		{
			// Redirect back to the homepage.
			$this->setMessage(JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $model->getError()), 'warning');
			$this->setRedirect('index.php');

			return false;
		}

		$useractivation = $uParams->get('useractivation');

		// Redirect to the login screen.
		if ($useractivation == 0)
		{
			$this->setMessage(JText::_('COM_USERS_REGISTRATION_SAVE_SUCCESS'));
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=login', false));
		}
		elseif ($useractivation == 1)
		{
			$this->setMessage(JText::_('COM_USERS_REGISTRATION_ACTIVATE_SUCCESS'));
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=login', false));
		}
		elseif ($return->getParam('activate'))
		{
			$this->setMessage(JText::_('COM_USERS_REGISTRATION_VERIFY_SUCCESS'));
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=registration&layout=complete', false));
		}
		else
		{
			$this->setMessage(JText::_('COM_USERS_REGISTRATION_ADMINACTIVATE_SUCCESS'));
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=registration&layout=complete', false));
		}

		return true;
	}

	/**
	 * Method to register a user.
	 *
	 * @return  boolean  True on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function register()
	{
		jimport('joomla.user.user');
		$db = JFactory::getDBO();
		$app = &JFactory::getApplication();
		$config =& JFactory::getConfig();
		$mainframe =& JFactory::getApplication('site');
		require_once JPATH_COMPONENT .'/helpers/fwuser.php';
		
		$name_active=$config->get('fromname');
		$email_active=$config->get('mailfrom');	
		
		// Check for request forgeries.
		//JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$post = JRequest::get('post');
		
		if(!$this->checkdata($post)){
			
			$msg = JText::_( 'Data invalid.!' );
			$link = 'index.php?option=com_fwuser&view=register&layout=default';
			$this->setRedirect($link, $msg);
		}
		
		$data = array();
		
		$data["name"] = $post["name"];
		$data["email"] = $post["email"];
		$data["address"] = $post["address"];
		$data["city"] = $post["city"];
		$data["company"] = $post["companyname"];
		$data["country"] = $post["country"];
		$data["state"] = "1";
		$data["postal"] = $post["postal"];
		$data["phone"] = $post["officenumber"];
		$data["mobilephone"] = $post["mobile"];
		$data["date"] = date('Y-m-d H:i:s');
		$data["receivetype"] = $this->splitArray($post["chk_receivetype"]);
		$data["propertyupdate"] = $this->splitArray($post["chk_propertyupdate"]);
		$data["newsupdate"] = $this->splitArray($post["chk_newsupdate"]);
		$data["published"] = 1;
		
		//Save data in fwuser_user		
		$model = $this->getModel('user');
		
		//check user is exist in database
		$exist = $model->userexist($data["email"]);
		
		if(!empty($exist)){
			$msg = JText::_('Your account has been created. Please use another email address.!');
			$link = 'index.php?option=com_fwuser&view=register&layout=default';
			$this->setRedirect($link, $msg);
		}else{
		
				if ($model->store($data)) {
					$msg = JText::_( 'Data stored successfull!' );
				} else {
					$msg = JText::_( 'Data stored failed' );
				}

				//Save data in user table joomla
				$instance = JUser::getInstance(0);
				
				$datauser=array();
				$datauser['id']				=0;
				$datauser['sendEmail']		=1;
				$datauser['groups']			=array(2);
				$datauser['block']			=1;
				if($data["name"]!=''){
					$datauser['name']		= $data["name"];
				}
				if($data["email"]!=''){
					$datauser['username']	= $data["email"];
				}
				if($post["password"]!=''){
					$datauser['password']	= $post["password"];
					$datauser['password2']	= $post["password"];
				}
				if($data["email"]!=''){
					$datauser['email']		= $data["email"];
				}
				if (!$instance->bind($datauser)) {
					$this->setRedirect($return,$instance->getError(),'error');
				}
				if (!$instance->save()) { 
					$this->setRedirect($return,$instance->getError(),'error');
				}
				
				//send email 
				$params=&$mainframe->getParams('com_users');
				
				if (!is_object($params)) {
					$params = &JComponentHelper::getParams('com_users');
				}
				$kind_active=$params->get( 'useractivation' );
				
				if($kind_active==0){
					$block=0;
					$published=1;
				}else if($kind_active==1){
					$block=1;
					$published=0;
				}else if($kind_active==2){
					$block=1;
					$published=0;
				}					
				
				$query="select id from #__users ORDER BY id DESC LIMIT 0,1";
				$db->setQuery($query);
				$max_id=$db->loadObject();
				
				$max_id=$max_id->id;
				
				
				
				$status = 0;
				
				//if($kind_active==1){

					$body ='Thank you for joining as a member of POISE Real Estate. As a member, you will receive the latest updates and announcements.!<br>';
					$body.='Below are your login details. To access your account and update your member details, visit: <br>';			
					$body.='<a href="www.poisereealestate.com" target="_blank">www.poisereealestate.com</a><br><br>';
					
					$body.='Thank you.<br><br>';
                                        $body.='Best regards,<br>';
                                        $body.='POISE Real Estate Pte Ltd<br>';
                                        
                                      
					fwuserHelper::sendMail($data["email"], $name_active, $email_active, 'Membership confirmation', $body );
				//}
				
				// Check the table in so it can be edited.... we are done with it anyway
				$link = 'index.php?option=com_fwuser&view=register&layout=confirm';
				$this->setRedirect($link, $msg);	
		}
		
	}
	
	public function update(){
		
		jimport('joomla.user.user');
		$db = JFactory::getDBO();
		$app = &JFactory::getApplication();
		$config =& JFactory::getConfig();
		$mainframe =& JFactory::getApplication('site');
		require_once JPATH_COMPONENT .'/helpers/fwuser.php';
		
		$name_active=$config->get('fromname');
		$email_active=$config->get('mailfrom');	
		
		// Check for request forgeries.
		//JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$post = JRequest::get('post');
		
		if($this->checkdata($post)){
			
		
		
		$data = array();
		
		$data["name"] = $post["name"];
		$data["email"] = $post["email"];
		$data["address"] = $post["address"];
		$data["city"] = $post["city"];
		$data["company"] = $post["companyname"];
		$data["country"] = $post["country"];
		$data["state"] = "1";
		$data["postal"] = $post["postal"];
		$data["phone"] = $post["officenumber"];
		$data["mobilephone"] = $post["mobile"];
		$data["date"] = date('Y-m-d H:i:s');
		$data["receivetype"] = $this->splitArray($post["chk_receivetype"]);
		$data["propertyupdate"] = $this->splitArray($post["chk_propertyupdate"]);
		$data["newsupdate"] = $this->splitArray($post["chk_newsupdate"]);
		$data["published"] = 1;
		
		//Save data in fwuser_user		
		$model = $this->getModel('user');
		
		//check user is exist in database
		$fwuser = $model->getfwuserbyemail($data["email"]);
		$data["id"] = $fwuser;
		
		
		
		if ($model->store($data)) {
			$msg = JText::_( 'Data updated successfull!' );
		} else {
			$msg = JText::_( 'Data updated failed' );
		}

		
		// Check the table in so it can be edited.... we are done with it anyway
		$link = 'index.php?option=com_fwuser&view=register&layout=confirm';
		$this->setRedirect($link, $msg);	
		}else{
			return false;
		}
		
	}
	
	public function checkdata($data){
		
		jimport('joomla.mail.helper');
		
		if(empty($data["name"])){
			
			$this->setError(JText::_('Please fill in Name in Full! '));
			return false;
			
		}
		if(empty($data["companyname"])){
			
			$this->setError(JText::_('Please fill in Company name! '));
			return false;
			
		}
		if(empty($data["mobile"])){
			
			$this->setError(JText::_('Please fill in Mobile Phone! '));
			return false;
			
		}
		if(empty($data["officenumber"])){
			
			$this->setError(JText::_('Please fill in Office Number! '));
			return false;
			
		}
		if(empty($data["password"])){
			
			$this->setError(JText::_('Please fill in Password! '));
			return false;
			
		}
		if(empty($data["confirmpassword"])){
			
			$this->setError(JText::_('Please fill in confirm password! '));
			return false;
			
		}elseif($data["confirmpassword"] != $data["password"]){
			$this->setError(JText::_('Confirm password not the same Password! '));
			return false;
		}
		
		if(empty($data["email"])){
			
			$this->setError(JText::_('Please fill in email! '));
			return false;
			
		}elseif(JMailHelper::isEmailAddress($data["email"])){
			$this->setError(JText::_('Email in valid! '));
			return false;
		}
		
		if(empty($data["address"])){
			
			$this->setError(JText::_('Please fill in Address! '));
			return false;
			
		}
		if(empty($data["city"])){
			
			$this->setError(JText::_('Please fill in City! '));
			return false;
			
		}
		if(empty($data["postal"])){
			
			$this->setError(JText::_('Please fill in Postal! '));
			return false;
			
		}
		
		return true;
	}
	
	public function splitArray($array){
		
		for ($i=0; $i<count($array); $i++) {
			$result .= $array[$i].',';
		}
		
		return $result;
	}
}
