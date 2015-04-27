<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined('JPATH_BASE') or die;

class JFormFieldAmenity extends JFormField
{
    protected $type = 'Amenity';

	protected function getInput()
	{
		$document = JFactory::getDocument();
        $document->addScript('components/com_iproperty/assets/js/addInput.js');
        
        $amen_language = "
        window.AmenLocale = {
            'general': '".addslashes(JText::_('COM_IPROPERTY_GENERAL_AMENITIES'))."',
            'interior': '".addslashes(JText::_('COM_IPROPERTY_INTERIOR_AMENITIES'))."',
            'exterior': '".addslashes(JText::_('COM_IPROPERTY_EXTERIOR_AMENITIES'))."',
			'accessibility': '".addslashes(JText::_('COM_IPROPERTY_ACCESSIBILITY_AMENITIES'))."',
			'green': '".addslashes(JText::_('COM_IPROPERTY_GREEN_AMENITIES'))."',
			'security': '".addslashes(JText::_('COM_IPROPERTY_SECURITY_AMENITIES'))."',
			'landscape': '".addslashes(JText::_('COM_IPROPERTY_LANDSCAPE_AMENITIES'))."',
			'community': '".addslashes(JText::_('COM_IPROPERTY_COMMUNITY_AMENITIES'))."',
			'appliance': '".addslashes(JText::_('COM_IPROPERTY_APPLIANCE_AMENITIES'))."'
        }";

        JFactory::getDocument()->addScriptDeclaration($amen_language);
        ?>
        <p><?php echo JText::_('COM_IPROPERTY_AMENITY_DESC'); ?></p>
        <div class="btn-group pull-left">
            <button class="btn btn-danger" type="button" onclick="deleteInput();"><i class="icon-minus"></i></button>
            <button class="btn btn-success" type="button" onclick="addInput();"><i class="icon-plus"></i></button>
        </div>
        <div class="clearfix"></div>
        <hr />
        <div class="span12" id="parah" style="text-align: center;"></div>
        <?php
	}
}