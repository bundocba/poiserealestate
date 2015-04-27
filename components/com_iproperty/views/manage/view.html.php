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

class IpropertyViewManage extends JViewLegacy
{    
    protected $items;
	protected $pagination;
	protected $state;
    protected $settings;
    protected $ipauth;
    protected $params;
    protected $return;
    protected $user;
    protected $userId;
    protected $dispatcher;
    protected $ipbaseurl;
    
    public function display($tpl = null)
    {       
        JPluginHelper::importPlugin( 'iproperty');
        $this->dispatcher = JDispatcher::getInstance();
        
        //require the search fields
        require_once JPATH_COMPONENT_ADMINISTRATOR .'/models/fields/company.php';
        require_once JPATH_COMPONENT_ADMINISTRATOR .'/models/fields/stypes.php';
        require_once JPATH_COMPONENT_ADMINISTRATOR .'/models/fields/city.php';
        require_once JPATH_COMPONENT_ADMINISTRATOR .'/models/fields/ipcategory.php';
        require_once JPATH_COMPONENT_ADMINISTRATOR .'/models/fields/beds.php';
        require_once JPATH_COMPONENT_ADMINISTRATOR .'/models/fields/baths.php';
        
        $uri                = JURI::getInstance();       
        $this->return       = base64_encode($uri);        
        $this->settings     = ipropertyAdmin::config();
        $this->ipauth       = new ipropertyHelperAuth();
        $this->user         = JFactory::getUser();
        $this->userId		= $this->user->get('id');
        $this->agent        = null;
        $this->ipbaseurl    = JURI::root(true);
        
        // if not admin, check for agent profile. If not found, kick them out
        $agentid            = $this->ipauth->getUagentId();
        if( !$this->user->get('id') ){
            $this->_displayLogin();
            return;
        }else if(!$this->ipauth->getAdmin()){
            if (!$this->ipauth->getAuthLevel() || !$this->agent = IpropertyHTML::buildAgent($agentid)){
                $this->_displayNoAccess();
                return;
            }
        }
        
        // Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raise(E_ERROR, 500, implode("\n", $errors));
			return false;
		}

        // if user is not admin AND user does not have and agent id or IP auth is disabled - no access
        if (!$this->ipauth->getAdmin() && (!$this->ipauth->getUagentId() || !$this->ipauth->getAuthLevel())){
            $this->setLayout('noaccess');
            $this->_displayNoAccess($tpl);
            return;
        }       

        // Initialise variables.
        $model = $this->getModel(JRequest::getWord('layout', 'proplist'));            
        $this->setLayout(JRequest::getWord('layout', 'proplist'));
        
        $this->items		= $model->getItems();
        $this->pagination	= $model->getPagination();
        $this->state		= $model->getState();
            
        $this->_prepareDocument();	
        
        parent::display($this->getLayout());
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
        $this->params->def('page_heading', JText::_('COM_IPROPERTY_AGENT_MANAGE' ));

        $title = JText::_('COM_IPROPERTY_AGENT_MANAGE');
        $this->iptitle = $title;
        $this->document->setTitle($title);

        // Set meta data according to menu params
        if ($this->params->get('menu-meta_description')) $this->document->setDescription($this->params->get('menu-meta_description'));
        if ($this->params->get('menu-meta_keywords')) $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        if ($this->params->get('robots')) $this->document->setMetadata('robots', $this->params->get('robots'));
        
        //Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));
    }  
    
    protected function getSortFields($type = 'property')
	{
		switch($type)
        {
            case 'property':
                default:
                return array(
                    'property_name' => JText::_('COM_IPROPERTY_REF'),
                    'street' => JText::_('COM_IPROPERTY_STREET'),
                    'title' => JText::_('COM_IPROPERTY_TITLE'),
                    'city' => JText::_('COM_IPROPERTY_CITY'),
                    'state' => JText::_('JSTATUS'),
                    'p.id' => JText::_('JGRID_HEADING_ID')
                );
                break;
            case 'agent':
                return array(
                    'ordering' => JText::_('COM_IPROPERTY_ORDER'),
                    'lname' => JText::_('COM_IPROPERTY_LNAME'),
                    'user_name' => JText::_('COM_IPROPERTY_USER'),
                    'company' => JText::_('COM_IPROPERTY_COMPANY'),
                    'email' => JText::_('COM_IPROPERTY_EMAIL'),
                    'state' => JText::_('JSTATUS'),
                    'id' => JText::_('JGRID_HEADING_ID')
                );
                break;
            case 'company':
                return array(
                    'ordering' => JText::_('COM_IPROPERTY_ORDER'),
                    'c.name' => JText::_('COM_IPROPERTY_TITLE'),
                    'c.email' => JText::_('COM_IPROPERTY_EMAIL'),
                    'state' => JText::_('JSTATUS'),
                    'id' => JText::_('JGRID_HEADING_ID')
                );
                break;
            case 'openhouse':
                return array();
                break;
        }
	}

    protected function _displayLogin($tpl = 'login')
    {
        $document               = JFactory::getDocument();
        $usersConfig            = JComponentHelper::getParams('com_users');
        $allowreg               = $usersConfig->get('allowUserRegistration');
        $settings               = ipropertyAdmin::config();
        $return                 = base64_encode(JRoute::_(ipropertyHelperRoute::getManageRoute(), false));

        $document->setTitle( JText::_('COM_IPROPERTY_PLEASE_LOG_IN' ) );

        $this->assignRef('return', $return);
        $this->assignRef('allowreg', $allowreg);
        $this->assignRef('settings', $settings);

        parent::display($tpl);
    }

    protected function _displayNoAccess($tpl = 'noaccess')
    {
        $app  = JFactory::getApplication();
        $option     = JRequest::getCmd('option');

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
