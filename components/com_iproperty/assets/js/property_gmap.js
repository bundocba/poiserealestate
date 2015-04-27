/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

var ipPropertyMap = (function(app) {
    // define vars
    app.map					= false;
    app.location 			= false;
    
    app.buildMap = function(){
		
		ipPropertyMap.location = new google.maps.LatLng(ipmapoptions.lat,ipmapoptions.lon);
		
        app.width   = jQuery(".ip-mapleft").css("width");
        app.height  = jQuery(".ip-prop-top").css("height");
        
        var mapoptions = {
            zoom: ipmapoptions.zoom,
            center: ipPropertyMap.location,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            maxZoom: ipmapoptions.maxZoom,
            streetViewControl: false
        };
        ipPropertyMap.map = new google.maps.Map(document.getElementById("ip-map-canvas"), mapoptions); 
        
        // add KML layer
        if (ipmapoptions.kml){
			var kmlLayer = new google.maps.KmlLayer(ipmapoptions.kml);
			kmlLayer.setMap(ipPropertyMap.map);
		}
        
        google.maps.event.trigger(ipPropertyMap.map, "resize");
        ipPropertyMap.map.setCenter(ipPropertyMap.location);
        
        // don't add the marker if there's a KML file supplied
        if (!ipmapoptions.kml){
            var marker = new google.maps.Marker({
                position: ipPropertyMap.location,
                map: ipPropertyMap.map,
                draggable: false,
                icon: ipmapoptions.mapicon
            });
        }
        
        jQuery('a[href="#propmap"]').on("shown", function(e) {
            jQuery("#ip-map-canvas").css({ "width": app.width, "height": app.height });
            google.maps.event.trigger(ipPropertyMap.map, "resize");
            ipPropertyMap.map.setCenter(ipPropertyMap.location);
        });
        
        // set resize window event to reset map when window changed
        jQuery(window).resize(function() {
            google.maps.event.trigger(ipPropertyMap.map, 'resize');
        });
    };

    return app;
})(ipPropertyMap || {});
