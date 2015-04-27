<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');

class IpropertyHelperQuery extends JObject
{
    private $_db    = null;
    private $_sort  = null;
    private $_order = null;

    public function __construct($db, $sort = 'p.street', $order = 'ASC')
    {
        $this->_db      = $db;
        $this->_sort    = $sort;
        $this->_order   = $order;
    }

    // all purpose query for all property types
    public function buildPropertyQuery($where, $type, $debug = false)
    {
        $user       = JFactory::getUser();
        $groups     = $user->getAuthorisedViewLevels();
        $nullDate   = $this->_db->Quote($this->_db->getNullDate());
        $date       = JFactory::getDate();
        $nowDate    = $this->_db->Quote($date->toSql());
        $settings   = ipropertyAdmin::config();

        $select_string  = false;
        $pmquery		= '';
        $query          = array();
        $joins          = array();

        switch ($type)
        {
            case 'advsearch':
                $select_string = 'p.id AS id,p.property_type, p.title AS title, p.show_map, p.street_num, p.street, p.street2, p.apt, p.city, p.locstate, p.province, p.region, p.country, p.short_description as short_description, p.property_name, p.stype_freq, p.latitude, p.longitude, p.price, p.price2, p.beds, p.baths, p.sqft, p.call_for_price, p.created, p.modified, p.available, p.stype, p.hide_address, LEFT(p.description,300) as description, p.alias as alias, p.publish_up, p.modified';
            break;
            case 'property':
				$select_string = 'p.*, p.id AS id,p.property_type, p.title AS title, p.street_num, p.street, p.street2, p.description as description, p.created as created, p.alias as alias';
			break;
            case 'agentproperty':
				$select_string = 'p.*, p.id AS id,p.property_type, p.title AS title, p.street_num, p.street, p.street2, p.description as description, p.created as created, p.alias as alias';
			break;
            case 'properties':
                $select_string = 'p.id AS id,p.property_type, p.price, p.call_for_price, p.show_map, p.hide_address, p.price2, p.stype_freq, p.title AS title, p.beds, p.baths, p.sqft, p.lot_acres, p.lotsize, p.street_num, p.street, p.street2, p.description as description, p.short_description, p.created, p.created_by, p.modified, p.stype, p.listing_office, p.city, p.locstate, p.county, p.region, p.country, p.province, p.alias as alias, p.featured, p.latitude, p.longitude, p.available, p.income, p.tax, p.publish_up, p.state, p.checked_out, p.checked_out_time, p.hits';
            break;
            case 'ipuser':
                $select_string = 'p.property_name, p.id AS id, p.price, p.title AS title, p.show_map, p.street_num, p.street, p.street2, p.city, p.locstate, p.state, p.featured, p.approved, p.created as created, p.alias as alias';
            break;
            case 'openhouse':
                $select_string = 'p.*, p.id AS id, p.title AS title, p.street_num, p.street, p.street2, p.description as description, p.alias as alias, oh.name as ohname, oh.openhouse_start as ohstart, oh.openhouse_end as ohend, oh.comments as comments, p.created as created, oh.openhouse_start as startdate, oh.openhouse_end as enddate';
                $joins[] = '#__iproperty_openhouses as oh ON oh.prop_id = p.id';
                $query[] = 'oh.openhouse_end >= '.$nowDate;
                $query[] = 'oh.state = 1';
                if(!isset($this->_sort) || !isset($this->_order)) {
                    $this->_sort = 'oh.openhouse_end';
                    $this->_order = 'ASC';
                }
            break;
            default: // properties
                $select_string = 'p.id AS id,p.property_type, p.title AS title, p.beds, p.baths, p.sqft, p.lot_acres, p.lotsize, p.street_num, p.street, p.street2, p.description as description, p.short_description, p.created, p.modified, p.stype, p.listing_office, p.city, p.locstate, p.county, p.region, p.country, p.province, p.alias as alias, p.featured';
            break;
        }

        // create where statements for sliders
        if (isset($where['sliders'])){
            foreach ($where['sliders'] as $field => $slider){
                if ($slider['min'] && $slider['max']) $query[] = '(p.'.$field.' BETWEEN ' . (int)$slider['min'] . ' AND ' . (int)$slider['max'] . ')';
                if ($slider['min'] && !$slider['max']) $query[] = 'p.'.$field.' >= ' . (int)$slider['min'];
                if ($slider['max'] && !$slider['min']) $query[] = 'p.'.$field.' <= ' . (int)$slider['max'];
            }
        }

        // create where statements for property items
        if (isset($where['property'])){
            foreach ($where['property'] as $field => $value){
                if ($field == 'keyword' && $value){
                    $searchwheres = self::textSearch($value, $where['searchfields']);
                    if ($searchwheres) $query[] = $searchwheres;
                } else if ($field == 'checked' && $value){
                    if(is_array($value) && !empty($value)){
                        foreach($value as $v){
                            $query[] = $this->_db->quoteName('p.'.$v).' = 1';                                                       
                        }
                    }
                } else {
                    $fieldvar = '';
                    if(is_array($value) && !empty($value)){
                        foreach ($value as $v){
                            $fieldvar .= JString::strtolower($this->_db->Quote($v, true)).',';
                        }
                        if(rtrim($fieldvar, ',') != "''") $query[] = $this->_db->quoteName('p.'.$field).' IN ('.rtrim($fieldvar, ',').')';                       
                    } else if ($value) {
                        if($field == 'waterfront') $field = 'frontage';
                        $query[] = $this->_db->quoteName('p.'.$field).' IN ('.JString::strtolower($this->_db->Quote($value, true)).')';
                    }
                }
            }
        }

        // create where statements for location items
        if (isset($where['location'])){
            foreach ($where['location'] as $field => $value){
                $fieldvar = '';
                if(is_array($value) && !empty($value)){
                    foreach ($value as $v){
                        $fieldvar .= $this->_db->Quote($v, true).',';
                    }
                    if(rtrim($fieldvar, ',') != "''") $query[] = $this->_db->quoteName('p.'.$field).' IN ('.rtrim($fieldvar, ',').')';                   
                } else if ($value) {
                    $query[] = $this->_db->quoteName('p.'.$field).' IN ('.$this->_db->Quote($value, true).')';               
                }
            }
        }

        // create where statement for category items
        if (isset($where['categories'])) {
            $checked = '';
            if(is_array($where['categories']))
            {
                foreach ($where['categories'] as $cat){
                    $checked .= (int) $cat . ',';
                }
            }else{
                $checked .= (int) $where['categories'];
            }
            if($checked){
                $pmquery .= 'pm.cat_id IN ('.rtrim($checked, ',').') AND';
            }
        }

        // create where statement for amenities
        if (isset($where['amenities'])){
            $checked = '';
            foreach ($where['amenities'] as $amen){
                $checked .= (int) $amen . ',';
            }
            if ($checked) {
				$query[] .= 'p.id IN (SELECT prop_id FROM #__iproperty_propmid WHERE amen_id IN ('.rtrim($checked, ',').'))';
			}
        }

        // create statement for agents
        if (isset($where['agents'])){
            $query[] = 'am.agent_id IN ('.(int) $where['agents'].')';
            $joins[] = '#__iproperty_agentmid am ON p.id = am.prop_id';
        }

        // create statement for hotsheet
        if (isset($where['hotsheet'])){		
            switch($where['hotsheet']){
                case 1: // new
                    $query[]    = 'p.created > DATE_SUB('.$nowDate.', INTERVAL '.(int)$settings->new_days.' DAY)';
                break;
                case 2: // updated
                    $query[]    = 'p.modified > DATE_SUB('.$nowDate.', INTERVAL '.(int)$settings->updated_days.' DAY)';
                break;
            }
        }
        
        // create statement to return recent listings
        if (isset($where['recent'])){
            $recent_type = (isset($where['recent']['type']) && $where['recent']['type'] == 1) ? 'modified' : 'created';
            $allowed_intervals = array('day', 'week', 'month', 'year');
            if (!$where['recent']['history'] || !$where['recent']['interval'] || !in_array(strtolower($where['recent']['interval']), $allowed_intervals)) return;
            $query[] = 'p.'.$recent_type.' >= DATE_SUB('.$nowDate.', INTERVAL '.(int)$where['recent']['history'].' '.$where['recent']['interval'].')';           
        }
        
        // create statement to return listings created after date 
        if (isset($where['created'])){
			$cdate = JFactory::getDate($where['created'])->toSql();
            $query[] = 'p.created >= '.$this->_db->Quote($cdate);           
        }
        
        // create statement to return listings modified after date 
        if (isset($where['modified'])){
			$mdate = JFactory::getDate($where['modified'])->toSql();
            $query[] = 'p.modified >= '.$this->_db->Quote($mdate);           
        }

        // add date and access level check
        if(is_array($groups) && !empty($groups)){
            $query[] = 'p.access IN ('.implode(",", $groups).')';
        }

        $query[] = '(p.publish_up = '.$nullDate.' OR p.publish_up <= '.$nowDate.')';
        $query[] = '(p.publish_down = '.$nullDate.' OR p.publish_down >= '.$nowDate.')';
        $query[] = 'p.state = 1';
        $query[] = 'p.approved = 1';

        // check that category is published
        $query[] = 'p.id IN (SELECT pm.prop_id FROM #__iproperty_propmid pm WHERE '.$pmquery.' pm.cat_id IN (SELECT id FROM #__iproperty_categories c WHERE c.state = 1 AND (c.publish_up = '.$nullDate.' OR c.publish_up <= '.$nowDate.') AND (c.publish_down = '.$nullDate.' OR c.publish_down >= '.$nowDate.') AND c.access IN ('.implode(",", $groups).')))';

        // function to handle any map tool searches
        if (isset($where['geopoint'])){
            if(isset($where['geopoint']['lat']) && isset($where['geopoint']['lon']) && isset($where['geopoint']['rad'])){
				// it's a radius search
				// thanks to Chris Veness for basic code
				// (c) http://www.movable-type.co.uk/scripts/latlong-db.html
				// rad must be in KM!!
                $R 		= 6371; // radius of Earth in KM
                $arad	= $where['geopoint']['rad'] / $R; // angular radius
                $max_lat = $where['geopoint']['lat'] + rad2deg($where['geopoint']['rad']/$R);
                $min_lat = $where['geopoint']['lat'] - rad2deg($where['geopoint']['rad']/$R);
                $max_lon = $where['geopoint']['lon'] + rad2deg($where['geopoint']['rad']/$R/cos(deg2rad($where['geopoint']['lat'])));
                $min_lon = $where['geopoint']['lon'] - rad2deg($where['geopoint']['rad']/$R/cos(deg2rad($where['geopoint']['lat'])));

                $query[] = '(p.latitude > ' . $min_lat . ' AND p.latitude < ' . $max_lat . ')';
                $query[] = '(p.longitude > ' . $min_lon . ' AND p.longitude < ' . $max_lon . ')';
                // refining query
                $query[] = 'acos(sin('.deg2rad($where['geopoint']['lat']).') * sin(radians(p.latitude)) + cos('.deg2rad($where['geopoint']['lat']).') * cos(radians(p.latitude)) * cos(radians(p.longitude) - ('.deg2rad($where['geopoint']['lon']).'))) <= '.$arad;
            } else if (is_array($where['geopoint']['sw']) && is_array($where['geopoint']['ne'])){
				// it's a rectangle search
				$min_lat = $where['geopoint']['sw'][0];
				$min_lon = $where['geopoint']['sw'][1];
				$max_lat = $where['geopoint']['ne'][0];
				$max_lon = $where['geopoint']['ne'][1];
				
                $query[] = '(p.latitude >= ' . $min_lat . ' AND p.latitude <= ' . $max_lat . ')';
                $query[] = '(p.longitude >= ' . $min_lon . ' AND p.longitude <= ' . $max_lon . ')';
			} else if (isset($where['geopoint']['paths'])){
				// it's a polygon search
				// not sure how to handle these yet
			}
        }
		
        // strip out any duplicated join statements
        $joins = array_unique($joins);
        $where = implode(" AND ", $query);

        // create query
        $query = $this->_db->getQuery(true);
        $query->select($select_string);
        $query->from('#__iproperty AS p');
        if (is_array($joins) && count($joins)){
            foreach ($joins as $join){
                $query->join('', $join);
            }
        }
        $query->where($where);
        if ($this->_sort && !$this->_order) // this means random sort (ie featured display)
        {
            $query->order($this->_sort);
        }else if ($this->_sort && $this->_order){
            $query->order($this->_sort . ' ' . $this->_order);
        }
        
        // add group by
		$query->group('p.id');
        //print_r(str_replace('#__', $this->_db->getPrefix(), $query));

        if($debug == 1) echo $query . '<br /><br />';
        return $query;
    }
    
