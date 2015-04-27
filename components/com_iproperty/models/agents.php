<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
require_once JPATH_COMPONENT.'/models/list.php';
jimport('joomla.application.component.model');

class IpropertyModelAgents extends IpropertyModelList
{
	protected $ipsettings;
    
    public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
                'company_id', 'c.id',
                'a.company', 'a.lname', 
                'a.fname'
            );
		}
        
        $this->ipsettings   =  ipropertyAdmin::config();

		parent::__construct($config);
	}
    
    protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
        $id	.= ':'.$this->getState('filter.company_id');

		return parent::getStoreId($id);
	}
    
    protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.        
        $app        = JFactory::getApplication();  

		// List state information
		$value = $app->input->get('limit', $this->ipsettings->perpage, 'uint');
		$this->setState('list.limit', $value);

		$value = $app->input->get('limitstart', 0, 'uint');
		$this->setState('list.start', $value);
        
        $orderCol = $app->input->get('filter_order', $this->ipsettings->default_a_sort);
		if (!in_array($orderCol, $this->filter_fields)) {
			$orderCol = 'a.ordering';
		}
		$this->setState('list.ordering', $orderCol);

		$listOrder = $app->input->get('filter_order_Dir', $this->ipsettings->default_a_order);
		if (!in_array(strtoupper($listOrder), array('ASC', 'DESC'))) {
			$listOrder = 'ASC';
		}
		$this->setState('list.direction', $listOrder);

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

        $coid = $app->getUserStateFromRequest($this->context.'.filter.company_id', 'filter_company_id', '', 'int');
		$this->setState('filter.company_id', $coid);

		// List state information.
		parent::populateState($orderCol, $listOrder);
	}
    
    protected function getListQuery($featured = false)
	{        
        $where  = array();
        $sort   = ($featured) ? 'RAND()' : $this->getState('list.ordering');
        $order  = ($featured) ? '' : ' '.$this->getState('list.direction');

        // Filter by company.
		$companyId = $this->getState('filter.company_id');
		if ($companyId && is_numeric($companyId)) {
			$where[] = 'a.company = '.(int) $companyId;
		}

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$where[] = 'a.id = '.(int) substr($search, 3);
			}
			else {
				$search     = JString::strtolower($search);
                $search     = explode(' ', $search);
                $searchwhere   = array();
                if (is_array($search)){ //more than one search word
                    foreach ($search as $word){
                        $searchwhere[] = 'LOWER(a.fname) LIKE '.$this->_db->Quote( '%'.$this->_db->escape( $word, true ).'%', false );
                        $searchwhere[] = 'LOWER(a.lname) LIKE '.$this->_db->Quote( '%'.$this->_db->escape( $word, true ).'%', false );
                    }
                } else {
                    $searchwhere[] = 'LOWER(a.fname) LIKE '.$this->_db->Quote( '%'.$this->_db->escape( $search, true ).'%', false );
                    $searchwhere[] = 'LOWER(a.lname) LIKE '.$this->_db->Quote( '%'.$this->_db->escape( $search, true ).'%', false );
                }
                $where[] = '('.implode( ' OR ', $searchwhere ).')';
			}
		}
        
       
        if($featured) $where[] = 'a.featured = 1';
        
        $query  = IpropertyHelperQuery::buildAgentsQuery($this->_db, $where, $sort, $order, false);
        
		return $query;
	}
    
    public function getFeatured()
    {
        $query      = self::getListQuery(true);        
        $featured   = $this->_getList($query, 0, $this->ipsettings->num_featured);
        
        return $featured;
    }
    
    public function getStart()
	{
		return $this->getState('list.start');
	}
}

?>