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

class plgIpropertyStreetViewTab extends JPlugin
{
	public function __construct(&$subject, $config)  
    {
		parent::__construct($subject, $config);
        $this->loadLanguage();
	}

	public function onAfterRenderMap($property, $settings)
	{
        $app        = JFactory::getApplication();
        $document   = JFactory::getDocument();
        
		if($app->getName() != 'site') return true;
        if(!$property->lat_pos || !$property->long_pos || !$property->show_map || $settings->map_provider != 1) return true;
        
        // load the javascript stub
        $document->addScript(JURI::root(true).'/plugins/iproperty/streetviewtab/streetview.js');
        
        $sv_script  = "jQuery(window).load(function($){
                        ipPropertyMap.doStreetView();
                    });"."\n";
        $document->addScriptDeclaration($sv_script);
        
        echo JHtmlBootstrap::addTab('ipMap', 'ipstreetviewplug', JText::_($this->params->get('tabtitle', 'PLG_IP_SV_STREETVIEW')));
            echo '<div id="ippano"></div>';
        echo JHtmlBootstrap::endTab();
        
		return true;
	}
}