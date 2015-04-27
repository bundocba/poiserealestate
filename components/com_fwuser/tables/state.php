<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.database.table');
class fwuserTablestate extends JTable{
	function __construct(&$db){
		parent::__construct('#__fwuser_state', 'id', $db);
	}
	public function bind($array, $ignore = ''){
		if (isset($array['params']) && is_array($array['params'])){
			$parameter = new JRegistry;
			$parameter->loadArray($array['params']);
			$array['params'] = (string)$parameter;
		}
		return parent::bind($array, $ignore);
	}
	public function load($pk = null, $reset = true){
		if (parent::load($pk, $reset)){
			$params = new JRegistry;
			$params->loadString(@$this->params);
			$this->params = $params;
			return true;
		}else{
			return false;
		}
	}
	protected function _getAssetName(){
		$k = $this->_tbl_key;
		return 'com_fwuser.state.'.(int) $this->$k;
	}
	protected function _getAssetTitle(){
		return $this->greeting;
	}
	protected function _getAssetParentId(JTable $table = NULL, $id = NULL){
		$asset = JTable::getInstance('Asset');
		$asset->loadByName('com_fwuser');
		return $asset->id;
	}
}
