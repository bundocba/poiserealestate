<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
jimport('joomla.log.log');
jimport( 'joomla.base.object');

/***********************
 * 
 * IProperty access control level. Admin and Super admin can edit everything no matter this setting.
 * 0 - Disabled: The IP ACL is disabled. Only administrators can manage IP objects - this applies to the admin panel as well as front-end editing.
 * 1 - Company: Allow all users to edit their own company listings, openhouses, etc as well as their own profile. (Super agents can also edit their own company profile as well as their own company agent profiles).
 * 2 - Agent: Allow agents to edit only their own listings, profile, openhouses, etc (Super agents may still edit all company listings, openhouses, agents, and company profile).
 * 3 - None: No user restrictions - any user who is given access can edit all available IP objects. This includes all front end listings, agents, and companies. If given access to the admin panel they can also edit categories and amenities. Only administrators can access and manage settings, import csv, backup, and restore views.
 * 
 */

class IpropertyHelperAuth
{ 
    private $db    			= null;
	private $settings		= null; // IP setting object
	private $app			= null;
	private $authlevel		= 4; // IP auth setting level-- set to 4 (NO EDIT) as default
	
	// these are the vars for the user
    private $user  			= false; // user object
    private $uagent			= false; // user's agent object
	private $uagent_id 		= false; // user's agent ID from agent table
	private $uagent_cid		= false; // user's agent company
	private $super 			= false; // is user super agent
	private $admin 			= false; // is user admin
	private $guest			= true; // is user guest
	
	// these are the vars for the object being edited
	private $company_id 	= false; // company ID (if this is a company edit auth)
	private $condition_id 	= false; // condition ID (if this is a condition edit auth)
	private $tenure_id 		= false; // tenure ID (if this is a tenure edit auth)
        private $propertytype_id 		= false; // propertytype ID (if this is a tenure edit auth)
	private $file_id 		= false; // file ID (if this is a file edit auth)
        private $fileagent_id 		= false; // file ID (if this is a file edit auth)
	private $training_id 		= false; // file ID (if this is a file edit auth)
	private $property_id 	= false; // property ID (if this is a property edit auth)
	private $property_cid	= false; // property's company ID (if this is a property edit auth)
	private $property_aid	= array(); // property's listing agent IDs (if this is a property edit auth)
	private $agent_id		= false; // agent's ID (if this is an agent edit auth)
	private $agent_cid		= false; // agent's company ID (if this is an agent edit auth)
	private $agent_uid		= false; // agent's Joomla user ID (if this is an agent edit auth)
    
    // storage for the params objects 
    private $cparams        = false; // company params
    private $aparams        = false; // agent params
	
	public function __construct($params = array('msg'=>false))
	{
		$this->settings	= ipropertyAdmin::config();
		$this->setAuthLevel($this->settings->edit_rights);	
		$this->db		= JFactory::getDbo();
        $this->user		= JFactory::getUser();
		$this->app 		= JFactory::getApplication();
		$this->guest	= (boolean) $this->user->guest;
		$this->getUserAgentInfo(); // create user agent info
		$this->isAdmin(); // check if user is an admin
        $this->displayMessages = ($params['msg']) ? true : false;
	}  

	/************************
	* PROPERTY AUTH FUNCTIONS
	************************/	
    
    public function canAddProp()
    {
		if ( $this->getAdmin() ) return true;
		if ( $this->checkAgentPropLimit() && $this->settings->edit_rights ) return true;
        return false;
    }
    public function canAddPropAgent()
    {
		if ( $this->getAdmin() ) return true;
		//if ( $this->checkAgentPropAgentLimit() && $this->settings->edit_rights ) return true;
        return false;
    }
	
    public function canEditProp($id)
    {
        if ( $this->getAdmin() ) return true;
		$this->getPropertyInfo($id);
		if ( $this->checkACL('property') ) return true;
		return false;
    }   
    
    public function canEditPropAgent($id)
    {
        if ( $this->getAdmin() ) return true;
		$this->getPropertyInfo($id);
		if ( $this->checkACL('propertyagent') ) return true;
		return false;
    }   
    
    public function canPublishProp($id, $state = 0)
    {
        if ( $this->getAdmin() ) return true;
		$this->getPropertyInfo($id);        
        if($state){ // publishing
            if ( $this->getSuper() ){
                if ( $this->checkACL('property') && $this->checkCompanyPropLimit() ) return true;
            } else {
                if ( $this->checkACL('property') && $this->checkAgentPropLimit() ) return true;
            }
        }else{ // unpublish
            if ( $this->checkACL('property') ) return true;
        }
		return false;
    }
    
    public function canFeatureProp($id, $state = 0)
    {
        if ( $this->getAdmin() ) return true;
		$this->getPropertyInfo($id);
		if ( $this->checkACL('property') ){            
            if ($state){
                if ( $this->checkAgentFeatPropLimit() ) return true;
            }else{
                return true;
            }
		}
		return false;
	}
	
