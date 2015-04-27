/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

var ipMapFunctions = (function(app) {
    var ipResultHead    = false;
    var rowOffset       = 0;
    // build result table row and marker
    app.buildResults = function(data){        
        // build header if needed
        if (!ipResultHead) {
			if (mapOptions.layout === 0) {
				buildHeaderTable();
			} else {
				buildHeaderOverview();
			}
        }
        // clear map markers and reset map
        ipMapFunctions.clearMap();
        
        // set the total results found
        jQuery("#ipResultsTicker").html('<span class="label label-success pull-right">'+searchOptions.totallistings+' '+langOptions.tprop+'</span>');
        jQuery.each(data, function(index, el){
            ipMapFunctions.addMarker(el);
            
            rowOffset = (rowOffset) ? 0 : 1;
            switch (mapOptions.layout){
				case 1:
					buildResultRowOverview(index, el, rowOffset);
				break;
				case 0:
				default:
					buildResultRowTable(index, el);
				break;
            }
        });
		// if we're using clustering add the markers array to clusterer        
        if (ipMapFunctions.mc) ipMapFunctions.mc.addMarkers(ipMapFunctions.ipMarkers);
    };

    function buildResultRowTable(index, el)
    {
        var tr      = jQuery('<tr class="ip-adv-row" id="ip-adv-listing-'+el.id+'" />');
        var pid     = jQuery('<td class="hidden-phone">'+((el.property_name) ? el.property_name : '--')+'</td>');
        var price   = el.call_for_price ? jQuery('<td>'+el.formattedprice+'</td>') : jQuery('<td>'+el.price+'</td>').formatCurrency(mapOptions.currencyFormat);
        var sqft    = jQuery('<td class="ip_numformat hidden-phone">'+el.sqft+'</td>');
        var street  = jQuery('<td><a href="'+el.proplink+'">'+el.street_address+'</a></td>');
        var beds    = jQuery('<td class="ip_numformat hidden-phone">'+el.beds+'</td>');
        var baths   = jQuery('<td class="ip_bathformat hidden-phone">'+el.baths+'</td>');
        var img     = el.thumb;
        var poptab  = jQuery('<td class="center" />');
        var btnGroup = jQuery('<div class="btn-group" />');
        var previewpop = false;
        
        // if the thumbnail img is not the nopic.png and show thumb option is enabled, display thumb popover
        if (mapOptions.showthumb && img.indexOf('nopic.png') === -1){
            var imgpopover = jQuery('<button class="btn btn-small" rel="popover"><span id="ip_previewimg'+index+'" class="icon-camera"></span></button>');
            jQuery(imgpopover).attr('data-content', img);
            btnGroup.append(imgpopover);
            jQuery(imgpopover).popover( {'trigger': 'hover', 'placement': 'left', 'html': true, 'title': false} );
            jQuery(imgpopover).click(function() {
                window.location = el.proplink.replace(/\&amp;/g,'&');
            });
        }
        
        // create map preview button
        if (el.lat_pos && el.long_pos){
            previewpop = jQuery('<button class="btn btn-small" id="ip-adv-mappreview-'+el.id+'"><span class="icon-home"></span></button>');            
            jQuery(previewpop).click(function() {
                jQuery('.ip-adv-row').removeClass('ip-overview-active');
                jQuery('html,body').animate({
                     scrollTop: jQuery('[name=ipmap_top]').offset().top
                }, 500);
                // set active class to show which listing is being viewed and change 
                // button color to show it's been viewed already
                jQuery('#ip-adv-mappreview-'+el.id).addClass('btn-success');
                jQuery('#ip-adv-listing-'+el.id).addClass('ip-overview-active');
                ipMapFunctions.openMarker(ipMapFunctions.ipMarkers[index]);
            });
        }else{
            previewpop = jQuery('<button class="btn btn-small disabled hasTip" title="No map available" id="ip-adv-mappreview-'+el.id+'"><span class="icon-home"></span></button>');
        }
        
        // add btn group to poptab column
        btnGroup.append(previewpop);
        if(typeof poptab != 'undefined') poptab.append(btnGroup);

        // now attach each element to the tr if it's in the settings
        jQuery.each(mapOptions.resultColumns, function(i, e){
            if (e === 'pid'){
                jQuery(tr).append(pid);
            } else if (e === 'price') {
                jQuery(tr).append(price);
            } else if (e === 'street') {
                jQuery(tr).append(street);
            } else if (e === 'beds') {
                jQuery(tr).append(beds);
            } else if (e === 'baths') {
                jQuery(tr).append(baths);
            } else if (e === 'sqft') {
                jQuery(tr).append(sqft);
            } else if (e === 'preview') {
                jQuery(tr).append(poptab);
            }
        });
        jQuery('#ipResultsBody').append(tr);
    }
    
    // for overview style
    function buildResultRowOverview(index, el, rowOffset)
    {
        var tr      = jQuery('<div class="row-fluid ip-row'+rowOffset+' ip-adv-row" id="ip-adv-listing-'+el.id+'" />');
        var imgtd	= jQuery('<div class="span3 ip-overview-img" />');
        var img     = jQuery('<div class="ip-property-thumb-holder" />').append(jQuery('<a href="'+el.proplink+'" />').append(el.thumb)).append(el.banner);
        var price   = el.call_for_price ? jQuery('<h4 class="ip-overview-price pull-right">'+el.formattedprice+'</h4>') : jQuery('<h4 class="ip-overview-price pull-right">'+el.price+'</h4>').formatCurrency(mapOptions.currencyFormat);
        var previewpop = false;
        
        var content	= jQuery('<div class="span9 ip-overview-desc" />');
        
        // create map preview button
        if (el.lat_pos && el.long_pos){
            var poptab  = jQuery('<div class="pull-right" />');     
            previewpop = jQuery('<button class="btn btn-small" id="ip-adv-mappreview-'+el.id+'"><span class="icon-home"></span></button>');            
            jQuery(previewpop).click(function() {
                jQuery('.ip-adv-row').removeClass('ip-overview-active');
                jQuery('html,body').animate({
                     scrollTop: jQuery('[name=ipmap_top]').offset().top
                }, 500);
                // set active class to show which listing is being viewed and change 
                // button color to show it's been viewed already
                jQuery('#ip-adv-mappreview-'+el.id).addClass('btn-success');
                jQuery('#ip-adv-listing-'+el.id).addClass('ip-overview-active');
                ipMapFunctions.openMarker(ipMapFunctions.ipMarkers[index]);
            });
        }else{
            previewpop = jQuery('<button class="btn btn-small disabled hasTip" title="No map available" id="ip-adv-mappreview-'+el.id+'"><span class="icon-home"></span></button>');
        }

        // now attach each element to the 'row' if it's in the settings
        if(typeof poptab != 'undefined') poptab.append(previewpop);
        imgtd.append(img).append(price);
        tr.append(imgtd).append(content).append(poptab);        
        
        // build display for overview content
        var contentstring = '<a href="'+el.proplink+'" class="ip-property-header-accent">'+el.street_address+'</a>';
        if (el.city) contentstring += ' - '+el.city;
        if (el.statename) contentstring += ', '+el.statename;
        if (el.province) contentstring += ', '+el.province;
        if (el.countryname) contentstring += ', '+el.countryname;
        contentstring += '<br /><em>';
        if (el.beds) contentstring += '<strong>'+langOptions.beds+'</strong>: '+el.beds+' ';
        if (el.baths) contentstring += '<strong>'+langOptions.baths+'</strong>: '+el.baths+' ';
        if (el.sqft) contentstring += '<strong>'+langOptions.sqft+'</strong>: '+el.sqft+' ';
        contentstring += '</em><br />';
        contentstring += el.short_description+ '<br />';
        contentstring += '<a href="'+el.proplink+'" class="small">('+langOptions.more+')</a>';
        content.html(contentstring);

        jQuery('#ipResultsBody').append(tr);
    }

    // helper function to handle no results situation
    app.setNoResults = function(){
        jQuery("#ipResultsBody").empty();
        ipMapFunctions.clearMap();
        // clear ticker
        jQuery("#ipResultsTicker").html("");
        // add noresults row
        var tr = jQuery('<div id="ipNoResults" class="alert alert-notice" style="text-align: center;">'+langOptions.noRecords+'</div>');
        jQuery('#ipResultsBody').append(tr);
    };

    // build marker HTML
    app.buildInfoWindow = function(listing){
        // remove line breaks from banner
        listing.banner = listing.banner.replace(/(\r\n|\n|\r)/gm," ");
        var contentContainer = jQuery('<div />');
        var contentString = '<div class="row-fluid ip-bubble-window">' +
                                '<div class="span5 ip-overview-img"><div class="ip-property-thumb-holder"><a href="'+listing.proplink+'">'+listing.thumb+'</a>'+listing.banner+'</div></div>' +
                                '<div class="span7">' +
                                '<h4><a href="'+listing.proplink+'">'+listing.street_address+', '+listing.city+'</a></h4>' +
                                '<div class="small"><strong>'+langOptions.pid+': </strong>'+listing.property_name+' | <strong>'+langOptions.price+': </strong>'+listing.formattedprice+'</div>' +
                                '<p class="ip-bubble-desc">'+listing.short_description.slice(0,185).trim()+'...'+'<div class="ip-bubble-cats">'+listing.caticons.join(' ')+'</div><a href="'+listing.proplink+'">('+langOptions.more+')</a></p>' +
                                '</div>' +
                            '</div>';
        contentContainer.html(contentString);
        return contentContainer.html();
    };

    function buildHeaderTable()
    {
        var column;
        var th_row = jQuery('<tr />');
        jQuery.each(mapOptions.resultColumns, function(index, el){
            var phoneHideArray = ['pid','beds','baths','sqft'];
            //console.log(el);
            var phoneHideClass = (jQuery.inArray(el, phoneHideArray) !== -1) ? ' class="hidden-phone"' : '';
            var header = jQuery('<th'+phoneHideClass+'/>');
            if (el === 'preview'){
                header = jQuery('<th class="center" />');
                column = jQuery('<span>'+langOptions[el]+'</span>');
            } else {
                column = jQuery('<a class="ip-column-header" data-column="'+el+'" href="#">'+langOptions[el]+'</a>');
                column.click(function(e){
                    e.preventDefault();
                    toggleSort(jQuery(this).attr('data-column'));
                    ipMapFunctions.getSelectedOptions();
                });
            }
            th_row.append(header);
            header.append(column);
            jQuery('#ipResultsHead').append(th_row);
        });
        ipResultHead = true;
    }
    
    function buildHeaderOverview()
    {
		// build orderby
        var orderby = jQuery('<select id="ordersortby" class="input-small" />');
        var asc     = jQuery("<option />");
        asc.attr('value','ASC').text(langOptions.asc);
        var desc    = jQuery("<option />");
        desc.attr('value','DESC').text(langOptions.desc);
        if (searchOptions.orderby === 'asc') {
            asc.attr('selected', 'selected');
        } else {
            desc.attr('selected', 'selected');
        }
        orderby.append(asc).append(desc);
        
        orderby.change(function(){
			mapOptions.order = jQuery(this).val();
			ipMapFunctions.getSelectedOptions();
		});
        
        var sortby	= jQuery('<select id="ipsortby" class="input-medium" style="margin-right: 5px;"/>');
        jQuery.each(mapOptions.resultColumns, function(index, el){
			if (el == 'preview') return;
			var option = jQuery("<option />");
			option.attr('value', el).text(langOptions[el]);
			if (searchOptions.sortby == 'p.'+ el) option.attr('selected', 'selected');
			sortby.append(option);
        });
        sortby.change(function(){
			mapOptions.sort = jQuery(this).val();
			ipMapFunctions.getSelectedOptions();
		});
        
        jQuery('#ipOrderBy').append(sortby).append(orderby);
        ipResultHead = true;
    }    

    function toggleSort(column)
    {
        if(mapOptions.sort == column){
            // we need to change the order
            if (mapOptions.order !== 'DESC') {
                mapOptions.order = 'DESC';
            } else {
                mapOptions.order = 'ASC';
            }
        } else {
            // we need to change the sort
            mapOptions.sort = column;
        }
    }
    return app;
})(ipMapFunctions || {});      
