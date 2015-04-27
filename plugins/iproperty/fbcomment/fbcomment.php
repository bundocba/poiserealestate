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

class plgIpropertyFbcomment extends JPlugin
{
    public function __construct(&$subject, $config)  
    {
        parent::__construct($subject, $config);
        $this->loadLanguage();
    }

    public function onBeforeRenderForms($property, $settings)
    {
		if ($this->params->get('position')) return true; 
        $this->_doCommentTab($property, $settings);
    }
	
	public function onAfterRenderForms($property, $settings)
    {
        if (!$this->params->get('position')) return true; 
        $this->_doCommentTab($property, $settings);
    }

	private function _doCommentTab($property, $settings)
	{
		$app        = JFactory::getApplication();
        $document   = JFactory::getDocument();
        
        if($app->getName() != 'site') return true;
        
        $posts  = (int) $this->params->get('number', 5);
        $scheme = (boolean) $this->params->get('scheme') ? 'dark' : 'light';
        $width  = (int) $this->params->get('width', 600);

        // check for moderator/appid
        $userid = (string) $this->params->get('moderator', 0);
        $appid  = (string) $this->params->get('app_id', 0);

        // add moderator/appid
        if ($userid) {
            $mod_tag = '<meta property="fb:admins" content="'.$userid.'"/>';
            $document->addCustomTag($mod_tag);
        } else if ($appid) {
            $app_tag = '<meta property="fb:app_id" content="'.$appid.'"/>';
            $document->addCustomTag($app_tag);
        }
		
		$document->addScript(JURI::root( true ).'/plugins/iproperty/fbcomment/fbcomment.js');
		$script  = "jQuery(window).load(function($){    
                    ipPropertyPlug.buildPlug();
                });";
		$document->addScriptDeclaration($script);

        echo JHtmlBootstrap::addTab('ipDetails', 'ipfbcommentplug', JText::_($this->params->get('tabtitle', 'PLG_IP_FBCOMMENT_COMMENTS')));
            ?>
            <div id="fb-root"></div> 
            <script src="//connect.facebook.net/en_US/all.js#xfbml=1"></script>
            <fb:comments href="<?php echo JURI::getInstance()->toString(); ?>" num_posts="<?php echo $posts; ?>" width="<?php echo $width; ?>" colorscheme="<?php echo $scheme; ?>"></fb:comments>
            <?php
        echo JHtmlBootstrap::endTab();
        
        return true;	
	}
}
