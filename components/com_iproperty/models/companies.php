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

class IpropertyModelCompanies extends IpropertyModelList
{
	protected $ipsettings;
    
    public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
                'name', 'c.name',
                'city', 'c.city'
            );
		}
        
        $this->ipsettings   =  ipropertyAdmin::config();

		parent::__construct($config);
	}
    
    protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');

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
        
        $orderCol = $app->input->get('filter_order', $this->ipsettings->default_c_sort);
		if (!in_array($orderCol, $this->filter_fields)) {
			$orderCol = 'ordering';
		}
		$this->setState('list.ordering', $orderCol);

		$listOrder = $app->input->get('filter_order_Dir', $this->ipsettings->default_c_order);
		if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', ''))) {
			$listOrder = 'ASC';
		}
		$this->setState('list.direction', $listOrder);

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		// List state information.
		parent::populateState($orderCol, $listOrder);
	}
    
    protected function getListQuery($featured = false)
	{        
        $where = array();
		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$where[] = 'id = '.(int) substr($search, 3);
			}
			else {
				$search     = JString::strtolower($search);
                $search     = explode(' ', $search);
                $searchwhere   = array();
                if (is_array($search)){ //more than one search word
                    foreach ($search as $word){
                        $searchwhere[] = 'LOWER(name) LIKE '.$this->_db->Quote( '%'.$this->_db->escape( $word, true ).'%', false );
                    }
                } else {
                    $searchwhere[] = 'LOWER(name) LIKE '.$this->_db->Quote( '%'.$this->_db->escape( $search, true ).'%', false );
                }
                $where[] = '('.implode( ' OR ', $searchwhere ).')';
			}
		}
        if($featured) $where[] = 'featured = 1';
        
        $sort   = ($featured) ? 'RAND()' : $this->getState('list.ordering');
        $order  = ($featured) ? '' : ' '.$this->getState('list.direction');
        
        $query  = IpropertyHelperQuery::buildCompaniesQuery($this->_db, $where, $sort, $order, false);
        
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