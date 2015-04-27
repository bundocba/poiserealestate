/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

jQuery(function($) {
	$(document).ready(function(){
		// create pagination vars
		var ipPaginationDocs = {
			limitstart: 0,
			availlimit: 100, // max avail docs per page
			currentpage: 1,
			totalavailimgs: 0,
			totalpages: 0,
			currentpageset: 1,
			totalpagesets: 1
		}

		// build collection for adding docs
		var adddocs = new Array();

		// get the docs and build the display
		getSelectedDocs();
		getAvailableDocs();

		//*********************************
		// SETUP FUNCTIONS
		//*********************************

		// hook up edit clicks
		$("#ip_selected_docs").on("click",  "i.icon-pencil", function(e) {
			e.preventDefault();
			var id = $(this).closest('li.ipimage').attr('id');
			var el = $("#"+id);
			$("#doctitle").val(el.data('title'));
			$("#docdescription").val(el.data('description'));
			$("#docid").val(id);
			$('#ip_docs_form').modal('show');
		});

		// hook up delete clicks
		$("#ip_selected_docs").on("click",  "i.icon-minus", function(e) {
			e.preventDefault();
			var id = $(this).closest('li.ipimage').attr('id');
			bootbox.confirm(ipGalleryOptions.language.confirm, ipGalleryOptions.language.cancel, ipGalleryOptions.language.ok, function(result) {
				if(result) deleteDoc(id);
			});
		});

		// hook up save image form button
		$("#ip_doc_form_save").click(function(e) {
			e.preventDefault();
			// trigger the saveimageTags function
			var id          = $("#docid").val();
			var title       = $("#doctitle").val();
			var description = $("#docdescription").val();
			saveDocTags(id, title, description);
		});

		// attach sortable to ul
		$("#ip_selected_docs").sortable({
			placeholder: "ip-state-highlight",
			update: function(event, ui) {
				saveDocOrder();
			}
		}).disableSelection();

		// hook up add avail clicks
		$("#ip_available_docs").on("click",  "i.icon-plus, img.ipimage", function(e) {
			e.preventDefault();
			var el = $(this).closest('li.ipavail');

			if (el.hasClass('to-add')){
				// it's already been scheduled for adding, so remove it
				el.removeClass('to-add');
				// temporary code until we add a cool style for to-add class
				el.css('background', '');
                el.css('box-shadow', '');
				// get index of id then remove it
				var index = $.inArray(el.attr('id'), adddocs);
				if (index != -1) adddocs.splice(index, 1);
			} else {
				// add to-add class and add to collection
				adddocs.push(el.attr('id'));
				el.addClass('to-add');
				// temporary code until we add a cool style for to-add class
				el.css('background', 'url('+ipGalleryOptions.ipbaseurl+'/components/com_iproperty/assets/images/tick.png) no-repeat 98% 98%');
                el.css('box-shadow', '0 0 10px #51A351');
			}
		});

		// hook up save avail image form button
		$("#ip_availdocs_form_save").click(function(e) {
			e.preventDefault();
			// trigger the saveimageTags function
			$.each( adddocs, function(index, value){
				addAvailDoc(value);
			});
		});

		// hook up avail image filter form
		$("#ip_availdocs_filter").change(function(e) {
			e.preventDefault();
			// trigger the saveimageTags function
			getAvailableDocs();
		});

		// attach sortable to ul
		$("#ip_selected_docs").sortable({
			placeholder: "ip-state-highlight",
			forcePlaceholderSize: true,
			helper: 'clone',
			update: function(event, ui) {
				saveDocOrder();
			}
		}).disableSelection();

		//*********************************
		// GET IMAGE FUNCTIONS
		//*********************************

		function getSelectedDocs()
        {
			var data        = new Object();
			data.task       = 'ajax.ajaxLoadGallery';
			data.propid     = ipGalleryOptions.propid;
			data.own        = 1;
			data.limitstart = 0;
			data.type		= 1;
			data.limit      = 100;

			var result  = doAjaxRequestD(data, function(result){
				if(data = ajaxParseResultD(result)){
					if(data.totalimgs >= 1){
						if ($('#ipnoresultsdocs')) $('#ipnoresultsdocs').remove();
						// we got docs
						$.each(data.photos, function(i, photo) {
							buildSelectedDocs(photo, false);
						});
					} else {
						$('#ip_selected_docs').prepend('<li id="ipnoresultsdocs" class="alert alert-info span12 fade in">'+ipGalleryOptions.language.noresults+'</li>');
					}
				} else {
					var decodeData = $.parseJSON(result);
					var errortext = '<div class="alert alert-error fade in">';
					errortext +=	'<button type="button" class="close" data-dismiss="alert">&times;</button>';
					errortext += 	'<strong>'+ipGalleryOptions.language.warning+'</strong> '+decodeData.message;
					errortext += 	'</div>';
					$('#ip_message').html(errortext);
				}
			});
		}

		function getAvailableDocs()
        {
			var data        = new Object();
			data.task       = 'ajax.ajaxLoadGallery';
			data.propid     = ipGalleryOptions.propid;
			data.own        = 0;
			data.limitstart = ipPaginationDocs.limitstart;
			data.type		= 1;
			data.limit      = ipPaginationDocs.availlimit;
			data.filter		= $("#ip_avail_filter").val();

			var result  = doAjaxRequestD(data, function(result){
				if(data = ajaxParseResultD(result)){
					if(data.totalimgs >= 1){
						// clear ul in case this was a filter
						$("#ip_available_docs").empty();
						// set pagination vars
						ipPaginationDocs.totalavailimgs = parseInt(data.totalimgs);
						ipPaginationDocs.totalpages	= Math.ceil( ipPaginationDocs.totalavailimgs / ipPaginationDocs.availlimit );
						buildPaginationD();
						// remove no reults if it exists
						if ($('#ip_availnoresults')) $('#ip_availnoresults').remove();
						// we got docs, parse them
						$.each(data.photos, function(i, photo) {
							buildAvailableDocs(photo);
						});
					} else {
						$('#ip_available_docs').prepend('<li id="ip_availnoresults" class="alert alert-info span12 fade in">'+ipGalleryOptions.language.noresults+'</li>');
					}
				} else {
					var decodeData = $.parseJSON(result);
					var errortext = '<div class="alert alert-error fade in">';
					errortext +=	'<button type="button" class="close" data-dismiss="alert">&times;</button>';
					errortext += 	'<strong>'+ipGalleryOptions.language.warning+'</strong> '+decodeData.message;
					errortext += 	'</div>';
					$('#ip_message').html(errortext);
				}
			});
		}

		//*********************************
		// ACTION FUNCTIONS
		//*********************************

		// save image order
		function saveDocOrder()
        {
			var order   = $("#ip_selected_docs").sortable("toArray");
            // remove the "no results" tag if this element is empty
            order.splice( $.inArray("ipnoresultsdocs", order), 1 );
			var data    = new Object();
			data.task   = 'ajax.ajaxSort';
			data.order  = JSON.stringify(order);

			var result  = doAjaxRequestD(data, function(result){
				if(!ajaxParseResultD(result)){
					var decodeData = $.parseJSON(result);
					var errortext = '<div class="alert alert-error fade in">';
					errortext +=	'<button type="button" class="close" data-dismiss="alert">&times;</button>';
					errortext += 	'<strong>'+ipGalleryOptions.language.warning+'</strong> '+decodeData.message;
					errortext += 	'</div>';
					$('#ip_message').html(errortext);
				}
			});
		}

		// save image tags
		function saveDocTags(id, title, description)
        {
			var data    = new Object();
			data.task   = 'ajax.ajaxSaveImageTags';
			data.imgid  = id;
			data.title  = title;
			data.descr  = description;

			var result  = doAjaxRequestD(data, function(result){
				var el  = $('#'+data.imgid);
				if(ajaxParseResultD(result)){
					var id = data.imgid;
					// set the text for the li values
					$(el).data( { 'title': data.title, 'description': data.descr } );
					// set the tags
					$(el).find('i.icon-zoom-in').attr('data-content', data.descr).attr('data-original-title', data.title);
					// reset the form values
					$("#docid").val('');
					$("#doctitle").val('');
					$("#docdescription").val('');
					$("#ip_docs_form").modal("hide");
				} else {
					var decodeData = $.parseJSON(result);
					var errortext = '<div class="alert alert-error fade in">';
					errortext +=	'<button type="button" class="close" data-dismiss="alert">&times;</button>';
					errortext += 	'<strong>Warning!</strong> '+decodeData.message;
					errortext += 	'</div>';
					$(el).prepend(errortext);
				}
			});
		}

		// delete image
		function deleteDoc(img)
        {
			var data    = new Object();
			data.task   = 'ajax.ajaxDelete';
			data.img    = img;

			var result  = doAjaxRequestD(data, function(result){
				var el  = $('#'+data.img);
				if(ajaxParseResultD(result)){
					$(el).effect("fade", {}, 500, function() {
						(el).remove();
						$('#ip_selected_docs').sortable('refresh');
						if ($("#ip_selected_docs").sortable("toArray").length == 0) $('#ip_selected_docs').prepend('<li id="ipnoresultsdocs" class="alert alert-info span12 fade in">'+ipGalleryOptions.language.noresults+'</li>').fadeIn();
					});
				} else {
					var decodeData = $.parseJSON(result);
					var errortext = '<div class="alert alert-error fade in">';
					errortext +=	'<button type="button" class="close" data-dismiss="alert">&times;</button>';
					errortext += 	'<strong>Warning!</strong> '+decodeData.message;
					errortext += 	'</div>';
					$(el).prepend(errortext);
				}
			});
		}

		// add image
		function addAvailDoc(img)
        {
			var data    = new Object();
			data.task   = 'ajax.ajaxAdd';
			data.imgid  = img;
			data.propid = ipGalleryOptions.propid;
			var result  = doAjaxRequestD(data, function(result){
				var decodeData = $.parseJSON(result);
				if (ajaxParseResultD(result)){
					// if we still have the no result banner remove it
					if ($('#ipnoresultsdocs')){
						$('#ipnoresultsdocs').effect("fade", {}, 500, function() {
							$('#ipnoresultsdocs').remove();
						});
					}
					var data = decodeData.data;
					buildSelectedDocs(data, true);
					$('#ip_selected_docs').sortable('refresh');
					// remove the image from the avail list and array
					$("#"+img).effect("fade", {}, 500, function() {
						var index = $.inArray(img, adddocs);
						if (index != -1) adddocs.splice(index, 1);
					});
					// save ordering
					saveDocOrder();
					$("#ip_avail_modal").modal('hide');
				} else {
					var errortext = '<div class="alert alert-error fade in">';
					errortext +=	'<button type="button" class="close" data-dismiss="alert">&times;</button>';
					errortext += 	'<strong>Warning!</strong> '+decodeData.message;
					errortext += 	'</div>';
					$('#ip_message').html(errortext);
				}
			});
		}

		//*********************************
		// UTILITY FUNCTIONS
		//*********************************

		// build generic ajax request object
		function doAjaxRequestD(data, callback)
        {
			var client = (ipGalleryOptions.client == 'administrator') ? 'administrator/' : '';
			$.ajax({
			  url: ipGalleryOptions.ipbaseurl+client+'index.php?option=com_iproperty&'+ipGalleryOptions.iptoken+'=1&format=raw',
			  data: data,
			  type: 'POST',
			  cache: false,
			  error: function(request, status, error_message){
				console.log(status+' - '+error_message);
				var data = { 'status': 'error', 'message': error_message };
				callback(data);
			  },
			  success: function(data) {
				callback(data);
			  }
			});
		}

		// build the object and li for the selected docs sortable
		buildSelectedDocs = function(img, prepend)
        {
			var imgpath = ipGalleryOptions.ipbaseurl+'components/com_iproperty/assets/images/';
			if (img.type == '.pdf') {
				imgpath += 'pdf-icon.png';
			} else {
				imgpath += 'doc-icon.png';
			}

			var imgtitle = img.title ? img.title : img.fname+img.type;

			// build li
			var li      = $('<li class="ipimage" id="' + img.id + '" />');
			var html    = $('<div class="thumbnail ipimage" />');
            var contain = $('<div class="ipimage-container" />');
			var imgtag  = '<img class="ipimage" src="'+imgpath+'" width="50" alt="'+img.fname+img.type+'" />';
			var buttons = $('<p />');
			var edit    = $('<i class="icon-pencil" style="cursor: pointer;" rel="tooltip" title="'+ipGalleryOptions.language.edit+'"></i>');
			var del     = $('<i class="icon-minus" style="cursor: pointer;" rel="tooltip" title="'+ipGalleryOptions.language.del+'"></i>');
			var info    = $('<i class="icon-zoom-in" style="cursor: pointer;" rel="popover" data-content="'+img.description+'" data-original-title="'+imgtitle+'"</i>');

            // insert img into container in order to control via css
            contain.append(imgtag);

			// append elements
            li.append(html);
			html.append(contain);

			html.append(buttons);
			buttons.append(edit);
			buttons.append(del);
			buttons.append(info);

			// init popovers and tooltips
			$(edit).tooltip();
			$(del).tooltip();
			$(info).popover( {'trigger': 'hover'} );

			// css for image container to keep consistent layout
            $(contain).css({'cursor':'move', 'overflow':'hidden', 'margin-bottom':'5px'});

			// attach title and description data
			li.data( {'title': imgtitle, 'description': img.description} );

			if (prepend) {
				$("#ip_selected_docs").prepend(li);
			} else {
				$("#ip_selected_docs").append(li);
			}

			// check if noresults banner is in place
			if ($('#ipnoresultsdocs').length != 0){
				$('#ipnoresultsdocs').effect("fade", {}, 500, function() {
					$('#ipnoresultsdocs').remove();
				});
			}
		}

		// build available docs
		function buildAvailableDocs(img)
        {
			var imgpath = ipGalleryOptions.ipbaseurl+'components/com_iproperty/assets/images/';
			if (img.type == '.pdf') {
				imgpath += 'pdf-icon.png';
			} else {
				imgpath += 'doc-icon.png';
			}

			var imgtitle = img.title ? img.title : img.fname+img.type;

			// build li
			var li      = $('<li class="span3 ipavail" id="' + img.id + '" />');
			var	html    = $('<div class="thumbnail" />');
			var imgtag  = $('<img class="ipimage" style="cursor: pointer;" src="'+imgpath+'" width="50" />');
			var buttons = $('<p></p>');
			var add     = $('<i class="icon-plus" style="cursor: pointer;" rel="tooltip" title="'+ipGalleryOptions.language.add+'"></i>');
			var info    = $('<i id="info'+img.id+'" class="icon-zoom-in" style="cursor: pointer;" rel="popover" data-original-title="'+imgtitle+'"</i>');

			// append objects
			li.append(html);
			html.append(imgtag);
			html.append(buttons);
			buttons.append(add).append(info);

			// init popovers and tooltips
			$(add).tooltip();
			$(info).popover( { 'trigger': 'hover', 'html': true } );

			$("#ip_available_docs").append(li);
		}

		// helper function to parse ajax result object
		function ajaxParseResultD(data)
        {
			var decodeData = $.parseJSON(data);
			if (decodeData.status == 'error'){
				if (ipGalleryOptions.debug) console.dir(decodeData);
				return false;
			} else {
				// if we have json data returned, return it, otherwise return true
				if ((decodeData.status == 'ok') && decodeData.data) return decodeData.data;
				return true;
			}
		}

		//*********************************
		// PAGINATION FUNCTIONS
		//*********************************

		function buildPaginationD()
        {
			$("#ip_avail_pager_docs").empty();
			if(ipPaginationDocs.totalpages > 1){
				$("#ip_avail_pager_docs").append('<ul id="ip_avail_pagination_docs"></ul>');
				ipPaginationDocs.totalpagesets = (Math.ceil(ipPaginationDocs.totalpages / 10));
				// build numbered page links
				var y = (ipPaginationDocs.currentpageset == 1) ? 1 : ((ipPaginationDocs.currentpageset - 1) * 10) + 1;
				var z = y + 10;
				for (y; y < z; y++){
					var pageitem = $('<li id="page'+y+'"><a href="#">'+y+'</a></li>');
					if (y <= ipPaginationDocs.totalpages){
						if (ipPaginationDocs.currentpage == y) $(pageitem).addClass('active');
						$(pageitem).click( function(e) {
							e.preventDefault();
							var pageid = $(this).attr('id').substring(4);
							ipPageD(pageid);
						});
						$("#ip_avail_pagination_docs").append(pageitem);
					}
				}
				if(ipPaginationDocs.totalpagesets > 1) {
					if (ipPaginationDocs.currentpageset < ipPaginationDocs.totalpagesets){
						var nextitem = $('<li><a href="#">'+ipGalleryOptions.language.next+'</a></li>');
						$(nextitem).click( function(e) {
							e.preventDefault();
							ipNextPageSetD();
						});
						$("#ip_avail_pagination_docs").append(nextitem);
					}
					if (ipPaginationDocs.currentpageset > 1){
						var previtem = $('<li><a href="#">'+ipGalleryOptions.language.previous+'</a></li>');
						$(previtem).click( function(e) {
							e.preventDefault();
							ipPrevPageSetD();
						});
						$("#ip_avail_pagination_docs").prepend(previtem);
					}
				}
			} else {
				// no need for pager so remove that element
				$("#ip_avail_pager_docs").empty();
			}
		}

		function ipPageD(page)
        {
			// empty the available docs ul
			$("#ip_available_docs").empty();
			// set start number since we just know the batch size and page requested
			ipPaginationDocs.limitstart = (page - 1) * ipPaginationDocs.availlimit;
			// remove class from active pager, add to new page
			$("#page"+ipPaginationDocs.currentpage).removeClass('active');
			$("#page"+page).addClass('active');
			ipPaginationDocs.currentpage = page;
			// now request a new batch of avail docs
			getAvailableDocs();
		}

		function ipNextPageSetD()
        {
			ipPaginationDocs.currentpageset++;
			$("#ip_avail_pager_docs").empty();
			buildPaginationD();
		}

		function ipPrevPageSetD()
        {
			ipPaginationDocs.currentpageset--;
			$("#ip_avail_pager_docs").empty();
			buildPaginationD();
		}
	});
});