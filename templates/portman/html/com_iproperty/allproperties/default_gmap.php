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

// check for template map icons
$templatepath       = JFactory::getApplication()->getTemplate();
$map_house_icon     = JURI::root(true).'/components/com_iproperty/assets/images/map/icon56.png';
if(JFile::exists('templates/'.$templatepath.'/images/iproperty/map/icon56.png')) $map_house_icon = JURI::root(true).'/templates/'.$templatepath.'/images/iproperty/map/icon56.png';

// get URL scheme
$scheme				= JURI::getInstance()->getScheme();

$this->document->addScript($scheme.'://maps.google.com/maps/api/js?sensor=false');

$map_js = 'var locations = [];';

foreach($this->items as $item){
	if($item->lat_pos && $item->long_pos){
		$map_js .= 'locations.push(['.$item->lat_pos.','.$item->long_pos.','.$item->id.']);';	
	}
}

$map_js .='
jQuery(function($) {
    $(window).load(function(){       
        var width = $("#ip_mainheader").css("width");
        var height = 300;
        var location = new google.maps.LatLng('.$this->settings->adv_default_lat.','.$this->settings->adv_default_long.');
        var mapoptions = {
            zoom: '.$this->settings->adv_default_zoom.',
            center: location,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            maxZoom: '.$this->settings->max_zoom.'
        }
        if(locations.length){
			var bounds = new google.maps.LatLngBounds();
			$("#ip-map-canvas").css({"width": width, "height": height});
			var map = new google.maps.Map(document.getElementById("ip-map-canvas"), mapoptions);
			google.maps.event.trigger(map, "resize");
			map.setCenter(location);
			$.each(locations, function(i, el){
                if (el[0] == 0 || el[1] == 0) return;
				var position = new google.maps.LatLng(el[0],el[1]);
				bounds.extend(position);
				map.fitBounds(bounds);
				var marker = new google.maps.Marker({
					position: position,
					map: map,
					draggable: false,
					icon: \''.$map_house_icon.'\'
				})
                
                // attach infoWindow opener
                google.maps.event.addListener(marker, \'click\', function () {
                    $(\'.ip-overview-row\').removeClass(\'ip-overview-active\');
                    $(\'html,body\').animate({
                         scrollTop: jQuery(\'[id=ip-listing-\'+el[2]+\']\').offset().top
                    }, 500); 
                    $(\'[id=ip-listing-\'+el[2]+\']\').addClass(\'ip-overview-active\');
                });
				checkZoom();
			});
		}
		
		// check zoom level and set to max if zoomed in too far
		function checkZoom(){
			var curzoom = map.getZoom();
			if (curzoom > 10) map.setZoom(8);
		}
    });
});';
$this->document->addScriptDeclaration( $map_js );
echo '
    <div id="ip-map-canvas" class="ip-map-div"></div>
    <div class="clearfix"></div>
    ';
?>
