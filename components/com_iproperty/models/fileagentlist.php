<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

// No direct access
defined('_JEXEC') or die;

// Base this model on the backend version.
require_once JPATH_ADMINISTRATOR.'/components/com_iproperty/models/fileagents.php';

class IpropertyModelFileAgentList extends IpropertyModelFiles
{
    public function __construct($config = array())
    {
            if (empty($config['filter_fields'])) {
                    $config['filter_fields'] = array(
                            'id', 'c.id',
                            'title', 'c.title'
                    );
            }

            parent::__construct($config);
    }
    protected function getItems()
    {
	
        // Initialise variables.
		$db         = $this->getDbo();
		$query      = $db->getQuery(true);
        $ipauth     = new ipropertyHelperAuth();

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'c.id as id,'.
                                'c.title as title,'.
                                'c.path as path,'.
                                'c.description as description'
			)
		);
		$query->from('`#__iproperty_fileagent` AS c');

       
        // Restrict list to display only relevent agents for agent access level
        if (!$ipauth->getAdmin()) {
            switch ($ipauth->getAuthLevel()){
                case 0:
                    // no security so no change. This is a placeholder
                break;
                case 1:
                case 2:
                    $query->where('c.id = '.(int)$ipauth->getUagentCid());
                break;
            }
        }

		// Filter by published state
		$published = $this->getState('filter.state');
		if (is_numeric($published)) {
			$query->where('c.state = '.(int) $published);
		} else if ($published === '') {
			$query->where('(c.state IN (0, 1))');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('c.id = '.(int) substr($search, 3));
			}
			else {
				$search     = JString::strtolower($search);
                $search     = explode(' ', $search);
                $searchwhere   = array();
                if (is_array($search)){ //more than one search word
                    foreach ($search as $word){
                        $searchwhere[] = 'LOWER(c.title) LIKE '.$db->Quote( '%'.$db->escape( $word, true ).'%', false );
                       
                    }
                } else {
                    $searchwhere[] = 'LOWER(c.ftitle) LIKE '.$db->Quote( '%'.$db->escape( $search, true ).'%', false );
                   
                }
                $query->where('('.implode( ' OR ', $searchwhere ).')');
			}
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
        if ($orderCol == 'ordering') {
			$orderCol = 'ordering';
		}
        $query->group('c.id');
		$query->order($db->escape($orderCol.' '.$orderDirn));

                
                $db->setQuery($query);
                
                $list = $db->loadAssocList();
                
		return $list;
	}
}