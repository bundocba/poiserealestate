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



class IpropertyViewPropAgentEdit extends JViewLegacy
{
    protected $form;

    public function display($tpl = null)
    {
        // Initialise variables.
       
        require_once JPATH_COMPONENT."/models/propagentform.php";
        
        $user       = JFactory::getUser();        
        
        // Make sure to include admin language file for form fields
        JFactory::getLanguage()->load('com_iproperty', JPATH_ADMINISTRATOR);

        $propertyId = JRequest::getInt('propertyId', false);

        
       
        $this->return_page  = $this->get('ReturnPage');
        $this->settings     = ipropertyAdmin::config();
        $this->ipauth       = new ipropertyHelperAuth();
        
        $model = new IpropertyModelPropAgentForm();
        $this->form         = $model->getForm();
        $this->state        = $model->getState();
        $this->items         = $model->getItem($propertyId);
        
              
       
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
       

        $title = ($this->item->id) ? JText::_('JACTION_EDIT').': '.$this->item->street_address : JText::_('COM_IPROPERTY_FORM_EDIT_PROPERTY');
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

       
    }
}
