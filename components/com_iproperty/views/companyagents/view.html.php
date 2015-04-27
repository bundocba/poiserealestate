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

class IpropertyViewCompanyAgents extends JViewLegacy
{
	protected $params;
    protected $items;
    protected $featured;
	protected $pagination;
	protected $state;
    protected $company;
    protected $settings;
    protected $ipauth;
    protected $ipbaseurl; 
    
    public function display($tpl = null)
	{
		$app                = JFactory::getApplication();
        $document           = JFactory::getDocument();
        $this->params       = $app->getParams(); 

        // Initialise variables.
		$this->items		= $this->get('Items');
        $this->featured     = $this->get('Featured');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
        
        $this->company      = IpropertyHTML::buildCompany($app->input->get('id', '', 'uint'));
        $this->settings     = ipropertyAdmin::config();
        $this->ipauth       = new ipropertyHelperAuth();
        $this->ipbaseurl    = JURI::root(true);
        
        // if no company is found, return no result
        if(!$this->company){
            $this->_displayNoResult('noresult');
            return;
        }

        // get IP plugins
        JPluginHelper::importPlugin( 'iproperty');
        $dispatcher = JDispatcher::getInstance();
		
		// create toolbar
        $dispatcher->trigger( 'onBeforeRenderToolbar', array( &$this->settings ) );

        $co_photo_width         = ($this->settings->company_photo_width) ? $this->settings->company_photo_width : '90';
        $agent_photo_width      = ($this->settings->agent_photo_width) ? $this->settings->agent_photo_width : '90';
        $co_folder              = $this->ipbaseurl.'/media/com_iproperty/companies/';
        $agents_folder          = $this->ipbaseurl.'/media/com_iproperty/agents/';
        $enable_featured        = $this->settings->agent_show_featured;
        $picfolder              = $this->ipbaseurl.$this->settings->imgpath;
        
        $this->assignRef('co_photo_width'       , $co_photo_width);
        $this->assignRef('agent_photo_width'    , $agent_photo_width);
        $this->assignRef('co_folder'            , $co_folder);
        $this->assignRef('agents_folder'        , $agents_folder);
        $this->assignRef('enable_featured'      , $enable_featured);
        $this->assignRef('folder'               , $picfolder);
        $this->assignRef('dispatcher'           , $dispatcher);
        
        //Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

        $this->_prepareDocument($this->company);

        parent::display($tpl);
	}

    protected function _prepareDocument($company)
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

        $title = (is_object($menu) && $menu->query['view'] == 'companyagents' && $menu->query['id'] == $company->id) ? $menu->title : $company->name.' '.JText::_('COM_IPROPERTY_AGENTS');
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

        // set meta keywords
        $orig_metakey = $this->document->getMetaData('keywords');
        if( $company->name ) $orig_metakey .= ','.$company->name;
        $this->document->setMetaData( "keywords", $orig_metakey);

        // set meta description
		$orig_metadesc = $this->document->getMetaData('description');
        if( $company->name ) $orig_metadesc .= '. '.$company->name;
		$this->document->setMetaData( "description", $orig_metadesc);

		// Breadcrumbs
        if(is_object($menu) && $menu->query['view'] != 'companyagents') {
			$pathway->addItem($this->iptitle);
		}
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
