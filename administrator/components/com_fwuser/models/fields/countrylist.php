<?php defined('_JEXEC') or die;
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');
class JFormFieldcountrylist extends JFormFieldList{
	protected $type = 'countrylist';
 
	protected function getOptions(){
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__fwuser_country');
		$query->order('name ASC');
		$db->setQuery((string)$query);
		$rows = $db->loadObjectList();
		
		$options = array();
		if($rows){
			// $options[] = JHtml::_('select.option', 0, JText::_("FI_CONTENT"));
			foreach($rows as $row){
				$options[] = JHtml::_('select.option', $row->id, $row->name);
			}
		}
		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}
}