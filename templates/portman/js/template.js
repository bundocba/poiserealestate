/**
 * @package     Joomla.Site
 * @subpackage  Templates.protostar
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       3.2
 */

(function($)
{	
	$(document).ready(function()
	{		
		jQuery('.gallery-detail .prev').click(function(){
			var first_item = jQuery('.gallery-thumbnails-detail li:first');
			var next_item = first_item.next();
			var main_image = jQuery('.gallery-main-image-detail').find('img');
			first_item.remove();
			jQuery('.gallery-thumbnails-detail ul').append(first_item);
			var src = next_item.find('img').attr('src');
			main_image.attr('src',src);
			return false;
		});
		jQuery('.gallery-detail .next').click(function(){
			var last_item = jQuery('.gallery-thumbnails-detail li:last');
			var main_image = jQuery('.gallery-main-image-detail').find('img');
			last_item.remove();
			jQuery('.gallery-thumbnails-detail ul').prepend(last_item);
			var src = last_item.find('img').attr('src');
			main_image.attr('src',src);
			return false;
		});
		jQuery( ".gallery-thumbnails-detail" ).delegate( "li", "click", function() {
			var src = jQuery(this).find('img').attr('src');
			var main_image = jQuery('.gallery-main-image-detail').find('img');
			main_image.hide();
			main_image.attr('src',src);
			main_image.fadeIn();
		});
		var numer_item = jQuery('.gallery-thumbnails-detail').find('li').length;
		if(numer_item<2){
			jQuery('.gallery-thumbnails-detail').hide();
			jQuery('.gallery-detail .control').hide();
			jQuery('.gallery-main-image-detail').css('height','auto');
		}
		$(".agent").colorbox({inline:true, width:"300px",height:"250px"});
		$(".member").colorbox({inline:true, width:"300px",height:"250px"});
		$(".iframe").colorbox({iframe:true, width:"25%", height:"65%"});
		$(".youtube").colorbox({iframe:true, width:"90%", height:"90%"});
		
		jQuery('#dd').on('click', function(event){
			jQuery(this).toggleClass('active');
			event.stopPropagation();
		});
		
		jQuery(document).click(function() {
			jQuery('.wrapper-dropdown-2').removeClass('active');
		});
		
		var bodyTag = document.getElementsByTagName("body")[0];
		bodyTag.className = bodyTag.className.replace("noJS", "hasJS");
		
		jQuery("select.custom").each(function() {					
			var sb = new SelectBox({
				selectbox: jQuery(this),
				height: 150,
				width: 164
			});
		});
		
		jQuery('#membership').hover(function() {
		  jQuery(this).find('.list-group').stop(true, true).delay(200).fadeIn();
		}, function() {
		  jQuery(this).find('.list-group').stop(true, true).delay(200).fadeOut();
		});
	})
})(jQuery);
