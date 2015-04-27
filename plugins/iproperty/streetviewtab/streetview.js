/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

var ipPropertyMap = (function(app) {
    
    // streetview vars
    app.svService = new google.maps.StreetViewService();
    
    // streetview functions
    app.doStreetView = function(){
        if (!ipPropertyMap.map) return false;
        var panoramaElement = document.getElementById("ippano");
        ipPropertyMap.svService.getPanoramaByLocation(ipPropertyMap.location, 25, function(data, status){
            switch(status){
                case google.maps.StreetViewStatus.OK:
                    jQuery('a[href="#ipstreetviewplug"]').on("shown", function(e) {
                        jQuery("#ippano").css({ "width": app.width, "height": app.height, "display": "block" }); 
                        setTimeout(function() {
                            var panorama = new google.maps.StreetViewPanorama(panoramaElement, {
                                position: ipPropertyMap.location
                            });                           
                            google.maps.event.trigger(panorama, "resize");
                        }, (10));
                    });
                    break;
                case google.maps.StreetViewStatus.ZERO_RESULTS:
					jQuery("#ipMapTabs li").has("a[href=#ipstreetviewplug]").hide();
                    jQuery("#ipstreetviewplug").hide();
                    break;
                default:
                    jQuery("#ippano").css("display", "none");
            }
        });        
    };
    return app;
})(ipPropertyMap || {});
