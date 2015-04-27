<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
jimport('joomla.application.component.controller');

require_once(JPATH_COMPONENT_ADMINISTRATOR.'/classes/admin.class.php');
require_once(JPATH_COMPONENT.'/helpers/auth.php');
require_once(JPATH_COMPONENT.'/helpers/html.helper.php');
require_once(JPATH_COMPONENT.'/helpers/property.php');
require_once(JPATH_COMPONENT.'/helpers/query.php');
require_once(JPATH_COMPONENT.'/helpers/route.php');

$controller = JControllerLegacy::getInstance('Iproperty');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
?>