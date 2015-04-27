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
jimport('joomla.filesystem.file');

$curr_lang  = JFactory::getLanguage();

// check for template map icons
$templatepath       = JFactory::getApplication()->getTemplate();
$map_house_icon     = JURI::root(true).'/components/com_iproperty/assets/images/map/icon56.png';
if(JFile::exists('templates/'.$templatepath.'/images/iproperty/map/icon56.png')) $map_house_icon = JURI::root(true).'/templates/'.$templatepath.'/images/iproperty/map/icon56.png';

// get URL scheme
$scheme				= JURI::getInstance()->getScheme();

$this->document->addScript($scheme.'://ecn.dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=7.0&mkt='.$curr_lang->get('tag') );

$map_js = 'var locations = [];';

foreach($this->items as $item){
    if($item->latitude && $item->longitude){
        $map_js .= 'locations.push(new Microsoft.Maps.Location('.$item->latitude.','.$item->longitude.'));';
    }
}

$map_js .='
jQuery(function($) {
    var width, height;
    $(window).load(function(){
        var location = new Microsoft.Maps.Location('.$this->settings->adv_default_lat.','.$this->settings->adv_default_long.');
        var width = $("#ip_mainheader").css("width");
        var height = 300;
        var mapoptions = {
            zoom: '.$this->settings->adv_default_zoom.',
            credentials:"'.$this->settings->map_credentials.'",
            center: location,
            mapTypeId: Microsoft.Maps.MapTypeId.road,
            enableSearchLogo: false
        }
        if(locations.length){
            $("#ip-map-canvas").css({ "position": "relative", width: width, height: height });
            var map = new Microsoft.Maps.Map(document.getElementById("ip-map-canvas"), mapoptions);
            $.each(locations, function(i, el){
                if (el[0] == 0 || el[1] == 0) return;
                var position = el;
                var marker = new Microsoft.Maps.Pushpin( position, {
                    draggable: false,
                    icon: \''.$map_house_icon.'\'
                })
                map.entities.push(marker);
                checkZoom();
                setBounds();
            });

            // function to emulate gmaps setBounds functionality
            function setBounds(){
                var viewRect = Microsoft.Maps.LocationRect.fromLocations(locations);
                var options = map.getOptions();
                options.bounds = viewRect;
                map.setView(options);
                checkZoom();
            }

            // check zoom level and set to max if zoomed in too far
            function checkZoom(){
                var curzoom = map.getZoom();
                //if (curzoom > mapOptions.maxZoom) map.setView( {zoom: mapOptions.maxZoom} );
            }

        }
        '."\n";

$map_js .= '
    function resizeMap() {
        map.setOptions( { width: width, height: height });
    }'."\n";

$map_js .=' });
});';
$this->document->addScriptDeclaration( $map_js );
echo '
    <div id="ip-map-canvas" class="ip-map-div"></div>
    <div class="clearfix"></div>
    ';
?>
