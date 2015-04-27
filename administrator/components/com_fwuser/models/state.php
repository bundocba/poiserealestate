<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modellist');
class fwuserModelstate extends JModelList{
	protected function getListQuery(){		
		$db = JFactory::getDBO();
		$mainframe = JFactory::getApplication();
		$country_id=$mainframe->getUserStateFromRequest('com_fwuser.state.country_id','country_id',0,'cmd');
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__fwuser_state');
		if($country_id!=0){
			$query->where('country_id='.$country_id);
		}
		$query->order('name ASC');

		return $query;
	}
}