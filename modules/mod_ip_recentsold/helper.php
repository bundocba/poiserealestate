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

class modIPRecentsoldHelper
{
	public static function getList(&$params)
	{
        $db     = JFactory::getDbo();
		$count  = (int) $params->get('count', 5);

        $where  = array();
        
        // determine if we need to hide no pic results
        $hidenopic = $params->get('hidenopic') ? true : false;

        if($params->get('random', 1)){
            $sort                   = 'RAND()';
            $order                  = '';
        }else{
            $sort                   = 'p.modified';
            $order                  = 'DESC';
        }
        
        // pull sale types if specified
        if ($params->get('prop_stype', 5)) $where['property']['stype'] = $params->get('prop_stype', 5);
        
        // update 2.0.1 - new option to select subcategories as well
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
        $where['searchfields']  = array('title','street','street2','short_description','description');
        
        // get items using query helper
        $pquery = new IpropertyHelperQuery($db, $sort, $order);
        $query  = $pquery->buildPropertyQuery($where, 'properties');
        $db->setQuery($query, 0, $count);      
        
        $items = ipropertyHelperProperty::getPropertyItems($db->loadObjectList(), true, false, $hidenopic);

		return $items;
	}
}