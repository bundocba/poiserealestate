<?php defined('_JEXEC') or die('Restricted access');
session_start();
jimport('joomla.application.component.controller');
jimport('joomla.application.component.model');
jimport('joomla.form.form');
JLoader::register('fwuserHelper', dirname(__FILE__) . DS . 'helpers' . DS . 'fwuser.php');
$controller = JControllerLegacy::getInstance('fwuser');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
?>