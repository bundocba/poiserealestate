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

class IpropertyModelAgentProperties extends IpropertyModelAllProperties
{    
    protected function getWhere()
    {        
        $where = parent::getWhere();
        
        $app                = JFactory::getApplication();
        $where['agents']    = $app->input->get('id', '', 'uint');  
       
        return $where;
       
    }
}

?>