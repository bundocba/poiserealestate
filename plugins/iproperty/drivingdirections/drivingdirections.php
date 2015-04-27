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

class plgIpropertyDrivingDirections extends JPlugin
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
        if((!$property->lat_pos || !$property->long_pos) || $property->hide_address || !$property->show_map) return true;

        $unit = ($settings->measurement_units) ? 'google.maps.UnitSystem.METRIC' : 'google.maps.UnitSystem.IMPERIAL';
        
        // load the javascript stub
        $document->addScript(JURI::root(true).'/plugins/iproperty/drivingdirections/directions.js');
        $use_gps = ( $this->params->get('use_gps', 1) && JFactory::getApplication()->client->mobile ) ? 1 : 0;
        
        $svscript = "
		var gotDir = false;
        var directionOptions = {
            displayMap: ".$this->params->get('display_map', 1).",
            useGPS: ".$use_gps.",
            unit: ".$unit.",
            notfound: '".addslashes(JText::_('PLG_IP_DD_ADDRESS_NOT_FOUND'))."',
            startrequired: '".addslashes(JText::_('PLG_IP_DD_STARTREQUIRED'))."'
        }
		
		function printDirections(){
			if (!gotDir) return;
			var printWin = window.open();
			var contents = document.getElementById('ip-directions-display');
			printWin.document.write(contents.innerHTML);
			printWin.print();
		}
            
        jQuery(document).ready(function($){            
            var displayDiv  = document.getElementById('ip-directions-display');
            var ddheight    = $('.ip-prop-top').css('height');
            
            $('#ip-directions-wrapper').css({ 'height': ddheight, 'overflow': 'auto' });
            ipPropertyMap.directionsDisplay.setPanel(displayDiv);

            // hook up click event
            $('#directionsgo').click(function(){
                $('#ip-directions-display').empty();
                ipPropertyMap.getDirections();
                $('#ip-directions-print').css({'display':'inline-block'});
            });
            
            // try getting geolocation
			if (directionOptions.useGPS){		
				if (navigator.geolocation) {
				  navigator.geolocation.getCurrentPosition(ipPropertyMap.doGeoSuccess);
				}
			}

            $('#ip-directions-close').click(function(){
                $('#ip-directions-message').toggle('fast');
                $('#ip-directions-print').css({'display':'none'});
            });
			
			$('#ip-directions-print').click(function(){
                printDirections();
            });
         });"."\n"; 

        $document->addScriptDeclaration($svscript);

        echo JHtmlBootstrap::addTab('ipMap', 'ipdirectionsplug', JText::_($this->params->get('tabtitle', 'PLG_IP_DD_DIRECTIONS')));
            ?>
            <div id="ip-directions-wrapper" align="center" style="padding: 8px;">
                <div>
                    <?php echo JText::_('PLG_IP_DD_DRIVING_INSTRUCTIONS'); ?><br />
                    <div id="ip-directions-message" class="alert" style="display: none;">
                        <button id="ip-directions-close" class="close" type="button">&times;</button>
                        <span id="ip-directions-messagetext"></span>
                    </div>
                    <div class="input-append">
                        <input id="origin" type="text" class="input-medium" size="40" maxlength="60" value="" />
                        <button id="directionsgo" class="btn" type="button"><span class="icon icon-search"></span></button>
                        <button id="ip-directions-print" class="btn btn-info hasTooltip" title="<?php echo JText::_('PLG_IP_DD_PRINT'); ?>" type="button" style="display: none;"><span class="icon icon-print"></span></button>
                    </div>
                </div>
                <div id="ip-directions-display"></div>
            </div>
            <?php
        echo JHtmlBootstrap::endTab();
        
		return true;
	}
}
