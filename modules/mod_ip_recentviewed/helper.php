<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Iproperty
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');
require_once(JPATH_SITE.'/components/com_iproperty/helpers/html.helper.php');
require_once(JPATH_SITE.'/components/com_iproperty/helpers/property.php');
require_once(JPATH_SITE.'/components/com_iproperty/helpers/route.php');
require_once(JPATH_SITE.'/components/com_iproperty/helpers/query.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_iproperty/classes/admin.class.php');


class modIPRecentviewedHelper
{
	public static function getList(&$params)
	{
        $db         = JFactory::getDbo();
		$count      = (int) $params->get('count', 5);
        $session    = JFactory::getSession();

        $where      = array();             
        
        // determine if we need to hide no pic results
        $hidenopic = $params->get('hidenopic') ? true : false;

        // create recentviews array if it doesn't exist
        if(!$recentviews = $session->get('rviews')){
            $recentviews = array();
        }    

        // if we're on a property view, then add the viewed ID to the session array
        if (JFactory::getApplication()->input->get('view') == 'property' && JFactory::getApplication()->input->get('option') == 'com_iproperty')
        {
            // add prop_id to recentviews array
            $recentviews[] = JFactory::getApplication()->input->getInt('id');
            // add recentviews back to session
            $session->set('rviews', $recentviews);
        }
        
        // if no recent listings in session, return
        if(!$recentviews || empty($recentviews)) return false;
        
        // reverse the array to get most recent first
        $recentviews = array_reverse($recentviews);        

        // remove any duplicates in case they've viewed same prop multiple times
        $recentviews = array_unique($recentviews);        
        
        // if set to order by random, do so - otherwise, sort by session order
        if($params->get('random', 1)){
            $sort                   = 'RAND()';
            $order                  = '';
        }else{
            $sort                   = 'FIND_IN_SET(id,\''.implode(',', $recentviews).'\')';
            $order                  = '';
        } 
        
        $where['property']['id']    = $recentviews; 
        
        // get items using query helper
        $pquery = new IpropertyHelperQuery($db, $sort, $order);
        $query  = $pquery->buildPropertyQuery($where, 'properties'); 
        $db->setQuery($query, 0, $count);      
        
        $items = ipropertyHelperProperty::getPropertyItems($db->loadObjectList(), true, false, $hidenopic);

		return $items;
	}
}