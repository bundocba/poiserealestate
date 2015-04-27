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

class JFormFieldCounty extends JFormFieldList
{
    protected $type = 'County';

    public function getOptions($city = false)
	{
        $city = (isset($this->element) && $this->element['city']) ? $this->element['city'] : $city;
        
        JFactory::getLanguage()->load('com_iproperty', JPATH_ADMINISTRATOR);
        $options = array();

		$db     = JFactory::getDbo();
        $query  = $db->getQuery(true);
        $query->select('DISTINCT(county) AS value, county AS text')
            ->from('#__iproperty')
            ->where('state = 1')
            ->where('county != ""');
            if($city){
                $query->where('city = '.$db->Quote($city));
            }
        $query->order('county ASC');
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
            array_unshift($options, JHtml::_('select.option', '', JText::_('COM_IPROPERTY_COUNTY')));
        }
        
        return $options;
    }
}