    public function canDeleteProp($id)
    {
        if ( $this->getAdmin() ) return true;
		$this->getPropertyInfo($id);
		if ( $this->checkACL('property') ) return true;
		return false;
	}   
    
    public function canApproveProp($id, $state = 0)
    {
        if ( $this->getAdmin() ) return true;
        // approval levels - 0: admin only. 1: Super agents can approve
        if ( $this->settings->approval_level && $this->getSuper() ){
            $this->getPropertyInfo($id);
            if ( $this->checkACL('property') ){
                if ($state){
                    if ( $this->checkACL('property') && $this->checkCompanyPropLimit() ) return true;
                }else{
                    return true;
                }
            }
        }
		return false;
    }
	
	/*********************
	* AGENT AUTH FUNCTIONS
	*********************/
	
    public function canAddAgent()
    {
		// only super agents or admins can add agents
		if ( $this->getAdmin() ) return true;
		if ( !$this->getSuper() ){
            if ($this->displayMessages){
                $this->app->enqueueMessage(JText::_('COM_IPROPERTY_ONLY_ADMINS_ACTION'), 'error');
            }
			return false;
		}
		if ( $this->checkCompanyAgentLimit() && $this->settings->edit_rights ) return true;
		return false;
    }
	
    public function canEditAgent($id)
    {
        if ( $this->getAdmin() ) return true;
		$this->getAgentInfo($id);
		if ( $this->checkACL('agent') ) return true;
		return false;
    }	
	
    public function canPublishAgent($id, $state = 0)
    {
        if ( $this->getAdmin() ) return true;
		$this->getAgentInfo($id);
        if($state){
            if ( $this->checkACL('agent') && $this->checkCompanyAgentLimit() ) return true;
        }else{ // check access and make sure the agent is not unpublishing his own agent
            if ( $this->checkACL('agent') && $id != $this->getUagentId()){
                return true;
            }else{
                if ($this->displayMessages){
                    $this->app->enqueueMessage(JText::_('COM_IPROPERTY_CANNOT_UNPUBLISH_OWN_AGENT'), 'error');
                }
            }
        }
		return false;
    } 
    
    public function canFeatureAgent($id, $state = 0)
    {
        if ( $this->getAdmin() ) return true;
		$this->getAgentInfo($id);
		// only super agents or admins can feature agents
		if ( $this->getSuper() && $this->checkACL('agent') ){
            if ($state){
                if ( $this->checkCompanyFeatAgentLimit() ) return true;
            }else{
                return true;
            }
        }
        return false;
	}
    
    public function canSuperAgent()
    {
        if ( $this->getAdmin() ) return true;
        if ($this->displayMessages){
            $this->app->enqueueMessage(JText::_('COM_IPROPERTY_ONLY_ADMINS_ACTION'), 'error');
        }
        return false;
    }
	
	public function canDeleteAgent($id)
    {
        if ( $this->getAdmin() ) return true;
		$this->getAgentInfo($id);
		if ( $this->getSuper() && $this->checkACL('agent') ) return true;
		return false;
	}    
	
	/************************
	* COMPANY AUTH FUNCTIONS
	************************/
    
    public function canAddCompany()
    {
		// only super agents or admins can add agents
		if ( $this->getAdmin() ) return true;
        if ($this->displayMessages){
            $this->app->enqueueMessage(JText::_('COM_IPROPERTY_ONLY_ADMINS_ACTION'), 'error');
        }
        return false;
    }
    
     public function canAddCondition()
    {
		// only super agents or admins can add agents
		if ( $this->getAdmin() ) return true;
        if ($this->displayMessages){
            $this->app->enqueueMessage(JText::_('COM_IPROPERTY_ONLY_ADMINS_ACTION'), 'error');
        }
        return false;
    }
	
    public function canAddTenure()
    {
		// only super agents or admins can add agents
		if ( $this->getAdmin() ) return true;
        if ($this->displayMessages){
            $this->app->enqueueMessage(JText::_('COM_IPROPERTY_ONLY_ADMINS_ACTION'), 'error');
        }
        return false;
    }
    
    public function canAddPropertyType()
    {
		// only super agents or admins can add agents
		if ( $this->getAdmin() ) return true;
        if ($this->displayMessages){
            $this->app->enqueueMessage(JText::_('COM_IPROPERTY_ONLY_ADMINS_ACTION'), 'error');
        }
        return false;
    }
	
     public function canAddFile()
    {
		// only super agents or admins can add agents
		if ( $this->getAdmin() ) return true;
        if ($this->displayMessages){
            $this->app->enqueueMessage(JText::_('COM_IPROPERTY_ONLY_ADMINS_ACTION'), 'error');
        }
        return false;
    }
    
