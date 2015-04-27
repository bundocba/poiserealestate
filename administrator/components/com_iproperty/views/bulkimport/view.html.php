<?php
/**
 * @version 2.0.1 2012-08-13
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.application.component.view');

class IpropertyViewBulkimport extends JViewLegacy 
{
    protected $user;
    protected $dataFiles;
    
    public function display($tpl = null)
	{
        $this->user   = JFactory::getUser();
        
        // Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

        // Check if the user should be in this editing area
        if (!$this->user->authorise('core.admin')){
            $this->setLayout('noaccess');
            $this->_displayNoAccess($tpl);
            return;
        }

		$dataFiles  = JFolder::files(JPATH_SITE.'/media/com_iproperty', '.', false, false, array('index.html', 'nopic.png'));
		$coptions   = array();
		foreach ($dataFiles as $cfiles)
        {            
			$coptions[] = JHTML::_('select.option', $cfiles, $cfiles);
		}
        
		$this->dataFiles = JHTML::_('select.genericlist', $coptions, 'datafile', 'size="10" class="inputbox" style="width: 300px;"');
        
        // Load the submenu.
		IpropertyHelper::addSubmenu(JFactory::getApplication()->input->getCmd('view', 'restore'));
		
		$this->addToolbar();
        $this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}
    
    protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_IPROPERTY_BULKIMPORT_FILE'), 'iproperty.png');

        // Only show these options to admin
        if ($this->user->authorise('core.admin')){
            IpToolbar::importBtn('bulkimport.import', JText::_('COM_IPROPERTY_IMPORT'));
        }
        JToolBarHelper::divider();
        JToolBarHelper::cancel('bulkimport.cancel','JTOOLBAR_CANCEL');
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