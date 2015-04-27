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
            clickResize = map_options.clickResize,
            credentials = map_options.credentials;

        // TODO: this should be set as default in Joomla form
        // removed in v3.2.1 - default field values need to be blank to geocode new location
        //$('#jform_latitude').val(start_lat);
        //$('#jform_longitude').val(start_lon);

        var coord   = new Microsoft.Maps.Location(start_lat, start_lon);

        var mapoptions = {
            zoom: start_zoom,
            center: coord,
            showMapTypeSelector: true,
            showScalebar: false,
            mapTypeId: Microsoft.Maps.MapTypeId.road
        }

        // set css
        $('#'+mapDiv).css( { 'height': '350px', 'width': $('#'+mapDiv).css('width'), 'position': 'relative' }); // TODO -- this may need to be set by CSS or param
        // Initialize the map
        map = new Microsoft.Maps.Map(document.getElementById(mapDiv), mapoptions);

        // Add a pin to the map
        marker = new Microsoft.Maps.Pushpin(coord, {draggable: true});
        map.entities.push(marker);

        Microsoft.Maps.Events.addHandler(marker, 'dragend', function() {
            latlng  = marker.getLocation();
            lat     = latlng.latitude;
            lon     = latlng.longitude;
            $('#jform_latitude').val(lat);
            $('#jform_longitude').val(lon);
        });

		if(clickResize){
            $('a[data-toggle="tab"]').on('show', function(e){
                if ($(e.target).attr('href') == clickResize){
                    setTimeout( function() {
                        resizeMap();
						map.setView({center: coord});
                    }, 10);
                }
            });
        }

        function resizeMap() {
            map.setOptions( { width: $('#'+mapDiv).css('width'), height: $('#'+mapDiv).css('height') });
        }

        map.setView({zoom:start_zoom});
    })
});