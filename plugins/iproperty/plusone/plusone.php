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

class plgIpropertyPlusone extends JPlugin
{	
	public function __construct(&$subject, $config)  
    {
		parent::__construct($subject, $config);
        $this->loadLanguage();
	}

	public function onAfterRenderProperty($property, $settings)
	{
        if ($this->params->get('position', 'bottom') == 'bottom') $this->_doPlusOne();
	}

    public function onBeforeRenderProperty($property, $settings)
	{
        if ($this->params->get('position', 'top') == 'top') $this->_doPlusOne();
	}
    
    private function _doPlusOne()
    {
        $app            = JFactory::getApplication();
        $document       = JFactory::getDocument();
        
		if($app->getName() != 'site') return true;
        
        $topmargin      = $this->params->get('topmargin', 10);
        $rightmargin    = $this->params->get('rightmargin', 10);
        $size           = ($this->params->get('size', '')) ? ' size="'.$size.'"' : '';
        $position       = $this->params->get('position', 'bottom');
        
        $topstyle = ($position == 'top') ? ' style="position: absolute; top: '.(int)$topmargin.'px; right: '.(int)$rightmargin.'px;"' : '';

        $document->addScript("https://apis.google.com/js/plusone.js");

        echo '
                <div class="ip-plusone-'.$position.'" id="plg_ip_plusone"'.$topstyle.'>
                    <g:plusone'.$size.'></g:plusone>
                </div>';

        return true;
    }
}