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

class IpropertyViewSettings extends JViewLegacy 
{
    protected $form;
	protected $item;
	protected $state;
    protected $settings;
    protected $ipauth;

	public function display($tpl = null)
	{
        // default to the editing layout
        JRequest::setVar('id', 1);
        $this->setLayout('edit');
        
        // Initialiase variables.
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');
        $this->settings = ipropertyAdmin::config(); 
        $this->ipauth   = new ipropertyHelperAuth();

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raise(E_ERROR, 500, implode("\n", $errors));
			return false;
		}
        
        // Load the submenu.
		IpropertyHelper::addSubmenu(JFactory::getApplication()->input->getCmd('view', 'settings'));

		$this->addToolbar();
        $this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

    protected function addToolbar()
	{
		$user       = JFactory::getUser();
        $notadmin   = (!$user->authorise('core.admin')) ? ': '.JText::_('COM_IPROPERTY_NOT_ADMIN_SETTINGS') : '';

		JToolBarHelper::title(JText::_('COM_IPROPERTY_EDIT_SETTINGS').$notadmin, 'iproperty.png');

		// If admin let them save and edit css
        if($user->authorise('core.admin')){
            JToolBarHelper::apply('settings.apply', 'JTOOLBAR_APPLY');
            JToolBarHelper::divider();
        }
        JToolBarHelper::cancel('settings.cancel', 'JTOOLBAR_CLOSE');
	}
}
?>