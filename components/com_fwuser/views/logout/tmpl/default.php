<?php defined('_JEXEC') or die;
JHTML::_('behavior.modal');
$Itemid=JRequest::getVar('Itemid',0);
$mainframe = JFactory::getApplication();
$db=& JFactory::getDBO();

$user=&JFactory::getUser();
$user_id=$user->get('id');

$app = JFactory::getApplication();
$app->logout();

$session = JFactory::getSession();
$mess=array('message' => JText::_('LOGOUT_SUCCESS'), 'type' => 'message');
$mess_all=array($mess);
$session->set('application.queue', $mess_all);

$mainframe->redirect('index.php');
?>