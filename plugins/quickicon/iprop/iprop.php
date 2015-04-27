<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined ( '_JEXEC' ) or die ();
require_once(JPATH_ADMINISTRATOR.'/components/com_iproperty/classes/admin.class.php');

class plgQuickiconIprop extends JPlugin 
{
	public function __construct(& $subject, $config) 
    {
		parent::__construct ( $subject, $config );

		// ! Always load language after parent::construct else the name of plugin isn't yet set
		$this->loadLanguage('plg_quickicon_iprop.sys');
	}

	public function onGetIcons($context) 
    {
		if ($context != $this->params->get('context', 'mod_quickicon') || !JFactory::getUser()->authorise('core.manage', 'com_iproperty')) {
			return null;
		}
        
        JFactory::getLanguage()->load('com_iproperty', JPATH_ADMINISTRATOR);

		$updateBadge    = '';
        $updateInfo     = ipropertyAdmin::checkUpdate(false);
        
        if($updateInfo === true){
            $updateBadge    = ' <span class="badge badge-important hasTooltip" title="'.JText::_('PLG_QUICKICON_IPROPERTY_UPDATE_AVAILABLE').'">!</span>';
        }

		$img    = 'folder-open';
        $link   = 'index.php?option=com_iproperty';
        
		return array( 
            array(
			'link' => JRoute::_($link),
			'image' => 'home',
			'text' => JText::_('PLG_QUICKICON_IPROPERTY') . $updateBadge,
			'icon' => '',
			'access' => array('core.manage', 'com_iproperty'),
			'id' => 'com_iproperty_icon' ),
            
            array(
            'link' => JRoute::_($link.'&view=categories'),
			'image' => '',
			'text' => '<span class="small">|-- '.JText::_('COM_IPROPERTY_CATEGORIES').'</span>',
			'icon' => '',
			'access' => array('core.manage', 'com_iproperty'),
			'id' => 'com_iproperty_categories_icon' ),
            
            array(
            'link' => JRoute::_($link.'&view=properties'),
			'image' => '',
			'text' => '<span class="small">|-- '.JText::_('COM_IPROPERTY_PROPERTIES').'</span>',
			'icon' => '',
			'access' => array('core.manage', 'com_iproperty'),
			'id' => 'com_iproperty_props_icon' ),
            
            array(
            'link' => JRoute::_($link.'&view=agents'),
			'image' => '',
			'text' => '<span class="small">|-- '.JText::_('COM_IPROPERTY_AGENTS').'</span>',
			'icon' => '',
			'access' => array('core.manage', 'com_iproperty'),
			'id' => 'com_iproperty_agents_icon' ),
            
            array(
            'link' => JRoute::_($link.'&view=companies'),
			'image' => '',
			'text' => '<span class="small">|-- '.JText::_('COM_IPROPERTY_COMPANIES').'</span>',
			'icon' => '',
			'access' => array('core.manage', 'com_iproperty'),
			'id' => 'com_iproperty_companies_icon' ),
            
            array(
            'link' => JRoute::_($link.'&view=amenities'),
			'image' => '',
			'text' => '<span class="small">|-- '.JText::_('COM_IPROPERTY_AMENITIES').'</span>',
			'icon' => '',
			'access' => array('core.manage', 'com_iproperty'),
			'id' => 'com_iproperty_amenities_icon' ),
            
            array(
            'link' => JRoute::_($link.'&view=openhouses'),
			'image' => '',
			'text' => '<span class="small">|-- '.JText::_('COM_IPROPERTY_OPENHOUSES').'</span>',
			'icon' => '',
			'access' => array('core.manage', 'com_iproperty'),
			'id' => 'com_iproperty_openhouse_icon' ),
            
            array(
            'link' => JRoute::_($link.'&view=settings'),
			'image' => '',
			'text' => '<span class="small">|-- '.JText::_('COM_IPROPERTY_SETTINGS').'</span>',
			'icon' => '',
			'access' => array('core.manage', 'com_iproperty'),
			'id' => 'com_iproperty_settings_icon' )           
        );
	}
}
