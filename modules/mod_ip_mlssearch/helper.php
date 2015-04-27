<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Iproperty
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
require_once('components/com_iproperty/helpers/html.helper.php');
require_once('components/com_iproperty/helpers/route.php');

class modMlsSearchHelper
{    
    function getPropId($listing_id)
    {
        $db = JFactory::getDbo();
        
        $query = $db->getQuery(true);
        $query->select('id, alias')
                ->from('#__iproperty')
                ->where('mls_id = '.$db->Quote($listing_id));
        
        $db->setQuery($query);
        
        if($result = $db->loadObject()){
            return $result->id.':'.$result->alias;
        }else{
            return false;
        }
    }
}