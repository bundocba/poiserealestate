/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

var ipPropertyMap = (function(app) {
    
    // directions vars
    app.directionsService 	= new google.maps.DirectionsService();
	app.directionsDisplay 	= new google.maps.DirectionsRenderer();
	
	// startpoint location
	ipPropertyMap.startpoint = false;
    
    // directions functions
    app.getDirections = function(){	
        if (!ipPropertyMap.map) return false;   
        if (!ipPropertyMap.startpoint) ipPropertyMap.startpoint = jQuery('#origin').val();
        if (!ipPropertyMap.startpoint) {
            jQuery('#ip-directions-messagetext').html(directionOptions.startrequired);
            jQuery('#ip-directions-message').toggle('fast');
            return false;
        }
        var request = {
            origin: ipPropertyMap.startpoint,
            destination: ipPropertyMap.location,
            travelMode: google.maps.DirectionsTravelMode.DRIVING,
            unitSystem: directionOptions.unit
        };
        ipPropertyMap.directionsService.route(request, function(response, status) {
            if (status === google.maps.DirectionsStatus.OK) {
				gotDir = true;
                ipPropertyMap.directionsDisplay.setDirections(response);
                if (directionOptions.displayMap){
                    ipPropertyMap.directionsDisplay.setMap(ipPropertyMap.map);
                    ipPropertyMap.map.fitBounds(response.routes[0].bounds);
                }
            } else {
                jQuery('#ip-directions-messagetext').html(directionOptions.notfound);
                jQuery('#ip-directions-message').toggle('fast');
                return false;
            }
        });
    };
    
	app.doGeoSuccess = function(position) {	
		// do reverse geocode
		var geocoder = new google.maps.Geocoder();
		var latlng = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
		geocoder.geocode({'latLng': latlng}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
			  if (results[1]) {
				jQuery('#origin').val(results[1].formatted_address);
			  }
			}
		  });	
	}

    return app;
})(ipPropertyMap || {});
