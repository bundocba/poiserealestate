<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
class fwuserViewLogin extends JViewLegacy
{
	function display($tpl = null){
		$mainframe = JFactory::getApplication();
		$db=& JFactory::getDBO();
		
		$user=&JFactory::getUser();
		$user_id=$user->get('id');
		if($user_id!=0&&$user_id!=''){
			$app = JFactory::getApplication();
			$app->logout();
			
			$session = JFactory::getSession();
			$mess=array('message' => JText::_('LOGOUT_SUCCESS'), 'type' => 'message');
			$mess_all=array($mess);
			$session->set('application.queue', $mess_all);
		}else{
			$username=JRequest::getVar('username','');
			$password=JRequest::getVar('password','');
			
			$query="select * from #__users where `username`='".$username."' AND `block`='0' ";
			$db->setQuery($query);
			$_check_exist_user=$db->loadObject();
			
			if($_check_exist_user->id!=''&&$_check_exist_user->id!=0){
				$options = array();
				$options['remember'] = true;
				$options['return'] = '';

				$credentials = array();
				$credentials['username'] = $username;
				$credentials['password'] = $password;

				$error = $mainframe->login($credentials, $options);
			}else{
				$error = 2;
			}
			if($error==1){
				$session = JFactory::getSession();
				$mess=array('message' => JText::_('LOGIN_SUCCESS'), 'type' => 'message');
				$mess_all=array($mess);
				$session->set('application.queue', $mess_all);
				$session->set('link_url', '');			
			}
			echo $error;
		}
		die;
	}
}