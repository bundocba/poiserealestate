<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
class fwuserViewActiveregister extends JViewLegacy{
	function display($tpl = null){
		$mainframe = JFactory::getApplication();
		$db=& JFactory::getDBO();
		
		$secu			=JRequest::getVar('secu','');
		
		$query="select * from #__users where `block`='1' ";
		$db->setQuery($query);
		$_check_exist_user=$db->loadObjectList();
		$user_id=0;
		$email = "";
		for($i=0;$i<count($_check_exist_user);$i++){
			if(MD5($_check_exist_user[$i]->id)==$secu){
				$user_id=$_check_exist_user[$i]->id;
				$email = $_check_exist_user[$i]->email;
				break;
			}
		}
		if(count($_check_exist_user)<1 || $user_id==0 ){
			$mainframe->redirect(JURI::root(),'Can not find user!','error');
		}

		$instance = JUser::getInstance($user_id);
		$acl = JFactory::getACL();
		$data=array();
		$data['id']				=$user_id;
		$data['block']			=0;
		if (!$instance->bind($data)) {
			return JError::raiseWarning('SOME_ERROR_CODE', $instance->getError());
		}
		if (!$instance->save()) {
			return JError::raiseWarning('SOME_ERROR_CODE', $instance->getError());
		}

		$query = "UPDATE #__fwuser_user SET `published`='1' WHERE `email`='".$email."'";
		$db->setQuery( $query );
		$db->query();
		
		$mainframe->redirect(JURI::root(),'Activate user successfully, you can login now!');		
	}
}