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

class JHtmlIcon
{
	public static function create($type = 'property', $attribs = array())
	{
		$uri = JURI::getInstance();
        
        switch($type)
        {
            case 'property':
                $controller = 'propform';
                break;
            case 'agent':
                $controller = 'agentform';
                break;
            case 'company':
                $controller = 'companyform';
                break;
        }

		$url    = 'index.php?option=com_iproperty&view='.$controller.'&task='.$controller.'.add&return='.base64_encode($uri).'&id=0';
        $text   = '<i class="icon-plus"></i> '.JText::_('JNEW').'&#160;';

		$button =  JHtml::_('link', JRoute::_($url), $text, 'class="btn btn-primary"');
        $output = '<span class="hasTooltip" title="'.JText::_('COM_IPROPERTY_CREATE_ITEM').'">'.$button.'</span>';
        return $output;
	}

	public static function edit($object, $type = 'property', $toolbar = false, $attribs = array())
	{
		// Initialise variables.
		$user	= JFactory::getUser();
		$userId	= $user->get('id');
		$uri    = JURI::getInstance();

		// Ignore if the state is negative (trashed).
		if ($object->state < 0) {
			return;
		}
        
        switch($type)
        {
            case 'property':
                $controller = 'propform';
                break;
            case 'agent':
                $controller = 'agentform';
                break;
            case 'company':
                $controller = 'companyform';
                break;
        }

		// Show checked_out icon if the article is checked out by a different user
		if (property_exists($object, 'checked_out') && property_exists($object, 'checked_out_time') && $object->checked_out > 0 && $object->checked_out != $user->get('id')) 
        {
            $checkoutUser   = JFactory::getUser($object->checked_out); 
            $url            = 'index.php?option=com_iproperty&view='.$controller.'&task='.$controller.'.checkin&id='.$object->id.'&return='.base64_encode($uri).'&'.JSession::getFormToken().'=1';
            
            $date			= addslashes(htmlspecialchars(JHtml::_('date', $object->checked_out_time, JText::_('DATE_FORMAT_LC')), ENT_COMPAT, 'UTF-8'));
            $time			= addslashes(htmlspecialchars(JHtml::_('date', $object->checked_out_time, 'H:i'), ENT_COMPAT, 'UTF-8'));
            $text           = '<span class="icon-eye-close"></span>';

            $attribs['title']	= JText::_('JLIB_HTML_CHECKED_OUT').'::'.JText::sprintf('COM_IPROPERTY_CHECKED_OUT_BY', $checkoutUser->name).'<br />'.$date.'<br />'.$time;
            $attribs['rel']		= 'nofollow';

            return JHtml::_('link', JRoute::_($url), $text, $attribs);
		}

		$url	= 'index.php?option=com_iproperty&view='.$controller.'&task='.$controller.'.edit&id='.$object->id.'&return='.base64_encode($uri);

		if ($object->state == 0) {
			$overlib = JText::_('JUNPUBLISHED');
		}
		else {
			$overlib = JText::_('JPUBLISHED');
		}

		if ($type == 'property')
        {
            $date = JHtml::_('date',$object->created);
            $author = $object->created_by ? JFactory::getUser($object->created_by)->name : 'Admin';

            $overlib .= '&lt;br /&gt;';
            $overlib .= $date;
            $overlib .= '&lt;br /&gt;';
            $overlib .= JText::sprintf('COM_IPROPERTY_CREATED_BY', htmlspecialchars($author, ENT_COMPAT, 'UTF-8'));
        }
        
        $icon	= $object->state ? 'edit' : 'eye-close';
		$text = '<span class="icon-'.$icon.'"></span>';
        
        if(!$toolbar) $text .= ' '.JText::_('JGLOBAL_EDIT');
        
        $attribs['title']	= JText::_('JGLOBAL_EDIT').' :: '.$overlib;
        $attribs['rel']		= 'nofollow';

		return JHtml::_('link', JRoute::_($url), $text, $attribs);
	}
    
