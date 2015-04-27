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
require_once __DIR__ . '/allproperties.php';

class IpropertyModelOpenhouses extends IpropertyModelAllProperties
{
	protected function getListQuery($featured = false)
	{
        $where  = $this->getWhere();         
        $sort   = ($featured) ? 'RAND()' : 'ohend';
        $order  = ($featured) ? '' : 'ASC';
        if($featured) $where['property']['featured'] = 1;
        
        $pquery = new IpropertyHelperQuery($this->_db, $sort, $order);
        $query  = $pquery->buildPropertyQuery($where, 'openhouse');       
        
		return $query;
	}
}

?>
