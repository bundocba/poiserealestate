/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

var ipMapFunctions = (function(app) {
	var selectedShape, drawingManager;
	// set options for display
    app.buildTools = function(){
		var polyOptions = {
          strokeWeight: 0,
          fillOpacity: 0.45,
		  fillColor: '#1E90FF',
          editable: true
        };
        // initialize drawingmanager object
        drawingManager = new google.maps.drawing.DrawingManager({
			// drawing controls options
			drawingControlOptions: {
				drawingModes: [
				  google.maps.drawing.OverlayType.CIRCLE,
				  //google.maps.drawing.OverlayType.POLYGON,
				  google.maps.drawing.OverlayType.RECTANGLE
				]
            },
            rectangleOptions: polyOptions,
            circleOptions: polyOptions,
            //polygonOptions: polyOptions,
            map: ipMapFunctions.map
		}); 
		google.maps.event.addListener(drawingManager, 'overlaycomplete', function(e) {
            if (e.type !== google.maps.drawing.OverlayType.MARKER) {
                // Switch back to non-drawing mode after drawing a shape.
                drawingManager.setDrawingMode(null);

                // Add an event listener that selects the newly-drawn shape when the user
                // mouses down on it.
                var newShape = e.overlay;
                newShape.type = e.type;
                google.maps.event.addListener(newShape, 'click', function() {
                  setSelection(newShape);
                });
                setSelection(newShape);
            }
        });	
		google.maps.event.addListener(drawingManager, 'drawingmode_changed', ipMapFunctions.clearSelection);
        google.maps.event.addListener(ipMapFunctions.map, 'click', ipMapFunctions.clearSelection);		
		google.maps.event.addListener(drawingManager, 'overlaycomplete', function(event) {
			switch (event.type) {
				case google.maps.drawing.OverlayType.CIRCLE:
					var radius = event.overlay.getRadius(); // returned in meters!! 
					var opts = { center: event.overlay.getCenter(), radius: radius };
					doToolSearch('circle', opts);
					break;
				case google.maps.drawing.OverlayType.POLYGON:
					var path = event.overlay.getPath(); 
					doToolSearch('polygon', path);
					break;
				case google.maps.drawing.OverlayType.RECTANGLE:
					var bounds = event.overlay.getBounds(); 
					doToolSearch('rectangle', bounds);
					break;	
			} 
		});
        google.maps.event.addListener(drawingManager, 'circlecomplete', function (circle){
            google.maps.event.addListener(circle, 'radius_changed', function () {
                var radius = circle.getRadius(); // returned in meters!! 
                var opts = { center: circle.getCenter(), radius: radius };
                doToolSearch('circle', opts);
            });
            google.maps.event.addListener(circle, 'center_changed', function () {
                var radius = circle.getRadius(); // returned in meters!! 
                var opts = { center: circle.getCenter(), radius: radius };
                doToolSearch('circle', opts);
            });
        });
        google.maps.event.addListener(drawingManager, 'rectanglecomplete', function (rectangle){
            google.maps.event.addListener(rectangle, 'bounds_changed', function () {
                var bounds = rectangle.getBounds(); 
                doToolSearch('rectangle', bounds);
            });
        });
        /*
        google.maps.event.addListener(drawingManager, 'polygoncomplete', function (polygon){
            google.maps.event.addListener(polygon, 'insert_at', function () {
                var path = polygon.getPath(); 
                doToolSearch('polygon', path);
            });
            google.maps.event.addListener(polygon, 'remove_at', function () {
                var path = polygon.getPath(); 
                doToolSearch('polygon', path);
            });
            google.maps.event.addListener(polygon, 'set_at', function () {
                var path = polygon.getPath(); 
                doToolSearch('polygon', path);
            });
        });
        */
	};
		 
	// utility methods
	app.clearSelection = function() {
		if (selectedShape) {
			selectedShape.setEditable(false);
			selectedShape.setMap(null);
			selectedShape = null;
			mapOptions.currentvals['geopoint'] = {};
			ipMapFunctions.getSelectedOptions();
		}
	};

	function setSelection(shape) {
		ipMapFunctions.clearSelection();
		selectedShape = shape;
		shape.setEditable(true);
	}
	
	function doToolSearch(type, options){
		mapOptions.currentvals['geopoint'] = {};
		switch(type){
			case 'circle':
				mapOptions.currentvals['geopoint']['lat'] = options.center.lat();
				mapOptions.currentvals['geopoint']['lon'] = options.center.lng();
				mapOptions.currentvals['geopoint']['rad'] = (options.radius / 1000); // convert meters to km
				break;
			case 'polygon':
				var paths = [];
				for (var i = 0; i < options.length; i++){
					var point = options.getAt(i);
					paths.push({ lat: point.lat(), lon: point.lng() });	
				}
				//mapOptions.currentvals['geopoint']['paths'] = paths;
				//console.dir(paths);
				break;
			case 'rectangle':
				var SW = options.getSouthWest();
				var NE = options.getNorthEast();
				var SW_array = [ SW.lat(), SW.lng() ];
				var NE_array = [ NE.lat(), NE.lng() ];
				mapOptions.currentvals['geopoint']['sw'] = SW_array;
				mapOptions.currentvals['geopoint']['ne'] = NE_array;
				break;
		}
		ipMapFunctions.getSelectedOptions();
	}
	
    return app;
})(ipMapFunctions || {});