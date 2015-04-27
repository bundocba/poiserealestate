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
jimport( 'joomla.filesystem.folder');
jimport( 'joomla.filesystem.file');

class IpropertyViewRestore extends JViewLegacy 
{
	protected $user;
    protected $backupFiles;
    
    public function display($tpl = null)
	{
        $this->user   = JFactory::getUser();

        // Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raise(E_ERROR, 500, implode("\n", $errors));
			return false;
		}

        // Check if the user should be in this editing area
        if (!$this->user->authorise('core.admin')){
            $this->setLayout('noaccess');
            $this->_displayNoAccess($tpl);
            return;
        }
        
        $backupFiles    = JFolder::files(JPATH_SITE.'/media/com_iproperty', '.sql.gz');
		$boptions       = array();
		foreach ($backupFiles as $bfile)
        {
			$boptions[] = JHTML::_('select.option', $bfile, $bfile);
		}

		$this->backupFiles = JHTML::_('select.genericlist', $boptions, 'bak_file', 'size="10" class="inputbox" style="width: 300px;"');
        
        // Load the submenu.
		IpropertyHelper::addSubmenu(JFactory::getApplication()->input->getCmd('view', 'restore'));

        $this->addToolbar();
        $this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
    }
    
    protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_IPROPERTY_RESTORE_FROM_BACKUP_COPY'), 'iproperty.png');
        if($this->user->authorise('core.admin')){
            IpToolbar::backupBtn('backup.restoreDB', JText::_('COM_IPROPERTY_RESTORE'));
        }
        JToolBarHelper::divider();
		IpToolbar::backBtn();
	}

    public function _displayNoAccess($tpl = null)
    {
        JToolBarHelper::title(JText::_('COM_IPROPERTY_NO_ACCESS'), 'iproperty.png');
        IpToolbar::backBtn();
        parent::display($tpl);
    }
}
?>