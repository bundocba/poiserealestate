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

class JFormFieldProvince extends JFormFieldList
{
    protected $type = 'Province';

    public function getOptions($locstate = false)
	{
        $locstate = (isset($this->element) && $this->element['locstate']) ? $this->element['locstate'] : $locstate;
        
        JFactory::getLanguage()->load('com_iproperty', JPATH_ADMINISTRATOR);
        $options = array();

        $db     = JFactory::getDbo();
        $query  = $db->getQuery(true);
        $query->select('DISTINCT(province) AS value, province AS text')
            ->from('#__iproperty')
            ->where('state = 1')
            ->where('province != ""');
            if($locstate){
                $query->where('locstate = '.(int)$locstate);
            }
        $query->order('province ASC');

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
            array_unshift($options, JHtml::_('select.option', '', JText::_('COM_IPROPERTY_PROVINCE')));
        }

		return $options;
    }
}


