<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');

// Base this model on the allproperties model.
require_once JPATH_SITE.'/components/com_iproperty/models/allproperties.php';

class IpropertyModelCat extends IpropertyModelAllProperties
{    
    var $_catid     = null;
    var $_catinfo   = null;
    
    public function __construct($config = array())
	{
        $app            = JFactory::getApplication();
        $this->_catid   = $app->input->get('id', '', 'uint');
        
        parent::__construct($config);
    }
    
    protected function getWhere()
    {        
        $where          = parent::getWhere();
        
        $cats           = array($this->_catid);
        
        // check for child categories
        $catchildren    = ipropertyHTML::getCatChildren($this->_catid);  
        if ($catchildren)
        {
            foreach ($catchildren as $c)
            {
                $cats[] = $c->id;
            }
        }
        $where['categories'] = $cats;
        
        return $where;
    }
    
    public function getCatInfo()
	{        
        if (empty($this->_catinfo))
		{
			$user       = JFactory::getUser();
            $groups     = $user->getAuthorisedViewLevels();
            
            // Filter by start and end dates.
            $nullDate   = $this->_db->Quote($this->_db->getNullDate());
            $date       = JFactory::getDate();
            $nowDate    = $this->_db->Quote($date->toSql());

            $query = $this->_db->getQuery(true);
            $query->select('*')
                ->from('#__iproperty_categories')
                ->where('id = '.(int)$this->_catid)
                ->where('state = 1')
                ->where('(publish_up = '.$nullDate.' OR publish_up <= '.$nowDate.')')
                ->where('(publish_down = '.$nullDate.' OR publish_down >= '.$nowDate.')');
            if(is_array($groups) && !empty($groups)){
                $query->where('access IN ('.implode(",", $groups).')');
            }           
            
            $this->_db->setQuery($query, 0, 1);
			$this->_catinfo = $this->_db->loadObject();
		}
		return $this->_catinfo;
	}
}

?>