<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controlleradmin');
class fwuserControllercountry extends JControllerAdmin{
	function display($cachable = false){
		JRequest::setVar('view', 'country');
		parent::display($cachable);
	}
	public function getModel($name = 'countryedit', $prefix = 'fwuserModel'){
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	public function delete(){
		$db = JFactory::getDBO();
		$arr_id=$_POST["cid"];
		$return = 'index.php?option=com_fwuser&view=country';
		for($i=0;$i<count($arr_id);$i++){
			$query="select id from #__fwuser_state WHERE `country_id`='".$arr_id[$i]."' ";
			$db->setQuery($query);
			$row_check=$db->loadObjectList();

			if(count($row_check)<=0){
				$sql="DELETE from #__fwuser_country WHERE `id`='".$arr_id[$i]."' ";
				$db->setQuery($sql);
				$db->query();
			}else{
				$this->setRedirect( $return, JText::_("COM_FWUSER_CANNOT_DELETE"),"error" );
				return false;
			}
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
		$return = 'index.php?option=com_fwuser&view=country';
		for($i=0;$i<count($arr_id);$i++){

		}
		return parent::publish();
	}
}