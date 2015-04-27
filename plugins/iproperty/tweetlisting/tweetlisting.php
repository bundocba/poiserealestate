<?php
/**
 * tweetpic plugin for iproperty
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2014 the Thinkery
 * @license GNU/GPL see LICENSE.php
 * thanks to Jennifer Swarts for original mod
 */

// no direct access
defined('_JEXEC' ) or die( 'Restricted access');
jimport('joomla.plugin.plugin');

$pluginpath = JPATH_PLUGINS.'/iproperty/tweetlisting';

require $pluginpath.'/assets/tmhOAuth.php';
require_once JPATH_SITE.'/components/com_iproperty/helpers/route.php';

class plgIpropertyTweetlisting extends JPlugin
{
	public function __construct(&$subject, $config)
    {
		parent::__construct($subject, $config);
        $this->loadLanguage();
	}

	public function onAfterSavePropertyEdit($prop_id, $isNew = false)
	{
        $app        = JFactory::getApplication();
        $document   = JFactory::getDocument();
        $db         = JFactory::getDbo();
        $settings   = ipropertyAdmin::config();

        $tweetnew   = $this->params->get('tweetnew', false);
        $tweetupd   = $this->params->get('tweetupdate', false);
        $showimg    = $this->params->get('showimage', true);
        
		//reset message
		$message = '';

        // only tweet new or updated if params are true
        switch ($isNew){
            case 1:
                $message = JText::_('PLG_IP_TWEETLISTING_TWEET_NEW_TEXT');
                if (!$tweetnew) return false;
            break;
            default:
                $message = JText::_('PLG_IP_TWEETLISTING_TWEET_UPDATE_TEXT');
                if (!$tweetupd) return false;
            break;
        }

        // twitter keys / info
        $consumerkey    = $this->params->get('consumer', false);
        $consumersecret = $this->params->get('csecret', false);
        $accesstoken    = $this->params->get('token', false);
        $accesssecret   = $this->params->get('asecret', false);
        $geocode        = $this->params->get('geocode', false);

        // bitly keys-- from http://bitly.com/a/your_api_key
        $bitk   = $this->params->get('bitlykey', false);
		$remote = false;

        // set default lat/lon
        $lat    = '';
        $lon    = '';

        if( !$consumerkey || !$consumersecret || !$accesstoken || !$accesssecret ) return false;

        // get the property
        $query = $db->getQuery(true);
        $query->select('p.*, st.name, c.title as countryname, c.mc_name as countryabbr, s.title as statename, s.mc_name as stateabbr')
              ->from('#__iproperty as p')
              ->leftJoin('#__iproperty_stypes AS st ON p.stype = st.id')
              ->leftJoin('#__iproperty_countries AS c ON p.country = c.id')
              ->leftJoin('#__iproperty_states AS s ON p.locstate = s.id')
              ->where('p.id = '.$db->quote( $prop_id ));
        $db->setQuery($query);
        $property = $db->loadObject();

        if(!$property) return false;
        
        if ($showimg){
            $imgquery = $db->getQuery(true);
            $imgquery->select('fname, type, path, remote, title')
                  ->from('#__iproperty_images')
                  ->where('propid = '.$db->quote( $prop_id ) . 'AND ordering = 1');
            $db->setQuery($imgquery);
            $image = $db->loadObject();

            if(!$image) {
				$image = false;
                $imagefile = false;
                $imagetitle = false;
            } else if ($image->remote) { // check if it's a remote image
                $remote = true;
                $imagefile = $image->path . $image->fname . $image->type;
                $imagetitle = $image->title;
            } else {
                $imagefile = JPATH_SITE . $image->path . $image->fname . $image->type;
                $imagetitle = $image->title;
            }
        } else {
            $image = false;
        }

        $link = JURI::root().ipropertyHelperRoute::getPropertyRoute($prop_id);
        if($bitk) $link = $this->_shortenUrl($link, $bitk);

        $message .= '@ '.$link;

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
			$message .= ' ' . $property->beds .  JText::_('PLG_IP_TWEETLISTING_TWEET_BEDS_TEXT');
		}
		if($this->params->get('showbaths', false)) {
			$message .= ' ' . $property->baths .  JText::_('PLG_IP_TWEETLISTING_TWEET_BATHS_TEXT');
		}
		if($this->params->get('showsqft', false)) {
			$units = (!$settings->measurement_units) ? JText::_( 'PLG_IP_TWEETLISTING_SQFT' ) : JText::_( 'PLG_IP_TWEETLISTING_SQM' );
			$message .= ' '.$property->sqft.' '.$units;
		}
		if($this->params->get('showreduced', false)) {
			if (($property->price2 != "0.00") && ($property->price2 > $property->price)) {
				$message .= " " . JText::_('PLG_IP_TWEETLISTING_TWEET_REDUCED_TEXT');
			}
		}
		
        if($this->params->get('showprice', false)) {
            $price = ipropertyHTML::getFormattedPrice($property->price, $property->stype_freq, false, $property->call_for_price);
            $message .= ' - '.$price;
        }		
        // if we are geolocating this tweet set lat/lon
        if($geocode && $property->latitude && $property->longitude){
            $lat = $property->latitude;
            $lon = $property->longitude;
        }

        /* Create a tmhOauth object with consumer/user tokens. */
		$tmhOAuth= new tmhOAuth( array(
		  'consumer_key'    => $consumerkey,
		  'consumer_secret' => $consumersecret,
		  'token'           => $accesstoken,
		  'secret'          => $accesssecret,
		) );
	
		// if we have a remote image, we need to save it locally.
		if ($remote) {
			if (!function_exists('copy')) $image = false; // set image to false if we can't use copy
			$remotefile = (string) $imagefile;
			$imagefile = tempnam(sys_get_temp_dir(), 'ipimg');
			copy($remotefile, $imagefile);			
		}
        
        // force 140
        if ($this->params->get('forcelength', false)){
            $message = substr($message, 0, 140);
        }
			
        $parameters = array(
			'status' => $message
		);
		
		if ($image){
			$parameters['media'][] = "@{$imagefile};type=image/jpg;filename={$imagetitle}";
		}
				
        if($lat) $parameters['lat'] = $lat;
        if($lon) $parameters['long'] = $lon;
		
		// if we have no image set these params to use different URL / mime type
		$url 		= $image ? '1.1/statuses/update_with_media' : '1.1/statuses/update';
		$multipart 	= $image ? true : false;

		$code = $tmhOAuth->user_request(array(
			'method' => 'POST',
			'url' => $tmhOAuth->url($url),
			'params' => $parameters,
			'multipart' => $multipart,
		));

		if ($code == 200) {
			JFactory::getApplication()->enqueueMessage('Tweeted: '. $parameters['status'] . ' ' . $lat . ' ' . $lon, 'message');
		} else {
			JFactory::getApplication()->enqueueMessage('Error posting to Twitter' . $tmhOAuth->response['response'], 'error');
		}
		
		if ($remote){
			// cleanup tmp image file
			unlink(imagefile);
		}
        return true;
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
}