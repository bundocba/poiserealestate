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



class IpropertyViewProperty extends JViewLegacy

{

    protected $item;

	protected $params;

	protected $print;

	protected $state;

	protected $user;

    protected $ipbaseurl;

    protected $ipauth;

    protected $mapOk;

    protected $app;

    protected $requestform;

    protected $stfform;

    

    public function display($tpl = null)

    {

        JPluginHelper::importPlugin('iproperty');

        $dispatcher = JDispatcher::getInstance();

        

        $app		= JFactory::getApplication();

		$user		= JFactory::getUser();

		

        $layout     = $this->getLayout();

        $settings   = ipropertyAdmin::config();

        

        // Check if login is required to view details - if so check user and display form if not logged in

        if($settings->require_login){

            if(!$user->get('id')){

                $this->_displayLogin('login');

                return;

            }

        }

        

		$this->item         = $this->get('Item');

        $this->images       = $this->get('Images');

        $this->docs         = $this->get('Docs');

        $this->amenities    = $this->get('Amenities');        

		$this->print        = $app->input->getBool('print');

		$this->state        = $this->get('State');

		$this->user         = $user;    

        $this->ipbaseurl    = JURI::root(true);

        $this->ipauth       = new ipropertyHelperAuth(); 

        $this->mapOk        = ($settings->map_provider && $this->item->lat_pos && $this->item->long_pos && $this->item->show_map);

        $this->app          = $app;

        

        // forms

        $this->requestform  = $this->get('Form');

        $this->stfform      = $this->get('StfForm');



        // if no property data is returned, change to no result template

        if(!$this->item){

            $this->_displayNoResult('noresult');

            return;

        }

		

		// if we have mapOk, then set IPGMAP

        if($this->mapOk) define('IPGMAP', 1);

        

        $this->assignRef('p'                , $this->item);

        $this->assignRef('settings'         , $settings);

        $this->assignRef('dispatcher'       , $dispatcher);

        

        // Increment the category hit counter

		$model = $this->getModel();

		$model->hit();       



        $this->_prepareDocument($this->item);



        parent::display($tpl);

    }



    protected function _prepareDocument($property)

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



        $title = ($property) ? $property->street_address : JText::_('COM_IPROPERTY_INTELLECTUAL_PROPERTY');

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

        if ($property->metadesc != '') {

            $this->document->setDescription($property->metadesc);

        } else if (!$property->metadesc && $this->params->get('menu-meta_description')) {

            $this->document->setDescription($this->params->get('menu-meta_description'));

        }



        if ($property->metakey != '') {

            $this->document->setMetadata('keywords', $property->metakey);

        } else if (!$property->metakey && $this->params->get('menu-meta_keywords')) {

            $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));

        }

        if ($this->params->get('robots')) $this->document->setMetadata('robots', $this->params->get('robots'));



        // Breadcrumbs

        if(is_object($menu) && $menu->query['view'] != 'property') {

            $pathway->addItem($this->iptitle);

        }

        

        //Escape strings for HTML output

		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

    }



    public function _displayNoResult($tpl = null)

    {

        $document       = JFactory::getDocument();

        $settings       = ipropertyAdmin::config();



        $document->setTitle( JText::_('COM_IPROPERTY_PROPERTY_NOT_FOUND') );

        

        if ($settings->hard404) JError::raiseError( 404, JText::_('COM_IPROPERTY_PROPERTY_NOT_FOUND') ); 



        parent::display($tpl);

    }



    public function _displayLogin($tpl = null)

    {

        $document               = JFactory::getDocument();

        $settings               = ipropertyAdmin::config();

        $usersConfig            = JComponentHelper::getParams('com_users');

        $allowreg               = $usersConfig->get('allowUserRegistration');

        $return                 = base64_encode(JRoute::_(ipropertyHelperRoute::getPropertyRoute(JRequest::getInt('id')), false));



        $document->setTitle( JText::_('COM_IPROPERTY_PLEASE_LOG_IN' ) );



        $this->assignRef('return', $return);

        $this->assignRef('allowreg', $allowreg);

        $this->assignRef('settings', $settings);



        parent::display($tpl);

    }

}



?>

