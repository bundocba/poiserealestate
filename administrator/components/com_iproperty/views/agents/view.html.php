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

class IpropertyViewAgents extends JViewLegacy
{
    protected $companies;
    protected $items;
	protected $pagination;
	protected $state;
    protected $settings;
    protected $ipauth;
    protected $cofield;

	public function display($tpl = null)
	{
        // Initialise variables.
        $this->companies	= $this->get('CompanyOrders');
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

        // if user is not admin AND user does not have and agent id or IP auth is disabled - no access
        if (!$this->ipauth->getAdmin() && (!$this->ipauth->getUagentId() || !$this->ipauth->getAuthLevel())){
            $this->setLayout('noaccess');
            $this->_displayNoAccess($tpl);
            return;
        }
        
        // Load the submenu and admin menu drop down        
		IpropertyHelper::addSubmenu(JFactory::getApplication()->input->getCmd('view', 'agents'));        

		$this->addToolbar();
        $this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

    protected function addToolbar()
	{
		require_once JPATH_COMPONENT .'/models/fields/company.php';
        $this->cofield = new JFormFieldCompany();
        
        JToolBarHelper::title(JText::_('COM_IPROPERTY_AGENTS'), 'iproperty.png');
        
        $admin = $this->ipauth->getAdmin() || $this->ipauth->getSuper();
        
        // Only show these options to super agents or admin
        if ($admin){
            JToolBarHelper::addNew('agent.add','JTOOLBAR_NEW');
        }
        
        // Any user with access to this view should be able to edit their own profile
        JToolBarHelper::editList('agent.edit','JTOOLBAR_EDIT');
        JToolBarHelper::divider();       
        
        if ($admin){            
            IpToolbar::featureList('agents.feature', JText::_('COM_IPROPERTY_FEATURE'));
            IpToolbar::unfeatureList('agents.unfeature', JText::_('COM_IPROPERTY_UNFEATURE'));
            JToolBarHelper::divider();
            JToolBarHelper::publishList('agents.publish', 'JTOOLBAR_PUBLISH');
            JToolBarHelper::unpublishList('agents.unpublish', 'JTOOLBAR_UNPUBLISH');
		}
        
        if($this->ipauth->getAdmin()){
            JToolBarHelper::divider();
            IpToolbar::superList('agents.super', JText::_('COM_IPROPERTY_SUPER'));
            IpToolbar::unsuperList('agents.unsuper', JText::_('COM_IPROPERTY_UNSUPER'));
        }

		// Only show these options to super agents or admin
        if ($this->ipauth->getAdmin() || $this->ipauth->getSuper()){
			JToolBarHelper::divider();
            JToolBarHelper::deleteList(JText::_('COM_IPROPERTY_CONFIRM_DELETE'), 'agents.delete', 'JTOOLBAR_DELETE');
		}
        
        // Add search filters
		JHtmlSidebar::setAction('index.php?option=com_iproperty&view=agents');
        
        JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_state',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('archived'=>false, 'trash'=>false, 'all'=>false)), 'value', 'text', $this->state->get('filter.state'), true)
		);
        
        JHtmlSidebar::addFilter(
			'- '.JText::_('JSELECT').' '.JText::_('COM_IPROPERTY_COMPANY').' -',
			'filter_company_id',
			JHtml::_('select.options', $this->cofield->getOptions(true), 'value', 'text', $this->state->get('filter.company_id'))
		);
	}
    
    protected function getSortFields()
	{
		return array(
            'ordering' => JText::_('COM_IPROPERTY_ORDER'),
			'lname' => JText::_('COM_IPROPERTY_LNAME'),
			'user_name' => JText::_('COM_IPROPERTY_USER'),
            'company' => JText::_('COM_IPROPERTY_COMPANY'),
			'email' => JText::_('COM_IPROPERTY_EMAIL'),
			'phone' => JText::_('COM_IPROPERTY_PHONE'),
			'prop_count' => JText::_('COM_IPROPERTY_PROPERTIES'),
            'state' => JText::_('JSTATUS'),
            'featured' => JText::_('COM_IPROPERTY_FEATURED'),
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