    public static function print_popup($item, $attribs = array(), $width = 800, $height = 480)
	{
		$url = ipropertyHelperRoute::getPropertyRoute($item->id.':'.$item->alias);
        $url .= '&layout=default_print&tmpl=component&print=1';

		$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width='.(int)$width.',height='.(int)$height.',directories=no,location=no';

		$text = '<i class="icon-print"></i>';

		$attribs['title']	= JText::_('JGLOBAL_PRINT');
		$attribs['onclick'] = "window.open(this.href,'win2','".$status."'); return false;";
		$attribs['rel']		= 'nofollow';

		return JHtml::_('link', JRoute::_($url), $text, $attribs);
	}
    
    public static function print_screen()
	{
		return '<a href="#" onclick="window.print();return false;"><i class="icon-print"></i> '.JText::_('JGLOBAL_PRINT').'</a>';
	}
    
    public static function featured($value = 0, $i, $canChange = true, $controller = false)
	{
		if (!$controller) return;
        $html = '';

        // Array of image, task, title, action
		$states	= array(
			0	=> array('star-empty',	$controller.'.feature',	'COM_IPROPERTY_UNFEATURED',	'COM_IPROPERTY_FEATURE'),
			1	=> array('star',		$controller.'.unfeature',	'COM_IPROPERTY_FEATURED',		'COM_IPROPERTY_UNFEATURE'),
		);
		$state	= JArrayHelper::getValue($states, (int) $value, $states[1]);
		$icon	= $state[0];
		if ($canChange) {
			$html	= '<a href="#" onclick="return listItemTask(\'cb'.$i.'\',\''.$state[1].'\')" class="btn btn-micro hasTooltip ' . ($value == 1 ? 'active' : '') . '" rel="tooltip" title="'.JText::_($state[3]).'"><i class="icon-'
					. $icon.'"></i></a>';
		}

		return $html;
	}

    public static function super($value = 0, $i, $canChange = true, $controller = false)
	{
		if (!$controller) return;
        
        // Array of image, task, title, action
		$states	= array(
			0	=> array('minus',	$controller.'.super',	'COM_IPROPERTY_UNSUPER',	'COM_IPROPERTY_SUPER'),
			1	=> array('plus',		$controller.'.unsuper',	'COM_IPROPERTY_SUPER',		'COM_IPROPERTY_UNSUPER'),
		);
		$state	= JArrayHelper::getValue($states, (int) $value, $states[1]);
		$icon	= $state[0];
		if ($canChange) {
			$html	= '<a href="#" onclick="return listItemTask(\'cb'.$i.'\',\''.$state[1].'\')" class="btn btn-micro hasTooltip ' . ($value == 1 ? 'active' : '') . '" rel="tooltip" title="'.JText::_($state[3]).'"><i class="icon-'
					. $icon.'"></i></a>';
		}

		return $html;
	}
    
    public static function approved($value = 0, $i, $canChange = true, $controller = false)
	{
		if (!$controller) return;
        
        // Array of image, task, title, action
		$states	= array(
			0	=> array('thumbs-down',	$controller.'.approve',	'COM_IPROPERTY_UNAPPROVED',	'COM_IPROPERTY_APPROVE'),
			1	=> array('thumbs-up',		$controller.'.unapprove',	'COM_IPROPERTY_APPROVED',		'COM_IPROPERTY_UNAPPROVE'),
		);
		$state	= JArrayHelper::getValue($states, (int) $value, $states[1]);
		$icon	= $state[0];
		if ($canChange) {
			$html	= '<a href="#" onclick="return listItemTask(\'cb'.$i.'\',\''.$state[1].'\')" class="btn btn-micro hasTooltip ' . ($value == 1 ? 'active btn-success' : 'btn-danger') . '" rel="tooltip" title="'.JText::_($state[3]).'"><i class="icon-'
					. $icon.'"></i></a>';
		}

		return $html;
	}
    
    public static function generic_button($url, $icon_class = 'icon-plus', $attribs = array())
    {
        $text = '<i class="'.$icon_class.'"></i>';
        
        return JHtml::_('link', JRoute::_($url), $text, $attribs);
    }
}