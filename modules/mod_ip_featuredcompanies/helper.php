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

class modIPFeaturedcompaniesHelper
{
    public static function getList(&$params)
    {
        $db             = JFactory::getDbo();
        $count          = (int)$params->get('count', 5);

        // Ordering
        switch ($params->get( 'ordering' ))
        {
            case '1':
                $sort           = 'name';
                $order          = 'ASC';
                break;
            case '2':
                $sort           = 'ordering';
                $order          = 'ASC';
                break;
            case '3':
            default:                
                $sort           = 'RAND()';
                $order          = '';
                break;
        }
        
        $where = array();
        $where[] = 'c.featured = 1';

        if ($params->get('city')) $where[] = 'c.city = '.$db->Quote($params->get('city'));
        
        $query  = IpropertyHelperQuery::buildCompaniesQuery($db, $where, $sort, $order, false);      
        $db->setQuery($query, 0, $count);
        
        return $db->loadObjectList();
    }
}
