<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controlleradmin');
class fwuserControllerannoun extends JControllerAdmin{
	function display($cachable = false){
		JRequest::setVar('view', 'announ');
		parent::display($cachable);
	}
	public function getModel($name = 'announedit', $prefix = 'fwuserModel'){
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	public function delete(){
            
		$db = JFactory::getDBO();
		$arr_id=$_POST["cid"];
		$return = 'index.php?option=com_fwuser&view=announ';
		for($i=0;$i<count($arr_id);$i++){
			$sql="DELETE from #__fwuser_announ WHERE `id`='".$arr_id[$i]."' ";
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
		$return = 'index.php?option=com_fwuser&view=announ';
		for($i=0;$i<count($arr_id);$i++){

		}
		return parent::publish();
	}
}