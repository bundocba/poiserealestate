<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controlleradmin');
class fwuserControlleruser extends JControllerAdmin{
	function display($cachable = false){
		JRequest::setVar('view', 'user');
		parent::display($cachable);
	}
	public function getModel($name = 'useredit', $prefix = 'fwuserModel'){
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	public function delete(){
		$db = JFactory::getDBO();
		$arr_id=$_POST["cid"];
		$return = 'index.php?option=com_fwuser&view=user';
		for($i=0;$i<count($arr_id);$i++){
			$query="select id from #__fwuser_user where `id`='".$arr_id[$i]."' ";
			$db->setQuery($query);
			$row_user=$db->loadObject();

			$sql="DELETE from #__users WHERE `id`='".$row_user->user_id."' ";
			$db->setQuery($sql);
			$db->query();
		}
		return parent::delete();
	}
	public function publish(){
		$db = JFactory::getDBO();
		$arr_id=$_POST["cid"];
		$task=$_POST["task"];
		if($task=='unpublish'){
			$block=1;
		}else{
			$block=0;
		}
		$return = 'index.php?option=com_fwuser&view=user';
		for($i=0;$i<count($arr_id);$i++){
			$query="select id from #__fwuser_user where `id`='".$arr_id[$i]."' ";
			$db->setQuery($query);
			$row_user=$db->loadObject();

			$sql="UPDATE #__users SET `block`='".$block."' WHERE `id`='".$row_user->user_id."' ";
			$db->setQuery($sql);
			$db->query();
		}
		return parent::publish();
	}
}