    public function buildPropertyAgentQuery($where, $type, $debug = false)
    {
        $user       = JFactory::getUser();
        $groups     = $user->getAuthorisedViewLevels();
        $nullDate   = $this->_db->Quote($this->_db->getNullDate());
        $date       = JFactory::getDate();
        $nowDate    = $this->_db->Quote($date->toSql());
        $settings   = ipropertyAdmin::config();

        $select_string  = false;
        $pmquery		= '';
        $query          = array();
        $joins          = array();

        switch ($type)
        {
            case 'advsearch':
                $select_string = 'p.id AS id,p.property_type, p.title AS title, p.show_map, p.street_num, p.street, p.street2, p.apt, p.city, p.locstate, p.province, p.region, p.country, p.short_description as short_description, p.property_name, p.stype_freq, p.latitude, p.longitude, p.price, p.price2, p.beds, p.baths, p.sqft, p.call_for_price, p.created, p.modified, p.available, p.stype, p.hide_address, LEFT(p.description,300) as description, p.alias as alias, p.publish_up, p.modified';
            break;
            case 'property':
				$select_string = 'p.*, p.id AS id,p.property_type, p.title AS title, p.street_num, p.street, p.street2, p.description as description, p.created as created, p.alias as alias';
			break;
            case 'agentproperty':
				$select_string = 'p.*, p.id AS id,p.property_type, p.title AS title, p.street_num, p.street, p.street2, p.description as description, p.created as created, p.alias as alias';
			break;
            case 'properties':
                $select_string = 'p.id AS id,p.property_type, p.price, p.call_for_price, p.show_map, p.hide_address, p.price2, p.stype_freq, p.title AS title, p.beds, p.baths, p.sqft, p.lot_acres, p.lotsize, p.street_num, p.street, p.street2, p.description as description, p.short_description, p.created, p.created_by, p.modified, p.stype, p.listing_office, p.city, p.locstate, p.county, p.region, p.country, p.province, p.alias as alias, p.featured, p.latitude, p.longitude, p.available, p.income, p.tax, p.publish_up, p.state, p.checked_out, p.checked_out_time, p.hits';
            break;
            case 'ipuser':
                $select_string = 'p.property_name, p.id AS id, p.price, p.title AS title, p.show_map, p.street_num, p.street, p.street2, p.city, p.locstate, p.state, p.featured, p.approved, p.created as created, p.alias as alias';
            break;
            case 'openhouse':
                $select_string = 'p.*, p.id AS id, p.title AS title, p.street_num, p.street, p.street2, p.description as description, p.alias as alias, oh.name as ohname, oh.openhouse_start as ohstart, oh.openhouse_end as ohend, oh.comments as comments, p.created as created, oh.openhouse_start as startdate, oh.openhouse_end as enddate';
                $joins[] = '#__iproperty_openhouses as oh ON oh.prop_id = p.id';
                $query[] = 'oh.openhouse_end >= '.$nowDate;
                $query[] = 'oh.state = 1';
                if(!isset($this->_sort) || !isset($this->_order)) {
                    $this->_sort = 'oh.openhouse_end';
                    $this->_order = 'ASC';
                }
            break;
            default: // properties
                $select_string = 'p.id AS id,p.property_type, p.title AS title, p.beds, p.baths, p.sqft, p.lot_acres, p.lotsize, p.street_num, p.street, p.street2, p.description as description, p.short_description, p.created, p.modified, p.stype, p.listing_office, p.city, p.locstate, p.county, p.region, p.country, p.province, p.alias as alias, p.featured';
            break;
        }

     
        
        // create statement to return listings created after date 
        if (isset($where['created'])){
			$cdate = JFactory::getDate($where['created'])->toSql();
            $query[] = 'p.created >= '.$this->_db->Quote($cdate);           
        }
        
        // create statement to return listings modified after date 
        if (isset($where['modified'])){
			$mdate = JFactory::getDate($where['modified'])->toSql();
            $query[] = 'p.modified >= '.$this->_db->Quote($mdate);           
        }

        // add date and access level check
        if(is_array($groups) && !empty($groups)){
            $query[] = 'p.access IN ('.implode(",", $groups).')';
        }

        $query[] = '(p.publish_up = '.$nullDate.' OR p.publish_up <= '.$nowDate.')';
        $query[] = '(p.publish_down = '.$nullDate.' OR p.publish_down >= '.$nowDate.')';
        $query[] = 'p.state = 1';
        //$query[] = 'p.approved = 1';

        // check that category is published
        //$query[] = 'p.id IN (SELECT pm.prop_id FROM #__iproperty_propmid pm WHERE '.$pmquery.' pm.cat_id IN (SELECT id FROM #__iproperty_categories c WHERE c.state = 1 AND (c.publish_up = '.$nullDate.' OR c.publish_up <= '.$nowDate.') AND (c.publish_down = '.$nullDate.' OR c.publish_down >= '.$nowDate.') AND c.access IN ('.implode(",", $groups).')))';

        
		
        // strip out any duplicated join statements
        $joins = array_unique($joins);
        $where = implode(" AND ", $query);

        // create query
        $query = $this->_db->getQuery(true);
        $query->select($select_string);
        $query->from('#__ipropertyagent AS p');
        if (is_array($joins) && count($joins)){
            foreach ($joins as $join){
                $query->join('', $join);
            }
        }
        $query->where($where);
        if ($this->_sort && !$this->_order) // this means random sort (ie featured display)
        {
            $query->order($this->_sort);
        }else if ($this->_sort && $this->_order){
            $query->order($this->_sort . ' ' . $this->_order);
        }
        
        // add group by
		$query->group('p.id');
        //print_r(str_replace('#__', $this->_db->getPrefix(), $query));

        if($debug == 1) echo $query . '<br /><br />';
        

        
        return $query;
    }

