<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
jimport('joomla.application.component.controllerform');

class IpropertyControllerSettings extends JControllerForm
{
	protected $text_prefix = 'COM_IPROPERTY';
    protected $context = 'settings';
    
    public function checkEditId($context, $recordid){
        return true;
    }

    protected function allowEdit($data = array(), $key = 'id')
	{
        $allow  = parent::allowEdit($data, $key);
        
        // Check if the user should be in this editing area
        $auth   = new ipropertyHelperAuth();
        $allow  = $auth->getAdmin();

        return $allow;
	}
    
    public function cancel()
    {
        $this->setRedirect(JRoute::_('index.php?option=com_iproperty', false));
	}
}
?>