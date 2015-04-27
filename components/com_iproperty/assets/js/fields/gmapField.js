/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

jQuery(function($) {
    $(document).ready(function(){
        var map         = null,
            marker      = null,
            start_lat   = map_options.startLat,
            start_lon   = map_options.startLon,
            start_zoom  = map_options.startZoom,
            mapDiv      = map_options.mapDiv,
            clickResize = map_options.clickResize;

        // TODO: this should be set as default in Joomla form
        // removed in v3.2.1 - default field values need to be blank to geocode new location
        //$('#jform_latitude').val(start_lat);
        //$('#jform_longitude').val(start_lon);

        var coord   = new google.maps.LatLng(start_lat, start_lon);

        var mapoptions = {
            zoom: start_zoom,
            center: coord,
            mapTypeControl: true,
            navigationControl: true,
            streetViewControl: false,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }

        // create the map
        map = new google.maps.Map(document.getElementById(mapDiv), mapoptions);
        
        // add KML layer
        if (map_options.kml){
			var kmlLayer = new google.maps.KmlLayer(kml);
			kmlLayer.setMap(map);
		}

        marker  = new google.maps.Marker({
            position: coord,
            draggable: true,
            visible: true,
            clickable: false,
            map: map
        });

        google.maps.event.addListener(marker, 'dragend', function() {
            latlng  = marker.getPosition();
            lat     = latlng.lat();
            lon     = latlng.lng();
            $('#jform_latitude').val(lat);
            $('#jform_longitude').val(lon);
        });

        if(clickResize){
            $('a[data-toggle="tab"]').on('show', function(e){
                if ($(e.target).attr('href') == clickResize){
                    setTimeout( function() {
                        google.maps.event.trigger(map, 'resize');
                        map.setCenter(coord);
                    }, 10);
                }
            });
        }

        map.setZoom( map.getZoom() );
    })
});