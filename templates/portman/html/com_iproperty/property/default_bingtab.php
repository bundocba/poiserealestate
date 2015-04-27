<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
$curr_lang  = JFactory::getLanguage();

// get URL scheme
$scheme				= JURI::getInstance()->getScheme();

$this->document->addScript($scheme.'://ecn.dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=7.0&mkt='.$curr_lang->get('tag') );

$map_js ='
jQuery(function($) {
    var width, height;
    $(window).load(function(){
        var center = new Microsoft.Maps.Location(ipmapoptions.lat,ipmapoptions.lon);
        
        var width = $(".ip-mapleft").css("width");
        var height = $(".ip-prop-top").css("height");
        
        var mapoptions = {
            center: center, 
            credentials:"'.$this->settings->map_credentials.'", 
            mapTypeId: Microsoft.Maps.MapTypeId.road, 
            zoom: ipmapoptions.zoom,
            enableSearchLogo: false
        }

        $("#ip-map-canvas").css({ "position": "relative", width: width, height: height });
        map = new Microsoft.Maps.Map(document.getElementById("ip-map-canvas"), mapoptions);
        var pin = new Microsoft.Maps.Pushpin(center, { icon: ipmapoptions.mapicon, draggable: false});
        map.entities.push(pin);
        '."\n";

$map_js .= '
    function resizeMap() {
        map.setOptions( { width: width, height: height });
    }
    $(\'a[href="#propmap"]\').on("shown", function(e) {
        resizeMap();
    });'."\n";

$map_js .=' });
});';
$this->document->addScriptDeclaration( $map_js );
echo '<div id="ip-map-canvas" class="ip-map-div"></div>';
?>
