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

if ($params->get('slideshow_type', 0)) JHtml::_('behavior.framework', 'mootools-core.js');

// Include the helper functions only once
require_once (dirname(__FILE__).'/helper.php');

$items = modIPSlideshowGalleriaHelper::getList($params);

if (!$items && $params->get('hide_mod', 1)){
    return false;
}else if(!$items){
    $params->def('layout', 'default_nodata');
}else{
    modIPSlideshowGalleriaHelper::loadScripts($params, $items);
}
require(JModuleHelper::getLayoutPath('mod_ip_slideshow_galleria', $params->get('layout', 'default')));
