<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controllerform');
class fwuserControllerannounEdit extends JControllerForm{
	public function __construct($config = array()){
		parent::__construct($config);		
		$this->view_list = 'announ';
	}
	public function save(){
		$db = JFactory::getDBO();
		$id = JRequest::getVar('id', 0);
		$return = 'index.php?option=com_fwuser&task=announedit.edit&id=' . $id;

		return parent::save();
	}
}