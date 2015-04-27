<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');
class fwuserController extends JControllerLegacy{
	
	function display($cachable = false, $urlparams = Array()){
		JRequest::setVar('view', JRequest::getCmd('view', 'user'));
		parent::display($cachable);
	}
}

?>