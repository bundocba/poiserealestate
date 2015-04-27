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
require_once(JPATH_SITE.'/components/com_iproperty/helpers/route.php');
require_once(JPATH_SITE.'/components/com_iproperty/helpers/query.php');

class modIPAgentHelper
{
    public static function getList(&$params)
    {
        $db     = JFactory::getDbo();
        $count  = (int)$params->get('count', 5); 
        $where 	= array();  
        $with_listings = ($params->get('hide_nolistings')) ? true : false;

        // Filter by featured
        if($params->get('featured')){
            $where[] = 'a.featured = 1';
        }

        // Filter by city
        if($params->get('city')){
            $where[] = 'a.city = '.$db->Quote($params->get('city'));
        }

        // Filter by company.
		$companyId = $params->get('company');
		if ($companyId && is_numeric($companyId)) {
			$where[] = 'a.company = '.(int) $companyId;
		}
        
        // Filter by agent image
        if ($params->get('hide_noimage')) {
            $where[] = 'a.icon AND a.icon != "nopic.png"';
        }
        
        $query  = IpropertyHelperQuery::buildAgentsQuery($db, $where, false, 'ASC', false, $with_listings);
        $query->order('RAND()');        
        $db->setQuery($query, 0, $count);
        
        return $db->loadObjectList();
    }    
}
