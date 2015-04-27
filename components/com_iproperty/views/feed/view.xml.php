<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
jimport('joomla.application.component.view');

class IpropertyViewFeed extends JViewLegacy
{
	public function display($tpl = null)
	{
        $app  = JFactory::getApplication();
        $option     = JRequest::getCmd('option');

        $dispatcher         = JDispatcher::getInstance();
        $this->ipbaseurl    = JURI::root(true);
        $document           = JFactory::getDocument();
        $settings           = ipropertyAdmin::config();
        $model              = $this->getModel();
        $property           = $this->get('data');

        if($property){
            $document->setTitle( $app->getCfg('sitename') );
        }
        $this->assignRef('properties', $property);
        $this->assignRef('settings', $settings);
        parent::display($tpl);
	}
}

?>
