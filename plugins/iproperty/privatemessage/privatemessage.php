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
require_once(JPATH_SITE.'/components/com_iproperty/helpers/route.php');
require_once(JPATH_SITE.'/components/com_iproperty/helpers/html.helper.php');

class plgIpropertyPrivatemessage extends JPlugin
{
    public function __construct(&$subject, $config)  
    {
        parent::__construct($subject, $config);
        $this->loadLanguage();
    }

    public function onAfterPropertyRequest($user_id, $post, $settings)
    {
        if (!$this->params->get('req_message', 1)) return true;
        return $this->_sendPrivateMessage($user_id, $post, $settings, 'request');
    }

    public function onAfterSendFriend($user_id, $post, $settings)
    {
        if (!$this->params->get('friend_message', 1)) return true;
        return $this->_sendPrivateMessage($user_id, $post, $settings, 'friend');
    }

    private function _sendPrivateMessage($user_id, $post, $settings, $type)
    {
        $db         = JFactory::getDbo();
        $app        = JFactory::getApplication();
        $prop_id    = (int)$post['prop_id'];
        
        if($app->getName() != 'site' || !$this->params->get('sender_id')) return true;

        $proplink   = JRoute::_(ipropertyHelperRoute::getPropertyRoute($prop_id), false);

        $message  = JText::_('PLG_IP_PRIVATEMESSAGE_NAME').": ".$post['sender_name']."\n";
        $message .= JText::_('PLG_IP_PRIVATEMESSAGE_EMAIL').": ".$post['sender_email']."\n";

        switch ($type){
            case 'friend':
                $message    .= JText::_('PLG_IP_PRIVATEMESSAGE_REQUEST').": ".$post['comments']."\n";
                $subject     = JText::_('PLG_IP_PRIVATEMESSAGE_FRIEND_SUBJECT');
                //$message    .= JText::_('PLG_IP_PRIVATEMESSAGE_DPHONE').$post['sender_dphone']."\n";
                $message    .= JText::_('PLG_IP_PRIVATEMESSAGE_FRIEND_EMAIL').": ".$post['recipient_email']."\n";
                break;
            case 'request':
                $message    .= JText::_('PLG_IP_PRIVATEMESSAGE_REQUEST').": ".$post['special_requests']."\n";
                $subject     = JText::_('PLG_IP_PRIVATEMESSAGE_REQUEST_SUBJECT');
                $message    .= JText::_('PLG_IP_PRIVATEMESSAGE_DPHONE').": ".$post['sender_dphone']."\n";
                $message    .= JText::_('PLG_IP_PRIVATEMESSAGE_EPHONE').": ".$post['sender_ephone']."\n";
                $message    .= JText::_('PLG_IP_PRIVATEMESSAGE_PREF').": ".$post['sender_preference']."\n";
                $message    .= JText::_('PLG_IP_PRIVATEMESSAGE_CTIME').": ".$post['sender_ctime']."\n";
                break;
            default:
                $message   .= JText::_('PLG_IP_PRIVATEMESSAGE_REQUEST').": ".$post['comments']."\n";
                $subject    = JText::_('PLG_IP_PRIVATEMESSAGE_FRIEND_SUBJECT');
                break;
        }

        $message .= JText::_('PLG_IP_PRIVATEMESSAGE_LINK').": ".$proplink."\n";

        // get the agents attached to the property
        $query = $db->getQuery(true);
        $query->select('a.*')
              ->from('#__iproperty_agents AS a')
              ->leftJoin('#__iproperty_agentmid AS am ON a.id = am.agent_id')
              ->where('a.state AND a.user_id AND am.prop_id = '.(int) $prop_id);

        $db->setQuery($query);
        $result = $db->loadObjectList();

        if ($result) {
            // we've got agents for this property
            // loop through results
            foreach($result as $agent)
            {
                $user = $agent->user_id;

                $data                   = new stdClass();
                $data->subject          = $subject;
                $data->message          = $message;
                $data->user_id_from     = $this->params->get('sender_id');
                $data->user_id_to       = $user;
                $data->date_time        = JFactory::getDate()->toSQL();
                $data->state            = 0; // 0 for unread
                $db->insertObject('#__messages', $data);
            }
        }
        return false;
    }
}