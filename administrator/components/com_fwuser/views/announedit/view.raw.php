<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
class fwuserViewannoun extends JViewLegacy{
	function display($tpl = null){
		$db = JFactory::getDBO();
		$mainframe = JFactory::getApplication();
		$id=JRequest::getVar('id',0);
		$query ="select * from #__fwuser_announ";
		$query .=" where announ_id='".$id."' ";
		$query.=" order by name ";
		$db->setQuery($query);
		$rows_state=$db->loadObjectList();

		$options = array();
		$options[] = JHTML::_('select.option', 0, JText::_('FI_ANNOUN'));
		for($i=0;$i<count($rows_state);$i++){
			$options[] = JHTML::_('select.option', $rows_state[$i]->id, $rows_state[$i]->name);
		}
		$dropdown_announ = JHTML::_('select.genericlist', $options, 'jform[announ]', 'class="inputbox" id="announ"', 'value', 'text', 0);
		echo $dropdown_announ;
	}	
}