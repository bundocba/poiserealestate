/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

var ipMapFunctions = (function(app) {
    // define vars
    app.ipMarkers = [];

    app.buildMap = function(mapOptions){
        var maptype, infoWindow;
        switch (mapOptions.maptype){
            case 'SATELLITE':
                maptype = google.maps.MapTypeId.SATELLITE;
                break;
            case 'TERRAIN':
                maptype = google.maps.MapTypeId.TERRAIN;
                break;
            case 'ROADMAP':
                maptype = google.maps.MapTypeId.ROADMAP;
                break;
            case 'HYBRID':
            default:
                maptype = google.maps.MapTypeId.HYBRID;
                break;
        }

        var mapInitOpts = {
            center: new google.maps.LatLng(mapOptions.lat, mapOptions.lng),
            zoom: mapOptions.zoom,
            mapTypeId: maptype,
            maxZoom: mapOptions.maxZoom
        };

        // clear existing map if it exists from cache
        jQuery("#ip-map-canvas").empty();
        // create map
        app.map = new google.maps.Map(document.getElementById("ip-map-canvas"), mapInitOpts);
        google.maps.event.trigger(ipMapFunctions.map, 'resize');
        app.bounds = new google.maps.LatLngBounds();

        // set resize window event to reset map when window changed
        jQuery(window).resize(function() {
            google.maps.event.trigger(ipMapFunctions.map, 'resize');
        });

        // build infoWindow
        app.infowindow = new google.maps.InfoWindow({
            content: ''
        });
		
		// build map tools
		if(mapOptions.maptools){
			ipMapFunctions.buildTools();
		}
		
		// if we're using clustering, set cluster params and attach
		if(mapOptions.mapcluster){
			//var mcOptions = { gridSize: 50, maxZoom: 15 };
			var mcOptions 	= {};
			app.mc = new MarkerClusterer(app.map, [], mcOptions);
		}
    }

    /**************
    // functions //
    **************/

    // addMarker to map
    app.addMarker = function(listing){
        if (listing.lat_pos == 0 || listing.long_pos == 0 || (listing.show_map != 1)) return;
        var markerOptions = {};
        markerOptions.map = app.map;
        markerOptions.position = new google.maps.LatLng(parseFloat(listing.lat_pos), parseFloat(listing.long_pos));
        if (mapOptions.marker) markerOptions.icon = mapOptions.ipbaseurl+mapOptions.marker;
        var marker = new google.maps.Marker(markerOptions);
        // add marker to array
        ipMapFunctions.ipMarkers.push(marker);
        // build the infoWindow
        marker.html = ipMapFunctions.buildInfoWindow(listing);
        // extend map bounds
        ipMapFunctions.bounds.extend(markerOptions.position);
        ipMapFunctions.map.fitBounds(ipMapFunctions.bounds);
        // check the zoom level
        checkZoom();

        // attach infoWindow opener
        google.maps.event.addListener(marker, 'click', function () {
            jQuery('.ip-adv-row').removeClass('ip-overview-active');
            app.openMarker(this);
            // set active class to show which listing is being viewed and change 
            // button color to show it's been viewed already
            jQuery('#ip-adv-mappreview-'+listing.id).addClass('btn-success');
            jQuery('#ip-adv-listing-'+listing.id).addClass('ip-overview-active');
        });
    }

    // clear markers from map
    app.clearMap = function(){
        for (var i = 0; i < ipMapFunctions.ipMarkers.length; i++ ) {
            ipMapFunctions.ipMarkers[i].setMap(null);
        }
        // reset array
        app.ipMarkers.length = 0;
        // reset bounds
        app.bounds = new google.maps.LatLngBounds();
        app.map.setZoom(8);
    }

    // check zoom level and set to max if it's zoomed in too far
    function checkZoom(){
        var curzoom = ipMapFunctions.map.getZoom();
        if (curzoom > mapOptions.maxZoom) ipMapFunctions.map.setZoom(mapOptions.maxZoom);
    }
    
    // generic openmarker function
    app.openMarker = function(marker){
        ipMapFunctions.infowindow.setContent(marker.html);
        ipMapFunctions.infowindow.open(ipMapFunctions.map, marker);
    }
    
    return app;
})(ipMapFunctions || {});