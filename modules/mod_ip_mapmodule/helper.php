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

class modIPMapmoduleHelper
{
	public static function getList(&$params)
	{
        $db     = JFactory::getDbo();
		$count  = (int) $params->get('count', 20);
        $sort   = 'p.price';
        $order  = 'DESC';

        $where  = array();        
        
        // determine if we need to hide no pic results
        $hidenopic = $params->get('hidenopic') ? true : false;
        
        // pull sale types if specified
        if ($params->get('prop_stype')) $where['property']['stype'] = $params->get('prop_stype');
        if ($params->get('cat_id') && $params->get('cat_subcats'))
        {            
            $cats_array = array( $params->get('cat_id') );
            $squery     = $db->setQuery(IpropertyHelperQuery::getCategories($params->get('cat_id')));
            $subcats    = $db->loadObjectList();
            
            foreach ($subcats as $s)
            {
                $cats_array[] = (int)$s->id;
            }
            $where['categories'] = $cats_array;
        } elseif ($params->get('cat_id')){
            $where['categories'] = $params->get('cat_id');
        }
		if ($params->get('city')) $where['property']['city'] = $params->get('city');
		if ($params->get('locstate')) $where['property']['locstate'] = $params->get('locstate');
		if ($params->get('country')) $where['property']['country'] = $params->get('country');
		if ($params->get('reo')) $where['property']['checked'][] = 'reo';
		if ($params->get('hoa')) $where['property']['checked'][] = 'hoa';
		if ($params->get('frontage')) $where['property']['checked'] = 'frontage';
		if ($params->get('agent')) $where['agents'] = $params->get('agent');
		if ($params->get('company')) $where['property']['listing_office'] = $params->get('company');
		
        $where['searchfields']  = array('title','street','street2','short_description','description');
        
        // get items using query helper
        $pquery = new IpropertyHelperQuery($db, $sort, $order);
        $query  = $pquery->buildPropertyQuery($where, 'properties');
        $db->setQuery($query, 0, $count);      
        
        $items = ipropertyHelperProperty::getPropertyItems($db->loadObjectList(), true, false, $hidenopic);

		return $items;
	}
}
