<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_login
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the login functions only once
require_once __DIR__ . '/helper.php';

$params->def('greeting', 1);

$type	          = ModLoginHelper::getType();
$return	          = ModLoginHelper::getReturnURL($params, $type);
$twofactormethods = ModLoginHelper::getTwoFactorMethods();
$user	          = JFactory::getUser();
$layout           = $params->get('layout', 'default');

jimport( 'joomla.access.access' );
$groups = JAccess::getGroupsByUser($user->id, false);
$is_agent = false;
foreach($groups as $key=>$value){
	if($value==10){
		$is_agent = true;
	}
}

// Logged users must load the logout sublayout
if (!$user->guest)
{
	switch($module->id){
		case '104':
			if($is_agent==false){
				$layout .= '_logout';
			}
			break;
		case '105':
			if($is_agent){
				$layout .= '_logout';
			}
			break;
	}
	
}

require JModuleHelper::getLayoutPath('mod_login', $layout);
