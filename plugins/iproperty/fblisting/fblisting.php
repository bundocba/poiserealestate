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

$pluginpath = JPATH_PLUGINS.'/iproperty/fblisting';

require $pluginpath.'/assets/facebook.php';
require_once JPATH_SITE.'/components/com_iproperty/helpers/route.php';
require_once JPATH_SITE.'/components/com_iproperty/helpers/html.helper.php';

class plgIpropertyFblisting extends JPlugin
{	
	public function __construct(&$subject, $config)  
    {
		parent::__construct($subject, $config);
        $this->loadLanguage();
        $this->appid 		= null;
        $this->appsecret 	= null;
        $this->pageid 		= null;
        $this->facebook 	= null;
	}

	public function onAfterSavePropertyEdit($prop_id, $isNew = false)
	{
        $app        = JFactory::getApplication();
        $document   = JFactory::getDocument();
        $db         = JFactory::getDBO();
        $extension	= new JTableExtension($db);
        $ext_id		= $this->getPlgId(); 
        
        // get the existing extension data
        $extension->load($ext_id);
        
        $postnew = $this->params->get('postnew', false);
        $postupd = $this->params->get('postupdate', false);

        // only tweet new or updated if params are true
        switch ($isNew){
            case 1:
                $message = JText::_('PLG_IP_FBLISTING_FB_NEW_TEXT');
                if (!$postnew) return false;
            break;
            default:
                $message = JText::_('PLG_IP_FBLISTING_FB_UPDATE_TEXT');
                if (!$postupd) return false;
            break; 
        }
      
        // FB keys / info
        $this->appid		= $this->params->get('appid', false);
        $this->appsecret	= $this->params->get('appsecret', false);
        $this->pageid		= $this->params->get('pageid', false) ?: 'me';
        $this->token 		= $this->params->get('apptoken', false);
        // bitly key
        $bitk   			= $this->params->get('bitlykey', false);
         
        if (!$this->appid || !$this->appsecret) {
			JFactory::getApplication()->enqueueMessage(JText::_('PLG_IP_FBLISTING_APPID_SECRET_REQUIRED'));
			return false;
		} else if (!$this->token) {
			JFactory::getApplication()->enqueueMessage(JText::_('PLG_IP_FBLISTING_NOTOKEN'));
			return false;
		}	
        $fbconfig = array(
			'appId' => $this->appid,
			'secret' => $this->appsecret,
			'cookie' => true
		);
     
        $this->facebook	= new Facebook($fbconfig);        
        // try to get a longterm token from existing token
        $this->facebook->setAccessToken($this->token);
        $this->facebook->setExtendedAccessToken();
        $token = $this->facebook->getAccessToken();
        // set the new token and STORE it
        if ($token) {
			$this->params->set('apptoken', $token);
			$extension->bind( array('params' => $this->params->toString()) );
			
			// check and store 
			if (!$extension->check()) {
				$this->setError($extension->getError());
				return false;
			}
			if (!$extension->store()) {
				$this->setError($extension->getError());
				return false;
			}
		}	
		if ($this->pageid !== 'me') {
			// if we're posting to a page, get the page token and set it
			$pagetoken = $this->get_page_token();
			$this->facebook->setAccessToken($pagetoken);
		}
		
		// get the property
		$query = 'SELECT * FROM #__iproperty WHERE id = '.$db->quote( $prop_id );
		$db->setQuery($query);
		$property = $db->loadObject();
		
		if(!$property) return false;
		
		$link = JURI::base().ipropertyHelperRoute::getPropertyRoute($prop_id.':'.$property->alias);
		
		// can't post with localhost as the link, it will fail
		if (strpos($link, 'localhost')) $link = 'http://yahoo.com';
		if($bitk) $link = $this->_shortenUrl($link, $bitk);
		$message .= '@ '.$link;  
		
		// get thumbnail
		$thumb = IpropertyHTML::getThumbnail($prop_id, false, false, 300, false, false, false, false, false);
			
		if($this->params->get('showsaletype', false)) {
			$message .= ' ' . ipropertyHTML::get_stype($property->stype);
		}
		if($this->params->get('showaddress', false)) {
			$property->street_address = ipropertyHTML::getStreetAddress($settings, $property);		
			$add = ipropertyHTML::getFullAddress($property);		
			$add = str_replace('<br />', ' - ', $add);
			$message .= ' ' . strip_tags($add);
		}
		if($this->params->get('showbeds', false)) {
			$message .= ' ' . $property->beds .  JText::_('PLG_IP_FBLISTING_TWEET_BEDS_TEXT');
		}
		if($this->params->get('showbaths', false)) {
			$message .= ' ' . $property->baths .  JText::_('PLG_IP_FBLISTING_TWEET_BATHS_TEXT');
		}
		if($this->params->get('showsqft', false)) {
			$units = (!$settings->measurement_units) ? JText::_( 'PLG_IP_FBLISTING_SQFT' ) : JText::_( 'PLG_IP_FBLISTING_SQM' );
			$message .= ' '.$property->sqft.' '.$units;
		}
		if($this->params->get('showreduced', false)) {
			if (($property->price2 != "0.00") && ($property->price2 > $property->price)) {
				$message .= " " . JText::_('PLG_IP_FBLISTING_TWEET_REDUCED_TEXT');
			}
		} 
	
		$fbpost = array(
			'message' => $message,
			'name' => IpropertyHTML::getPropertyTitle($prop_id),
			'caption' => JText::_('PLG_IP_FBLISTING_LINK_CAPTION'),
			'link' => $link,
			'picture' => $thumb
		);	

		try {
			$result = $this->facebook->api('/'.$this->pageid.'/feed/', 'post', $fbpost); 
			if ($result['id']) {
				// we successfully posted       
				JFactory::getApplication()->enqueueMessage(JText::_('PLG_IP_FBLISTING_SUCCESS'));
				return true;
			}
		} catch (FacebookApiException $e) {
			// this means there's no token or an old token
			JFactory::getApplication()->enqueueMessage(JText::_('PLG_IP_FBLISTING_ADMINLOGIN'));
			error_log($e->getType());
			error_log($e->getMessage());
			return false;
		}
        return false;
	}
    
