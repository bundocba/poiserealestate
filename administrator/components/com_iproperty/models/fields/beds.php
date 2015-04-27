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

require_once (JPATH_ADMINISTRATOR.'/components/com_iproperty/classes/admin.class.php');

class JFormFieldBeds extends JFormFieldList
{
    protected $type = 'Beds';

    public function getOptions()
	{
        JFactory::getLanguage()->load('com_iproperty', JPATH_ADMINISTRATOR);
        $options    = array();
        
        // get high and low values from ip settings
        $settings   = ipropertyAdmin::config();
        $lowbeds    = $settings->adv_beds_low;
        $highbeds   = $settings->adv_beds_high;

        // loop through between high and low values to create list
        for($i = $lowbeds; $i <= $highbeds; $i++){
            $options[] = array('value' => $i, 'text' => $i);
        }
        
        // Merge any additional options in the XML definition.
		if(isset($this->element))
        {            
            $options = array_merge(parent::getOptions(), $options);
            array_unshift($options, JHtml::_('select.option', '', JText::_('COM_IPROPERTY_BEDS')));
        }

        return $options;
    }
}


