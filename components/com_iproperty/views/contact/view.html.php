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

class IpropertyViewContact extends JViewLegacy
{
	protected $item;
    protected $form;
    protected $state;
    protected $return_page;
    
    public function display($tpl = null)
	{		
        $this->item         = $this->get('Item');
        $this->form         = $this->get('Form');
        $this->state        = $this->get('State');

        if(!$this->item){
            $this->_displayNoResult('noresult');
            return;
        }        

        switch($this->state->get('layout'))
        {
            case 'company':
                $this->_displayCompany($this->item);
                break;
            case 'agent':
                $this->_displayAgent($this->item);
                break;
        }
	}

    protected function _prepareDocument($contact)
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

        $title = JText::_('COM_IPROPERTY_CONTACT').' '.$contact->name;
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
        if(is_object($menu) && $menu->query['view'] != 'contact') {
			$pathway->addItem($this->iptitle);
		}
        
        //Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));
	}

    public function _displayCompany($company)
    {        
        JHtml::_('bootstrap.tooltip');
        JHTML::_('behavior.formvalidation');
        
        JPluginHelper::importPlugin( 'iproperty');
        $dispatcher = JDispatcher::getInstance();

        $this->ipbaseurl    = JURI::root(true);
        $this->ctype        = 'company';

		$document           = JFactory::getDocument();
		$this->settings     = ipropertyAdmin::config();
        $this->ipauth       = new ipropertyHelperAuth();

        if(!$this->settings->co_show_contact){
            $this->_displayNoAccess('noaccess');
            return;
        }

		//create toolbar
        $dispatcher->trigger( 'onBeforeRenderToolbar', array( &$this->settings ) );        

        $co_photo_width = ($this->settings->company_photo_width) ? $this->settings->company_photo_width : '90';
        $company_folder = $this->ipbaseurl.'/media/com_iproperty/companies/';
        
        $this->assignRef('company', $company);
        $this->assignRef('co_photo_width', $co_photo_width);
        $this->assignRef('company_folder', $company_folder);
        $this->assignRef('dispatcher', $dispatcher);  
        
        $this->_prepareDocument($company);
		parent::display();
    }

    public function _displayAgent($agent)
    {
        JHtml::_('bootstrap.tooltip');
        JHTML::_('behavior.formvalidation');
        
        JPluginHelper::importPlugin( 'iproperty');
        $dispatcher = JDispatcher::getInstance();

        $this->ipbaseurl    = JURI::root(true);
        $this->ctype        = 'agent';

		$document           = JFactory::getDocument();
		$this->settings     = ipropertyAdmin::config();
        $this->ipauth       = new ipropertyHelperAuth();

        if(!$this->settings->agent_show_contact){
            $this->_displayNoAccess('noaccess');
            return;
        }

		//create toolbar
        $dispatcher->trigger( 'onBeforeRenderToolbar', array( &$this->settings ) );

        $agent_photo_width  = ($this->settings->agent_photo_width) ? $this->settings->agent_photo_width : '90';
        $agents_folder      = $this->ipbaseurl.'/media/com_iproperty/agents/';

        $this->assignRef('agent', $agent);
        $this->assignRef('agent_photo_width', $agent_photo_width);
        $this->assignRef('agents_folder', $agents_folder);
        $this->assignRef('dispatcher', $dispatcher);
        
        $this->_prepareDocument($agent);
		parent::display();
    }

    public function _displayNoAccess($tpl = null)
    {
        $document       = JFactory::getDocument();
        $document->setTitle( JText::_('COM_IPROPERTY_NO_ACCESS' ) );

        parent::display($tpl);
    }
    
    public function _displayNoResult($tpl = null)
    {
        $document   = JFactory::getDocument();
        $settings   = ipropertyAdmin::config();
        
        $document->setTitle( JText::_('COM_IPROPERTY_NO_RESULTS') );
        
        if ($settings->hard404) JError::raiseError( 404, JText::_('COM_IPROPERTY_NO_RESULTS') ); 

        parent::display($tpl);
    }
}

?>
