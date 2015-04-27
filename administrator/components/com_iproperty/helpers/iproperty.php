<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

class IpropertyHelper
{
    public static $extension = 'com_iproperty';
    
	public static function addSubmenu($vName)
	{
        $user       = JFactory::getUser();
        
        JHtmlSidebar::addEntry(
            JText::_('COM_IPROPERTY_CONTROL_PANEL' ),
            'index.php?option=com_iproperty',
            $vName == 'iproperty'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_IPROPERTY_CATEGORIES' ),
            'index.php?option=com_iproperty&view=categories',
            $vName == 'categories'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_IPROPERTY_PROPERTIES' ),
            'index.php?option=com_iproperty&view=properties',
            $vName == 'properties'
        );
        
        JHtmlSidebar::addEntry(
            JText::_('COM_IPROPERTY_PROPERTYAGENT' ),
            'index.php?option=com_iproperty&view=propertyagents',
            $vName == 'propertyagents'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_IPROPERTY_AGENTS' ),
            'index.php?option=com_iproperty&view=agents',
            $vName == 'agents'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_IPROPERTY_COMPANIES' ),
            'index.php?option=com_iproperty&view=companies',
            $vName == 'companies'
        );
        
        JHtmlSidebar::addEntry(
            JText::_('COM_IPROPERTY_CONDITION' ),
            'index.php?option=com_iproperty&view=conditions',
            $vName == 'conditions'
        );
        
        JHtmlSidebar::addEntry(
            JText::_('COM_IPROPERTY_TENURE' ),
            'index.php?option=com_iproperty&view=tenures',
            $vName == 'tenures'
        );
        
         JHtmlSidebar::addEntry(
            JText::_('COM_IPROPERTY_AREA' ),
            'index.php?option=com_iproperty&view=areas',
            $vName == 'areas'
        );
        
        JHtmlSidebar::addEntry(
            JText::_('COM_IPROPERTY_STATUS' ),
            'index.php?option=com_iproperty&view=statuss',
            $vName == 'statuss'
        );
        
        JHtmlSidebar::addEntry(
            JText::_('COM_IPROPERTY_PROPERTYTYPE' ),
            'index.php?option=com_iproperty&view=propertytypes',
            $vName == 'propertytypes'
        );
        
        JHtmlSidebar::addEntry(
            JText::_('COM_IPROPERTY_FILE' ),
            'index.php?option=com_iproperty&view=files',
            $vName == 'files'
        );
        
         JHtmlSidebar::addEntry(
            JText::_('COM_IPROPERTY_FILEAGENT' ),
            'index.php?option=com_iproperty&view=fileagents',
            $vName == 'fileagents'
        );
        
         JHtmlSidebar::addEntry(
            JText::_('COM_IPROPERTY_TRAINING' ),
            'index.php?option=com_iproperty&view=trainings',
            $vName == 'trainings'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_IPROPERTY_AMENITIES' ),
            'index.php?option=com_iproperty&view=amenities',
            $vName == 'amenities'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_IPROPERTY_OPENHOUSES' ),
            'index.php?option=com_iproperty&view=openhouses',
            $vName == 'openhouses'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_IPROPERTY_SETTINGS' ),
            'index.php?option=com_iproperty&view=settings',
            $vName == 'settings'
        ); 
        
        if($user->authorise('core.admin'))
        {
            JHtmlSidebar::addEntry(
                '<h4><span class="label label-warning">'.JText::_('JADMINISTRATION').'</span></h4>',
                '',
                false
            );
            
            JHtmlSidebar::addEntry(
                JText::_('COM_IPROPERTY_BACKUP' ),
                'index.php?option=com_iproperty&view=backup',
                $vName == 'backup'
            );
            
            JHtmlSidebar::addEntry(
                JText::_('COM_IPROPERTY_RESTORE' ),
                'index.php?option=com_iproperty&view=restore',
                $vName == 'restore'
            );
            
            JHtmlSidebar::addEntry(
                JText::_('COM_IPROPERTY_BULKIMPORT_FILE' ),
                'index.php?option=com_iproperty&view=bulkimport',
                $vName == 'bulkimport'
            );
            
            JHtmlSidebar::addEntry(
                JText::_('COM_IPROPERTY_EDIT_CSS' ),
                'index.php?option=com_iproperty&view=editcss',
                $vName == 'editcss'
            );
        }       
	}

