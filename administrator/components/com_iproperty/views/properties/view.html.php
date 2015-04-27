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

class IpropertyViewProperties extends JViewLegacy 
{
    protected $items;
	protected $pagination;
	protected $state;
    protected $settings;
    protected $ipauth;
    protected $approveoptions;
    
    public function display($tpl = null)
	{
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

        // if user is not admin AND user does not have and agent id or IP auth is disabled - no access
        if (!$this->ipauth->getAdmin() && (!$this->ipauth->getUagentId() || !$this->ipauth->getAuthLevel())){
            $this->setLayout('noaccess');
            $this->_displayNoAccess($tpl);
            return;
        }
        
        // build approve options
        $aoptions   = array();
        $aoptions[] = JHtml::_('select.option', '1', 'COM_IPROPERTY_APPROVED');
        $aoptions[] = JHtml::_('select.option', '0', 'COM_IPROPERTY_UNAPPROVED');
        $this->approveoptions = $aoptions;
        
        // Load the submenu.
		IpropertyHelper::addSubmenu(JFactory::getApplication()->input->getCmd('view', 'properties'));

		$this->addToolbar();
        $this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

    protected function addToolbar()
	{
        require_once JPATH_COMPONENT .'/models/fields/company.php';
        require_once JPATH_COMPONENT .'/models/fields/stypes.php';
        require_once JPATH_COMPONENT .'/models/fields/city.php';
        require_once JPATH_COMPONENT .'/models/fields/ipcategory.php';
        require_once JPATH_COMPONENT .'/models/fields/agent.php';
        require_once JPATH_COMPONENT .'/models/fields/beds.php';
        require_once JPATH_COMPONENT .'/models/fields/baths.php';
        
        // build fields to grab options for filters
        $this->cofield = new JFormFieldCompany();
        $this->stypesfield = new JFormFieldStypes();
        $this->citiesfield = new JFormFieldCity();
        $this->ipcatsfield = new JFormFieldIpCategory();
        $this->agentsfield = new JFormFieldAgent();
        $this->bedsfield = new JFormFieldBeds();
        $this->bathsfield = new JFormFieldBaths();
        
        JToolBarHelper::title(JText::_('COM_IPROPERTY_PROPERTIES'), 'iproperty.png');

        // Any user with access to this view should see these
        JToolBarHelper::addNew('property.add', 'JTOOLBAR_NEW');
        JToolBarHelper::editList('property.edit', 'JTOOLBAR_EDIT');
        JToolBarHelper::divider();        
        JToolBarHelper::publishList('properties.publish', 'JTOOLBAR_PUBLISH');
        JToolBarHelper::unpublishList('properties.unpublish', 'JTOOLBAR_UNPUBLISH');
        JToolBarHelper::divider();

        // Only show these options to super agents or admin
        if ($this->ipauth->getAdmin() || $this->ipauth->getSuper()){
            if($this->settings->edit_rights){
                IpToolbar::approveList('properties.approve', JText::_('COM_IPROPERTY_APPROVE' ));
                IpToolbar::unapproveList('properties.unapprove', JText::_('COM_IPROPERTY_UNAPPROVE' ));
                JToolBarHelper::divider();
            }
                
            IpToolbar::featureList('properties.feature', JText::_('COM_IPROPERTY_FEATURE' ));
            IpToolbar::unfeatureList('properties.unfeature', JText::_('COM_IPROPERTY_UNFEATURE' ));
            JToolBarHelper::divider();            
		}
        
        // Only show these options to super agents or admin
        if ($this->ipauth->getAdmin() || $this->ipauth->getSuper()){
            IpToolbar::clearHits(JText::_('COM_IPROPERTY_CONFIRM_CLEAR'), 'properties.clearHits', 'COM_IPROPERTY_CLEAR_HITS');
		}
        
        // Any user with access to this view should see these      
        JToolBarHelper::deleteList(JText::_('COM_IPROPERTY_CONFIRM_DELETE'), 'properties.delete', 'JTOOLBAR_DELETE');		
        
        // Add search filters
		JHtmlSidebar::setAction('index.php?option=com_iproperty&view=properties');
        
        JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_state',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('archived'=>false, 'trash'=>false, 'all'=>false)), 'value', 'text', $this->state->get('filter.state'), true)
		);
        
        JHtmlSidebar::addFilter(
			'- '.JText::_('JSELECT').' '.JText::_('COM_IPROPERTY_APPROVED').' -',
			'filter_approved',
			JHtml::_('select.options', $this->approveoptions, 'value', 'text', $this->state->get('filter.approved'), true)
		);
        
        JHtmlSidebar::addFilter(
			'- '.JText::_('JSELECT').' '.JText::_('COM_IPROPERTY_BEDS').' -',
			'filter_beds',
			JHtml::_('select.options', $this->bedsfield->getOptions(), 'value', 'text', $this->state->get('filter.beds'))
		); 
        
        JHtmlSidebar::addFilter(
			'- '.JText::_('JSELECT').' '.JText::_('COM_IPROPERTY_BATHS').' -',
			'filter_baths',
			JHtml::_('select.options', $this->bathsfield->getOptions(false), 'value', 'text', $this->state->get('filter.baths'))
		);
        
        JHtmlSidebar::addFilter(
			'- '.JText::_('JSELECT').' '.JText::_('COM_IPROPERTY_CATEGORY').' -',
			'filter_cat_id',
			JHtml::_('select.options', $this->ipcatsfield->getOptions(), 'value', 'text', $this->state->get('filter.cat_id'))
		);
        
        JHtmlSidebar::addFilter(
			'- '.JText::_('JSELECT').' '.JText::_('COM_IPROPERTY_COMPANY').' -',
			'filter_company_id',
			JHtml::_('select.options', $this->cofield->getOptions(true), 'value', 'text', $this->state->get('filter.company_id'))
		);
        
        JHtmlSidebar::addFilter(
			'- '.JText::_('JSELECT').' '.JText::_('COM_IPROPERTY_AGENT').' -',
			'filter_agent_id',
			JHtml::_('select.options', $this->agentsfield->getOptions(true), 'value', 'text', $this->state->get('filter.agent_id'))
		);
        
        JHtmlSidebar::addFilter(
			'- '.JText::_('JSELECT').' '.JText::_('COM_IPROPERTY_STYPE').' -',
			'filter_stype',
			JHtml::_('select.options', $this->stypesfield->getOptions(true), 'value', 'text', $this->state->get('filter.stype'))
		);
        
        JHtmlSidebar::addFilter(
			'- '.JText::_('JSELECT').' '.JText::_('COM_IPROPERTY_CITY').' -',
			'filter_city',
			JHtml::_('select.options', $this->citiesfield->getOptions(), 'value', 'text', $this->state->get('filter.city'))
		);
	}
    
    protected function getSortFields()
	{
		return array(
			'mls_id' => JText::_('COM_IPROPERTY_REF'),
			'street' => JText::_('COM_IPROPERTY_STREET'),
            'title' => JText::_('COM_IPROPERTY_TITLE'),
			'city' => JText::_('COM_IPROPERTY_CITY'),
			'beds' => JText::_('COM_IPROPERTY_BEDS'),
			'baths' => JText::_('COM_IPROPERTY_BATHS'),
			'sqft' => (!$this->settings->measurement_units) ? JText::_('COM_IPROPERTY_SQFT' ) : JText::_('COM_IPROPERTY_SQM' ),
			'hits' => JText::_('COM_IPROPERTY_HITS'),
			'access' => JText::_('COM_IPROPERTY_ACCESS'),
            'state' => JText::_('JSTATUS'),
            'featured' => JText::_('COM_IPROPERTY_HOT'),
			'p.id' => JText::_('JGRID_HEADING_ID')
		);
	}

    public function _displayNoAccess($tpl = null)
    {
        JToolBarHelper::title(JText::_('COM_IPROPERTY_NO_ACCESS'), 'iproperty.png');
        JToolBarHelper::back();
        JToolBarHelper::spacer();

        parent::display($tpl);
    }
}
?>