<?php
/**
 * @version 2.0 2012-06-18
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

// no direct access
defined('_JEXEC' ) or die( 'Restricted access');
jimport('joomla.plugin.plugin');

class plgIpropertyTweetthis extends JPlugin
{
	public function __construct(&$subject, $config)  
    {
		parent::__construct($subject, $config);
        $this->loadLanguage();
	}
    
    public function onAfterRenderProperty($property, $settings)
	{
        if ($this->params->get('position', 'bottom') == 'bottom') $this->_doTweetThis($property, $settings);
	}

    public function onBeforeRenderProperty($property, $settings)
	{
        if ($this->params->get('position', 'top') == 'top') $this->_doTweetThis($property, $settings);
	}

	private function _doTweetThis($property, $settings)
	{
        $app = JFactory::getApplication();
        $doc = JFactory::getDocument();
        
        if($app->getName() != 'site') return true;
        
        $proplink       = urlencode(JURI::root().$property->proplink);
        $title          = $this->params->get('tweettitle', 'Tweet');
        $via            = $this->params->get('tweetvia', '');
        $hashtags       = $this->params->get('tweethashtags', false);
        $lang           = $this->params->get('tweetlang', 'en');
        $topmargin      = $this->params->get('topmargin', 10);
        $rightmargin    = $this->params->get('rightmargin', 10);
        
        // make sure the hashtags are trimmed properly
        $tags = array_map('trim', explode(",", $hashtags));
        $tags = implode(',', $tags);
        
        $tweetscript = '!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");';

        $doc->addScriptDeclaration( $tweetscript );

        echo '
                <div class="ip-tweet-this" style="position: absolute; top: '.(int)$topmargin.'px; right: '.(int)$rightmargin.'px;">            
                    <a href="https://twitter.com/share" class="twitter-share-button" data-url="'.$proplink.'" data-via="'.$via.'" data-lang="'.$lang.'" data-hashtags="'.$tags.'">'.$title.'</a>
                </div>';
        
        return true;
	}
}