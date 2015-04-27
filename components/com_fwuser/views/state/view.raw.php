<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
class fwuserViewstate extends JViewLegacy{
	function display($tpl = null){
		$db = JFactory::getDBO();
		$mainframe = JFactory::getApplication();
		$id=JRequest::getVar('id',0);
		$prefix=JRequest::getVar('prefix','');
		$query ="select * from #__fwuser_state";
		$query .=" where country_id='".$id."' ";
		$query.=" order by name ";
		$db->setQuery($query);
		$rows_state=$db->loadObjectList();

		$options = array();
		$options[] = JHTML::_('select.option', 0, JText::_('FI_STATE'));
		for($i=0;$i<count($rows_state);$i++){
			$options[] = JHTML::_('select.option', $rows_state[$i]->id, $rows_state[$i]->name);
		}
		if($prefix=='ship'){ //dung cho phan checkout1 cua com_fwproduct
			$dropdown_state = JHTML::_('select.genericlist', $options, 'state_ship', 'class="inputbox" id="state"', 'value', 'text', 0);
		}else{
			$dropdown_state = JHTML::_('select.genericlist', $options, 'state', 'class="inputbox" id="state"', 'value', 'text', 0);
		}
		echo $dropdown_state;
	}	
}