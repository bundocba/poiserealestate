/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

var ipMapFunctions = (function(app) {
    // generic build dropdowns wrapper
    app.buildSliders = function(ipSliders){
		// clear any existing cached dropdowns
		jQuery('.ip-adv-dropdown-container').each(function(){
			jQuery(this).remove();
		});		
        // build dropdowns
        jQuery.each(ipSliders, function(index, el){
            buildDropdown(el);
        });
		
		// attach change events
		jQuery(".ip_adv_dropdown").change(function() {
			var id = jQuery(this).attr('id').split('_');
			var input = id[0];
			var value = jQuery(this).val();
			// set values in currentvals
			if (id[1] == 'min'){
				mapOptions.currentvals.sliders[input].min = (value == langOptions.nolimit) ? null : value;
			} else if (id[1] == 'max') {
				mapOptions.currentvals.sliders[input].max = (value == langOptions.nolimit) ? null : value;
			}
			// trigger getSelectedOptions
			ipMapFunctions.getSelectedOptions();
		});
		
    }

    function buildDropdown(input) {
        var nolimitstring = langOptions.nolimit;
        var maxstring = input.nolimit ? nolimitstring : input.value.max;
		var minstring = input.nolimit ? nolimitstring : input.value.min;
        
        // set min / max step
        var minsteps = 7;
        var maxsteps = 13;
		
		// set initial vals for the currentvals array
        mapOptions.currentvals.sliders[input.param] = {};
        mapOptions.currentvals.sliders[input.param].min = input.nolimit ? null : input.selected.min;
        mapOptions.currentvals.sliders[input.param].max = input.nolimit ? null : input.selected.max;

        var container = jQuery('<div class="well ip-adv-dropdown-container" />');
        var drop_min = jQuery('<select id="' + input.param + '_min" class="pull-left ip_adv_dropdown" />');
        var drop_max = jQuery('<select id="' + input.param + '_max" class="pull-right ip_adv_dropdown" />');

        // attach elements
        jQuery("#mapDropdowns").append(container);
        jQuery(container).append(drop_min).append(input.title).append(drop_max);

        // figure out values for select
        var step = false; // set this explicitly if desired
        var min = input.value.min;
        var max = input.value.max;
        var nolimit = input.nolimit;
        var diff = max - min;

        // try to find a step number that divides evenly
        if (!step) {
            for (var i = minsteps; i < maxsteps; i++) {
                if( diff % i === 0 ){
                    step = i;
                    break;
                }
            }
        }

        var inc = (diff / step);
        // build options
        if (diff > 10) {
            for (var i = inc; i < max; i = i + inc) {
                var option = jQuery('<option value="' + i + '"></option>');
				if (input.hasOwnProperty('labelUnit')) {
					option.html(i).formatCurrency(mapOptions.currencyFormat);
				} else {
					option.html(i);
				}
                option.clone().appendTo(drop_min);
                option.clone().appendTo(drop_max);
            }
        } else {
            for (var i = min; i < max; i++) {
				var option = jQuery('<option value="' + i + '"></option>');
                if (input.hasOwnProperty('labelUnit')) {
					option.html(i).formatCurrency(mapOptions.currencyFormat);
				} else {
					option.html(i);
				}
                option.clone().appendTo(drop_min);
                option.clone().appendTo(drop_max);
            }
        }
        var minoption = jQuery('<option selected="selected">'+minstring+'</option>').prependTo(drop_min);
		var maxoption = jQuery('<option selected="selected">'+maxstring+'</option>').appendTo(drop_max);
		
		if (input.hasOwnProperty('labelUnit')) {
			jQuery(minoption).formatCurrency(mapOptions.currencyFormat);
			jQuery(maxoption).formatCurrency(mapOptions.currencyFormat);
		}
		
		if (!input.nolimit) {
			jQuery(minoption).attr('value', min);
			jQuery(maxoption).attr('value', max);
		}
    }

    // clear all dropdown values
    app.clearSliders = function(){
		jQuery.each(ipSliders, function(index, input){
			var slidediv = jQuery("#"+input.param+".ip-adv-slider");
			// clear the selected options 
			mapOptions.currentvals.sliders[input.param] = { min: null, max: null };
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
