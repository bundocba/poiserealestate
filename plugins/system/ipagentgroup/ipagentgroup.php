<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

// no direct access
defined('_JEXEC' ) or die( 'Restricted access');
jimport('joomla.plugin.plugin');

class plgSystemIpagentgroup extends JPlugin
{
	public function __construct(&$subject, $config)  
    {
		parent::__construct($subject, $config);
        $this->loadLanguage();
	}
    
    public function onAfterInitialise()
	{       
        // Check for logged in user
        $user = JFactory::getUser();
        if(!$user->id) return;

        // Check if user is assigned to IP agent profile
        $db = JFactory::getDbo();
        
        $query = $db->getQuery(true);
        $query->select('user_id, agent_type')
                ->from('#__iproperty_agents')
                ->where('user_id = '.(int)$user->id)
                ->where('state = 1');
        
        $db->setQuery($query, 0, 1);
        $agent = $db->loadObject();
        
        // If valid IP agent
        if($agent && $agent->user_id){
            // assign the agent group to the user if group is set
            if($this->params->get('agentgroup'))
                JUserHelper::addUserToGroup($user->id, $this->params->get('agentgroup'));
            
            // check if agent is a super agent type
            if($agent->agent_type){
                // super agent
                // assign the user to super agent group if set
                if($this->params->get('super_agentgroup'))
                    JUserHelper::addUserToGroup($user->id, $this->params->get('super_agentgroup'));
            }else{
                // not a super agent
                // remove the user from super agent group if set
                if($this->params->get('super_agentgroup'))
                    JUserHelper::removeUserFromGroup($user->id, $this->params->get('super_agentgroup'));
            }
        // Not a valid IP agent
        }else{
            // remove the user from agent group if set
            if($this->params->get('agentgroup')) 
                JUserHelper::removeUserFromGroup($user->id, $this->params->get('agentgroup'));
            // remove the user from super agent group if set
            if($this->params->get('super_agentgroup'))
                JUserHelper::removeUserFromGroup($user->id, $this->params->get('super_agentgroup'));
        }
        return;
    }   
}