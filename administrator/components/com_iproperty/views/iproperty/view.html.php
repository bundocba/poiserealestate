<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
jimport( 'joomla.application.component.view');

class IpropertyViewIproperty extends JViewLegacy 
{
    protected $user;
    protected $dispatcher;
    protected $fproperties;
    protected $tproperties;
    protected $ausers; 
    protected $settings;
    protected $ipauth;
    
    public function display($tpl = null)
	{
        JPluginHelper::importPlugin( 'iproperty');        

        // Initialiase variables.
        $this->user             = JFactory::getUser();
        $this->dispatcher       = JDispatcher::getInstance();
		$this->fproperties		= $this->get('Fprops');
		$this->tproperties		= $this->get('Tprops');
		$this->ausers           = $this->get('Ausers');
        $this->settings         = ipropertyAdmin::config(); 
        $this->ipauth           = new ipropertyHelperAuth();

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raise(E_ERROR, 500, implode("\n", $errors));
			return false;
		}
        
        // Load the submenu.
		IpropertyHelper::addSubmenu(JFactory::getApplication()->input->getCmd('view', 'iproperty'));
        
		$this->addToolbar();
        $this->sidebar = JHtmlSidebar::render();
        parent::display($tpl);
	}
    
    protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_IPROPERTY'), 'iproperty.png');

        // Only show config option to admin
        if ($this->ipauth->getAdmin()){
            JToolBarHelper::preferences('com_iproperty');
        }
	}
}
?>