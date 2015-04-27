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

class IpropertyModelIproperty extends JModelLegacy
{
    public function getFprops()
    {
        return $this->getIpProperties('featured');
    }
    
    public function getTprops()
    {
        return $this->getIpProperties('popular', 'p.hits', 'DESC');
    }    
    
    protected function getIpProperties($type, $sort = 'p.id', $order = 'ASC', $limit = 15)
	{
		$where = '';
        switch($type){
            case 'popular':
                $where = ' AND p.hits != 0';
                break;
            case 'featured':
                $where = ' AND p.featured = 1';
                break;
            default:
                //nothing
                break;
        }
        
        $query = "SELECT p.id, p.street, p.street2, p.street_num, p.title, p.hits, p.listing_office,"
                ." (SELECT COUNT(s.id) FROM #__iproperty_saved AS s WHERE s.prop_id = p.id) AS saved"
                ." FROM #__iproperty AS p"
                ." WHERE p.state = 1"
                .$where
                ." AND (p.publish_up = '0000-00-00 00:00:00' OR p.publish_up <= NOW())"
			    ." AND (p.publish_down = '0000-00-00 00:00:00' OR p.publish_down >= NOW())"
                ." ORDER BY $sort $order LIMIT 0,15";
        $this->_db->setQuery($query);
        return $this->_db->loadObjectList();
	}

    public function getAusers()
    {
        $query = "SELECT u.id, u.name, u.username, u.email, u.registerDate,"
                ." (SELECT COUNT(DISTINCT(s.prop_id)) FROM #__iproperty_saved AS s WHERE s.user_id = u.id AND s.active = 1 AND s.type = 0) AS active_saves,"
                ." (SELECT COUNT(DISTINCT(s.prop_id)) FROM #__iproperty_saved AS s WHERE s.user_id = u.id AND s.active = 0 AND s.type = 0) AS inactive_saves"
                ." FROM #__users AS u, #__iproperty_saved AS s"
                ." WHERE u.block = 0 AND s.user_id = u.id"
                ." GROUP BY u.id"
                ." ORDER BY s.active DESC LIMIT 0,15";
        $this->_db->setQuery($query);
        return $this->_db->loadObjectList();
    }
    
    public function getSavedProperties($user_id, $active)
	{
        // Filter by start and end dates.
        $nullDate   = $this->_db->Quote($this->_db->getNullDate());
        $date       = JFactory::getDate();
        $nowDate    = $this->_db->Quote($date->toSql());

        $query = $this->_db->getQuery(true);
        $query->select('DISTINCT(p.id), s.*, p.id as id, p.price, p.price2, p.stype, p.stype_freq, p.call_for_price, p.title AS title, p.street_num, p.street AS street, p.street2, p.city, p.state, p.hide_address, p.sqft, p.approved, s.id AS save_id, s.timestamp AS created, p.alias as alias')
            ->from('#__iproperty_saved as s')
            ->leftJoin('#__iproperty as p on p.id = s.prop_id')
            ->where('s.user_id = '.(int)$user_id)
            ->where('s.active = '.(int)$active)
            ->where('s.type = 0')
            ->where('(p.publish_up = '.$nullDate.' OR p.publish_up <= '.$nowDate.')')
            ->where('(p.publish_down = '.$nullDate.' OR p.publish_down >= '.$nowDate.')')
            ->group('p.id')
            ->order('s.timestamp DESC');

        $this->_db->setQuery($query);
        $properties = $this->_db->loadObjectList();
        
        return $properties;
	}
}//Class end
?>