    // function to build the where clauses for free text searches
    private static function textSearch($keyword, $searchfields)
    {	
        $db             = JFactory::getDbo();
        $keyword_search = array();
        $keyword_array  = explode(' ', $keyword);
        $against 		= '';
        
        // if the keyword search is more than one word, don't use the wild card *
        if(count($keyword_array) > 1){
            $word_string = '';
            foreach($keyword_array as $word){
                $word_string .= $word.' ';	
            }
            $against .= '+"'.trim($word_string).'"';
        }elseif(is_numeric($keyword)){
            $tmp = array();
            foreach($searchfields as $sf){
                $tmp[] = 'LOWER( p.'.$sf.' ) LIKE '.$db->Quote( '%'.$keyword.'%', false );
            }
            return "(".implode( ' OR ', $tmp ).")";
        }else{// single word, match with wild card *
            $against .= '+'.$keyword.'*';
        }

        if (is_array($searchfields) && $keyword)
        {
			$keyword_search = 'MATCH ('.implode(',', $searchfields).') AGAINST ('.$db->Quote(trim($against)).' IN BOOLEAN MODE)';
            return $keyword_search;
        }
        return false;
    }

    public static function buildAgentsQuery( $db, $where, $sort = false, $order = 'ASC', $debug = false, $with_listings = false )
    {
        $query = $db->getQuery(true);

        $query->select('a.*, a.id as id, c.id AS companyid, c.name AS companyname, CONCAT_WS(" ",fname,lname) AS name, c.alias as co_alias')
            ->from('#__iproperty_agents as a')
            ->leftJoin('#__iproperty_companies as c on c.id = a.company');
            if($with_listings == true){
                $query->leftJoin('#__iproperty_agentmid as am ON am.agent_id = a.id');
            }        
            if( !empty($where) ) {
                if(is_array($where)) {
                    foreach($where as $w){
                        $query->where($w);
                    }
                }
            }
        $query->where('a.state = 1 AND c.state = 1');
        $query->group('a.id');
        if( $sort ) $query->order($db->escape($sort.' '.$order)); 

        if( $debug ) echo $query . '<br /><br />';
        return $query;
    }
    
