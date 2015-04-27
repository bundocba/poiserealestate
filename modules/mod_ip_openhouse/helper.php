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
require_once(JPATH_SITE.'/components/com_iproperty/helpers/html.helper.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_iproperty/classes/admin.class.php');

class modIPOpenhouseHelper
{
    public static function getList(&$params)
	{
		$db         = JFactory::getDbo();
        $count 		= $params->get('count', 10);
        
        // Filter by start and end dates.
        $nullDate   = $db->Quote($db->getNullDate());
        $date       = JFactory::getDate();
        $nowDate    = $db->Quote($date->toSql());
        
        $query = $db->getQuery(true);
        $query->select('o.*, o.prop_id as prop_id, p.alias as alias, o.openhouse_start as start_date, o.openhouse_end as end_date')
                ->from('#__iproperty_openhouses as o')
                ->leftJoin('#__iproperty as p on p.id = o.prop_id')
                ->where('o.state = 1 AND p.state = 1 AND p.approved = 1')
                ->where('(p.publish_up = '.$nullDate.' OR p.publish_up <= '.$nowDate.')')
                ->where('(p.publish_down = '.$nullDate.' OR p.publish_down >= '.$nowDate.')')
                ->where('o.openhouse_end >= '.$nowDate)
                ->order('o.openhouse_end ASC');
        
        $db->setQuery($query, 0, $count);
        return $db->loadObjectList();
	}
}