     public function canAddFileAgent()
    {
		// only super agents or admins can add agents
		if ( $this->getAdmin() ) return true;
        if ($this->displayMessages){
            $this->app->enqueueMessage(JText::_('COM_IPROPERTY_ONLY_ADMINS_ACTION'), 'error');
        }
        return false;
    }
	
    public function canAddTraining()
    {
		// only super agents or admins can add agents
		if ( $this->getAdmin() ) return true;
        if ($this->displayMessages){
            $this->app->enqueueMessage(JText::_('COM_IPROPERTY_ONLY_ADMINS_ACTION'), 'error');
        }
        return false;
    }
	
    public function canEditCompany($id)
    {
        if ( $this->getAdmin() ) return true;
		$this->setCompanyId($id);
		if ( $this->checkACL('company') ) return true;
		return false;
    }
    
     public function canEditCondition($id)
    {
        if ( $this->getAdmin() ) return true;
		$this->setConditionId($id);
		if ( $this->checkACL('condition') ) return true;
		return false;
    }
	
	 public function canEditTenure($id)
    {
        if ( $this->getAdmin() ) return true;
		$this->setConditionId($id);
		if ( $this->checkACL('condition') ) return true;
		return false;
    }
    
     public function canEditPropertyType($id)
    {
        if ( $this->getAdmin() ) return true;
		$this->setConditionId($id);
		if ( $this->checkACL('condition') ) return true;
		return false;
    }
	
	 public function canEditFile($id)
    {
        if ( $this->getAdmin() ) return true;
		$this->setConditionId($id);
		if ( $this->checkACL('condition') ) return true;
		return false;
    }
    
     public function canEditFileAgent($id)
    {
        if ( $this->getAdmin() ) return true;
		$this->setConditionId($id);
		if ( $this->checkACL('condition') ) return true;
		return false;
    }
	
	 public function canEditTraining($id)
    {
        if ( $this->getAdmin() ) return true;
		$this->setConditionId($id);
		if ( $this->checkACL('condition') ) return true;
		return false;
    }
    
    public function canPublishCompany($id)
    {
        if ( $this->getAdmin() ) return true;
		if ($this->displayMessages){
            $this->app->enqueueMessage(JText::_('COM_IPROPERTY_ONLY_ADMINS_ACTION'), 'error');
        }
        return false;
    }
    
    public function canPublishCondition($id)
    {
        if ( $this->getAdmin() ) return true;
		if ($this->displayMessages){
            $this->app->enqueueMessage(JText::_('COM_IPROPERTY_ONLY_ADMINS_ACTION'), 'error');
        }
        return false;
    }
	
	 public function canPublishTenure($id)
    {
        if ( $this->getAdmin() ) return true;
		if ($this->displayMessages){
            $this->app->enqueueMessage(JText::_('COM_IPROPERTY_ONLY_ADMINS_ACTION'), 'error');
        }
        return false;
    }
    
     public function canPublishPropertyType($id)
    {
        if ( $this->getAdmin() ) return true;
		if ($this->displayMessages){
            $this->app->enqueueMessage(JText::_('COM_IPROPERTY_ONLY_ADMINS_ACTION'), 'error');
        }
        return false;
    }
	
	 public function canPublishFile($id)
    {
        if ( $this->getAdmin() ) return true;
		if ($this->displayMessages){
            $this->app->enqueueMessage(JText::_('COM_IPROPERTY_ONLY_ADMINS_ACTION'), 'error');
        }
        return false;
    }
    
     public function canPublishFileAgent($id)
    {
        if ( $this->getAdmin() ) return true;
		if ($this->displayMessages){
            $this->app->enqueueMessage(JText::_('COM_IPROPERTY_ONLY_ADMINS_ACTION'), 'error');
        }
        return false;
    }
	
	public function canPublishTraining($id)
    {
        if ( $this->getAdmin() ) return true;
		if ($this->displayMessages){
            $this->app->enqueueMessage(JText::_('COM_IPROPERTY_ONLY_ADMINS_ACTION'), 'error');
        }
        return false;
    }
	
    public function canFeatureCompany($id)
    {
        if ( $this->getAdmin() ) return true;
        if ($this->displayMessages){
            $this->app->enqueueMessage(JText::_('COM_IPROPERTY_ONLY_ADMINS_ACTION'), 'error');
        }
        return false;
    }  
    
     public function canFeatureCondition($id)
    {
        if ( $this->getAdmin() ) return true;
        if ($this->displayMessages){
            $this->app->enqueueMessage(JText::_('COM_IPROPERTY_ONLY_ADMINS_ACTION'), 'error');
        }
        return false;
    }  
	