    public static function getActions()
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		$actions = array('core.admin', 'core.manage');

		foreach ($actions as $action) {
			$result->set($action,	$user->authorise($action, 'com_iproperty'));
		}

		return $result;
	}
}

class IpToolbar extends JToolbarHelper
{
    public static function approveList($task = 'approve', $alt = 'COM_IPROPERTY_APPROVE')
	{
		$bar = JToolbar::getInstance('toolbar');
		$bar->appendButton('Standard', 'thumbs-up', $alt, $task, true);
	} 
    
    public static function unapproveList($task = 'unapprove', $alt = 'COM_IPROPERTY_UNAPPROVE')
	{
		$bar = JToolbar::getInstance('toolbar');
		$bar->appendButton('Standard', 'thumbs-down muted', $alt, $task, true);
	}
    
    public static function featureList($task = 'feature', $alt = 'COM_IPROPERTY_FEATURE')
	{
		$bar = JToolbar::getInstance('toolbar');
		$bar->appendButton('Standard', 'star', $alt, $task, true);
	} 
    
    public static function unfeatureList($task = 'unfeature', $alt = 'COM_IPROPERTY_UNFEATURE')
	{
		$bar = JToolbar::getInstance('toolbar');
		$bar->appendButton('Standard', 'star-empty', $alt, $task, true);
	}
    
    public static function superList($task = 'super', $alt = 'COM_IPROPERTY_SUPER')
	{
		$bar = JToolbar::getInstance('toolbar');
		$bar->appendButton('Standard', 'plus', $alt, $task, true);
	} 
    
    public static function unsuperList($task = 'unsuper', $alt = 'COM_IPROPERTY_UNSUPER')
	{
		$bar = JToolbar::getInstance('toolbar');
		$bar->appendButton('Standard', 'minus', $alt, $task, true);
	}
    
    public static function clearHits($msg = '', $task = 'clearhits', $alt = 'COM_IPROPERTY_CLEAR_HITS')
	{
		$bar = JToolbar::getInstance('toolbar');

		if ($msg)
		{
			$bar->appendButton('Confirm', $msg, 'refresh', $alt, $task, true);
		}
		else
		{
			$bar->appendButton('Standard', 'refresh', $alt, $task, true);
		}
	}
    
    public static function saveCatList($task = 'saveCats', $alt = 'JTOOLBAR_APPLY', $check = false)
	{
		$bar = JToolbar::getInstance('toolbar');
		$bar->appendButton('Standard', 'publish', $alt, $task, $check);
	} 
    
    public static function backupBtn($task = 'backupDB', $alt = 'COM_IPROPERTY_BACKUP', $check = false)
	{
		$bar = JToolbar::getInstance('toolbar');
		$bar->appendButton('Standard', 'new', $alt, $task, $check);
	}
    
    public static function restoreBtn($task = 'restoreDB', $alt = 'COM_IPROPERTY_RESTORE', $check = false)
	{
		$bar = JToolbar::getInstance('toolbar');
		$bar->appendButton('Standard', 'new', $alt, $task, $check);
	}
    
    public static function backBtn($alt = 'JTOOLBAR_BACK', $href = 'javascript:history.back();')
	{
		$bar = JToolbar::getInstance('toolbar');
		$bar->appendButton('Link', 'arrow-left', $alt, $href);
	}
    
    public static function importBtn($task = 'import', $alt = 'COM_IPROPERTY_IMPORT', $check = false)
	{
		$bar = JToolbar::getInstance('toolbar');
		$bar->appendButton('Standard', 'new', $alt, $task, $check);
	}
}