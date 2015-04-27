<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
JHtml::_('behavior.framework', true);

class IpropertyViewPropForm extends JViewLegacy
{
    protected $form;
    protected $item;
    protected $return_page;
    protected $state;
    protected $settings;
    protected $ipauth;
    protected $dispatcher;
    protected $iptitle;

    public function display($tpl = null)
    {
        // Initialise variables.
        $app        = JFactory::getApplication();
        $document   = JFactory::getDocument();
        $user       = JFactory::getUser();        
        
        // Make sure to include admin language file for form fields
        JFactory::getLanguage()->load('com_iproperty', JPATH_ADMINISTRATOR);

        // Get model data.
        $this->state        = $this->get('State');
        $this->item         = $this->get('Item');
        $this->form         = $this->get('Form');
        $this->return_page  = $this->get('ReturnPage');
        $this->settings     = ipropertyAdmin::config();
        $this->ipauth       = new ipropertyHelperAuth();

        // Import IP plugins for additional form tabs (IPresserve, IReport)
        JPluginHelper::importPlugin( 'iproperty');
        $this->dispatcher = JDispatcher::getInstance();

        if (empty($this->item->id)) {
            $authorised = $this->ipauth->canAddProp();
        }
        else {
            $authorised = $this->ipauth->canEditProp($this->item->id);
        }
		jimport( 'joomla.user.helper' );
                /*$groups = JUserHelper::getUserGroups($user->id);	
                if(!in_array(10,$groups)){
                    JError::raise(E_ERROR, 403, JText::_('JERROR_ALERTNOAUTHOR')); 
                    return false;
                }*/
        /* if (!$authorised) {
            JError::raise(E_ERROR, 403, JText::_('JERROR_ALERTNOAUTHOR'));
            return false;
        } */

        if (!empty($this->item)) {
            $this->form->bind($this->item);
        }

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raise(E_WARNING, 500, implode("\n", $errors));
            return false;
        }

        // Create a shortcut to the parameters.
        $params	= $this->state->get('params');

        //Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

        $this->params   = $params;
        $this->user     = $user;
	$this->setLayout('edit');
        $this->_prepareDocument();
        parent::display($tpl);
    }

    protected function _prepareDocument()
    {
        $app        = JFactory::getApplication();
        $menus      = $app->getMenu();
        $pathway    = $app->getPathway();
        $title      = null;

        $menu = $menus->getActive();
        if ($menu) {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        } else {
            $this->params->def('page_heading', JText::_('COM_IPROPERTY_FORM_EDIT_PROPERTY' ));
        }

        $title = ($this->item->id) ? JText::_('JACTION_EDIT').': '.$this->item->street_address : JText::_('COM_IPROPERTY_FORM_ADD_PROPERTY');
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

        $pathway = $app->getPathWay();
        $pathway->addItem($this->iptitle, '');

        if ($this->params->get('menu-meta_description'))
        {
            $this->document->setDescription($this->params->get('menu-meta_description'));
        }

        if ($this->params->get('menu-meta_keywords'))
        {
            $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        }

        if ($this->params->get('robots'))
        {
            $this->document->setMetadata('robots', $this->params->get('robots'));
        }
    }
}
