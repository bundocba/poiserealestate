<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modeladmin');
class fwuserModelcountryEdit extends JModelAdmin{
	protected function allowEdit($data = array(), $key = 'id'){
		return JFactory::getUser()->authorise('core.edit',
			'com_fwuser.country.'.((int) isset($data[$key]) ? $data[$key] : 0)) or parent::allowEdit($data, $key);
	}
	public function getTable($type = 'country', $prefix = 'fwuserTable', $config = array()){
		return JTable::getInstance($type, $prefix, $config);
	}
	public function getForm($data = array(), $loadData = true){
		$form = $this->loadForm('com_fwuser.country',
								'countryedit',
								array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)){
			return false;
		}
		return $form;
	}
	protected function loadFormData(){
		$data = JFactory::getApplication()->getUserState('com_fwuser.edit.country.data', array());
		
		if (empty($data)){
			$data = $this->getItem();
		}
		
		return $data;
	}
	public function getScript(){
		return 'administrator/components/com_fwuser/models/forms/country.js';
	}
}
