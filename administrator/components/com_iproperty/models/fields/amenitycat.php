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

class JFormFieldAmenityCat extends JFormFieldList
{
    protected $type = 'AmenityCat';

	public function getOptions()
	{
		$options = array( 0 => JText::_('COM_IPROPERTY_GENERAL_AMENITIES'), 
                          1 => JText::_('COM_IPROPERTY_INTERIOR_AMENITIES'), 
                          2 => JText::_('COM_IPROPERTY_EXTERIOR_AMENITIES'),
						  3 => JText::_('COM_IPROPERTY_ACCESSIBILITY_AMENITIES'),
						  4 => JText::_('COM_IPROPERTY_GREEN_AMENITIES'),
						  5 => JText::_('COM_IPROPERTY_SECURITY_AMENITIES'),
						  6 => JText::_('COM_IPROPERTY_LANDSCAPE_AMENITIES'),
						  7 => JText::_('COM_IPROPERTY_COMMUNITY_AMENITIES'),
						  8 => JText::_('COM_IPROPERTY_APPLIANCE_AMENITIES')
		);
        asort($options);
        
        return $options;
	}
}