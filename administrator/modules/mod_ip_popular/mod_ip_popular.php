<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Iproperty
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license see LICENSE.php
 * @notes  based on Joomla mod_popular module
 */

// no direct access
defined('_JEXEC') or die;

// Include the helper functions only once
require_once (dirname(__FILE__).'/helper.php');

// Get module data
$list = modIpPopularHelper::getList($params);

// Render the module
require JModuleHelper::getLayoutPath('mod_ip_popular', $params->get('layout', 'default'));