<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Iproperty
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license see LICENSE.php
 */
 
defined('_JEXEC') or die('Restricted access');

require_once(dirname(__FILE__).'/helper.php');

if (!$params->get('zillow_id') && $params->get('hide_mod')){
    return false;
}else if(!$params->get('zillow_id')){
    $params->def('layout', 'default_nodata');
}else{
    $Mrates = modIPMratesHelper::MRatesCall($params);
    if(is_null($Mrates)) $params->def('layout', 'default_nodata');
}
require(JModuleHelper::getLayoutPath('mod_ip_zillowmrates', $params->get('layout', 'default')));