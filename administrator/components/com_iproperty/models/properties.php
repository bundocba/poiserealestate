<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
jimport('joomla.application.component.modellist');

class IpropertyModelProperties extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'p.id',
                'mls_id', 'p.mls_id',
				'street', 'p.street',
                'title', 'p.title',
                'company', 'p.listing_office', 'company_title',
                'city', 'p.city',
                'category', 'pm.cat_id',
                'stype', 'p.stype',
                'agent', 'am.agent_id',
                'beds', 'p.beds',
                'baths', 'p.baths',
                'hits', 'p.hits',
				'state', 'p.state',
                'featured', 'p.featured',
                'approved', 'p.approved',
                'user_name', 'u.name',
                'sqft', 'p.sqft',
                'price', 'p.price',
                'access', 'p.access'
			);
		}

		parent::__construct($config);
	}

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.state');
        $id	.= ':'.$this->getState('filter.company_id');
        $id	.= ':'.$this->getState('filter.stype');
        $id	.= ':'.$this->getState('filter.city');
        $id	.= ':'.$this->getState('filter.cat_id');
        $id	.= ':'.$this->getState('filter.agent_id');
        $id	.= ':'.$this->getState('filter.beds');
        $id	.= ':'.$this->getState('filter.baths');

		return parent::getStoreId($id);
	}

	public function getTable($type = 'Property', $prefix = 'IpropertyTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

        $filters = array('search', 'state', 'company_id', 'stype', 'city', 'cat_id', 'agent_id', 'beds', 'baths', 'approved');

		// Load the filter state.
        foreach ($filters as $f){
            $search = $app->getUserStateFromRequest($this->context.'.filter.'.$f, 'filter_'.$f);
            $this->setState('filter.'.$f, $search);
        }

		// List state information.
		parent::populateState('p.id', 'asc');
	}

    protected function getListQuery()
	{
		// Initialise variables.
		$db         = $this->getDbo();
		$query      = $db->getQuery(true);
        $ipauth     = new ipropertyHelperAuth();

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'p.id as id, p.mls_id, p.title, p.alias, p.short_description, p.description,'.
                'p.state, p.featured, p.hits, p.approved,p.hide_address, p.apt,'.
                'p.beds, p.baths, p.sqft, p.price, p.price2,'.
                'p.street_num, p.street, p.street2, p.city, p.locstate, p.province,'.
                'p.hits, p.access, p.checked_out, p.checked_out_time,'.
                'p.agent_notes, p.stype_freq, p.call_for_price,'.
                'p.stype, CONCAT_WS(" ", p.street_num, p.street, p.street2) AS street_address'
			)
		);
		$query->from('`#__iproperty` AS p');

        // Join over view levels
        $query->select('ag.title as groupname');
		$query->join('LEFT', '`#__viewlevels` AS ag ON ag.id = p.access');

        // Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id = p.checked_out');

        // Join over prop mid table
		$query->join('LEFT', '#__iproperty_propmid AS pm ON pm.prop_id = p.id');

        // Join over agent mid table
		$query->join('LEFT', '#__iproperty_agentmid AS am ON am.prop_id = p.id');

        // Join over the company
		$query->select('c.name as company_title');
		$query->join('LEFT', '`#__iproperty_companies` AS c ON c.id = p.listing_office');

        // Restrict list to display only relevent agents for agent access level
        if (!$ipauth->getAdmin()) {
            switch ($ipauth->getAuthLevel()){
                case 1: //company
                    $query->where('p.listing_office = '.(int)$ipauth->getUagentCid());
                    break;
                case 2: //agent
                    $query->where('p.listing_office = '.(int)$ipauth->getUagentCid());
                    if (!$ipauth->getSuper()) $query->where('am.agent_id = '.(int)$ipauth->getUagentId());                   
                    break;
            }
        }

		// Filter by published state
		$published = $this->getState('filter.state');
		if (is_numeric($published)) {
			$query->where('p.state = '.(int) $published);
		} else if ($published === '') {
			$query->where('(p.state IN (0, 1))');
		}

        // Filter by approved state
		$approved = $this->getState('filter.approved');
		if (is_numeric($approved)) {
			$query->where('p.approved = '.(int) $approved);
		} else if ($approved === '') {
			$query->where('(p.approved IN (0, 1))');
		}

        // Filter by company.
		$companyId = $this->getState('filter.company_id');
		if ($companyId && is_numeric($companyId)) {
			$query->where('p.listing_office = '.(int) $companyId);
		}

        // Filter by sale type.
		$stypeId = $this->getState('filter.stype');
		if ($stypeId && is_numeric($stypeId)) {
			$query->where('p.stype = '.(int) $stypeId);
		}

        // Filter by city.
		$cityFilter = $this->getState('filter.city');
		if ($cityFilter) {
			$query->where('p.city = '.$db->Quote($cityFilter));
		}

        // Filter by category.
		$catId = $this->getState('filter.cat_id');
		if ($catId && is_numeric($catId))
        {
			// 2.0.1 - added search for subcategories when parent is selected in filter
            $cats_array = array($catId);
            $squery = $db->setQuery(IpropertyHelperQuery::getCategories($catId));
            $subcats = $db->loadObjectList();

            foreach($subcats as $s){
                $cats_array[] = (int)$s->id;
            }
			$query->where('pm.cat_id IN ('.implode(',', $cats_array).')');

            //$query->where('pm.cat_id = '.(int) $catId);
		}

        // Filter by agent.
		$agentId = $this->getState('filter.agent_id');
		if ($agentId && is_numeric($agentId)) {
			$query->where('am.agent_id = '.(int) $agentId);
		}

        // Filter by beds.
		$bedsId = $this->getState('filter.beds');
		if ($bedsId && is_numeric($bedsId)) {
			$query->where('p.beds >= '.(int) $bedsId);
		}

        // Filter by baths.
		$bathsId = $this->getState('filter.baths');
		if ($bathsId && is_numeric($bathsId)) {
			$query->where('p.baths >= '.(int) $bathsId);
		}

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('p.id = '.(int) substr($search, 3));
			}
			else {
				$search     = JString::strtolower($search);
                $search     = explode(' ', $search);
                $searchwhere   = array();
                if (is_array($search)){ //more than one search word
                    foreach ($search as $word){
                        $searchwhere[] = 'LOWER(p.mls_id) LIKE '.$db->Quote( '%'.$db->escape( $word, true ).'%', false );
                        $searchwhere[] = 'LOWER(p.short_description) LIKE '.$db->Quote( '%'.$db->escape( $word, true ).'%', false );
                        $searchwhere[] = 'LOWER(p.street) LIKE '.$db->Quote( '%'.$db->escape( $word, true ).'%', false );
                        $searchwhere[] = 'LOWER(p.description) LIKE '.$db->Quote( '%'.$db->escape( $word, true ).'%', false );
                        $searchwhere[] = 'LOWER(p.title) LIKE '.$db->Quote( '%'.$db->escape( $word, true ).'%', false );
                        $searchwhere[] = 'LOWER(p.city) LIKE '.$db->Quote( '%'.$db->escape( $word, true ).'%', false );
                        $searchwhere[] = 'LOWER(p.region) LIKE '.$db->Quote( '%'.$db->escape( $word, true ).'%', false );
                        $searchwhere[] = 'LOWER(p.province) LIKE '.$db->Quote( '%'.$db->escape( $word, true ).'%', false );
                    }
                } else {
                    $searchwhere[] = 'LOWER(p.mls_id) LIKE '.$db->Quote( '%'.$db->escape( $search, true ).'%', false );
                    $searchwhere[] = 'LOWER(p.short_description) LIKE '.$db->Quote( '%'.$db->escape( $search, true ).'%', false );
                    $searchwhere[] = 'LOWER(p.street) LIKE '.$db->Quote( '%'.$db->escape( $search, true ).'%', false );
                    $searchwhere[] = 'LOWER(p.descripton) LIKE '.$db->Quote( '%'.$db->escape( $search, true ).'%', false );
                    $searchwhere[] = 'LOWER(p.title) LIKE '.$db->Quote( '%'.$db->escape( $search, true ).'%', false );
                    $searchwhere[] = 'LOWER(p.city) LIKE '.$db->Quote( '%'.$db->escape( $search, true ).'%', false );
                    $searchwhere[] = 'LOWER(p.region) LIKE '.$db->Quote( '%'.$db->escape( $search, true ).'%', false );
                    $searchwhere[] = 'LOWER(p.province) LIKE '.$db->Quote( '%'.$db->escape( $search, true ).'%', false );
                }
                $query->where('('.implode( ' OR ', $searchwhere ).')');
			}
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
        $query->group('p.id');
		$query->order($db->escape($orderCol.' '.$orderDirn));

		return $query;
	}
}//Class end
?>