<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modellist');
class fwuserModeluser extends JModelList{
	protected function getListQuery(){		
		$db = JFactory::getDBO();
		$mainframe = JFactory::getApplication();

		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__fwuser_user');
		$query->order('name ASC');

		return $query;
	}
}