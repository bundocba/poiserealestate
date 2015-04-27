<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Iproperty
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license see LICENSE.php
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access');

class modIpCatMenu
{
	function ipGetCatSiblings($parent = 0, $params = null) 
    {
        $db         = JFactory::getDBO();
        $user       = JFactory::getUser();
        $groups     = $user->getAuthorisedViewLevels();
        
        // Filter by start and end dates.
        $nullDate   = $db->Quote($db->getNullDate());
        $date       = JFactory::getDate();
        $nowDate    = $db->Quote($date->toSql());
        
        $query = $db->getQuery(true);
        $query->select('c.id, c.title, c.alias')
                ->from('#__iproperty_categories AS c')
                ->where('c.access IN ('.implode(",", $groups).')')
                ->where('(c.publish_up = '.$nullDate.' OR c.publish_up <= '.$nowDate.')')
                ->where('(c.publish_down = '.$nullDate.' OR c.publish_down >= '.$nowDate.')');
        
        if(is_numeric($parent)){
            $query->where('c.parent = '.(int)$parent);
        }
        if(!$parent && $params->get('cat_filter', 0) > 0){
            $query->where('c.id = '.(int)$params->get('cat_filter', 0));
        }
        
        /*UNDER DEVELOPMENT - GET COUNT OF LISTINGS IN EACH CATEGORY*/
        // Join over prop mid table
        /*if($params->get('show_cat_count', 0)) // TODO: CREATE PARAM FOR CAT COUNT
        {
            $query->select('COUNT(pm.id) AS cat_count');
            $query->join('LEFT', '#__iproperty_propmid AS pm ON pm.cat_id = c.id');

            // Join over property table
            $query->join('LEFT', '#__iproperty AS p ON p.id = pm.prop_id')
                    ->where('p.state = 1')
                    ->where('p.approved = 1');

            $query->group('c.id');
        }*/
        /*END DEVELOPMENT*/
        
        $query->order($params->get('ordering', 'c.ordering'). ' ASC');

        //echo $query;
        $db->setQuery($query);
        return $db->loadObjectList();
	}
}

?>