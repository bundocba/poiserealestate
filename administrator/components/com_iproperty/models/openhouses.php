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

class IpropertyModelOpenhouses extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'o.id',
				'name', 'o.name',
                'street', 'p.street',
                'title', 'p.title',
                'property_name', 'p.property_name',
                'openhouse_end', 'o.openhouse_end',
                'openhouse_start', 'o.openhouse_start',
                'company', 'p.listing_office',
				'state', 'o.state'
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

		return parent::getStoreId($id);
	}

	public function getTable($type = 'Openhouse', $prefix = 'IpropertyTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$state = $app->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '', 'string');
		$this->setState('filter.state', $state);

        $state = $app->getUserStateFromRequest($this->context.'.filter.company_id', 'filter_company_id', '', 'int');
		$this->setState('filter.company_id', $state);

		// List state information.
		parent::populateState('openhouse_end', 'asc');
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
				'o.id, o.name as ohname, o.comments, o.prop_id,'.
                'o.state, o.checked_out, o.checked_out_time,'.
                'o.openhouse_start, o.openhouse_end,'.
				'p.property_name, p.street_num, p.street, p.street2, p.title,'.
                'CONCAT_WS(" ",p.street_num, p.street, p.street2) AS street_address,'.
                'p.listing_office as company'				
			)
		);

		$query->from('`#__iproperty_openhouses` AS o');

        // Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id = o.checked_out');

        // Join over the property
		$query->join('LEFT', '`#__iproperty` AS p ON p.id = o.prop_id');

        // Restrict list to display only relevent agents for agent access level
        if (!$ipauth->getAdmin()) {
            switch ($ipauth->getAuthLevel()){
                case 1: //company level
                    $query->where('p.listing_office = '.(int)$ipauth->getUagentCid());
                break;
                case 2:
                    // agent or company - only display own agent own listing openhouses
                    // if super agent, display other company agents openhouses as well
                    $query->where('p.listing_office = '.(int)$ipauth->getUagentCid());
                    if (!$ipauth->getSuper()) $query->where('o.prop_id IN ( SELECT prop_id FROM #__iproperty_agentmid WHERE agent_id = '.(int)$ipauth->getUagentId().')');
                break;
            }
        }

		// Filter by published state
		$published = $this->getState('filter.state');
		if (is_numeric($published)) {
			$query->where('o.state = '.(int) $published);
		} else if ($published === '') {
			$query->where('(o.state IN (0, 1))');
		}

        // Filter by company.
		$companyId = $this->getState('filter.company_id');
		if ($companyId && is_numeric($companyId)) {
			$query->where('p.listing_office = '.(int) $companyId);
		}

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('o.id = '.(int) substr($search, 3));
			}
            else if(stripos($search, 'pid:') === 0) {
				$query->where('p.id = '.(int) substr($search, 4));
			}
			else {
				$search     = JString::strtolower($search);
                $search     = explode(' ', $search);
                $searchwhere   = array();
                if (is_array($search)){ //more than one search word
                    foreach ($search as $word){
                        $searchwhere[] = 'LOWER(o.name) LIKE '.$db->Quote( '%'.$db->escape( $word, true ).'%', false );
                        $searchwhere[] = 'LOWER(p.street) LIKE '.$db->Quote( '%'.$db->escape( $word, true ).'%', false );
                        $searchwhere[] = 'LOWER(p.title) LIKE '.$db->Quote( '%'.$db->escape( $word, true ).'%', false );
                        $searchwhere[] = 'LOWER(p.property_name) LIKE '.$db->Quote( '%'.$db->escape( $word, true ).'%', false );
                    }
                } else {
                    $searchwhere[] = 'LOWER(o.name) LIKE '.$db->Quote( '%'.$db->escape( $search, true ).'%', false );
                    $searchwhere[] = 'LOWER(p.street) LIKE '.$db->Quote( '%'.$db->escape( $search, true ).'%', false );
                    $searchwhere[] = 'LOWER(p.title) LIKE '.$db->Quote( '%'.$db->escape( $search, true ).'%', false );
                    $searchwhere[] = 'LOWER(p.property_name) LIKE '.$db->Quote( '%'.$db->escape( $search, true ).'%', false );
                }
                $query->where('('.implode( ' OR ', $searchwhere ).')');
			}
		}        

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
        $query->group('o.id');
		$query->order($db->escape($orderCol.' '.$orderDirn));

		return $query;
	}
}//Class end
?>