	 public function canFeatureTenure($id)
    {
        if ( $this->getAdmin() ) return true;
        if ($this->displayMessages){
            $this->app->enqueueMessage(JText::_('COM_IPROPERTY_ONLY_ADMINS_ACTION'), 'error');
        }
        return false;
    }  
	
	 public function canFeaturePropertyType($id)
    {
        if ( $this->getAdmin() ) return true;
        if ($this->displayMessages){
            $this->app->enqueueMessage(JText::_('COM_IPROPERTY_ONLY_ADMINS_ACTION'), 'error');
        }
        return false;
    }  
	
	 public function canFeatureFile($id)
    {
        if ( $this->getAdmin() ) return true;
        if ($this->displayMessages){
            $this->app->enqueueMessage(JText::_('COM_IPROPERTY_ONLY_ADMINS_ACTION'), 'error');
        }
        return false;
    } 

	public function canFeatureTraining($id)
    {
        if ( $this->getAdmin() ) return true;
        if ($this->displayMessages){
            $this->app->enqueueMessage(JText::_('COM_IPROPERTY_ONLY_ADMINS_ACTION'), 'error');
        }
        return false;
    } 
	
	public function canRegisterTraining($id)
    {
        if ( $this->getAdmin() ) return true;
        if ($this->displayMessages){
            $this->app->enqueueMessage(JText::_('COM_IPROPERTY_ONLY_ADMINS_ACTION'), 'error');
        }
        return false;
    } 

    public function canDeleteCompany($id)
    {
        if ( $this->getAdmin() ) return true;
        if ($this->displayMessages){
            $this->app->enqueueMessage(JText::_('COM_IPROPERTY_ONLY_ADMINS_ACTION'), 'error');
        }
        return false;
    } 
    
    public function canDeleteCondition($id)
    {
        if ( $this->getAdmin() ) return true;
        if ($this->displayMessages){
            $this->app->enqueueMessage(JText::_('COM_IPROPERTY_ONLY_ADMINS_ACTION'), 'error');
        }
        return false;
    } 
	
	public function canDeleteTenure($id)
    {
        if ( $this->getAdmin() ) return true;
        if ($this->displayMessages){
            $this->app->enqueueMessage(JText::_('COM_IPROPERTY_ONLY_ADMINS_ACTION'), 'error');
        }
        return false;
    } 
	
	public function canDeleteFile($id)
    {
        if ( $this->getAdmin() ) return true;
        if ($this->displayMessages){
            $this->app->enqueueMessage(JText::_('COM_IPROPERTY_ONLY_ADMINS_ACTION'), 'error');
        }
        return false;
    } 
    
    public function canDeleteFileAgent($id)
    {
        if ( $this->getAdmin() ) return true;
        if ($this->displayMessages){
            $this->app->enqueueMessage(JText::_('COM_IPROPERTY_ONLY_ADMINS_ACTION'), 'error');
        }
        return false;
    } 
	
	public function canDeleteTraining($id)
    {
        if ( $this->getAdmin() ) return true;
        if ($this->displayMessagnes){
            $this->app->enqueueMessage(JText::_('COM_IPROPERTY_ONLY_ADMINS_ACTION'), 'error');
        }
        return false;
    } 
    
	/***********************
	* GENERIC AUTH FUNCTIONS
	***********************/

    public function canEditOpenhouse($id)
    {
        if ( $this->getAdmin() ) return true;
        
        $query = $this->db->getQuery(true);
        $query->select('prop_id')
                ->from('#__iproperty_openhouses')
                ->where('id = '.(int)$id);
        
        $this->db->setQuery($query);
        
        if($id = $this->db->loadResult()){
            $this->getPropertyInfo($id);
            if ( $this->checkACL('property') ) return true;
        }
		return false;
    }

	// this populates the user's agent info
	private function getUserAgentInfo()
    {
		if ( $this->user->id > 0 ){ // this will only execute if user is logged in
			$query = $this->db->getQuery(true);
			$query->select('a.*')
                ->from('#__iproperty_agents AS a')
                ->join('LEFT', '`#__iproperty_companies` AS c ON c.id = a.company')
                ->where('a.state = 1')
                ->where('c.state = 1')
                ->where('a.user_id = '.(int) $this->user->id );
			$this->db->setQuery($query);
			//if(FALSE !== ($result = $this->db->loadObject())){ //throwing errors in admin panel
            if ($result = $this->db->loadObject()){
				$this->setUagent($result);
				$this->setUagentId($result->id);
				if($result->agent_type) $this->setSuper(true);
				if($result->company) $this->setUagentCid($result->company);
			}
		}
	}
	
	// this one populates an agent object
	private function getAgentInfo($id)
    {
		if ( $this->user->id > 0 ){ // this will only execute if user is logged in
			$query = $this->db->getQuery(true);
			$query->select('id, company, user_id, state')
                ->from('#__iproperty_agents')
                ->where('id = '.(int) $id);
			$this->db->setQuery($query);
			if (FALSE !== ($result = $this->db->loadObject())){
                $this->setAgentId($result->id);
                $this->setAgentCid($result->company);
                $this->setAgentUid($result->user_id);
			}
		}
	}
	
