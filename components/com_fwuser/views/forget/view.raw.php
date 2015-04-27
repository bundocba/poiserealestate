<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
class fwuserViewForget extends JViewLegacy{
	function display($tpl = null){
		$mainframe = JFactory::getApplication();
		$db=& JFactory::getDBO();
		$email=JRequest::getVar('email','');
		
		$config =& JFactory::getConfig();
		$name_active=$config->get('fromname');
		$email_active=$config->get('mailfrom');

		$query="select * from #__users where `email`='".$email."' ";
		$db->setQuery($query);
		$row_current_user=$db->loadObject();
		if($row_current_user->id==''||$row_current_user->id==0){
			echo '2';
			die;
		}

		$user_id  = $row_current_user->id;
		$user_name= $row_current_user->username;
		$password = md5(microtime());
		$password = substr($password,0,5);
		
		// Save user Joomla
		$instance = JUser::getInstance($user_id);
		$acl = JFactory::getACL();
		$data=array();
		$data['id']			=$user_id;
		$data['password']	=$password;
		$data['password2']	=$password;
		if (!$instance->bind($data)) {
		}
		if (!$instance->save()) {
		}
		
		$body ='Hi, user!<br>';
		$body.='There is your new password of account from our website:<br>';
		$body.='Username:'.$user_name.'<br>';
		$body.='Password:'.$password.'<br>';
		$body.='If you have any question, please contact us!<br><br>';
		$body.='Best regard.';
		fwuserHelper::sendMail( $email, $name_active, $email_active, 'New password', $body );

		echo '1';
		die;
	}
}