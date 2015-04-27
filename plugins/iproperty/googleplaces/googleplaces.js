/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

var ipPropertyMap = (function(app) {
    
    // gplaces functions
    app.doGplaces = function(){
        if (!ipPropertyMap.map) return false;
		var gpBnds;
		// if we are using gplaces plugin create the service
		if (ipmapoptions.places) {
			ipPropertyMap.gplacesService = new google.maps.places.PlacesService(ipPropertyMap.map); 
		} else {
			return false;
		}
		
		var placeRq = {
			location: ipPropertyMap.location,
			radius: gplacesOptions.distance,
			types: gplacesOptions.types,
			language: gplacesOptions.language
		};
   
		ipPropertyMap.gplacesService.search(placeRq, placesCallback);
	};
	
	function placesCallback(results, status) {
        if (!ipPropertyMap.map) return false;
		if (status === google.maps.places.PlacesServiceStatus.OK) {
			for (var i = 0; i < results.length; i++) {
				var place = results[i];
				createPlace(place);
			}
		} else if(status === google.maps.places.PlacesServiceStatus.ZERO_RESULTS) {
			var placesrow = jQuery('<tr><td colspan="4">'+gplacesOptions.noresults+'</td></tr>').appendTo('#ipgplacestable tbody');
		}
	}
	
	function getDistance(place){
        if (!ipPropertyMap.map) return false;
		if (typeof(Number.prototype.toRad) === "undefined") {
		  Number.prototype.toRad = function() {
			return this * Math.PI / 180;
		  };
		}

		// start point
		var lat1 = Number(ipmapoptions.lat);
		var lon1 = Number(ipmapoptions.lon);
		// end point
		var lat2 = Number(place.geometry.location.lat());
		var lon2 = Number(place.geometry.location.lng());

		var R 		= gplacesOptions.radius; // switch depending on measurement units
		var dLat 	= (lat2-lat1).toRad();
		var dLon 	= (lon2-lon1).toRad();
		var lat1 	= lat1.toRad();
		var lat2 	= lat2.toRad();

		var a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.sin(dLon/2) * Math.sin(dLon/2) * Math.cos(lat1) * Math.cos(lat2);
		var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
		var d = R * c;

		return Math.round(d*100)/100;
	}
	
	// build the marker object and table rows
	function createPlace(place){
        if (!ipPropertyMap.map) return false;
		var type        = place.types[0];
		type            = type.replace('_', ' ');
		type            = type.clean();
		type            = type.capitalize();
		var gpmarker    = new google.maps.MarkerImage(place.icon, null, null, null, new google.maps.Size(25, 25));
		var marker      = new google.maps.Marker({
		  map: ipPropertyMap.map,
		  position: place.geometry.location,
		  title: place.name,
		  icon: gpmarker
		});
		var placesrow = jQuery('<tr><td>'+place.name+'</td><td>'+place.vicinity+'</td><td>'+type+'</td><td>'+getDistance(place)+' '+gplacesOptions.radiustag+'</td></tr>').appendTo('#ipgplacestable tbody');
	}

    return app;
})(ipPropertyMap || {});
