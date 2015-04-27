<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die;

abstract class JHtmlIpAdministrator
{
	public static function featured($value = 0, $i, $canChange = true, $controller = false)
	{
		if(!$controller) return;

        // Array of image, task, title, action
		$states	= array(
			0	=> array('star-empty',	$controller.'.feature',	'COM_IPROPERTY_UNFEATURED',	'COM_IPROPERTY_FEATURE'),
			1	=> array('star',		$controller.'.unfeature',	'COM_IPROPERTY_FEATURED',		'COM_IPROPERTY_UNFEATURE'),
		);
		$state	= JArrayHelper::getValue($states, (int) $value, $states[1]);
		$icon	= $state[0];
        
        $html = '';
		if ($canChange) {
			$html	= '<a href="#" onclick="return listItemTask(\'cb'.$i.'\',\''.$state[1].'\')" class="btn btn-micro ' . ($value == 1 ? 'active' : '') . '" rel="tooltip" title="'.JText::_($state[3]).'"><i class="icon-'
					. $icon.'"></i></a>';
		}

		return $html;
	}

    public static function super($value = 0, $i, $canChange = true, $controller = false)
	{
		if(!$controller) return;
        
        // Array of image, task, title, action
		$states	= array(
			0	=> array('minus',	$controller.'.super',	'COM_IPROPERTY_UNSUPER',	'COM_IPROPERTY_SUPER'),
			1	=> array('plus',		$controller.'.unsuper',	'COM_IPROPERTY_SUPER',		'COM_IPROPERTY_UNSUPER'),
		);
		$state	= JArrayHelper::getValue($states, (int) $value, $states[1]);
		$icon	= $state[0];
        
        $html = '';
		if ($canChange) {
			$html	= '<a href="#" onclick="return listItemTask(\'cb'.$i.'\',\''.$state[1].'\')" class="btn btn-micro ' . ($value == 1 ? 'active' : '') . '" rel="tooltip" title="'.JText::_($state[3]).'"><i class="icon-'
					. $icon.'"></i></a>';
		}

		return $html;
	}
    
    public static function approved($value = 0, $i, $canChange = true, $controller = false)
	{
		if(!$controller) return;
        
        // Array of image, task, title, action
		$states	= array(
			0	=> array('thumbs-down',	$controller.'.approve',	'COM_IPROPERTY_UNAPPROVED',	'COM_IPROPERTY_APPROVE'),
			1	=> array('thumbs-up',		$controller.'.unapprove',	'COM_IPROPERTY_APPROVED',		'COM_IPROPERTY_UNAPPROVE'),
		);
		$state	= JArrayHelper::getValue($states, (int) $value, $states[1]);
		$icon	= $state[0];
        
        $html = '';
		if ($canChange) {
			$html	= '<a href="#" onclick="return listItemTask(\'cb'.$i.'\',\''.$state[1].'\')" class="btn btn-micro ' . ($value == 1 ? 'active btn-success' : 'btn-danger') . '" rel="tooltip" title="'.JText::_($state[3]).'"><i class="icon-'
					. $icon.'"></i></a>';
		}

		return $html;
	}
    
    public static function calendar($value, $name, $id, $format = '%Y-%m-%d', $attribs = null)
	{
        static $done;        

		if ($done === null)
		{
			$done = array();
		}

		$readonly = isset($attribs['readonly']) && $attribs['readonly'] == 'readonly';
		$disabled = isset($attribs['disabled']) && $attribs['disabled'] == 'disabled';
        $showtime = (isset($attribs['showtime']) && $attribs['showtime'] == 'true') ? 'true' : 'false';
        
		if (is_array($attribs))
		{
			$attribs = JArrayHelper::toString($attribs);
		}

		if (!$readonly && !$disabled)
		{
			// Load the calendar behavior
			JHtml::_('behavior.calendar');
			JHtml::_('bootstrap.tooltip');

			// Only display the triggers once for each control.
			if (!in_array($id, $done))
			{
				$document = JFactory::getDocument();
				$document
					->addScriptDeclaration(
					'window.addEvent(\'domready\', function() {Calendar.setup({
                    // Id of the input field
                    inputField: "' . $id . '",
                    // Format of the input field
                    ifFormat: "' . $format . '",
                    // Trigger for the calendar (button ID)
                    button: "' . $id . '_img",
                    // Alignment (defaults to "Bl")
                    align: "Tl",
                    singleClick: true,
                    firstDay: ' . JFactory::getLanguage()->getFirstDay() . ',
                    showsTime: '.$showtime.'
                    });});'
				);
				$done[] = $id;
			}
			return '<div class="input-append"><input type="text" title="' . (0 !== (int) $value ? JHtml::_('date', $value) : '') . '" name="' . $name . '" id="' . $id
				. '" value="' . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '" ' . $attribs . ' /><button class="btn" id="' . $id . '_img"><i class="icon-calendar"></i></button></div>';

		}
		else
		{
			return '<input type="text" title="' . (0 !== (int) $value ? self::_('date', $value, null, null) : '')
				. '" value="' . (0 !== (int) $value ? JHtml::_('date', $value, 'Y-m-d H:i', null) : '') . '" ' . $attribs
				. ' /><input type="hidden" name="' . $name . '" id="' . $id . '" value="' . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '" />';
		}
	}
}