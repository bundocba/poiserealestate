<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
class fwuserViewannoun extends JViewLegacy{
	function display($tpl = null){
		$db = JFactory::getDBO();
		$mainframe = JFactory::getApplication();
		$id=JRequest::getVar('id',0);
		$prefix=JRequest::getVar('prefix','');
		$query ="select * from #__fwuser_announ";
		$query .=" where announ_id='".$id."' ";
		$query.=" order by name ";
		$db->setQuery($query);
		$rows_announ=$db->loadObjectList();

		$options = array();
		$options[] = JHTML::_('select.option', 0, JText::_('FI_STATE'));
		for($i=0;$i<count($rows_announ);$i++){
			$options[] = JHTML::_('select.option', $rows_announ[$i]->id, $rows_announ[$i]->name);
		}
		if($prefix=='ship'){ //dung cho phan checkout1 cua com_fwproduct
			$dropdown_announ = JHTML::_('select.genericlist', $options, 'state_ship', 'class="inputbox" id="announ"', 'value', 'text', 0);
		}else{
			$dropdown_announ = JHTML::_('select.genericlist', $options, 'state', 'class="inputbox" id="announ"', 'value', 'text', 0);
		}
		echo $dropdown_announ;
	}	
}