    public static function buildCompaniesQuery( $db, $where, $sort = false, $order = 'ASC', $debug = false )
    {
        $query = $db->getQuery(true);

        $query->select('c.*, c.id as id')
            ->from('#__iproperty_companies as c')
            ->where('c.state = 1');
            if( !empty($where) ) {
                if(is_array($where)) {
                    foreach($where as $w){
                        $query->where($w);
                    }
                }
            }
        $query->group('c.id');
        if( $sort ) $query->order($db->escape($sort.' '.$order)); 

        if($debug) echo $query . '<br /><br />';
        return $query;
    } 

    public static function getCategories($parent = '', $order = 'ordering ASC')
    {
        $db         = JFactory::getDbo();
        $user       = JFactory::getUser();
        $groups     = $user->getAuthorisedViewLevels();

        // Filter by start and end dates.
        $nullDate   = $db->Quote($db->getNullDate());
        $date       = JFactory::getDate();
        $nowDate    = $db->Quote($date->toSql());

        $query = $db->getQuery(true);
        $query->select('*, id as id')
            ->from('#__iproperty_categories')
            ->where('(publish_up = '.$nullDate.' OR publish_up <= '.$nowDate.')')
            ->where('(publish_down = '.$nullDate.' OR publish_down >= '.$nowDate.')')
            ->where('state = 1');
            if(is_numeric($parent)){
                $query->where('parent = '.(int)$parent);
            }
            // add date and access level check
            if(is_array($groups) && !empty($groups)){
                $query->where('access IN ('.implode(",", $groups).')');
            }
        $query->order($order);

        return $query;
    }
}
?>
