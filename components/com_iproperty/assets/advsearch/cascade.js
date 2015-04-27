/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

 var ipMapFunctions = (function(app) {
    
	app.createCascade = function(elements){
		app.elements = elements;
		var i = 0;
		// pass in array of elements that need to be cascaded
		jQuery.each(elements, function(inputname, options){
			buildLocationSelect(inputname, options);
			var selected = mapOptions.currentvals.location[inputname] ? mapOptions.currentvals.location[inputname] : false;
			if (selected) i++;
			//if we want to populate the list
			if (options.index == 0 || options.index == i){
				var $selectname = jQuery('#'+options.datagroup+'-'+options.param);
				populateSelect($selectname, inputname, options);
			}
		});
	}
	
	function buildLocationSelect(input, options)
    {
        var selectorContainer = jQuery('<div class="span4 ip-adv-select-container" />');
        var selector    = 	jQuery('<select id="'+options.datagroup+'-'+ options.param+'" class="ip-adv-select ipadv-location" data-group="'+options.datagroup+'" data-index="'+options.index+'" />');
		selector.append(jQuery('<option value>'+options.title+'</value>'));
		selector.attr('disabled', 'disabled');
        jQuery(selectorContainer).append(selector);
        jQuery("#" + options.tab).append(selectorContainer);
		// attach onChange event to select list 
		selector.bind('change', updateSelects);
    }
	
	function populateSelect($select, input, options)
    {	
		// first empty the select
		$select.empty();
		// now remove the disabled attr
		$select.removeAttr('disabled');
		// and add back in the null value
		$select.append(jQuery('<option value>'+options.title+'</value>'));
		// now do the ajax request
		var url = mapOptions.ipbaseurl+'index.php?option=com_iproperty&'+mapOptions.token+'=1&format=raw&task=ajax.ajaxGetInputs';
		var vals = (options.index > 0) ? mapOptions.currentvals.location : {};	
		jQuery.ajax({
			type: "GET",
			dataType:"json",
			data: {
				'input': input,
				'currentvals': JSON.stringify(vals)
			},
			url: url,
			success: function(result){
				if (result.data) {
					jQuery.each(result.data, function(value, title){
						if(title){
							var option = jQuery('<option value="'+value+'">'+title+'</option>');
							$select.append(option);
							if(mapOptions.currentvals.location[input]){
								if (jQuery.isArray(mapOptions.currentvals.location[input])){
									jQuery.each(mapOptions.currentvals.location[input], function(i, v){
                                        if(value == v) option.attr('selected', 'selected');
                                    });
								} else {
									if(value == mapOptions.currentvals.location[input]){
										option.attr('selected', 'selected');
									}
								}
							}
						}
					});
				}
			}
		});
	}
	
	function updateSelects()
    {
		var currentindex = parseInt(jQuery(this).attr('data-index'));
		// clear existing values that are lower than this
		clearData(this);
		jQuery.each(jQuery(".ipadv-location"), function(i, e){
			var el = jQuery(e); 
			var elindex = parseInt(el.attr('data-index'));
			var input 	= el.attr('id').split('-');
			var options = ipMapFunctions.elements[input[1]];
			// now get the new values for the select list only if it's the next lower
			if(elindex == (currentindex + 1)) populateSelect(el, input[1], options);
		});
		// now that we have reset all selects, call get values and trigger data call
		ipMapFunctions.getSelectedOptions();		
	}
	
	function clearData(element)
    {
		var currentindex 	= parseInt(jQuery(element).attr('data-index'));
		var currinput 		= jQuery(element).attr('id').split('-');
		mapOptions.currentvals.location[currinput[1]] = jQuery(element).val();
		jQuery.each(jQuery(".ipadv-location"), function(i, e){
			var el = jQuery(e); 
			var elindex = parseInt(el.attr('data-index'));
			if(elindex > currentindex){
				var input 	= el.attr('id').split('-');
				// remove the item selection, since this is lower on the chain
				delete mapOptions.currentvals.location[input[1]];
				el.val('');
				el.attr('disabled', 'disabled');
			}
		});
	}

    return app;
})(ipMapFunctions || {});	