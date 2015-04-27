<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @license see LICENSE.php
 */

//no direct access
defined('_JEXEC') or die('Restricted Access');

require_once(JPATH_ADMINISTRATOR.'/components/com_iproperty/classes/admin.class.php');
require_once(JPATH_SITE.'/components/com_iproperty/helpers/auth.php');
require_once(JPATH_SITE.'/components/com_iproperty/helpers/route.php');

$doc    = JFactory::getDocument();
$ipauth = new ipropertyHelperAuth();

// See if we should show the toolbar. Yes, if: 
// a) this is an admin user
// b) front end editing is enabled and the user is a valid IP agent
if($ipauth->getAdmin() || ($ipauth->getAuthLevel() && $ipauth->getUagent())){
    require(JModuleHelper::getLayoutPath('mod_ip_agenttoolbar', $params->get('layout', 'default')));
}else{
    return false;
}