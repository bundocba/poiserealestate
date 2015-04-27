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

class plgIpropertyFblike extends JPlugin
{	
	public function __construct(&$subject, $config)  
    {
		parent::__construct($subject, $config);
        $this->loadLanguage();
	}
    
    public function onAfterRenderProperty($property, $settings)
	{
        if ($this->params->get('position', 'bottom') == 'bottom') $this->_doFbLike($property, $settings);
	}

    public function onBeforeRenderProperty($property, $settings)
	{
        if ($this->params->get('position', 'top') == 'top') $this->_doFbLike($property, $settings);
	}

	private function _doFbLike($property, $settings)
	{
        $app        = JFactory::getApplication();
        $document   = JFactory::getDocument();
        
		if($app->getName() != 'site') return true;

		$uri 	= JFactory::getURI();
		$thumb	= ipropertyHTML::getThumbnail($property->id, '', '', '', '', '', true, false, false);

		$document->setMetaData('og:title', $property->street_address);
        $document->setMetaData('og:site_name', $app->getCfg('sitename'));
		$document->setMetaData('og:image', $thumb);

        echo '<div id="plg_ip_facebook">';
        echo '<iframe id="ip_facebook" src="//www.facebook.com/plugins/like.php?href='.urlencode($uri);
        echo '&amp;layout='.$this->params->get('layout');
        echo '&amp;show_faces='.($this->params->get('show_faces') == 'yes' ? 'true' : 'false');
        echo '&amp;width='.$this->params->get('width');
        echo '&amp;action='.$this->params->get('verb');
        echo '&amp;font='.urlencode($this->params->get('font'));
        echo '&amp;colorscheme='.$this->params->get('color_scheme').'"';
        echo ' scrolling="no"';
        echo ' frameborder="0"';
        echo ' allowTransparency="true"';
        echo ' style="border:none; overflow:hidden; width:'.$this->params->get('width').'px; height:'.$this->params->get('height').'px">';
        echo JText::_('Your browser does not support Iframes!');
        echo '</iframe>';
        echo '</div>';

        return true;
	}
}