	// this populates the property's info
	private function getPropertyInfo($id)
    {
		if ( $this->user->id > 0 ){ // this will only execute if user is logged in
			// unset the existing property_aid array
			$this->property_aid = array();
			
			$query = $this->db->getQuery(true);
			$query->select('a.listing_office as office, b.agent_id as agent')
                ->from('#__iproperty AS a')
                ->leftJoin('#__iproperty_agentmid AS b ON b.prop_id = a.id')
                ->where('a.id = '.(int) $id);	
			$this->db->setQuery($query);
			if (FALSE !== ($result = $this->db->loadObjectList())){
				$this->setPropertyId($id);
				foreach($result as $row){
					$this->setPropertyCid($row->office);
					$this->property_aid[] = $row->agent;
				}
				
			}
		}
	}	
	
	// check if user is an admin
	private function isAdmin()
    {
        // if user is administrator or user is assigned to an agent profile and IP edit rights is set to '3' (no restrictions), set user to admin
		if ( ($this->user && ($this->user->authorise('core.admin') || $this->user->authorise('core.manage', 'com_iproperty'))) || ($this->getUagentId() && $this->getAuthLevel() == 3) ) $this->setAdmin(true);
	}
	
    
    /************************
	* AGENT LIMIT CHECKS
	************************/
    
	// check if user is over limit for property
	private function checkAgentPropLimit()
    {
		if ( !$this->getUagentId() ){
            if($this->displayMessages){
                $this->app->enqueueMessage(JText::_('COM_IPROPERTY_NO_AGENT_ID_SET'), 'error');
            }
			return false;
		}
        
        // make sure company isn't over limit
        if (!$this->checkCompanyPropLimit()) return false;

        // if super agent, and we're not over the company limit he can add
        if ( $this->getSuper() ) return true;
        
		// only check agent's prop limit if not a super agent
		if ( $this->getUagentId() ){
			// get the agent params
			$agparams   = $this->getAgentParams();
            $max        = $agparams->get('maxlistings');
            if (!$max || $max == 0) return true; // no max limit - free for all
            
			$query = $this->db->getQuery(true);
			$query->select('count(a.prop_id)')
                ->from('#__iproperty_agentmid a')
                ->leftJoin('#__iproperty b ON b.id = a.prop_id')
                ->where('a.agent_id = ' .(int)$this->getUagentId())
                ->where('b.state');
			$this->db->setQuery($query);
			if(FALSE !== ($result = $this->db->loadResult())){
				if ($result < $max){ 
					return true; 
				} else {
                    if($this->displayMessages){
                        $this->app->enqueueMessage(JText::_('COM_IPROPERTY_AGENT_OVER_PROP_LIMIT'), 'error');
                    }
                    return false;
                }
			}
		}
		return false;		
	}
    
        // check if user is over limit for property
	private function checkAgentPropAgentLimit()
    {
		if ( !$this->getUagentId() ){
            if($this->displayMessages){
                $this->app->enqueueMessage(JText::_('COM_IPROPERTY_NO_AGENT_ID_SET'), 'error');
            }
			return false;
		}
        
        // make sure company isn't over limit
        if (!$this->checkCompanyPropLimit()) return false;

        // if super agent, and we're not over the company limit he can add
        if ( $this->getSuper() ) return true;
        
		// only check agent's prop limit if not a super agent
		if ( $this->getUagentId() ){
			// get the agent params
			$agparams   = $this->getAgentParams();
            $max        = $agparams->get('maxlistings');
            if (!$max || $max == 0) return true; // no max limit - free for all
            
			$query = $this->db->getQuery(true);
			$query->select('count(a.prop_id)')
                ->from('#__iproperty_agentmid a')
                ->leftJoin('#__ipropertyagent b ON b.id = a.prop_id')
                ->where('a.agent_id = ' .(int)$this->getUagentId())
                ->where('b.state');
			$this->db->setQuery($query);
			if(FALSE !== ($result = $this->db->loadResult())){
				if ($result < $max){ 
					return true; 
				} else {
                    if($this->displayMessages){
                        $this->app->enqueueMessage(JText::_('COM_IPROPERTY_AGENT_OVER_PROP_LIMIT'), 'error');
                    }
                    return false;
                }
			}
		}
		return false;		
	}
	// check if user is over limit for property
	private function checkAgentFeatPropLimit()
    {
		if ( !$this->getUagentId() ){
            if($this->displayMessages){
                $this->app->enqueueMessage(JText::_('COM_IPROPERTY_NO_AGENT_ID_SET'), 'error');
            }
			return false;
		}
        // make sure company isn't over limit
        if (!$this->checkCompanyFeatPropLimit()) return false;
        
        // if super agent, return true since we're not over company limit
        if ( $this->getSuper() ) return true;
        
		// only check agent's prop limit if not a super agent
		if ( $this->getUagentId() ){
			// get the agent params
			$agparams   = $this->getAgentParams();
            $max        = $agparams->get('maxflistings');
            if (!$max || $max == 0) return true; // no max limit - free for all
            
			$query = $this->db->getQuery(true);
			$query->select('count(a.prop_id)')
                ->from('#__iproperty_agentmid a')
                ->leftJoin('#__iproperty b ON b.id = a.prop_id')
                ->where('a.agent_id = ' .(int)$this->getUagentId())
                ->where('b.state')
                ->where('b.featured');
			$this->db->setQuery($query);
			if (FALSE !== ($result = $this->db->loadResult())){
				if ($result < $max){ 
					return true; 
				} else {
                    if($this->displayMessages){
                        $this->app->enqueueMessage(JText::_('COM_IPROPERTY_AGENT_OVER_PROP_LIMIT'), 'error');
                    }
                    return false;
                }
			}
		}
		return false;	
	}    
    
