<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Iproperty
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');
require_once(JPATH_SITE.'/components/com_iproperty/helpers/route.php');

class modIpCityLinksHelper
{
    public static function getList(&$params, $falang)
    {
        $db         = JFactory::getDbo();
        
        $cat        = (int) $params->get('cat_id', 0);
        $county     = (string) $params->get('county', 0);
        $region     = (string) $params->get('region', 0);
        $province   = (string) $params->get('province', 0);
        $locstate   = (int) $params->get('locstate', 0);
        $country    = (int) $params->get('country', 0);
        $limit      = (int) $params->get('limit', '');
        $order      = (int) $params->get('order_by', 0);
        $order      = ($order) ? 'count DESC' : 'p.city ASC';
        
        $user       = JFactory::getUser();
        $groups     = $user->getAuthorisedViewLevels(); 
        
        // Filter by start and end dates.
        $nullDate   = $db->Quote($db->getNullDate());
        $date       = JFactory::getDate();
        $nowDate    = $db->Quote($date->toSql());        
        
        $query = $db->getQuery(true);
        
        if($falang){
            $query->select('count(p.id) as count, p.id, p.city');
        }else{
            $query->select('count(p.id) as count, p.city');
        }
            $query->from('#__iproperty as p')
                ->where('p.state = 1')
                ->where('p.approved = 1')
                ->where('(p.publish_up = '.$nullDate.' OR p.publish_up <= '.$nowDate.')')
                ->where('(p.publish_down = '.$nullDate.' OR p.publish_down >= '.$nowDate.')');
        if($cat){
            $query->join('', '#__iproperty_propmid as pm on pm.prop_id = p.id')
                ->join('', '#__iproperty_categories as c on c.id = pm.cat_id')
                ->where('pm.cat_id = '.(int)$cat)
                ->where('c.state = 1')
                ->where('(c.publish_up = '.$nullDate.' OR c.publish_up <= '.$nowDate.')')
                ->where('(c.publish_down = '.$nullDate.' OR c.publish_down >= '.$nowDate.')');
        }
                
        if($county){
            $query->where('p.county = '.$db->Quote($county));
        }
        
        if($region){
            $query->where('p.region = '.$db->Quote($region));
        }
        
        if($province){
            $query->where('p.province = '.$db->Quote($province));
        }
        
        if($locstate){
            $query->where('p.locstate = '.$locstate);
        }
        
        if($country){
            $query->where('p.country = '.$country);
        }
        
        if(is_array($groups) && !empty($groups)){
            $query->where('p.access IN ('.implode(",", $groups).')');
            if($cat) $query->where('c.access IN ('.implode(",", $groups).')');
        }
        $query->group('p.city')
                ->order($order); 

        $db->setQuery($query, 0, $limit);
        if ($results = $db->loadObjectList()) return $results;

        return false;
    }
}
