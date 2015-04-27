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
        var maptype;
        switch (mapModOptions.maptype){
            case 'SATELLITE':
                maptype = Microsoft.Maps.MapTypeId.aerial;
                break;
            case 'TERRAIN':
                maptype = Microsoft.Maps.MapTypeId.ordinance;
                break;
            case 'ROADMAP':
                maptype = Microsoft.Maps.MapTypeId.road;
                break;
            case 'HYBRID':
            default:
                maptype = Microsoft.Maps.MapTypeId.aerial;
                break;
        }

        var mapInitOpts = {
            credentials: mapModOptions.credentials,
            center: new Microsoft.Maps.Location(mapModOptions.lat, mapModOptions.lng),
            zoom: mapModOptions.zoom,
            mapTypeId: maptype,
            enableSearchLogo: false
        };

        app.locations = new Array(); // for storing the location objects to setBounds

        // clear existing map if it exists from cache
        jQuery("#ip-map-mod-canvas").empty();
        // create map        
        ipMapModFunctions.map = new Microsoft.Maps.Map(document.getElementById("ip-map-mod-canvas"), mapInitOpts);
        resizeMap();

        // set resize window event to reset map when window changed
        jQuery(window).resize(function() {
            resizeMap();
        });

        app.infobox = new Microsoft.Maps.Infobox(new Microsoft.Maps.Location(0, 0), { visible: false, offset: new Microsoft.Maps.Point(0, 20) });
        ipMapModFunctions.map.entities.push(ipMapModFunctions.infobox);

    }

    /**************
    // functions //
    **************/

    function resizeMap() {
        ipMapModFunctions.map.setOptions( { width: jQuery('#ip-map-mod-canvas').css('width'), height: jQuery('#ip-map-mod-canvas').css('height') });
    }

    // function to emulate gmaps setBounds functionality
    function setBounds(){
        var viewRect = Microsoft.Maps.LocationRect.fromLocations(ipMapModFunctions.locations);
        var options = ipMapModFunctions.map.getOptions();
        options.bounds = viewRect;
        ipMapModFunctions.map.setView(options);
        checkZoom();
    }

    // addMarker to map
    app.addMarker = function(listing){
        if (listing.lat_pos == 0 || listing.long_pos == 0 || (listing.show_map != 1)) return;
        var coord = new Microsoft.Maps.Location(parseFloat(listing.lat_pos), parseFloat(listing.long_pos));
        // add location to array
        ipMapModFunctions.locations.push(coord);
        // create marker
        var markerOptions = {};
        markerOptions.draggable = false;
        if (mapModOptions.marker) markerOptions.icon = mapModOptions.ipbaseurl+mapModOptions.marker;
        var marker = new Microsoft.Maps.Pushpin(coord, markerOptions);
        // build the infoWindow
        var infoContent = ipMapModFunctions.buildInfoWindow(listing);
        // add htmlcontent
        marker.htmlcontent = infoContent;
        // add marker to array
        ipMapModFunctions.map.entities.push(marker);
        // add marker to array
        ipMapModFunctions.ipMarkers.push(marker);
        // add click handler
        Microsoft.Maps.Events.addHandler(marker, 'click', ipMapModFunctions.openMarker);
        setBounds();
    }

    app.openMarker = function(e){
        var marker = (e.targetType == 'pushpin') ? e.target : e;
        ipMapModFunctions.infobox.setLocation(marker.getLocation());
        ipMapModFunctions.infobox.setOptions({ visible: true, htmlContent: marker.htmlcontent });
    }

    // check zoom level and set to max if it's zoomed in too far
    function checkZoom(){
        var curzoom = ipMapModFunctions.map.getZoom();
        if (curzoom > mapModOptions.maxZoom) ipMapModFunctions.map.setView( {zoom: mapModOptions.maxZoom} );
    }

    // clear markers from map
    app.clearMap = function(){
        ipMapModFunctions.map.entities.clear();
        ipMapModFunctions.locations.length = 0;
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