    /************************
	* COMPANY LIMIT CHECKS
	************************/
    
    // return true if company's total props is < limit
    private function checkCompanyPropLimit()
    {
        if ( $this->getUagentCid() ){
			// get the company params
			$coparams   = $this->getCompanyParams();
            $max        = $coparams->get('maxlistings');
            if (!$max || $max == 0) return true; // no max limit - free for all
            
			$query = $this->db->getQuery(true);
			$query->select('count(id)')
                ->from('#__iproperty')
                ->where('state = 1')
                ->where('listing_office = '.(int)$this->getUagentCid());
			$this->db->setQuery($query);
			if (FALSE !== ($result = $this->db->loadResult())){
				if ($result < $max){ 
					return true;
				} else {
                    if ($this->displayMessages){
                        $this->app->enqueueMessage(JText::_('COM_IPROPERTY_COMPANY_OVER_PROP_LIMIT'), 'error');
                    }
                    return false;
                }
			}
		}
        return false; // no company_id
    }
    
    // return true if number of featured properties for an agent < total number allowed
	private function checkCompanyFeatPropLimit()
    {
		if( $this->getUagentCid() ){
			// get the company params
			$coparams   = $this->getCompanyParams();
            $max        = $coparams->get('maxflistings');
            if (!$max || $max == 0) return true; // no max limit - free for all
            
			$query = $this->db->getQuery(true);
			$query->select('count(id)')
                ->from('#__iproperty')
                ->where('featured = 1')
                ->where('listing_office = '.(int)$this->getUagentCid());
			$this->db->setQuery($query);
			if (FALSE !== ($result = $this->db->loadResult())){
				if ($result < $max){
					return true;
				} else {
                    if ($this->displayMessages){
                        $this->app->enqueueMessage(JText::_('COM_IPROPERTY_COMPANY_OVER_FPROPERTY_LIMIT'), 'error');
                    }
					return false;
				}
			}
		} 
		return false;
	}     
	
    // return true if company's number of agents < total agents allowed
	private function checkCompanyAgentLimit()
    {
		if ( $this->getUagentCid() ){
			// get the company params
			$coparams   = $this->getCompanyParams();
            $max        = $coparams->get('maxagents');
            if (!$max || $max == 0) return true; // no max limit - free for all
            
			$query = $this->db->getQuery(true);      
			$query->select('count(id)')
                ->from('#__iproperty_agents')
                ->where('state = 1')
                ->where('company = '.(int)$this->getUagentCid());
			$this->db->setQuery($query);
			if (FALSE !== ($result = $this->db->loadResult())){
				if ($result < $max){
					return true;
				} else {
                    if ($this->displayMessages){
                        $this->app->enqueueMessage(JText::_('COM_IPROPERTY_COMPANY_OVER_AGENT_LIMIT'), 'error');
                    }
					return false;
				}
			}
		}
		return false;
	}
    
    // return true if number of featured agents < total number of featured allowed
	private function checkCompanyFeatAgentLimit()
    {
		if ( $this->getUagentCid() ){
			// get the company params
			$coparams   = $this->getCompanyParams();
            $max        = $coparams->get('maxfagents');
            if (!$max || $max == 0) return true; // no max limit - free for all
            
			$query = $this->db->getQuery(true);
			$query->select('count(id)')
                ->from('#__iproperty_agents')
                ->where('featured = 1')
                ->where('company = '.(int)$this->getUagentCid());
			$this->db->setQuery($query);
			if (FALSE !== ($result = $this->db->loadResult())){
				if ($result < $max){
					return true;
				} else {
                    if ($this->displayMessages){
                        $this->app->enqueueMessage(JText::_('COM_IPROPERTY_COMPANY_OVER_FAGENT_LIMIT'), 'error');
                    }
					return false;
				}
			}
		}
		return false;
	}
    
