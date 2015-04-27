<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined('JPATH_BASE') or die;
JFormHelper::loadFieldClass('list');

class JFormFieldStypes extends JFormFieldList
{
    protected $type = 'Stypes';

    public function getOptions($available = false)
	{
        $available = (isset($this->element) && $this->element['available']) ? true : $available;
        
        JFactory::getLanguage()->load('com_iproperty', JPATH_ADMINISTRATOR);
        $options = array();
        
        $db     = JFactory::getDbo();
        $query  = $db->getQuery(true);
        $query->select('DISTINCT(s.id), s.id AS value, s.name AS text')
            ->from('#__iproperty_stypes as s')
            ->where('s.state = 1');
            if($available){
                $query->join('INNER','#__iproperty AS p ON p.stype = s.id');
            }
        $query->order('s.name ASC');
        $db->setQuery($query);
        
        try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}
        
        // Merge any additional options in the XML definition.
		if(isset($this->element) && !$this->element['multiple'])
        {            
            $options = array_merge(parent::getOptions(), $options);
            array_unshift($options, JHtml::_('select.option', '', JText::_('COM_IPROPERTY_STYPE')));
        }
        
        return $options;
    }
}