    private function _shortenURL($url, $key)
    {
        $connectURL = 'https://api-ssl.bitly.com/v3/shorten?access_token='.$key.'&longUrl='.urlencode(trim($url));		
		if (strpos($url, 'localhost') !== false) {
			// bit.ly won't return results for localhost
			JFactory::getApplication()->enqueueMessage('Localhost URL not valid for bit.ly');
			return '';
		}
		$shortUrl = json_decode($this->_curl_get_result($connectURL));
		if ($shortUrl) {
			return $shortUrl->data->url;
		} else {
			return '';
		}
    }

    private function _curl_get_result($url)
    {
		try {
			$ch = curl_init();
			$timeout = 5;
			curl_setopt($ch,CURLOPT_URL,$url);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
			$data = curl_exec($ch);		
			curl_close($ch);			
			return $data;
		} catch ( Exception $e ){
				JFactory::getApplication()->enqueueMessage('Curl error on bit.ly request: '.$e->getMessage());
		}
		return false;
    }   
    
    private function get_page_token() {
		$pages = $this->facebook->api('/me/accounts');
		foreach ($pages['data'] as $page) {
			if($page['id'] == $this->pageid) {
				return $page['access_token']; 
			}   
		}
	}
	
	private function getPlgId(){
		// stupid hack since there doesn't seem to be another way to get plugin id
		$db = JFactory::getDBO();
		$sql = 'SELECT `extension_id` FROM `#__extensions` WHERE `element` = "fblisting" AND `folder` = "iproperty"';
		$db->setQuery($sql);
		if( !($plg = $db->loadObject()) ){
			return false;
		} else {
			return (int) $plg->extension_id;
		}
	}
    
}
