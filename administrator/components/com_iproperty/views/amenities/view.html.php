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

class IpropertyViewAmenities extends JViewLegacy
{
    protected $items;
	protected $pagination;
	protected $state;
    protected $settings;
    protected $ipauth;
    protected $catfield;

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

        /// if user is not admin - no access
        if (!$this->ipauth->getAdmin()){
            $this->setLayout('noaccess');
            $this->_displayNoAccess($tpl);
            return;
        }
        
        // Load the submenu.
		IpropertyHelper::addSubmenu(JFactory::getApplication()->input->getCmd('view', 'amenities'));

		$this->addToolbar();
        $this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

    protected function addToolbar()
	{
		require_once JPATH_COMPONENT .'/models/fields/amenitycat.php';
        $this->catfield = new JFormFieldAmenityCat();
        
        JToolBarHelper::title(JText::_('COM_IPROPERTY_AMENITIES'), 'iproperty.png');

        // Only show these options to admin
        if ($this->ipauth->getAdmin()){
            JToolBarHelper::addNew('amenity.add','JTOOLBAR_NEW');
            JToolBarHelper::editList('amenity.edit','JTOOLBAR_EDIT');
            JToolBarHelper::divider();
            IpToolbar::saveCatList('amenities.saveCats', 'JTOOLBAR_APPLY');
            JToolBarHelper::divider();            
            JToolBarHelper::deleteList(JText::_('COM_IPROPERTY_CONFIRM_DELETE'), 'amenities.delete','JTOOLBAR_DELETE');
		}
        
        // Add search filters
		JHtmlSidebar::setAction('index.php?option=com_iproperty&view=amenities');
        
        JHtmlSidebar::addFilter(
			'- '.JText::_('JSELECT').' '.JText::_('COM_IPROPERTY_CATEGORY').' -',
			'filter_cat_id',
			JHtml::_('select.options', $this->catfield->getOptions(), 'value', 'text', $this->state->get('filter.cat_id'))
		);
	}
    
    protected function getSortFields()
	{
		return array(
			'title' => JText::_('COM_IPROPERTY_TITLE'),
			'cat' => JText::_('COM_IPROPERTY_CATEGORY')
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