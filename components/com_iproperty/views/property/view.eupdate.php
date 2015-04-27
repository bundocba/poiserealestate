<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license see LICENSE.php
 */

/*
 * TO USE THIS CLASS:
 * SET UP A CRON JOB USING YOUR WEB CONTROL PANEL OR A COMMAND-LINE CRONTAB INTERFACE.
 * CHECK WITH YOUR WEBHOST IF YOU DON'T KNOW HOW TO DO THIS!! IT VARIES BY HOST AND
 * WE CANNOT PROVIDE A 'ONE SIZE FITS ALL' ANSWER.
 * 
 * SET UP THE CRON JOB TO REQUEST THE PAGE "http://yoursite.com/index.php?option=com_iproperty&view=property&format=eupdate&secret=YOUR_JOOMLA_CONFIG_FILE_SECRET_MD5_HASHED&listing=true&search=true&limit=X"
 * 
 * IN THE ABOVE URL, SET listing=false IF YOU DO *NOT* WANT TO SEND UPDATES ON SAVED PROPERTIES 
 * IN THE ABOVE URL, SET search=false IF YOU DO *NOT* WANT TO SEND UPDATES ON SAVED SEARCHES
 * IN THE ABOVE URL, SET limit=X WHERE X = THE MAX LISTINGS YOU WANT TO SEND IN A SAVED SEARCH UPDATE-- RECOMMENDED IS ABOUT 25
 * IN THE ABOVE URL, SET secret = AN MD5 HASH OF YOUR JOOMLA CONFIGURATION.PHP $secret VARIABLE VALUE
 * TO GENERATE AN MD5 HASH TRY http://www.miraclesalad.com/webtools/md5.php 
 *
 * IF BOTH LISTINGS AND SEARCH ARE false, OR SECRET IS ABSENT OR INCORRECT, NOTHING WILL HAPPEN AND NO EMAILS WILL BE SENT!
 * 
 */
 
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

defined( '_JEXEC' ) or die( 'Restricted access');
jimport( 'joomla.application.component.view');
require_once (JPATH_COMPONENT.'/models/advsearch.php');

class IpropertyViewProperty extends JViewLegacy
{    
    private $searchstring;
    private $limit;
    private $last_sent;
    
    public function display($tpl = null)
    {
        $settings       = ipropertyAdmin::config();
		$config 		= JFactory::getConfig();
		$db             = JFactory::getDbo();
		$key			= JRequest::getVar('secret', false);
        $listing		= JRequest::getBool('listing', false); // pass in if you want to run saved listing update
        $search			= JRequest::getBool('search', false); // pass in if you want to run saved search update
        $this->limit    = JRequest::getInt('limit', 25); // pass in the limit for saved search listings sent
        
		$secret			= md5($config->get('secret'));

		$date       = JFactory::getDate();
        $nowDate    = $db->Quote($date->toSql());
		
		if ($key != $secret) JExit('COM_IPROPERTY_INVALID_EUPDATE_KEY');
        if (!$search && !$listing) JExit('NO SEARCH UPDATES SPECIFIED');

        // Saved properties updates
        if ($listing && $settings->show_propupdate)
        {
            $query = $db->getQuery(true);
            $query->select('s.id as sid, s.*, p.id as id')
                    ->from('#__iproperty_saved as s')
                    ->join('', '#__iproperty as p on p.id = s.prop_id')
                    ->where('p.modified >= s.last_sent')
                    ->where('s.active')
                    ->where('s.email_update')
                    ->where('s.type = 0');

            $db->setQuery($query);
            if(FALSE != ($result = $db->loadObjectList())){
                foreach($result as $r)
                {
                    $r->address = ipropertyHTML::getPropertyTitle($r->prop_id);
                    $this->sendPropUpdate($r);
                    
                    // SET LAST RUN TO NOW
                    $query = $db->getQuery(true);
                    $query->update('#__iproperty_saved')
                            ->set('last_sent = '.$nowDate)
                            ->where('id = '.(int)$r->sid);
                    
                    $db->setQuery($query);
                    $db->execute();
                }	
            }
        }
        
        // Search criteria updates
        if ($search && $settings->show_searchupdate)
        {
            $query = $db->getQuery(true);
            $query->select('*')
                    ->from('#__iproperty_saved')
                    ->where('active')
                    ->where('email_update')
                    ->where('type = 1');

            $db->setQuery($query);
            if(FALSE !== ($result = $db->loadObjectList())){
                foreach($result as $r){					
                    $this->searchstring = json_decode($r->search_string);
                    $this->last_sent = $r->last_sent;
                    $props = $this->getData();
                    $this->sendSearchUpdate($props, $r);
                    
                    // SET LAST RUN TO NOW
                    $query = $db->getQuery(true);
                    $query->update('#__iproperty_saved')
                            ->set('last_sent = '.$nowDate)
                            ->where('id = '.(int)$r->id);
                    
                    $db->setQuery($query);
                    $db->execute();
                }	
            }
        }
        return true;
    }