    /************************
	* PARAM GETTERS
	************************/   
	
    public function getCompanyParams()
    {
        if (!$this->cparams){
            $query = $this->db->getQuery(true);
            $query->select('params')
                    ->from('#__iproperty_companies')
                    ->where('id = '.(int)$this->getUagentCid());
            $this->db->setQuery($query, 0, 1);
            $result         = $this->db->loadResult();
            $this->cparams  = new JRegistry( $result );
        } else {
            $cparams = $this->cparams;
        }
        $cparams = $this->cparams;
        
        return $cparams;
    }

    public function getAgentParams()
    {
        if (!$this->aparams){
            $query = $this->db->getQuery(true);
            $query->select('params')
                    ->from('#__iproperty_agents')
                    ->where('id = '.(int)$this->getUagentId());
            $this->db->setQuery($query, 0, 1);
            $result         = $this->db->loadResult();
            $this->aparams  = new JRegistry( $result );
        } else {
            $aparams = $this->aparams;
        }
        $aparams = $this->aparams;
        
        return $aparams;
    }	
    
	/************************
	* MAIN ACL FUNCTION
	************************/    
	
	private function checkACL($type)
    {
		if ( !$this->getUagentCid() || !$this->getUagentId() ){
            if($this->displayMessages){
                $this->app->enqueueMessage(JText::_('COM_IPROPERTY_NO_AGENT_ID_SET'), 'error');
            }
			return false; // not logged in or we have no agent info
		}	
		switch ($this->settings->edit_rights){
			case 0: // ACL is disabled
				if ($this->displayMessages){
                    $this->app->enqueueMessage(JText::_('COM_IPROPERTY_ACCESS_DISABLED'), 'error');
                }
				return false;
			break;
			case 1: // company ACL
				switch ($type){
					case 'property':
						if ( $this->getPropertyCid() == $this->getUagentCid() ) return true; // check same company 
						if ($this->displayMessages){
                            $this->app->enqueueMessage(JText::_('COM_IPROPERTY_AGENT_COMPANY_MISMATCH'), 'error');
                        }
						return false;
					break;
					case 'agent': 
						if ( $this->getAgentCid() != $this->getUagentCid() ){
                            if ($this->displayMessages){
                                $this->app->enqueueMessage(JText::_('COM_IPROPERTY_AGENT_COMPANY_MISMATCH'), 'error');
                            }
							return false; // check same company 
						}	
						if ( $this->getSuper() ) return true; 
						if ( $this->getAgentId() == $this->getUagentId() ) return true; 
						return false;
					break;
					case 'company':
						if ( $this->getCompanyId() != $this->getUagentCid() ){
                            if ($this->displayMessages){
                                $this->app->enqueueMessage(JText::_('COM_IPROPERTY_AGENT_COMPANY_MISMATCH'), 'error');
                            }
							return false; // check same company 
						}	
						if ( $this->getSuper() ) return true; // assuming only super agents can do edit company
                        if ($this->displayMessages){
                            $this->app->enqueueMessage(JText::_('COM_IPROPERTY_ONLY_ADMINS_ACTION'), 'error');
                        }
						return false;
					break;
					default:
						return false;
					break;
				}
			break;
			case 2: // agent ACL
				switch ($type){
					case 'property':
						if ( $this->getPropertyCid() != $this->getUagentCid() ){
                            if ($this->displayMessages){
                                $this->app->enqueueMessage(JText::_('COM_IPROPERTY_AGENT_COMPANY_MISMATCH'), 'error');
                            }
							return false; // check same company
						}	
						if ( $this->getSuper() ) return true;
						if ( in_array( $this->getUagentId(), $this->property_aid) ) return true;
						return false;
					break;
					case 'agent':
						if ( $this->getAgentCid() != $this->getUagentCid() ){
                            if ($this->displayMessages){
                                $this->app->enqueueMessage(JText::_('COM_IPROPERTY_AGENT_COMPANY_MISMATCH'), 'error');
                            }
							return false; // check same company 
						}	
						if ( $this->getSuper() ) return true; 
						if ( $this->getAgentId() == $this->getUagentId() ) return true; 
						return false;
					break;
					case 'company':
						if ( $this->getCompanyId() != $this->getUagentCid() ) {
                            if ($this->displayMessages){
                                $this->app->enqueueMessage(JText::_('COM_IPROPERTY_AGENT_COMPANY_MISMATCH'), 'error');
                            }
							return false; // check same company 
						}
						if ( $this->getSuper() ) return true; // assuming only super agents can do edit company
                        if ($this->displayMessages){
                            $this->app->enqueueMessage(JText::_('COM_IPROPERTY_ONLY_ADMINS_ACTION'), 'error');
                        }
						return false;
					break;
					default:
						return false;
					break;
				}
			break;
			case 3: // no restrictions - anybody can edit
			default:
                return true;
			break;
		}
        if($this->displayMessages){
            $this->app->enqueueMessage(JText::_('COM_IPROPERTY_NO_AGENT_ID_SET'), 'error');
        }
		return false; // in case the settings object isn't set
	}
	
