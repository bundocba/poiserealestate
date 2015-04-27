<?php defined('_JEXEC') or die('Restricted access');
if (!JFactory::getUser()->authorise('core.manage', 'com_fwmedia')){
	// return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}
JLoader::register('fwuserHelper', dirname(__FILE__) . DS . 'helpers' . DS . 'fwuser.php');
jimport('joomla.application.component.controller');
$controller = JControllerLegacy::getInstance('fwuser');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
?>