    public function _displayNoAccess($tpl = null)
    {
        JToolBarHelper::title( '<span class="ip_adminHeader">'.JText::_('NO ACCESS' ).'</span>', 'iproperty');
        JToolBarHelper::back();
        parent::display($tpl);
    }

    private function sendPropUpdate($record)
    {
        if (!is_object($record)) return false;
		$user = JFactory::getUser($record->user_id);
		if($user->block) return false;
		$config = JFactory::getConfig();
		
		$url        = JURI::base().ipropertyHelperRoute::getPropertyRoute($record->prop_id);
        $manageurl  = JURI::base().IpropertyHelperRoute::getIpuserRoute();
		
		$mailer = JFactory::getMailer();
		
		$sender = array( 
			$config->get('mailfrom' ),
			$config->get('fromname' ) 
        );
        
        $secret     = $config->get('secret');
        $hash       = md5($record->user_id . $record->sid . $secret);
        $unsublink  = JURI::base()."index.php?option=com_iproperty&task=ipuser.unsubscribeSaved&id=".$record->sid."&token=".$hash;
		 
		$mailer->setSender($sender);
		$mailer->addRecipient($user->email);
		
		$subject 	= sprintf(JText::_('COM_IPROPERTY_PROP_UPDATE_SUBJECT'), $config->get('sitename'));
		$body		= '<p>'.sprintf(JText::_('COM_IPROPERTY_PROP_UPDATE_EMAIL'), $record->address, $config->get('sitename'), $url).'</p>';
        $body      .= '<p><a href="'.$manageurl.'">'.JText::_('COM_IPROPERTY_MANAGE_SAVED').'</a><br />';
        $body      .= '<a href="'.$unsublink.'">'.JText::_('COM_IPROPERTY_UNSUBSCRIBE').'</a><br />';
        $body      .= '<a href="'.$unsublink.'&all=1">'.JText::_('COM_IPROPERTY_UNSUBSCRIBE_ALL').'</a></p>';
		
		$mailer->setSubject($subject);
		$mailer->setBody($body);
        $mailer->isHTML(true);
		
		$mailer->Send();
    }

    private function sendSearchUpdate($props, $record)
    {
        $search_id  = $record->id;
        $user_id    = $record->user_id;
        $manageurl  = JURI::base().IpropertyHelperRoute::getIpuserRoute();
        
        if (!count($props)) return false;
		$user = JFactory::getUser($user_id);
		if($user->block) return false;
		$config = JFactory::getConfig();
		$mailer = JFactory::getMailer();
		
		$sender = array( 
			$config->get('mailfrom'),
			$config->get('fromname') 
        );
        
        $secret     = $config->get('secret');
        $hash       = md5($user_id . $search_id . $secret);
        $unsublink  = JURI::base()."index.php?option=com_iproperty&task=ipuser.unsubscribeSaved&id=".$search_id."&token=".$hash;
		 
		$mailer->setSender($sender);
		$mailer->addRecipient($user->email);
		
		$subject 	= sprintf(JText::_('COM_IPROPERTY_SEARCH_UPDATE_SUBJECT'), $config->get('sitename'));
		$body		= '<p>'.sprintf(JText::_('COM_IPROPERTY_SEARCH_UPDATE_EMAIL'), $record->notes, $config->get('sitename')).'</p>';
        
        $body .= '<ul>';
        foreach ($props as $p){
            $url 	= JURI::base().ipropertyHelperRoute::getPropertyRoute($p->id);
            $title  = IpropertyHtml::getPropertyTitle($p->id);
            $body  .= '<li><a href="'.$url.'">'.$title.'</a></li>';
        }
        $body .= '</ul>';

        $body       .= '<p><a href="'.$manageurl.'">'.JText::_('COM_IPROPERTY_MANAGE_SAVED').'</a><br />';
        $body       .= '<a href="'.$unsublink.'">'.JText::_('COM_IPROPERTY_UNSUBSCRIBE').'</a><br />';
        $body       .= '<a href="'.$unsublink.'&all=1">'.JText::_('COM_IPROPERTY_UNSUBSCRIBE_ALL').'</a></p>';
		
		$mailer->setSubject($subject);
		$mailer->setBody($body);
        $mailer->isHTML(true);
	
		$mailer->Send();
    }
    
    private function getData()
	{
        $searchstring 	= json_decode(json_encode($this->searchstring), true); // convert from object to array
        // add in the date search so we get only new/modified listings
        $searchstring['modified'] 	= $this->last_sent;
        $searchstring['created'] 	= $this->last_sent; 
		
		$config                 = array();
        $config['where']        = $searchstring;
        $config['limitstart']   = 0;
        $config['limit']        = $this->limit;

        $model          = new IpropertyModelAdvsearch($config);
        $properties     = $model->getItems();
			
		return $properties->data['listings'];
	}
}
?>
