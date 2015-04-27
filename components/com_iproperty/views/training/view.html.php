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
require_once JPATH_ADMINISTRATOR.'/components/com_iproperty/models/trainings.php';

class IpropertyViewTraining extends JViewLegacy
{
    protected $params;
    protected $items;
    protected $featured;
    protected $pagination;
    protected $state;
    protected $settings;
    protected $ipauth;
    protected $ipbaseurl;    
    
    public function display($tpl = null)
    {
		$app                = JFactory::getApplication();
        $document           = JFactory::getDocument();
        $this->params       = $app->getParams();

		
        
            $filemodel = new IpropertyModelTrainings();
            // Initialise variable
            $this->items	= $filemodel->getItems();

            $this->pagination	= $this->get('Pagination');
            $this->state		= $this->get('State');

            $this->settings     = ipropertyAdmin::config();
            $this->ipauth       = new ipropertyHelperAuth();        
            $this->ipbaseurl    = JURI::root(true);

            // get IP plugins
            JPluginHelper::importPlugin( 'iproperty');
            $dispatcher = JDispatcher::getInstance();       

                    // create toolbar
            $dispatcher->trigger( 'onBeforeRenderToolbar', array( &$this->settings ) );

            $co_photo_width     = ($this->settings->file_photo_width) ? $this->settings->file_photo_width : '90';
            $enable_featured    = $this->settings->agent_show_featured;        

            $this->assignRef('co_photo_width'   , $co_photo_width);
            $this->assignRef('enable_featured'  , $enable_featured);
            $this->assignRef('dispatcher'       , $dispatcher);

            //Escape strings for HTML output
                    $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

            $this->_prepareDocument();              
       
        parent::display($tpl);
    }

    protected function _prepareDocument()
    {
        $app            = JFactory::getApplication();
		$menus          = $app->getMenu();
		$pathway        = $app->getPathway();
		$this->params   = $app->getParams();
		$title          = null;

        $menu = $menus->getActive();
		if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', JText::_('COM_IPROPERTY_INTELLECTUAL_PROPERTY' ));
		}

        $title = (is_object($menu) && $menu->query['view'] == 'trainings') ? $menu->title : JText::_('COM_IPROPERTY_TRAININGS_TITLE');
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

		// Breadcrumbs
        if(is_object($menu) && $menu->query['view'] != 'trainings') {
			$pathway->addItem($this->iptitle);
		}
	}
}

?>
