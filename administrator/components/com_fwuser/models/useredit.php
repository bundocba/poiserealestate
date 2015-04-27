<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modeladmin');
class fwuserModeluserEdit extends JModelAdmin{
	protected function allowEdit($data = array(), $key = 'id'){
		return JFactory::getUser()->authorise('core.edit',
			'com_fwuser.user.'.((int) isset($data[$key]) ? $data[$key] : 0)) or parent::allowEdit($data, $key);
	}
	public function getTable($type = 'users', $prefix = 'fwuserTable', $config = array()){
		return JTable::getInstance($type, $prefix, $config);
	}
	public function getForm($data = array(), $loadData = true){
		$form = $this->loadForm('com_fwuser.user',
								'useredit',
								array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)){
			return false;
		}
		return $form;
	}
	protected function loadFormData(){
		$data = JFactory::getApplication()->getUserState('com_fwuser.edit.user.data', array());
		
		if (empty($data)){
			$data = $this->getItem();
		}
		
		return $data;
	}
	public function getScript(){
		return 'administrator/components/com_fwuser/models/forms/user.js';
	}
	
	public function store(){
		$row=& $this->getTable();
		$post=JRequest::get('post');
		
		$data = array();
		
		$data["name"] = $post["name"];
		$data["email"] = $post["email"];
		$data["address"] = $post["address"];
		$data["city"] = $post["city"];
		$data["country"] = $post["country"];
		$data["state"] = $post["state"];
		$data["phone"] = $post["phone"];
		$data["mobilephone"] = $post["mobilephone"];
		$data["date"] = $post["date"];
		$data["receivetype"] = $this->splitArray($post["receivetype"]);
		$data["propertyupdate"] = $this->splitArray($post["propertyupdate"]);
		$data["newsupdate"] = $this->splitArray($post["newsupdate"]);
		$data["published"] = 1;
		
		//ràng bu?c các trý?ng trong form
		//Phýõng th?c bind() s? copy d? li?u t? m?ng vào thu?c tính týõng ?ng c?a ð?i tý?ng table
		if(!$row->bind($data))	
		{
			$this->setError($this->_db->getErrorMsg);
			return false;
		}
		//ki?m tra giá tr? các trý?ng trong form
		if(!$row->check())
		{
			$this->setError($this->_db->getErrorMsg);
			return false;
		}
		//Store the web link table to the database 
		//Phýõng th?c store() l?y d? li?u t? ð?i tý?ng và lýu vào CSDL
		//N?u id=0 nó s? thêm m?i või l?nh insert,ngý?c l?i s? update d? li?u
		if(!$row->store())
		{
			$this->setError($this->_db->getErrorMsg);
			return false;
		}
		return true;
	}
}
