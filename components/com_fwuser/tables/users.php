<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.database.table');
class fwuserTableusers extends JTable{
	function __construct(&$db){
		parent::__construct('#__fwuser_user', 'id', $db);
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
	public function store($updateNulls = false)
	{
		// Transform the params field
		if (is_array($this->params)) {
			$registry = new JRegistry;
			$registry->loadArray($this->params);
			$this->params = (string) $registry;
		}
        
        // Verify that the alias is unique
		$table = JTable::getInstance('users', 'fwuserTable');
		if ($table->load(array('alias' => $this->alias)) && ($table->id != $this->id || $this->id == 0))
		{
			$this->setError(JText::_('COM_IPROPERTY_ERROR_UNIQUE_ALIAS'));
			return false;
		}
        
		// Attempt to store the data.
		return parent::store($updateNulls);
	}
	protected function _getAssetName(){
		$k = $this->_tbl_key;
		return 'com_fwuser.user.'.(int) $this->$k;
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
