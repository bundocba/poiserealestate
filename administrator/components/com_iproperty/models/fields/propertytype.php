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

class JFormFieldPropertyType extends JFormFieldList
{
    protected $type = 'PropertyType';

    public function getOptions($available = false)
	{
        $available = (isset($this->element) && $this->element['available']) ? true : $available;
        
        JFactory::getLanguage()->load('com_iproperty', JPATH_ADMINISTRATOR);
        $options = array();

		$db     = JFactory::getDbo();
        $query  = $db->getQuery(true);
        $query->select('DISTINCT(c.id), c.id AS value, c.title AS text')
            ->from('#__iproperty_propertytype as c');
        if($available){
            $query->join('INNER','#__iproperty AS p ON p.propertytype = c.id');
        }
        $query->order('c.title ASC');
		
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
		if(isset($this->element))
        {            
            $options = array_merge(parent::getOptions(), $options);
            array_unshift($options, JHtml::_('select.option', '', JText::_('COM_IPROPERTY_PROPERTYTYPE')));
        }
        
        return $options;
    }
}


