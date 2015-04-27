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

class IpropertyViewCategories extends JViewLegacy
{
	protected $state;
    protected $settings;
    protected $ipauth;
    
    public function display($tpl = null)
	{
        // Initialise variables.
		$this->state		= $this->get('State');
        $this->settings     = ipropertyAdmin::config();
        $this->ipauth       = new ipropertyHelperAuth();

        // Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raise(E_ERROR, 500, implode("\n", $errors));
			return false;
		}

        // if user is not admin - no access
        if (!$this->ipauth->getAdmin()){
            $this->setLayout('noaccess');
            $this->_displayNoAccess($tpl);
            return;
        }
        
        // Load the submenu.
		IpropertyHelper::addSubmenu(JFactory::getApplication()->input->getCmd('view', 'categories'));

		$this->addToolbar();
        $this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

    protected function addToolbar($tpl = null)
    {
        //create the toolbar
        JToolBarHelper::title(JText::_('COM_IPROPERTY_CATEGORIES'), 'iproperty.png');

        if($this->getLayout() != 'remove'){
            JToolBarHelper::addNew('category.add', 'JTOOLBAR_NEW');
            JToolBarHelper::editList('category.edit', 'JTOOLBAR_EDIT');
            JToolBarHelper::divider();            
            JToolBarHelper::publishList('categories.publish', 'JTOOLBAR_PUBLISH');
            JToolBarHelper::unpublishList('categories.unpublish', 'JTOOLBAR_UNPUBLISH');
            JToolBarHelper::divider();
            
            JToolBarHelper::deleteList('', 'categories.delete', 'JTOOLBAR_DELETE');
		}else{
			JToolBarHelper::back();
		}
        
        // Add search filters
		JHtmlSidebar::setAction('index.php?option=com_iproperty&view=categories');
        
        JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_state',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('archived'=>false, 'trash'=>false, 'all'=>false)), 'value', 'text', $this->state->get('filter.state'), true)
		);
	}

    public function _displayNoAccess($tpl = null)
    {
        JToolBarHelper::title(JText::_('COM_IPROPERTY_NO_ACCESS'), 'iproperty.png');
        JToolBarHelper::back();
        parent::display($tpl);
    }
}
?>