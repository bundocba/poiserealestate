<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
require_once(JPATH_ADMINISTRATOR.'/components/com_iproperty/classes/admin.class.php');

function IPropertyBuildRoute( &$query )
{
    $settings       = ipropertyAdmin::config();
    if(!isset($settings->ip_router)) return array();

    // if this is an edit form view or feed view, forget about routing - it's already in the correct view
    if(isset($query['view']) && in_array($query['view'], array('manage','agentform','companyform','propform','openhouseform','feed')) || isset($query['layout']) == 'modal') return array();
    
    $segments	= array();

	// get a menu item based on Itemid or currently active
	$app		= JFactory::getApplication();
	$menu		= $app->getMenu();
    
    // create an array for all IP views requiring id's, and all views not requiring id's
    $ip_id_views    = array('agentproperties', 'cat', 'companyagents', 'companyproperties', 'contact', 'property');
    $ip_noid_views  = array('advsearch', 'agents', 'allproperties', 'companies', 'home', 'ipuser', 'manage', 'openhouses');

	// we need a menu item.  Either the one specified in the query, or the current active one if none specified
	if (empty($query['Itemid'])) {
		$menuItem = $menu->getActive();
		$menuItemGiven = false;
	}
	else {
		$menuItem = $menu->getItem($query['Itemid']);
		$menuItemGiven = true;
	}
    
    // check again
	if ($menuItemGiven && isset($menuItem) && $menuItem->component != 'com_iproperty')
	{
		$menuItemGiven = false;
		unset($query['Itemid']);
	}
    
    if (isset($query['view']))
	{
		$view = $query['view'];
	}
	else
	{
		// we need to have a view in the query or it is an invalid URL
		return $segments;
	}
    
    // are we dealing with an IP 'no id' view, or IP 'id' view with a menu item already existing?
	if (($menuItem instanceof stdClass) && $menuItem->query['view'] == $query['view'] && (in_array($query['view'], $ip_noid_views) || isset($query['id']) && $menuItem->query['id'] == (int) $query['id']))
	{
		unset($query['view']);		

		if (isset($query['id']))
		{
			unset($query['id']);
		}
        
        if (isset($query['layout']))
		{
			unset($query['layout']);
		}

		return $segments;
	}
    
    
    if(isset($query['id']))
    {         
        if ((strpos($query['id'], ':') === false || (substr($query['id'], strpos($query['id'], ':') + 1) == '')) && in_array($query['view'], $ip_id_views))
        {
            switch($query['view'])
            {
                case 'agentproperties':
                    $qtable = 'iproperty_agents';
                    break;
                case 'cat':
                    $qtable = 'iproperty_categories';
                    break;
                case 'companyagents':
                case 'companyproperties':
                    $qtable = 'iproperty_companies';
                    break;
                case 'contact':
                    $qtable = ($query['layout'] == 'agent') ? 'iproperty_agents' : 'iproperty_companies';
                    break;
                case 'property':
                    $qtable = 'iproperty';
                    break;                   
            }
            
            $db = JFactory::getDbo();
            $dbQuery = $db->getQuery(true)
                ->select('id, alias')
                ->from('#__'.$qtable)
                ->where('id = ' . (int) $query['id']);
            $db->setQuery($dbQuery);
            $result = $db->loadObject();
            $tmp_alias = $result->alias;
        }else{
            $tmp_alias = substr($query['id'], strpos($query['id'], ':') + 1);
        }       
    }
    
    if(isset($query['view']))
    {
        $segments[] = $query['view'];
        unset( $query['view'] );
    }
    if (isset($query['id'])) 
    {
        // we want to trim the additonal safegaurd alias numerical id 'my-property-3' - trim the '3' if it exists and is the same as the property db id
        $alias_parts = explode('-', $tmp_alias);
        if(is_numeric($alias_parts[count($alias_parts) - 1]) && $alias_parts[count($alias_parts) - 1] == (int)$query['id']){
            unset($alias_parts[count($alias_parts) - 1]);
            $new_alias = implode('-', $alias_parts);
            $query['id'] = (int)$query['id'] . ':' . $new_alias;
        }else{
            $query['id'] = (int)$query['id'] . ':' . $tmp_alias;
        }    
        
        $segments[] = $query['id'];
        unset( $query['id'] );
    }
    if (isset($query['layout'])) 
    {
        $segments[] = $query['layout'];
        unset( $query['layout'] );
	}    
	
    return $segments;
}

function IPropertyParseRoute( $segments )
{    
    // if this is an edit form view or feed view, forget about routing - it's already in the correct view
    if(isset($segments[0]) && in_array($segments[0], array('manage','agentform','companyform','propform','openhouseform','feed'))) return;
    
    $vars = array();
    
    //Get the active menu item.
	$app    = JFactory::getApplication();
	$menu   = $app->getMenu();
	$item   = $menu->getActive();
    
    // if this is an item being edited or saved, return just the id
    if(is_numeric($segments[0])){
        $vars['id'] = $segments[0];
        return $vars;
    }
    
    if (!isset($item))
	{
		$vars['view']   = $segments[0];
		$vars['id']     = $segments[1];

		return $vars;
	}
    
    $ip_id_views    = array('agentproperties', 'cat', 'companyagents', 'companyproperties', 'contact', 'property');
    $ip_noid_views  = array('advsearch', 'agents', 'allproperties', 'companies', 'home', 'ipuser', 'manage', 'openhouses');
    
    if(in_array($segments[0], $ip_noid_views)){
        $vars['view'] = $segments[0];
        return $vars;
    }elseif(in_array($segments[0], $ip_id_views)){
        $vars['view']   = $segments[0];
        $vars['layout'] = isset($segments[2]) ? $segments[2] : '';
            
        list($id, $alias) = explode(':', $segments[1], 2);
        $vars['id'] = $id;
        return $vars;
    }
    
    return $vars;
}
