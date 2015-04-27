<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');

class plgSearchIpsearch extends JPlugin
{
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

    public function onContentSearchAreas()
	{
		static $areas = array(
			'iproperty' => 'PLG_IP_SEARCH_PROPERTIES'
		);
		return $areas;
	}
	
	public function onContentSearch( $text, $phrase='', $ordering='', $areas=null )
	{	
		$db		= JFactory::getDBO();
		$user	= JFactory::getUser();
        $groups	= $user->getAuthorisedViewLevels();
	
		require_once(JPATH_ADMINISTRATOR.'/components/com_search/helpers/search.php');
        require_once(JPATH_ADMINISTRATOR.'/components/com_iproperty/classes/admin.class.php');
        require_once(JPATH_SITE.'/components/com_iproperty/helpers/html.helper.php');
        require_once(JPATH_SITE.'/components/com_iproperty/helpers/route.php');
        
        $settings = ipropertyAdmin::config();
	
		$searchText = $text;
		if (is_array( $areas )) {
			if (!array_intersect( $areas, array_keys( $this->onContentSearchAreas() ) )) {
				return array();
			}
		}
	
		$sdesc 			= $this->params->get( 'search_short', 		1 );
        $fdesc 			= $this->params->get( 'search_full', 		1 );
		$limit 			= $this->params->def( 'search_limit', 		50 );
	
		$text = trim( $text );
		if ($text == '') {
			return array();
		}
	
		$wheres = array();
		switch ($phrase) {
			case 'exact':
				$text		= $db->Quote( '%'.$db->escape( $text, true ).'%', false );
				$wheres2 	= array();
				$wheres2[] 	= 'p.street_num LIKE '.$text;
                $wheres2[] 	= 'p.street LIKE '.$text;
                $wheres2[] 	= 'p.street2 LIKE '.$text;
                $wheres2[] 	= 'p.title LIKE '.$text;
                $wheres2[] 	= 'p.city LIKE '.$text;
                $wheres2[] 	= 'p.county LIKE '.$text; //1.5.5
                $wheres2[] 	= 'p.region LIKE '.$text; //1.5.5
                $wheres2[]  = 'p.province LIKE '.$text; //1.6.2
                $wheres2[]  = 'p.mls_id LIKE '.$text; //1.5.6
                $wheres2[]  = 'c.title LIKE '.$text; //1.6.2
                $wheres2[]  = 'c.desc LIKE '.$text; //1.6.2
                $wheres2[]  = 's.title LIKE '.$text; //3.1.2
				if($sdesc ) {
                    $wheres2[] 	= 'p.short_description LIKE '. $text;
                }
                if($fdesc ) {
                    $wheres2[] 	= 'p.description LIKE '. $text;
                }
				$where 		= '(' . implode( ') OR (', $wheres2 ) . ')';
				break;
			case 'all':
			case 'any':
			default:
				$words = explode( ' ', $text );
				$wheres = array();
				foreach ($words as $word) {
					$word		= $db->Quote( '%'.$db->escape( $word, true ).'%', false );
					$wheres2 	= array();
					$wheres2[] 	= 'p.street_num LIKE '.$word;
                    $wheres2[] 	= 'p.street LIKE '.$word;
                    $wheres2[] 	= 'p.street2 LIKE '.$word;
                    $wheres2[] 	= 'p.title LIKE '.$word;
                    $wheres2[] 	= 'p.city LIKE '.$word;
                    $wheres2[] 	= 'p.county LIKE '.$word; //1.5.5
                    $wheres2[] 	= 'p.region LIKE '.$word; //1.5.5
                    $wheres2[]  = 'p.province LIKE '.$word; //1.6.2
                    $wheres2[]  = 'p.mls_id LIKE '.$word; //1.5.6
                    $wheres2[]  = 'c.title LIKE '.$word; //1.6.2
                    $wheres2[]  = 'c.desc LIKE '.$word; //1.6.2
                    $wheres2[]  = 's.title LIKE '.$word; //3.1.2
					if($sdesc ) {
						$wheres2[] 	= 'p.short_description LIKE '. $word;
					}
                    if($fdesc ) {
						$wheres2[] 	= 'p.description LIKE '. $word;
					}
					$wheres[] 	= implode( ' OR ', $wheres2 );
				}
				$where = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';
				break;
		}
	
		$morder = '';
		switch ($ordering) {
			case 'oldest':
				$order = 'p.created ASC';
				break;
	
			case 'popular':
				$order = 'p.hits DESC';
				break;
	
			case 'alpha':
				$order = ($settings->showtitle) ? 'p.title ASC' : 'p.street ASC';
				break;
			
			case 'category':
				$order = ($settings->showtitle) ? 'pm.cat_id ASC, p.title ASC' : 'pm.cat_id ASC, p.street ASC';
				
			case 'newest':
				default:
				$order = 'p.created DESC';
				break;
		}
	
		$rows = array();
		
		if ( $limit > 0 )
		{
            // Filter by start and end dates.
            $nullDate   = $db->Quote($db->getNullDate());
            $date       = JFactory::getDate();
            $nowDate    = $db->Quote($date->toSql());
            
            $query = $db->getQuery(true);
            $query->select('p.title, p.mls_id, p.street_num, p.street, p.street2, p.created AS created, p.alias as alias, p.hide_address AS hidden, p.short_description, p.description, c.title AS section, p.id AS property_id, c.id AS cat_id, p.hits, "2" AS browsernav, s.title as state_name')
                    ->from('#__iproperty as p')
                    ->innerJoin('#__iproperty_propmid as pm on pm.prop_id = p.id')
                    ->innerJoin('#__iproperty_categories as c on c.id = pm.cat_id')
                    ->leftJoin('#__iproperty_states as s on s.id = p.locstate')
                    ->where('p.state = 1 and p.approved = 1 and c.state = 1')
                    ->where('(p.publish_up = '.$nullDate.' OR p.publish_up <= '.$nowDate.')')
                    ->where('(p.publish_down = '.$nullDate.' OR p.publish_down >= '.$nowDate.')')
                    ->where('(c.publish_up = '.$nullDate.' OR c.publish_up <= '.$nowDate.')')
                    ->where('(c.publish_down = '.$nullDate.' OR c.publish_down >= '.$nowDate.')')
                    ->where($where);
            if(is_array($groups) && !empty($groups)){
                $query->where('p.access IN ('.implode(",", $groups).')')
                    ->where('c.access IN ('.implode(",", $groups).')');
            }
            $query->group('p.id');
            $query->order($order);
			
			$db->setQuery( $query, 0, $limit );
			$list = $db->loadObjectList();
			$limit -= count($list);
	
			if(isset($list))
			{
				foreach($list as $key => $item)
				{
                    $available_cats = ipropertyHTML::getAvailableCats($item->property_id);
                    $first_cat = $available_cats[0];
                    $desc_text = ($item->short_description) ? $item->short_description : $item->description;

                    if($item->hidden){
                        $list[$key]->title = $item->section.': '.JText::_('PLG_IP_SEARCH_ADDRESS_HIDDEN');
                    }else{
                        $list[$key]->title = $item->section.': '.ipropertyHTML::getStreetAddress($settings, $item);
                    }
                    $list[$key]->text = $desc_text;
                    $list[$key]->href = JRoute::_(ipropertyHelperRoute::getPropertyRoute($item->property_id.':'.$item->alias, $first_cat, true));
				}
			}
			$rows[] = $list;
		}
	
	
		$results = array();
		if(count($rows))
		{
			foreach($rows as $row)
			{
				$new_row = array();
				foreach($row AS $key => $post) {
					$new_row[] = $post;
				}
				$results = array_merge($results, (array) $new_row);
			}
		}
	
		return $results;
	}
}