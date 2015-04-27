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

class IpropertyViewEditcss extends JViewLegacy 
{
    protected $settings;
    protected $user;
    
    public function display($tpl = null)
	{
		// Initialiase variables.
        $this->settings = ipropertyAdmin::config(); 
        $this->user     = JFactory::getUser();

        // Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raise(E_ERROR, 500, implode("\n", $errors));
			return false;
		}

        // if no agent id and user is not admin - no access
        if (!$this->user->authorise('core.admin')){
            $this->setLayout('noaccess');
            $this->_displayNoAccess($tpl);
            return;
        }else if(JRequest::getVar('edit_css_file')){
            $this->setLayout('edit');
            $this->edit();
            return;
        }

		JToolBarHelper::title(JText::_('COM_IPROPERTY_SELECT_CSS'), 'iproperty.png');

		JToolBarHelper::custom('editcss.edit', 'edit.png', 'edit_f2.png', 'COM_IPROPERTY_EDIT_CSS', false, false );
        JToolBarHelper::divider();
        JToolBarHelper::cancel('editcss.cancel');
        
        $css_files  = JFolder::files(JPATH_COMPONENT_SITE.'/assets/css', '.css');
        $css_list   = array();
        foreach ($css_files as $css)
        {
            $cssfiles[] = JHTML::_('select.option', $css, $css);
        }
        $this->cssList = JHTML::_('select.radiolist', $cssfiles, 'edit_css_file', 'size="5" class="inputbox"', 'text', 'text', 'iproperty.css');
        
        // Load the submenu.
		IpropertyHelper::addSubmenu(JFactory::getApplication()->input->getCmd('view', 'editcss'));
        $this->sidebar = JHtmlSidebar::render();
        
		parent::display($tpl);
	}
	
	public function edit($tpl = null)
	{
        JFactory::getApplication()->input->set('hidemainmenu', true);
        
        $app                = JFactory::getApplication();
        $this->fname	    = JRequest::getVar('edit_css_file');        
        $this->filename     = JPATH_COMPONENT_SITE.'/assets/css'.'/'.$this->fname;
        
        // raise a notice explaining that template css files will override default css styles if they exist
        if($this->fname == 'iproperty.css' || $this->fname == 'advsearch.css' || $this->fname == 'catmap.css'){
            JError::raise(E_NOTICE, JText::_('SOME_ERROR_CODE'), sprintf(JText::_('COM_IPROPERTY_CSS_OVERRIDE_NOTICE'), $this->fname));
        }       

        if (JFile::getExt($this->filename) !== 'css') {
            $msg = JText::_('COM_IPROPERTY_CSS_WRONG_TYPE');
            $app->redirect(JRoute::_('index.php?option=com_iproperty', false), $msg, 'error');
        }

        $content = JFile::read($this->filename);

        if (!$content){
            $msg = JText::sprintf('Operation Failed Could not open', $this->filename);
            $app->redirect(JRoute::_('index.php?option=com_iproperty', false), $msg, 'error');
        }
            
        $this->content = htmlspecialchars($content, ENT_COMPAT, 'UTF-8');

		JToolBarHelper::title(JText::_('COM_IPROPERTY_EDIT_CSS' ).': '.$this->fname, 'iproperty.png');

		JToolBarHelper::apply('editcss.apply');
		JToolBarHelper::save('editcss.save');
		JToolBarHelper::divider();
		JToolBarHelper::cancel('editcss.cancel');
		
		parent::display($tpl);
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