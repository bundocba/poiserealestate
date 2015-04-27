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

class JFormFieldBaths extends JFormFieldList
{
    protected $type = 'Baths';

    public function getOptions($fractions = true)
	{
        JFactory::getLanguage()->load('com_iproperty', JPATH_ADMINISTRATOR);
        $options    = array();
        
        // check ip settings to see if we're using fractions, or if requested by xml
        $settings   = ipropertyAdmin::config();
        $nformat	= $settings->nformat ? '.' : ',';
        $fractions  = (!$fractions) ? false : (($settings->baths_fraction || (isset($this->element) && $this->element['fractions'])) ? true : false);

        // set high and low values
        $lowbaths   = $settings->adv_baths_low;
        $highbaths  = $settings->adv_baths_high;
        
        // loop through and create fractional values if needed, otherwise int
        for($i = $lowbaths; $i <= $highbaths; $i++){
            $options[] = array('value' => $i, 'text' => $i);
            if($fractions && $i != $highbaths){
                $options[] = array('value' => $i.'.25', 'text' => $i.$nformat.'25');
                $options[] = array('value' => $i.'.5', 'text' => $i.$nformat.'5');
                $options[] = array('value' => $i.'.75', 'text' => $i.$nformat.'75');
            }
        }
        
        // Merge any additional options in the XML definition.
		if(isset($this->element))
        {            
            $options = array_merge(parent::getOptions(true), $options);
            array_unshift($options, JHtml::_('select.option', '', JText::_('COM_IPROPERTY_BATHS')));
        }

        return $options;
    }
}
