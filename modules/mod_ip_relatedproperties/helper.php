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

class modIPRelatedHelper
{
    public static function getList(&$params, $prop_id)
	{
        $db     = JFactory::getDbo();
        $user   = JFactory::getUser();
        $groups	= $user->getAuthorisedViewLevels();
        
        $count                  = (int) $params->get('count', 5);
		$search_cat             = (int) $params->get('search_cat', 1);
        $search_city            = (int) $params->get('search_city', 1);
        $search_state           = (int) $params->get('search_state', 1);
        $search_province        = (int) $params->get('search_province', 1);
        $search_county          = (int) $params->get('search_county', 1);
        $search_region          = (int) $params->get('search_region', 1);
        $search_country         = (int) $params->get('search_country', 1);  
        
        // Ordering
        switch ($params->get( 'ordering' ))
        {
            case '1':
                $sort           = 'price';
                $order          = 'ASC';
                break;
            case '2':
                $sort           = 'price';
                $order          = 'DESC';
                break;
            case '3':
                $sort           = 'p.street';
                $order          = 'ASC';
                break;
            case '4':
                $sort           = 'p.street';
                $order          = 'DESC';
                break;
            case '5':
            default:
                $sort           = 'RAND()';
                $order          = '';
                break;
        }      

        //get current property data        
        $where['property']['id'] = (int) $prop_id;
        $pquery = new IpropertyHelperQuery($db);
        $query  = $pquery->buildPropertyQuery($where, 'property');
        $db->setQuery($query, 0, 1);
        
        if($property = $db->loadObject())
        {           
            $searchwhere    = array();            
            $query          = $db->getQuery(true); 
            
            // Filter by start and end dates.
            $nullDate   = $db->Quote($db->getNullDate());
            $date       = JFactory::getDate();
            $nowDate    = $db->Quote($date->toSql());
           
            // Select the required fields from the table.
            $query->select('p.id, p.alias')
                ->from('`#__iproperty` AS p')            
                ->leftJoin('#__iproperty_propmid as pm on pm.prop_id = p.id')
                ->leftJoin('#__iproperty_categories as c on c.id = pm.cat_id')
                ->where('(p.publish_up = '.$nullDate.' OR p.publish_up <= '.$nowDate.')')
                ->where('(p.publish_down = '.$nullDate.' OR p.publish_down >= '.$nowDate.')')
                ->where('(c.publish_up = '.$nullDate.' OR c.publish_up <= '.$nowDate.')')
                ->where('(c.publish_down = '.$nullDate.' OR c.publish_down >= '.$nowDate.')')
                ->where('p.id != '.(int)$property->id)
                ->where('p.state = 1')
                ->where('p.approved = 1');
            if(is_array($groups) && !empty($groups)){
                $query->where('p.access IN ('.implode(",", $groups).')')
                    ->where('c.access IN ('.implode(",", $groups).')');
            }
            
            // set order
            if ($sort && !$order) // this means random sort (ie featured display)
			{
				$query->order($sort);
			}else if ($sort && $order){
				$query->order($sort . ' ' . $order);
			}

            // Join over prop mid table if getting related by category
            if($search_cat)
            {
                $cats = ipropertyHTML::getAvailableCats($property->id);
                
                if($cats)
                {
                    $searchwhere[] = 'pm.cat_id IN ('.implode(',', $cats).')';
                }
            }        
            
            // Filter by city.
            if($property->city && $search_city)         $searchwhere[] = 'p.city = '.$db->Quote($property->city);            
            if($property->locstate && $search_state)    $searchwhere[] = 'p.locstate = '.(int)$property->locstate;
            if($property->province && $search_province) $searchwhere[] = 'p.province = '.$db->Quote($property->province);
            if($property->county && $search_county)     $searchwhere[] = 'p.county = '.$db->Quote($property->county);
            if($property->region && $search_region)     $searchwhere[] = 'p.region = '.(int)$property->region;
            
            if( count($searchwhere)) $query->where('('.implode(' OR ', $searchwhere).')');

            $db->setQuery($query, 0, $count);
            return $db->loadObjectList();
            
        }else{
            return false;
        }
	}
}
