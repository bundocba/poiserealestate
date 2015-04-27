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

require_once (JPATH_SITE.'/components/com_iproperty/helpers/html.helper.php');
require_once (JPATH_ADMINISTRATOR.'/components/com_iproperty/classes/admin.class.php');

class JFormFieldPrice extends JFormFieldList
{
    protected $type = 'Price';

    public function getOptions($do_high = false, $increment = false)
	{
        $do_high    = (isset($this->element) && $this->element['do_high']) ? true : $do_high;
        $increment  = (isset($this->element) && $this->element['increment']) ? $this->element['increment'] : $increment;
        
        JFactory::getLanguage()->load('com_iproperty', JPATH_ADMINISTRATOR);
        $options = array();
        
        // get high and low price values from ip settings
        $settings   = ipropertyAdmin::config();
        $high       = $settings->adv_price_high;
        $low        = $settings->adv_price_low;
        $steps      = $increment ? ($do_high ? ceil(($high - $low) / $increment) : floor(($high - $low) / $increment) ) : 10; // you can edit this to make more or fewer steps
        $increment  = $increment ? $increment : ($high - $low) / $steps;

        $i          = 0;
        $t_price    = $low;
        $temp_price = '';

        while ($i <= $steps) 
        {
            if ($i == 0 && $settings->adv_nolimit && !$do_high ) {
                $temp_value = '';
                $temp_price = ipropertyHTML::getFormattedPrice(0, '', true);
            } else if ($i == $steps && $settings->adv_nolimit && $do_high) {
                $temp_value = '';
                $temp_price = ipropertyHTML::getFormattedPrice($high, '', true) . '+';
            } else {
                $temp_value = $t_price;
                $temp_price = ipropertyHTML::getFormattedPrice($t_price, '', true);
            }
            $options[$temp_value]   =  $temp_price;
            
            $t_price    = $t_price + $increment;
            $i++;
        }
        
        // Merge any additional options in the XML definition.
		if(isset($this->element))
        {            
            $options = array_merge(parent::getOptions(), $options);
            array_unshift($options, JHtml::_('select.option', '', JText::_('COM_IPROPERTY_PRICE')));
        }

		return $options;
    }
}