	/************************
	* GET & SET FUNCTIONS
	************************/	
	
	public function setUagentId($id){
		$this->uagent_id = (int) $id;
	}
	
	public function getUagentId(){
		return $this->uagent_id;
	}

	public function setUagent($agent){
		$this->uagent = $agent;
	}
	
	public function getUagent(){
		return $this->uagent;
	}
	
	public function setUagentCid($id){
		$this->uagent_cid = (int)$id;
	}
	
	public function getUagentCid(){
		return $this->uagent_cid;
	}	
	
	public function setAgentId($id){
		$this->agent_id = (int)$id;
	}
	
	public function getAgentId(){
		return $this->agent_id;
	}

	public function setAgentCid($id){
		$this->agent_cid = (int)$id;
	}
	
	public function getAgentCid(){
		return $this->agent_cid;
	}	

	public function setAgentUid($id){
		$this->agent_uid = (int)$id;
	}
	
	public function getAgentUid(){
		return $this->agent_uid;
	}	
	
	public function setCompanyId($id){
		$this->company_id = (int) $id;
	}
	
	public function getCompanyId(){
		return $this->company_id;
	}
        
    public function setConditionId($id){
		$this->condition_id = (int) $id;
	}
	
	public function getConditionId(){
		return $this->condition_id;
	}
	
	public function setTenureId($id){
		$this->tenure_id = (int) $id;
	}
	
	public function getTenureId(){
		return $this->tenure_id;
	}
        
        public function setPropertyTypeId($id){
		$this->propertytype_id = (int) $id;
	}
	
	public function getPropertyTypeId(){
		return $this->propertytype_id;
	}
	
	public function setFileId($id){
		$this->file_id = (int) $id;
	}
	
	public function getFileId(){
		return $this->file_id;
	}
        
        public function setFileAgentId($id){
		$this->fileagent_id = (int) $id;
	}
        
        public function getFileAgentId(){
		return $this->fileagent_id;
	}

	public function setPropertyId($id){
		$this->property_id = (int) $id;
	}
	
	public function getPropertyId(){
		return $this->property_id;
	}
	
	public function setPropertyCid($id){
		$this->property_cid = (int) $id;
	}
	
	public function getPropertyCid(){
		return $this->property_cid;
	}	
    
	public function setAdmin($bool){
		$this->admin = (boolean) $bool;
	}
	
	public function getAdmin(){
		return $this->admin;
	}
	
	public function setSuper($bool){
		$this->super = (boolean) $bool;
	}
	
	public function getSuper(){
		return $this->super;
	}
	
	public function setGuest($bool){
		$this->guest = (boolean) $bool;
	}
	
	public function getGuest(){
		return $this->guest;
	}
	
	public function setUser($user){
		$this->user = $user;
	}
	
	public function getUser(){
		return $this->user;
	}

	public function setAuthLevel($level){
		$this->authlevel = (int) $level;
	}
	
	public function getAuthLevel(){
		return $this->authlevel;
	}	
	
	/****************************
	* Debug 
	****************************/
	
	public function printDebug()
    {
		$authinfo = new StdClass();
		
		// these are the vars for the user
		$authinfo->user 		= $this->getUser();
		$authinfo->uagent		= $this->getUagent();
		$authinfo->uagent_id 	= $this->getUagentId();
		$authinfo->uagent_cid	= $this->getUagentCid();
		$authinfo->super 		= $this->getSuper();
		$authinfo->admin		= $this->getAdmin();
		$authinfo->guest		= $this->getGuest();
		$authinfo->company_id	= $this->getCompanyId();
		$authinfo->condition_id	= $this->getConditionId();
		$authinfo->tenure_id	= $this->getTenureId();
                $authinfo->propertype_id	= $this->getPropertyTypeId();
		$authinfo->file_id		= $this->getFileId();
		$authinfo->fileagent_id		= $this->getFileAgentId();
		$authinfo->property_id	= $this->getPropertyId();
		$authinfo->property_cid	= $this->getPropertyCid();
		$authinfo->property_aid	= $this->property_aid;
		$authinfo->agent_id		= $this->getAgentId();
		$authinfo->agent_cid	= $this->getAgentCid();
		$authinfo->agent_uid	= $this->getAgentUid();
		$authinfo->acl			= $this->settings->edit_rights;
	
		return $authinfo;
	}
}
?>