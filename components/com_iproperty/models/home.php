<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
jimport('joomla.application.component.model');

class IpropertyModelHome extends JModelLegacy
{
	var $_featured  = null;
	var $_data      = null;
	var $_types     = null;
	
	public function __construct()
	{
		parent::__construct();		
	}
	
	public function getItems()
	{
	    $app  = JFactory::getApplication();
        
		$settings =  ipropertyAdmin::config();
		$perpage  = $settings->perpage;
		
		if (empty($this->_data))
		{			
            $array  = array();
            $parent = 0;           
            $user   = JFactory::getUser();
            $groups	= $user->getAuthorisedViewLevels();            
            
            //Loop through categories
            $this->catinfoRec($array, $parent);

            foreach($array as $cat)
            {
                $p = $cat->parent;
                $c = $cat->id;
                while(isset($array[$p])){
                    if(!isset($array[$p]->entriesR)){
                        $array[$p]->entriesR = 0;
                    }
                    if(!isset($array[$p]->children)){
                        $array[$p]->children=array();
                    }

                    $array[$p]->children[] = $c;
                    $c = $p;
                    $p = $array[$p]->parent;
                }
            }

            for($i = 0; $i < count($array); $i++)
            {
                $cat = &$array[$i];
                
                if(isset($cat->children)){
                    $cat->children = array_unique($cat->children);
                }
            }
            
            // Filter by start and end dates.
            $nullDate   = $this->_db->Quote($this->_db->getNullDate());
            $date       = JFactory::getDate();
            $nowDate    = $this->_db->Quote($date->toSql());

			$query = $this->_db->getQuery(true);
            $query->select('*, id')
                ->from('#__iproperty_categories')
                ->where('parent = '.(int)$parent)
                ->where('(publish_up = '.$nullDate.' OR publish_up <= '.$nowDate.')')
                ->where('(publish_down = '.$nullDate.' OR publish_down >= '.$nowDate.')')
                ->where('state = 1');
            if(is_array($groups) && !empty($groups)){
                $query->where('access IN ('.implode(",", $groups).')');
            }
            $query->order('ordering ASC');

            $this->_db->setQuery($query);
            $array[0]= new StdClass();
            $array[0]->children = $this->_db->loadColumn();
            
            $this->_data = $array;
		}
		return $this->_data;
	}

    public function catinfoRec(&$array,$parent)
    {
		$query = $this->getCategories($parent);
		$this->_db->setQuery($query);
    	$cats = $this->_db->loadObjectList("id");

        foreach($cats as $cat){
			$cat->id            = $cat->id."";
            $cat->entries       = ipropertyHTML::countCatObjects($cat->id);
			$cat->entriesR      = 0;
			$cat->children      = array();
			$array[$cat->id]    = $cat;
			$this->catinfoRec($array, $cat->id);
		}
	}

    public function getCategories($parent="")
    {
		$user   = JFactory::getUser();
        $groups	= $user->getAuthorisedViewLevels();
        
        // Filter by start and end dates.
        $nullDate   = $this->_db->Quote($this->_db->getNullDate());
        $date       = JFactory::getDate();
        $nowDate    = $this->_db->Quote($date->toSql());
        
        $query = $this->_db->getQuery(true);
        $query->select('*, id')
            ->from('#__iproperty_categories')
            ->where('parent = '.(int)$parent)
            ->where('(publish_up = '.$nullDate.' OR publish_up <= '.$nowDate.')')
            ->where('(publish_down = '.$nullDate.' OR publish_down >= '.$nowDate.')')
            ->where('state = 1');
        if(is_array($groups) && !empty($groups)){
            $query->where('access IN ('.implode(",", $groups).')');
        }
        $query->order('ordering ASC');

		return  $query;
	}
    
    public function getFeatured()
	{       
        $where = array();
        $where['property']['featured'] = 1;
        
        $pquery = new IpropertyHelperQuery($this->_db, 'RAND()', '');
        $query  = $pquery->buildPropertyQuery($where, 'properties');
        $this->_db->setQuery($query);
        
        if ($items = $this->_db->loadObjectList()){
            return ipropertyHelperProperty::getPropertyItems($items);
        }else{
            return false;
        }
	}
}

?>