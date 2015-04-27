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

class IpropertyViewFiles extends JViewLegacy
{
    protected $items;
	protected $pagination;
	protected $state;
    protected $settings;
    protected $ipauth;

    public function display($tpl = null)
	{
		$user = JFactory::getUser();

        // Initialise variables.
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
        $this->settings     = ipropertyAdmin::config();      
        $this->ipauth       = new ipropertyHelperAuth();

        // Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raise(E_ERROR, 500, implode("\n", $errors));
			return false;
		}

        // if user is not admin AND not super agent or IP auth is disabled - no access
        if (!$this->ipauth->getAdmin() && (!$this->ipauth->getSuper() || !$this->ipauth->getAuthLevel())){
            $this->setLayout('noaccess');
            $this->_displayNoAccess($tpl);
            return;
        }
        
        // Load the submenu.
		IpropertyHelper::addSubmenu(JFactory::getApplication()->input->getCmd('view', 'files'));

		$this->addToolbar();
                 $this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

    protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_IPROPERTY_FILES'), 'iproperty.png');

        // Only show these options to super agents or admin
        if ($this->ipauth->getAdmin()){
            JToolBarHelper::addNew('file.add', 'JTOOLBAR_NEW');
        }
        if ($this->ipauth->getAdmin() || $this->ipauth->getSuper()){
            JToolBarHelper::editList('file.edit', 'JTOOLBAR_EDIT');
        }
        if ($this->ipauth->getAdmin()){   
            JToolBarHelper::divider();
            JToolBarHelper::divider();
            JToolBarHelper::publishList('files.publish', 'JTOOLBAR_PUBLISH');
            JToolBarHelper::unpublishList('files.unpublish', 'JTOOLBAR_UNPUBLISH');
            JToolBarHelper::divider();
            JToolBarHelper::deleteList(JText::_('COM_IPROPERTY_CONFIRM_DELETE'), 'files.delete', 'JTOOLBAR_DELETE');
		}
        
        // Add search filters
		JHtmlSidebar::setAction('index.php?option=com_iproperty&view=files');
        
        JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_state',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('archived'=>false, 'trash'=>false, 'all'=>false)), 'value', 'text', $this->state->get('filter.state'), true)
		);
	}
    
    protected function getSortFields()
	{
		return array(
            'ordering' => JText::_('COM_IPROPERTY_ORDER'),
			'c.name' => JText::_('COM_IPROPERTY_TITLE'),
			'c.email' => JText::_('COM_IPROPERTY_EMAIL'),
            'c.phone' => JText::_('COM_IPROPERTY_PHONE'),
			'c.website' => JText::_('COM_IPROPERTY_WEBSITE'),
			'phone' => JText::_('COM_IPROPERTY_PHONE'),
            'state' => JText::_('JSTATUS'),
            'featured' => JText::_('COM_IPROPERTY_FEATURED'),
            'agent_count' => JText::_('COM_IPROPERTY_AGENTS'),
            'prop_count' => JText::_('COM_IPROPERTY_PROPERTIES'),
			'id' => JText::_('JGRID_HEADING_ID')
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