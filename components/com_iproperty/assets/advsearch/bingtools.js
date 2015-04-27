/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

var ipMapFunctions = (function(app) {
	var selectedShape, drawingManager, toolboxOptions;	
    app.buildTools = function(){
		toolboxOptions = {
			// set options for display
			toolBarPolygonIcon: mapOptions.ipbaseurl+"components/com_iproperty/assets/js/bingTools/images/polygonIcon.png",
			toolBarPolygonHoverIcon: mapOptions.ipbaseurl+"components/com_iproperty/assets/js/bingTools/images/polygonHoverIcon.png",
			toolBarPolygonActiveIcon: mapOptions.ipbaseurl+"components/com_iproperty/assets/js/bingTools/images/polygonActiveIcon.png",
			toolBarPolylineIcon: mapOptions.ipbaseurl+"components/com_iproperty/assets/js/bingTools/images/polylineIcon.png",
			toolBarPolylineHoverIcon: mapOptions.ipbaseurl+"components/com_iproperty/assets/js/bingTools/images/polylineHoverIcon.png",
			toolBarPolylineActiveIcon: mapOptions.ipbaseurl+"components/com_iproperty/assets/js/bingTools/images/polylineActiveIcon.png",
			toolBarPushPinIcon: mapOptions.ipbaseurl+"components/com_iproperty/assets/js/bingTools/images/pushpinIcon.png",
			toolBarPushPinHoverIcon: mapOptions.ipbaseurl+"components/com_iproperty/assets/js/bingTools/images/pushpinHoverIcon.png",
			toolBarPushPinActiveIcon: mapOptions.ipbaseurl+"components/com_iproperty/assets/js/bingTools/images/pushpinActiveIcon.png",
			toolBarRectangleIcon: mapOptions.ipbaseurl+"components/com_iproperty/assets/js/bingTools/images/rectangleIcon.png",
			toolBarRectangleHoverIcon: mapOptions.ipbaseurl+"components/com_iproperty/assets/js/bingTools/images/rectangleHoverIcon.png",
			toolbarRectangleActiveIcon: mapOptions.ipbaseurl+"components/com_iproperty/assets/js/bingTools/images/rectangleActiveIcon.png",
			toolbarCircleIcon: mapOptions.ipbaseurl+"components/com_iproperty/assets/js/bingTools/images/circleIcon.png",
			toolbarCircleHoverIcon: mapOptions.ipbaseurl+"components/com_iproperty/assets/js/bingTools/images/circleHoverIcon.png",
			toolbarCircleActiveIcon: mapOptions.ipbaseurl+"components/com_iproperty/assets/js/bingTools/images/circleActiveIcon.png",
			DragHandleImage: mapOptions.ipbaseurl+"components/com_iproperty/assets/js/bingTools/images/DragHandleWhite.gif",
			DragHandleImageActive: mapOptions.ipbaseurl+"components/com_iproperty/assets/js/bingTools/images/DragHandleGreen.gif"
		}
		drawingManager = new ShapeToolboxModule(ipMapFunctions.map, toolboxOptions);
		drawingManager.show();
	}
	
    return app;
})(ipMapFunctions || {});
