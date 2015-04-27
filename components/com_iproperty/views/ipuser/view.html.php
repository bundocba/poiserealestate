<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
jimport('joomla.application.component.view');

class IpropertyViewIPuser extends JViewLegacy
{
    var $params     = null;
    var $ipbaseurl  = null;
    var $settings   = null;
    var $ipauth     = null;
    
    public function display($tpl = null)
    {
        JPluginHelper::importPlugin( 'iproperty');
        
        $app            = JFactory::getApplication();
        $dispatcher     = JDispatcher::getInstance();

        $this->params       = $app->getParams();
        $this->ipbaseurl    = JURI::root(true);
        $this->settings     = ipropertyAdmin::config();
        $this->ipauth       = new ipropertyHelperAuth();
        
        $user               = JFactory::getUser();
        $userid             = $user->get('id');
        $document           = JFactory::getDocument();
        
        if (!$this->settings->show_saveproperty && !$this->settings->show_savesearch)
        {
            $this->_displayNoAccess();
            return;
        }else if (!$userid){
            $this->_displayLogin();
            return;
        }       
        
        // load jQuery cookie plugin
        $document->addScript($this->ipbaseurl.'/components/com_iproperty/assets/js/jquery.cookie.js');

        $properties = $this->get('ipproperties');
        $searches   = $this->get('searches');

        //create toolbar
        $dispatcher->trigger( 'onBeforeRenderToolbar', array( &$this->settings ) );

        $this->assignRef('properties', $properties);
        $this->assignRef('searches', $searches);
        $this->assignRef('user', $user);
        $this->assignRef('dispatcher', $dispatcher);
        
        //Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

        $this->_prepareDocument();       

        parent::display($tpl);
    }

    protected function _prepareDocument() 
    {
        $app            = JFactory::getApplication();
        $document       = JFactory::getDocument();
        $menus          = $app->getMenu();
        $pathway        = $app->getPathway();
        $this->params   = $app->getParams();
        $title          = null;

        $menu = $menus->getActive();
        if ($menu) {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        } else {
            $this->params->def('page_heading', JText::_('COM_IPROPERTY_MY_FAVORITES' ));
        }

        $title = JText::_('COM_IPROPERTY_MY_FAVORITES');
        $this->iptitle = $title;
        if (empty($title)) {
            $title = $app->getCfg('sitename');
        }
        elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
            $title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
        }
        elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
        }
        $this->document->setTitle($title);

        // Set meta data according to menu params
        if ($this->params->get('menu-meta_description')) $this->document->setDescription($this->params->get('menu-meta_description'));
        if ($this->params->get('menu-meta_keywords')) $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        if ($this->params->get('robots')) $this->document->setMetadata('robots', $this->params->get('robots'));

        // Breadcrumbs TODO (Add the whole tree)
        if(is_object($menu) && $menu->query['view'] != 'ipuser') {
            $pathway->addItem($this->iptitle);
        }        
    }

    protected function _displayLogin($tpl = 'login')
    {
        $document               = JFactory::getDocument();
        $usersConfig            = JComponentHelper::getParams('com_users');
        $allowreg               = $usersConfig->get('allowUserRegistration');
        $settings               = ipropertyAdmin::config();
        $return                 = base64_encode(JRoute::_(ipropertyHelperRoute::getIpuserRoute(), false));

        $document->setTitle( JText::_('COM_IPROPERTY_PLEASE_LOG_IN' ) );

        $this->assignRef('return', $return);
        $this->assignRef('allowreg', $allowreg);
        $this->assignRef('settings', $settings);

        parent::display($tpl);
    }

    protected function _displayNoAccess($tpl = 'noaccess')
    {
        $app                = JFactory::getApplication();
        $this->ipbaseurl    = JURI::root(true);
        $document           = JFactory::getDocument();
        $settings           = ipropertyAdmin::config();
        $pathway            = $app->getPathway();

        // Get the menu item object
        $menus = $app->getMenu();
        $menu  = $menus->getActive();

        $document->setTitle( JText::_('COM_IPROPERTY_NO_ACCESS' ));
        //set breadcrumbs
        if(is_object($menu) && $menu->query['view'] != 'ipuser') {
            $pathway->addItem(JText::_('COM_IPROPERTY_NO_ACCESS' ), '');
        }

        $this->assignRef('settings', $settings);

        parent::display($tpl);
    }
}

?>
