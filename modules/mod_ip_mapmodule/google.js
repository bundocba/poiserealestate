/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

var ipMapModFunctions = (function(app) {
    // define vars
    app.ipMarkers = [];

    app.buildMap = function(mapModOptions){
        var maptype, infoWindow;
        switch (mapModOptions.maptype){
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
            center: new google.maps.LatLng(mapModOptions.lat, mapModOptions.lng),
            zoom: mapModOptions.zoom,
            mapTypeId: maptype,
            maxZoom: mapModOptions.maxZoom
        };

        // clear existing map if it exists from cache
        jQuery("#ip-map-mod-canvas").empty();
        // create map
        app.map = new google.maps.Map(document.getElementById("ip-map-mod-canvas"), mapInitOpts);
        google.maps.event.trigger(ipMapModFunctions.map, 'resize');
        app.bounds = new google.maps.LatLngBounds();

        // set resize window event to reset map when window changed
        jQuery(window).resize(function() {
            google.maps.event.trigger(ipMapModFunctions.map, 'resize');
        });

        // build infoWindow
        app.infowindow = new google.maps.InfoWindow({
            content: ''
        });
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
        if (mapModOptions.marker) markerOptions.icon = mapModOptions.ipbaseurl+mapModOptions.marker;
        var marker = new google.maps.Marker(markerOptions);
        // add marker to array
        ipMapModFunctions.ipMarkers.push(marker);
        // build the infoWindow
        marker.html = ipMapModFunctions.buildInfoWindow(listing);
        // extend map bounds
        ipMapModFunctions.bounds.extend(markerOptions.position);
        ipMapModFunctions.map.fitBounds(ipMapModFunctions.bounds);
        // check the zoom level
        checkZoom();

        // attach infoWindow opener
        google.maps.event.addListener(marker, 'click', function () {
            app.openMarker(this);
        });
    }

    // clear markers from map
    app.clearMap = function(){
        for (var i = 0; i < ipMapModFunctions.ipMarkers.length; i++ ) {
            ipMapModFunctions.ipMarkers[i].setMap(null);
        }
        // reset array
        app.ipMarkers.length = 0;
        // reset bounds
        app.bounds = new google.maps.LatLngBounds();
        app.map.setZoom(8);
    }

    // check zoom level and set to max if it's zoomed in too far
    function checkZoom(){
        var curzoom = ipMapModFunctions.map.getZoom();
        if (curzoom > mapModOptions.maxZoom) ipMapModFunctions.map.setZoom(mapModOptions.maxZoom);
    }
    
    // generic openmarker function
    app.openMarker = function(marker){
        ipMapModFunctions.infowindow.setContent(marker.html);
        ipMapModFunctions.infowindow.open(ipMapModFunctions.map, marker);
    }
	
	// build marker HTML
    app.buildInfoWindow = function(listing){
        // remove line breaks from banner
        listing.banner = listing.banner.replace(/(\r\n|\n|\r)/gm," ");
        var contentContainer = jQuery('<div />');
        var contentString = '<div class="row-fluid ip-bubble-window">' +
                                '<div class="span5 ip-overview-img"><div class="ip-property-thumb-holder"><a href="'+listing.proplink+'">'+listing.thumb+'</a>'+listing.banner+'</div></div>' +
                                '<div class="span7">' +
                                '<h4><a href="'+listing.proplink+'">'+listing.street_address+', '+listing.city+'</a></h4>' +
                                '<div class="small"><strong>'+langOptions.pid+': </strong>'+listing.mls_id+' | <strong>'+langOptions.price+': </strong>'+listing.formattedprice+'</div>' +
                                '<p class="ip-bubble-desc">'+listing.short_description.slice(0,185).trim()+'...'+'<div class="ip-bubble-cats">'+listing.caticons.join(' ')+'</div><a href="'+listing.proplink+'">('+langOptions.more+')</a></p>' +
                                '</div>' +
                            '</div>';
        contentContainer.html(contentString);
        return contentContainer.html();
    };
    
    return app;
})(ipMapModFunctions || {});
