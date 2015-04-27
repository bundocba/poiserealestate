<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
jimport('joomla.log.log');
jimport( 'joomla.base.object');

class IpropertyHelperData extends JObject
{
    // main worker function
    public static function getInputs($input, $currentvals)
    {
        if (!$input) return array('status' => 'error', 'message' => 'GETINPUTS FAILED-- NO VALID INPUT PASSED');
        if (!is_array($currentvals)) return array('status' => 'error', 'message' => 'GETINPUTS FAILED-- NO VALID ARRAY PASSED- CURRENTVALS');
        // set the vars based on the input
		$return = '';
        switch ($input) {
			case 'stype':
				$return = self::getStypes();
				break;
			case 'amenities':
				$return = self::getAmenities();
				break;
			case 'categories':
				$return = self::getCategories();
				break;
			default:
				$return = self::getData($input, $currentvals);
				break;
		}
		
		if ($return){
			$return_array = array( 'status' => 'ok', 'message' => 'inputs generated', 'data' => $return );
		} else {
			$return_array = array( 'status' => 'error', 'message' => 'failed to generate inputs- '.$input, 'data' => '' );
		}

        return $return_array;
    }

    private static function getData($input, $currentvals)
    {
        $db         = JFactory::getDbo();
        $nullDate   = $db->Quote($db->getNullDate());
        $date       = JFactory::getDate();
        $nowDate    = $db->Quote($date->toSql());
        $query      = $db->getQuery(true);

        // deal with the lookup table situations
        switch($input)
        {
            case 'country':
                $query->select('DISTINCT b.title AS name, b.id AS id');
                $query->from('#__iproperty AS p');
                $query->join('', '#__iproperty_countries AS b ON b.id = p.country');
                break;
            case 'locstate':
                $query->select('DISTINCT b.title AS name, b.id AS id');
                $query->from('#__iproperty AS p');
                $query->join('', '#__iproperty_states AS b ON b.id = p.locstate');
                break;
            default:
                // @since 3.2.1 - setting id for use with falang; id is required for translation
                $query->select('DISTINCT p.'.$input.' AS name, p.id AS id, p.'.$input.' AS search_field');
                $query->from('#__iproperty AS p');
                break;
        }
        // add other where items
        $query->order('name');
        $query->where('(p.publish_up = '.$nullDate.' OR p.publish_up <= '.$nowDate.')')
            ->where('(p.publish_down = '.$nullDate.' OR p.publish_down >= '.$nowDate.')')
            ->where('p.state = 1')
            ->where('p.approved = 1');
        foreach ($currentvals as $k => $v)
        {
            if ( (bool) $v !== false ){
                $fieldvar = '';
                if(is_array($v) && !empty($v)){
                    foreach ($v as $value){
                        $fieldvar .= JString::strtolower($db->Quote($value)).',';
                    }
                    $query->where( 'p.'.$k.' IN ('.rtrim($fieldvar, ',').')');
                } else if ($v) {
                    $query->where('p.'.$k.' IN ('.JString::strtolower($db->Quote(trim($v))).')');
                }
            }
        }

        $db->setQuery($query);
		$return = array();
        if (false !== ($result = $db->loadObjectList())){
            foreach ($result as $r){
                // @since 3.2.1 - set id as name for all but country and locstate
                // Required fix for Falang translations
                $r->id = ($input == 'country' || $input == 'locstate') ? $r->id : $r->search_field;
                if($r->id && $r->name) $return[$r->id] = $r->name;
            }
            return $return;
        } else {
            return false;
        }
    }

    private static function getAmenities()
    {
        $db     = JFactory::getDbo();
        $query  = $db->getQuery(true);

        $query->select('*,id')
            ->from('#__iproperty_amenities')
            ->order('title ASC');
        $db->setQuery($query);

        if (false !== ($result = $db->loadObjectList())){
            $amenities = array();
            foreach ($result as $r){
                $amenities[] = array($r->id, $r->title, $r->cat);
            }
            return $amenities;
        } else {
            return false;
        }
    }

    private static function getCategories()
    {
        $db     	= JFactory::getDbo();
        $query  	= $db->getQuery(true);
        $nullDate   = $db->Quote($db->getNullDate());
        $date       = JFactory::getDate();
        $nowDate    = $db->Quote($date->toSql());

        $query->select('id, title, parent as cat_parent')
            ->from('#__iproperty_categories c')
			->where('(c.publish_up = '.$nullDate.' OR c.publish_up <= '.$nowDate.')')
			->where('(c.publish_down = '.$nullDate.' OR c.publish_down >= '.$nowDate.')')
			->where('c.state = 1')
            ->order('c.parent, c.ordering');
        $db->setQuery($query);

        if (false !== ($result = $db->loadObjectList())) {
            $categories = array();
            foreach ($result as $r){
                $categories[] = array($r->id, $r->title, $r->cat_parent);
            }
            return $categories;
        } else {
            return false;
        }
    }

    private static function getStypes()
    {
        $db     = JFactory::getDbo();
        $query  = $db->getQuery(true);

        $query->select('id, name')
            ->from('#__iproperty_stypes')
            ->where('state = 1');
        $db->setQuery($query);

        if (false !== ($result = $db->loadObjectList())) {
            $stypes = array();
            foreach ($result as $r){
                $stypes[$r->id] = $r->name;
            }
            return $stypes;
        } else {
            return false;
        }
    }
}
?>
