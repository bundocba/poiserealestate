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
        var maptype;
        switch (mapOptions.maptype){
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
            credentials: mapOptions.credentials,
            center: new Microsoft.Maps.Location(mapOptions.lat, mapOptions.lng),
            zoom: mapOptions.zoom,
            mapTypeId: maptype,
            enableSearchLogo: false
        };

        app.locations = new Array(); // for storing the location objects to setBounds

        // clear existing map if it exists from cache
        jQuery("#ip-map-canvas").empty();
        // create map        
        ipMapFunctions.map = new Microsoft.Maps.Map(document.getElementById("ip-map-canvas"), mapInitOpts);
        resizeMap();

        // set resize window event to reset map when window changed
        jQuery(window).resize(function() {
            resizeMap();
        });

        app.infobox = new Microsoft.Maps.Infobox(new Microsoft.Maps.Location(0, 0), { visible: false, offset: new Microsoft.Maps.Point(0, 20) });
        ipMapFunctions.map.entities.push(ipMapFunctions.infobox);

		// build map tools
		if(mapOptions.maptools){
			Microsoft.Maps.registerModule("ShapeToolboxModule", mapOptions.ipbaseurl+"components/com_iproperty/assets/js/bingTools/scripts/ShapeToolboxModule.js");
			Microsoft.Maps.loadModule("ShapeToolboxModule", { callback: ipMapFunctions.buildTools });
		}
    }

    /**************
    // functions //
    **************/

    function resizeMap() {
        ipMapFunctions.map.setOptions( { width: jQuery('#ip-map-canvas').css('width'), height: jQuery('#ip-map-canvas').css('height') });
    }

    // function to emulate gmaps setBounds functionality
    function setBounds(){
        var viewRect = Microsoft.Maps.LocationRect.fromLocations(ipMapFunctions.locations);
        var options = ipMapFunctions.map.getOptions();
        options.bounds = viewRect;
        ipMapFunctions.map.setView(options);
        checkZoom();
    }

    // addMarker to map
    app.addMarker = function(listing){
        if (listing.lat_pos == 0 || listing.long_pos == 0 || (listing.show_map != 1)) return;
        var coord = new Microsoft.Maps.Location(parseFloat(listing.lat_pos), parseFloat(listing.long_pos));
        // add location to array
        ipMapFunctions.locations.push(coord);
        // create marker
        var markerOptions = {};
        markerOptions.draggable = false;
        if (mapOptions.marker) markerOptions.icon = mapOptions.ipbaseurl+mapOptions.marker;
        var marker = new Microsoft.Maps.Pushpin(coord, markerOptions);
        // build the infoWindow
        var infoContent = ipMapFunctions.buildInfoWindow(listing);
        // add htmlcontent
        marker.htmlcontent = infoContent;
        // add marker to array
        ipMapFunctions.map.entities.push(marker);
        // add marker to array
        ipMapFunctions.ipMarkers.push(marker);
        // add click handler
        Microsoft.Maps.Events.addHandler(marker, 'click', ipMapFunctions.openMarker);
        setBounds();
    }

    app.openMarker = function(e){
        var marker = (e.targetType == 'pushpin') ? e.target : e;
        ipMapFunctions.infobox.setLocation(marker.getLocation());
        ipMapFunctions.infobox.setOptions({ visible: true, htmlContent: marker.htmlcontent });
    }

    // check zoom level and set to max if it's zoomed in too far
    function checkZoom(){
        var curzoom = ipMapFunctions.map.getZoom();
        if (curzoom > mapOptions.maxZoom) ipMapFunctions.map.setView( {zoom: mapOptions.maxZoom} );
    }

    // clear markers from map
    app.clearMap = function(){
        ipMapFunctions.map.entities.clear();
        ipMapFunctions.locations.length = 0;
    }
    return app;
})(ipMapFunctions || {});