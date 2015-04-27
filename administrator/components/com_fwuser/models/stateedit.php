<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modeladmin');
class fwuserModelstateEdit extends JModelAdmin{
	protected function allowEdit($data = array(), $key = 'id'){
		return JFactory::getUser()->authorise('core.edit',
			'com_fwuser.state.'.((int) isset($data[$key]) ? $data[$key] : 0)) or parent::allowEdit($data, $key);
	}
	public function getTable($type = 'state', $prefix = 'fwuserTable', $config = array()){
		return JTable::getInstance($type, $prefix, $config);
	}
	public function getForm($data = array(), $loadData = true){
		$form = $this->loadForm('com_fwuser.state',
								'stateedit',
								array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)){
			return false;
		}
		return $form;
	}
	protected function loadFormData(){
		$data = JFactory::getApplication()->getUserState('com_fwuser.edit.state.data', array());
		
		if (empty($data)){
			$data = $this->getItem();
		}
		
		return $data;
	}
	public function getScript(){
		return 'administrator/components/com_fwuser/models/forms/state.js';
	}
}
