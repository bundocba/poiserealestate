/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

var ipMapFunctions = (function(app) {
	/**********************************************
    // HANDLE COOKIE HERE
    **********************************************/
	// call this function before building inputs to set currentvals if required
	app.handleCookie = function(clearcookie){
		jQuery.cookie.json = true;
		ipMapFunctions.ipcookie = jQuery.cookie('ipadvsearch'+mapOptions.itemid);
		// if we have clearcookie == true delete the existing cookie
		if (clearcookie === true){
			if (ipMapFunctions.ipcookie !== null && ipMapFunctions.ipcookie !== undefined) {
				jQuery.removeCookie('ipadvsearch'+mapOptions.itemid);				
				ipMapFunctions.ipcookie = null;			
			}
		}
		// check for cookie
		if (ipMapFunctions.ipcookie !== null && ipMapFunctions.ipcookie !== undefined){
			mapOptions.currentvals = ipMapFunctions.ipcookie;
		} else {
			// cookie is null so create it using currentvals
            // to set an expiration date for the cookie, add it like this:
            // var expdate = 1; // can be a js date object OR an integer. Int value will be number of DAYS til expiration
            // ipMapFunctions.ipcookie = jQuery.cookie('ipadvsearch'+mapOptions.itemid, mapOptions.currentvals, { expires: expdate } );
			ipMapFunctions.ipcookie = jQuery.cookie('ipadvsearch'+mapOptions.itemid, mapOptions.currentvals);
		}
	};

    /**********************************************
    // FUNCTIONS BELOW
    **********************************************/

    // generic ajax helper function
    app.doAjaxRequest = function (data, callback){
        jQuery.ajax({
          url: mapOptions.ipbaseurl+'index.php?option=com_iproperty&'+mapOptions.token+'=1&format=raw',
          data: data,
          type: 'POST',
          cache: false,
          error: function(request, status, error_message){
            console.log(status+' - '+error_message);
            console.dir(request);
            var returndata = { 'status': 'error', 'message': 'Ajax request error: '+error_message };
            callback(returndata);
          },
          success: function(returndata) {
            callback(returndata);
          }
        });
    }

    // helper function to parse ajax result object
    app.ajaxParseResult = function (data){
        var decodeData = jQuery.parseJSON(data);
        if (decodeData.status == 'error'){
            if (mapOptions.debug) console.dir(decodeData);
            return false;
        } else {
            // check for no results string
            if (decodeData.message == 'no results'){
                ipMapFunctions.setNoResults();
                return false;
            }
            // if we have json data returned, return it, otherwise return true
            if ((decodeData.status == 'ok') && decodeData.data) return decodeData.data;
            return true;
        }
    }

    // helper to grab result data
    app.getAjaxResults = function (){
        var data = {
            'searchvars': mapOptions.currentvals,
            'itemid': mapOptions.itemid,
            'limitstart': searchOptions.limitstart,
            'limit': searchOptions.limit,
            'task': 'ajax.ajaxSearch',
            'sort': mapOptions.sort,
            'order': mapOptions.order
        };
        var result = ipMapFunctions.doAjaxRequest( data, function(result){
            var returndata = ipMapFunctions.ajaxParseResult(result);
            if(returndata){
                jQuery("#ipResultsBody").empty();
                searchOptions.totallistings = returndata.total;
                searchOptions.totalpages = Math.ceil( searchOptions.totallistings / searchOptions.limit );
                ipMapFunctions.buildPagination();
                ipMapFunctions.buildResults(returndata.listings);
            } else {
                var decodeData = jQuery.parseJSON(result);
                var errortext = '<div class="alert alert-error fade in">';
                errortext +=    '<button type="button" class="close" data-dismiss="alert">&times;</button>';
                errortext +=    decodeData.message;
                errortext +=    '</div>';
                if (decodeData.message !== 'no results') jQuery('#mapSliders').prepend(errortext);
            }
        });
    }

    // get all selected items on page
    app.getSelectedOptions = function(){
        jQuery.each(tabGroups, function(index, el){
            // clear the existing array if any
            mapOptions.currentvals[index] = {};
            var checked = new Array();
            // get all checked checkboxes
            jQuery.each(jQuery('#'+index+' input:checked'), function(){
                checked.push(jQuery(this).val());
            });

            // add these to the currentvals
			switch (index){
				case 'amenities':
				case 'categories':
					if (checked.length) mapOptions.currentvals[index] = checked;
					break;
				default:
					if (checked.length) mapOptions.currentvals[index]['checked'] = checked;
					break;
			}

            // get all selected options
            jQuery.each(jQuery('#'+index+' select.ip-adv-select'), function(){		
                var thisval = jQuery(this).val() ? jQuery(this).val() : false;				
                var ids = this.id.split('-');
                // multiple selects return an array of values for val!
                // so check for array before adding value
                if ( thisval.length ){
					var tempval;
					var tempname = ids[1];
                    if (jQuery.isArray(thisval) && (!thisval[0])) {
                        return false;
                    } else if (jQuery.isArray(thisval)) {
                        tempval = new Array();
                        jQuery.each(thisval, function(index, el){
                            tempval.push(el);
                        });
                    } else {
                        tempval = thisval;
                    }
                    mapOptions.currentvals[index][tempname] = tempval;
                }
            });

            // get any text input values
            jQuery.each(jQuery('#'+index+' input.ipadv-text'), function(){
                var thisval = false;
                if ( false !== (thisval = jQuery(this).val()) ){
                    var ids = this.id.split('-');
                    mapOptions.currentvals[index][ids[1]] = thisval;
                }
            });

        });
        // set cookie
        ipMapFunctions.handleCookie(true);
        // now make request for data
        ipMapFunctions.getAjaxResults();
    }


	// clear selected items on page
    app.clearSelectedOptions = function(){
        jQuery.each(tabGroups, function(index, el){		
            // clear the existing array if any
            mapOptions.currentvals[index] = {};
            var checked = new Array();
            // get all checked checkboxes
            jQuery.each(jQuery('#'+index+' input:checked'), function(){
                // uncheck them
                jQuery(this).attr('checked', false);
            });

            // unset all selected options
            jQuery.each(jQuery('#'+index+' select.ip-adv-select'), function(){		
                jQuery(this).val('');				
            });

            // unset any text input values
            jQuery.each(jQuery('#'+index+' input.ipadv-text'), function(){
                jQuery(this).val('');
            });

        });
        // unset geopoint and shape if needed
        mapOptions.currentvals.geopoint = {};
        if(mapOptions.maptools)	ipMapFunctions.clearSelection();       
        // set cookie
        ipMapFunctions.handleCookie(true);
        // now make request for data
        ipMapFunctions.getAjaxResults();
    }

    //*********************************
    // PAGINATION FUNCTIONS
    //*********************************

    app.buildPagination = function () {
        jQuery("ul.ip-advsearch-pagination").remove(); // clear existing pagination element
        if (searchOptions.totalpages > 1) {
            var pager = jQuery('<ul class="ip-advsearch-pagination span6" />');
            searchOptions.totalpagesets = Math.ceil(searchOptions.totalpages / searchOptions.maxpages);
            // build numbered page links
            var y = (searchOptions.currentpageset == 1) ? 1 : ((searchOptions.currentpageset - 1) * searchOptions.maxpages) + 1;
            var z = y + searchOptions.maxpages;
            for (y; y < z; y++){
                var pageitem = jQuery('<li class="page'+y+'"><a href="#">'+y+'</a></li>');
                if (y <= searchOptions.totalpages){
                    if (searchOptions.currentpage == y) jQuery(pageitem).addClass('active');
                    jQuery(pageitem).click( function(e) {
                        e.preventDefault();
                        var pageid = jQuery(this).attr('class').substring(4);
                        ipPage(pageid);
                    });
                    jQuery(pager).append(pageitem);
                }
            }
            if(searchOptions.totalpagesets > 1) {
                if (searchOptions.currentpageset < searchOptions.totalpagesets){
                    var nextitem = jQuery('<li><a href="#">'+langOptions.next+'</a></li>');
                    jQuery(nextitem).click( function(e) {
                        e.preventDefault();
                        ipNextPageSet();
                    });
                    jQuery(pager).append(nextitem);
                }
                if (searchOptions.currentpageset > 1){
                    var previtem = jQuery('<li><a href="#">'+langOptions.previous+'</a></li>');
                    jQuery(previtem).click( function(e) {
                        e.preventDefault();
                        ipPrevPageSet();
                    });
                    jQuery(pager).prepend(previtem);
                }
            }
            // now append them after they're built (since we're cloning)
            jQuery(".ip-pagination").append(pager);
        }
    }

    function ipPage(page) {
        // empty the available images ul
        jQuery("#ipResultsBody").empty();
        // set start number since we just know the batch size and page requested
        searchOptions.limitstart = (page - 1) * searchOptions.limit;
        // remove class from active pager, add to new page
        jQuery(".page"+searchOptions.currentpage).removeClass('active');
        jQuery(".page"+page).addClass('active');
        searchOptions.currentpage = page;
        // now request a new batch of avail images
        ipMapFunctions.getSelectedOptions();
    }

    function ipNextPageSet() {
        searchOptions.currentpageset++;
        ipMapFunctions.buildPagination();
    }

    function ipPrevPageSet() {
        searchOptions.currentpageset--;
        ipMapFunctions.buildPagination();
    }
    return app;
})(ipMapFunctions || {});
