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

class IpropertyModelAgents extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'fname', 'a.fname',
                'lname', 'a.lname',
                'company', 'a.company', 'company_title',
                'email', 'a.email',
                'phone', 'a.phone',
				'state', 'a.state',
				'ordering', 'a.ordering',
                'state', 'featured', 'a.featured',
                'user_name', 'u.name',
                'agent_type', 'a.agent_type',
                'prop_count'
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

	public function getTable($type = 'Agent', $prefix = 'IpropertyTable', $config = array())
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
		parent::populateState('company asc, ordering', 'asc');
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
				'a.id as id,'.
                'a.fname as fname,'.
                'a.lname as lname,'.
				'a.company as company,'.
                'a.email as email,'.
                'a.phone as phone,'.
                'a.featured as featured,'.
				'a.state as state,'.
                'a.ordering as ordering,'.
				'a.icon as icon,'.
                'a.agent_type,'.
                'a.checked_out,'.
                'a.checked_out_time,'.
                'a.alias,'.
                'count(DISTINCT(am.prop_id)) AS prop_count'   
			)
		);
		$query->from('`#__iproperty_agents` AS a');

        // Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id = a.checked_out');

        // Join over the company
		$query->select('c.name as company_title');
		$query->join('LEFT', '`#__iproperty_companies` AS c ON c.id = a.company');
        
        // Join over the agent mid to get property count
		$query->join('LEFT', '`#__iproperty_agentmid` AS am ON am.agent_id = a.id');

        // Join over user
        $query->select('u.username as user_username, u.id as user_id, u.name as user_name');
		$query->join('LEFT', '`#__users` AS u ON u.id = a.user_id');

        // Restrict list to display only relevent agents for agent access level
        if (!$ipauth->getAdmin()) {
            switch ($ipauth->getAuthLevel()){
                case 0:
                    // no security so no change. This is a placeholder
                break;
                case 1:
                case 2:
                    // agent or company - only display own agent profile to agent
                    // if super agent, display other company agents as well
                    $query->where('a.company = '.(int)$ipauth->getUagentCid());
                    if (!$ipauth->getSuper()) $query->where('a.id = '.(int)$ipauth->getUagentId());
                break;
            }
        }

		// Filter by published state
		$published = $this->getState('filter.state');
		if (is_numeric($published)) {
			$query->where('a.state = '.(int) $published);
		} else if ($published === '') {
			$query->where('(a.state IN (0, 1))');
		}

        // Filter by company.
		$companyId = $this->getState('filter.company_id');
		if ($companyId && is_numeric($companyId)) {
			$query->where('a.company = '.(int) $companyId);
		}

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			}
			else {
				$search     = JString::strtolower($search);
                $search     = explode(' ', $search);
                $searchwhere   = array();
                if (is_array($search)){ //more than one search word
                    foreach ($search as $word){
                        $searchwhere[] = 'LOWER(a.fname) LIKE '.$db->Quote( '%'.$db->escape( $word, true ).'%', false );
                        $searchwhere[] = 'LOWER(a.lname) LIKE '.$db->Quote( '%'.$db->escape( $word, true ).'%', false );
                    }
                } else {
                    $searchwhere[] = 'LOWER(a.fname) LIKE '.$db->Quote( '%'.$db->escape( $search, true ).'%', false );
                    $searchwhere[] = 'LOWER(a.lname) LIKE '.$db->Quote( '%'.$db->escape( $search, true ).'%', false );
                }
                $query->where('('.implode( ' OR ', $searchwhere ).')');
			}
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
        if ($orderCol == 'ordering' || $orderCol == 'company_title') {
			$orderCol = 'company_title '.$orderDirn.', ordering';
		}
        $query->group('a.id');
		$query->order($db->escape($orderCol.' '.$orderDirn));

		return $query;
	}
}//Class end