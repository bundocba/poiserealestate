<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @license see LICENSE.php
 */

//no direct access
defined('_JEXEC') or die('Restricted Access');

// Include the helper functions only once
require_once (dirname(__FILE__).'/helper.php');

// Get module data
$falang = JPluginHelper::isEnabled('system', 'falangdriver');
$items  = modIpCityLinksHelper::getList($params, $falang);

if(!$items && $params->get('hide_mod')){ // hide module if possible with template
    return false;
}else if(!$items){ // display no data message
    $params->def('layout', 'default_nodata');
}
require(JModuleHelper::getLayoutPath('mod_ip_citylinks', $params->get('layout', 'default')));