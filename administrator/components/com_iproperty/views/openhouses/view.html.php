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

class IpropertyViewOpenhouses extends JViewLegacy 
{
	protected $items;
	protected $pagination;
	protected $state;
    protected $settings;
    protected $ipauth;
    protected $cofield;
    
    public function display($tpl = null)
	{
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
        
        // Load the submenu.
		IpropertyHelper::addSubmenu(JFactory::getApplication()->input->getCmd('view', 'openhouses'));

		$this->addToolbar(); 
        $this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}
    
    protected function addToolbar()
	{
		require_once JPATH_COMPONENT .'/models/fields/company.php';
        $this->cofield = new JFormFieldCompany();
        
        JToolBarHelper::title(JText::_('COM_IPROPERTY_OPENHOUSES'), 'iproperty.png');

        JToolBarHelper::addNew('openhouse.add', 'JTOOLBAR_NEW');
        JToolBarHelper::editList('openhouse.edit', 'JTOOLBAR_EDIT');
        JToolBarHelper::divider();
        JToolBarHelper::publishList('openhouses.publish', 'JTOOLBAR_PUBLISH');
        JToolBarHelper::unpublishList('openhouses.unpublish', 'JTOOLBAR_UNPUBLISH');
        JToolBarHelper::divider();        
        JToolBarHelper::deleteList(JText::_('COM_IPROPERTY_CONFIRM_DELETE'), 'openhouses.delete', 'JTOOLBAR_DELETE');
        
        // Add search filters
		JHtmlSidebar::setAction('index.php?option=com_iproperty&view=openhouses');
        
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
			'o.name' => JText::_('COM_IPROPERTY_NAME'),
			'p.property_name' => JText::_('COM_IPROPERTY_REF'),
            'o.openhouse_start' => JText::_('COM_IPROPERTY_START'),
			'o.openhouse_end' => JText::_('COM_IPROPERTY_END'),
			'company' => JText::_('COM_IPROPERTY_COMPANY'),
            'o.state' => JText::_('JSTATUS'),
			'id' => JText::_('JGRID_HEADING_ID')
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
