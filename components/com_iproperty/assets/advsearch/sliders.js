/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

var ipMapFunctions = (function(app) {
    
    // generic build slider wrapper
    app.buildSliders = function(ipSliders){
		// clear any existing cached sliders
		jQuery('.ip-adv-slidecontainer').each(function(){
			jQuery(this).remove();
		});
        // build sliders
        jQuery.each(ipSliders, function(index, el){
            ipMapFunctions.buildSlider(el);
        });
    };

    app.buildSlider = function(input) {
        jQuery.ui.slider.prototype.widgetEventPrefix = 'slider'; // workaround for Mootools conflict
        var nolimitstring = langOptions.nolimit;
		var RTL_right 	= langOptions.isRTL ? 'left' : 'right';
		var RTL_left 	= langOptions.isRTL ? 'right' : 'left';
        var container = jQuery('<div class="ip-adv-slidecontainer" />');
        var slidediv = jQuery('<div id="' + input.param + '" class="ip-adv-slider" />');
        var vals = jQuery('<div class="clearfix form-inline"><input type="text" id="' + input.param + '_min" class="ip-adv-slidevalue input-small pull-'+RTL_left+'" disabled="disabled" /><label>' + input.title + '</label><input type="text" id="' + input.param + '_max" class="ip-adv-slidevalue input-small pull-'+RTL_right+'" disabled="disabled" /></div>');
        var sel_min, sel_max;
        // calculate step size
        //var width = jQuery("#mapSliders").width();
        //var step = width / (input.value.max - input.value.min);
        //console.log(input.param + ' - ' + step);
        // attach elements
        jQuery(container).append(slidediv).append(vals);
        jQuery("#mapSliders").append(container);           

        // attach min/max data to slidediv
        jQuery(slidediv).data({
            'min': input.selected.min,
            'max': input.selected.max
        });

        // set initial vals for the currentvals array
        if (typeof mapOptions.currentvals.sliders[input.param] === 'undefined') {	
			// currentvals is not set -- no cookie-- so create object
			mapOptions.currentvals.sliders[input.param] = {};
			mapOptions.currentvals.sliders[input.param].min = input.nolimit ? null : input.selected.min;
			mapOptions.currentvals.sliders[input.param].max = input.nolimit ? null : input.selected.max;
			
			// set vars for current slider positions to min and max defaults
			sel_min = input.value.min;
			sel_max = input.value.max;
		} else {
			// there's no cookie so we can set to current vals
			sel_min = (mapOptions.currentvals.sliders[input.param].min == null) ? input.value.min : mapOptions.currentvals.sliders[input.param].min;
			sel_max = (mapOptions.currentvals.sliders[input.param].max == null) ? input.value.max : mapOptions.currentvals.sliders[input.param].max;
		} 

        jQuery(slidediv).slider({
            min: input.value.min,
            max: input.value.max,
            //step: step,
            range: true,
            disabled: input.disabled,
            values: [sel_min, sel_max],
            animate: true,
            create: function(event, ui) {
                var min = (input.nolimit == true && sel_min == input.value.min) ? nolimitstring : sel_min;
                var max = (input.nolimit == true && sel_max == input.value.max) ? nolimitstring : sel_max;
                jQuery("#" + input.param + "_min").val(min);
                jQuery("#" + input.param + "_max").val(max);

                // format the slider value if required, otherwise just round it
                if (input.hasOwnProperty('labelUnit')) { // this is a currency formatted slider
                    if (min != nolimitstring) jQuery("#" + input.param + "_min").formatCurrency(mapOptions.currencyFormat);
                    if (max != nolimitstring) jQuery("#" + input.param + "_max").formatCurrency(mapOptions.currencyFormat);
                }
            },
            slide: function(event, ui) {
                // fix for jquery allowing handles to overlap
                if (ui.values[0] == ui.values[1]) {
                    return false;
                }

                var min = (input.nolimit && (ui.values[0] == jQuery("#" + input.param).data('min'))) ? nolimitstring : Math.round(ui.values[0]);
                var max = (input.nolimit && (ui.values[1] == jQuery("#" + input.param).data('max'))) ? nolimitstring : Math.round(ui.values[1]);

                // update vals for the currentvals array
                mapOptions.currentvals.sliders[input.param].min = (min == nolimitstring) ? null : min;
                mapOptions.currentvals.sliders[input.param].max = (max == nolimitstring) ? null : max;

                jQuery("#" + input.param + "_min").val(min);
                jQuery("#" + input.param + "_max").val(max);

                // format the slider value if required, otherwise just round it
                if (input.hasOwnProperty('labelUnit')) { // this is a currency formatted slider
                    if (min != nolimitstring) jQuery("#" + input.param + "_min").formatCurrency(mapOptions.currencyFormat);
                    if (max != nolimitstring) jQuery("#" + input.param + "_max").formatCurrency(mapOptions.currencyFormat);
                }
            },
            change: function(event, ui){
                // this is where we trigger the search action
                ipMapFunctions.getSelectedOptions();
            }
        });
    };
    
    // clear all slider values
    app.clearSliders = function(){
		jQuery.each(ipSliders, function(index, input){
			var slidediv = jQuery("#"+input.param+".ip-adv-slider");
			// clear the selected options 
			mapOptions.currentvals.sliders[input.param] = { min: null, max: null };
			// reset slider position
			jQuery(slidediv).slider( "option", "values", [ input.value.min, input.value.max ] );
            var min = input.nolimit ? langOptions.nolimit : input.value.min;
            var max = input.nolimit ? langOptions.nolimit : input.value.max; 
            // set text values
            jQuery("#" + input.param + "_min").val(min);
            jQuery("#" + input.param + "_max").val(max);
            // format the slider value if required, otherwise just round it
			if (input.hasOwnProperty('labelUnit')) { // this is a currency formatted slider
				if (min != langOptions.nolimit) jQuery("#" + input.param + "_min").formatCurrency(mapOptions.currencyFormat);
				if (max != langOptions.nolimit) jQuery("#" + input.param + "_max").formatCurrency(mapOptions.currencyFormat);
			}
        });
	}
    
    return app;
})(ipMapFunctions || {});
