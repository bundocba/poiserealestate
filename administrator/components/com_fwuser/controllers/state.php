<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controlleradmin');
class fwuserControllerstate extends JControllerAdmin{
	function display($cachable = false){
		JRequest::setVar('view', 'state');
		parent::display($cachable);
	}
	public function getModel($name = 'stateedit', $prefix = 'fwuserModel'){
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	public function delete(){
		$db = JFactory::getDBO();
		$arr_id=$_POST["cid"];
		$return = 'index.php?option=com_fwuser&view=state';
		for($i=0;$i<count($arr_id);$i++){
			$sql="DELETE from #__fwuser_state WHERE `id`='".$arr_id[$i]."' ";
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
		$return = 'index.php?option=com_fwuser&view=state';
		for($i=0;$i<count($arr_id);$i++){

		}
		return parent::publish();
	}
}