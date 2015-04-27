<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 Andy Sharman @ udjamaflip.com
 * @modified and rewritten by the Thinkery
 * @license see LICENSE.php
 */

//no direct access
defined('_JEXEC') or die('Restricted Access');

// Include the syndicate functions only once
require_once (dirname(__FILE__).'/helper.php');

// Get module data
$data = modIpTagCloudHelper::getWords($params);
if(!$data) return false;

// Let the magic begin
$realWordList   = modIpTagCloudHelper::filterWords($data, $params->get('excludelist'), $params->get('exclude_nonalph'));
$wordArray      = modIpTagCloudHelper::parseString($realWordList, $params->get('tagcount', 25));
$items          = modIpTagCloudHelper::outputWords($wordArray, $params->get('minsize', 10), $params->get('maxsize', 25), $params->get('fontcolor', '#135cae'));

if ( !$items && $params->get('hide_mod', 1) ){ // hide module if possible with template
    return false;
}else if( !$items ){ // display no data message
    $params->def('layout', 'default_nodata');
}
require(JModuleHelper::getLayoutPath('mod_ip_tagcloud', $params->get('layout', 'default')));
?>