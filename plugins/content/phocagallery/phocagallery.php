<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
jimport( 'joomla.plugin.plugin' );

if (!JComponentHelper::isEnabled('com_phocagallery', true)) {
	return JError::raiseError(JText::_('PLG_CONTENT_PHOCAGALLERY_ERROR'), JText::_('PLG_CONTENT_PHOCAGALLERY_COMPONENT_NOT_INSTALLED'));
}

if (! class_exists('PhocaGalleryLoader')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_phocagallery'.DS.'libraries'.DS.'loader.php');
}

phocagalleryimport('phocagallery.path.path');
phocagalleryimport('phocagallery.path.route');
phocagalleryimport('phocagallery.library.library');
phocagalleryimport('phocagallery.text.text');
phocagalleryimport('phocagallery.access.access');
phocagalleryimport('phocagallery.file.file');
phocagalleryimport('phocagallery.file.filethumbnail');
phocagalleryimport('phocagallery.image.image');
phocagalleryimport('phocagallery.image.imagefront');
phocagalleryimport('phocagallery.render.renderfront');
phocagalleryimport('phocagallery.render.renderadmin');
phocagalleryimport('phocagallery.render.renderdetailwindow');
phocagalleryimport('phocagallery.ordering.ordering');
phocagalleryimport('phocagallery.picasa.picasa');
phocagalleryimport('phocagallery.html.category');

class plgContentPhocaGallery extends JPlugin
{	
	var $_plugin_number	= 0;
	
	public function __construct(& $subject, $config) {
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}
	
	public function _setPluginNumber() {
		$this->_plugin_number = (int)$this->_plugin_number + 1;
	}
	
	public function onContentPrepare($context, &$article, &$params, $page = 0) {
	
		$user		= &JFactory::getUser();
		$gid 		= $user->get('aid', 0);
		$db 		= &JFactory::getDBO();
		//$menu 		= &JSite::getMenu();
		$document	= &JFactory::getDocument();
		$path 		= PhocaGalleryPath::getPath();
		
		// PARAMS - direct from Phoca Gallery Global configuration
		$component			=	'com_phocagallery';
		$paramsC			= JComponentHelper::getParams($component) ;
		
		// LIBRARY
		$library 								= &PhocaGalleryLibrary::getLibrary();
		$libraries['pg-css-sbox-plugin'] 		= $library->getLibrary('pg-css-sbox-plugin');
		$libraries['pg-css-pg-plugin'] 			= $library->getLibrary('pg-css-pg-plugin');
		$libraries['pg-css-ie'] 				= $library->getLibrary('pg-css-ie');
		$libraries['pg-group-shadowbox']		= $library->getLibrary('pg-group-shadowbox');
		$libraries['pg-group-highslide']		= $library->getLibrary('pg-group-highslide');
		$libraries['pg-group-highslide-slideshow']	= $library->getLibrary('pg-group-highslide-slideshow');
		$libraries['pg-overlib-group']			= $library->getLibrary('pg-overlib-group');
		$libraries['pg-group-jak-pl']			= $library->getLibrary('pg-group-jak-pl');
		
		// PicLens CSS and JS will be loaded only one time in the site (pg-pl-piclens)
		// BUT PicLens Category will be loaded everytime new category should be displayed on the site
		$libraries['pg-pl-piclens']	= $library->getLibrary('pg-pl-piclens');
		
		
		// Start Plugin
		$regex_one		= '/({phocagallery\s*)(.*?)(})/si';
		$regex_all		= '/{phocagallery\s*.*?}/si';
		$matches 		= array();
		$count_matches	= preg_match_all($regex_all,$article->text,$matches,PREG_OFFSET_CAPTURE | PREG_PATTERN_ORDER);
		$cssPgPlugin	= '';
		$cssSbox		= '';
		
	// Start if count_matches
	if ($count_matches != 0) {
		
		
		PhocaGalleryRenderFront::renderAllCSS();
		
	
		for($i = 0; $i < $count_matches; $i++) {
			
			$this->_setPluginNumber();
			// Plugin variables
			$view 					= '';
			$catid					= 0;
			$imageid				= 0;
			$imagerandom			= 0;
			$image_background_shadow	= $paramsC->get( 'image_background_shadow', 'shadow1');
			$limitstart				= 0;
			$limitcount				= 0;
			$switch_width			= $paramsC->get( 'switch_width', 640);
			$switch_height			= $paramsC->get( 'switch_height', 480);
			$basic_image_id			= $paramsC->get( 'switch_image', 0);
			$switch_fixed_size		= $paramsC->get( 'switch_fixed_size', 0);
			$enable_switch			= 0;
			
			$tmpl['display_name'] 			= $paramsC->get( 'display_name', 1);
			$tmpl['display_icon_detail'] 	= $paramsC->get( 'display_icon_detail', 1);
			$tmpl['display_icon_download'] 	= $paramsC->get( 'display_icon_download', 1);
			$tmpl['detail_window'] 	= $paramsC->get( 'detail_window', 0);
			
			// No boxplus in plugin:
		/*	if ($tmpl['detail_window']  == 9 || $tmpl['detail_window']  == 10) {
				$tmpl['detail_window'] = 2;
			}*/
			
			
			$detail_buttons			= $paramsC->get( 'detail_buttons', 1);
			$hide_categories		= $paramsC->get( 'hide_categories', '');
			
			$namefontsize			= $paramsC->get( 'font_size_name', 12);
			$namenumchar			= $paramsC->get( 'char_length_name', 11);
			
			$display_description	= $paramsC->get( 'display_description_detail', 0);
			$description_height		= $paramsC->get( 'description_detail_height', 16);
			$category_box_space		= $paramsC->get( 'category_box_space', 0);
			
			$margin_box 			= $paramsC->get( 'margin_box', 5);
			$padding_box 			= $paramsC->get( 'padding_box', 5);
			
			// CSS
			$font_color 			= $paramsC->get( 'font_color', '#b36b00');
			$background_color 		= $paramsC->get( 'background_color', '#fcfcfc');
			$background_color_hover = $paramsC->get( 'background_color_hover', '#f5f5f5');
			$image_background_color = $paramsC->get( 'image_background_color', '#f5f5f5');
			$border_color 			= $paramsC->get( 'border_color', '#e8e8e8');
			$border_color_hover 	= $paramsC->get( 'border_color_hover', '#b36b00');
			
			$highslide_class		= $paramsC->get( 'highslide_class', 'rounded-white');
			$highslide_opacity		= $paramsC->get( 'highslide_opacity', 0);
			$highslide_outline_type	= $paramsC->get( 'highslide_outline_type', 'rounded-white');
			$highslide_fullimg		= $paramsC->get( 'highslide_fullimg', 0);
			$highslide_slideshow	= $paramsC->get( 'highslide_slideshow', 1);
			$highslide_close_button	= $paramsC->get( 'highslide_close_button', 0);
			$tmpl['enablecustomcss']			= $paramsC->get( 'enable_custom_css', 0);
			$tmpl['customcss']					= $paramsC->get( 'custom_css', '');
			$tmpl['displayratingimg']			= $paramsC->get( 'display_img_rating', 0);
			
			$tmpl['jakslideshowdelay']			= $paramsC->get( 'jak_slideshow_delay', 5);
			$tmpl['jakorientation']				= $paramsC->get( 'jak_orientation', 'none');
			$tmpl['jakdescription']				= $paramsC->get( 'jak_description', 1);
			$tmpl['jakdescriptionheight']		= $paramsC->get( 'jak_description_height', 0);
			$tmpl['imageordering']				= $paramsC->get( 'image_ordering', 9);
			$tmpl['highslidedescription']		= $paramsC->get( 'highslide_description', 0 );
			$tmpl['pluginlink']					= 0;
			$tmpl['jakdatajs'] 					= array();
			$minimum_box_width 					= '';
			
			$tmpl['boxplus_theme']				= $paramsC->get( 'boxplus_theme', 'lightsquare');
			$tmpl['boxplus_bautocenter']		= (int)$paramsC->get( 'boxplus_bautocenter', 1);
			$tmpl['boxplus_autofit']			= (int)$paramsC->get( 'boxplus_autofit', 1);
			$tmpl['boxplus_slideshow']			= (int)$paramsC->get( 'boxplus_slideshow', 0);
			$tmpl['boxplus_loop']				= (int)$paramsC->get( 'boxplus_loop', 0);
			$tmpl['boxplus_captions']			= $paramsC->get( 'boxplus_captions', 'bottom');
			$tmpl['boxplus_thumbs']				= $paramsC->get( 'boxplus_thumbs', 'inside');
			$tmpl['boxplus_duration']			= (int)$paramsC->get( 'boxplus_duration', 250);
			$tmpl['boxplus_transition']			= $paramsC->get( 'boxplus_transition', 'linear');
			$tmpl['boxplus_contextmenu']		= (int)$paramsC->get( 'boxplus_contextmenu', 1);
			
			
			// Component settings - some behaviour is set in component and cannot be set in plugin
			// but plugin needs to accept it 
			$tmplCom['displayicondownload']		= $paramsC->get( 'display_icon_download', 0 );
			
			$plugin_type			= 0;
			$padding_mosaic			= 3;
			$float					= '';
			$enable_piclens			= $paramsC->get( 'enable_piclens', 0);
			$enable_overlib			= $paramsC->get( 'enable_oberlib', 0);
			
			
			
			// Image categories
			$img_cat				= 1;
			$img_cat_size			= 'small';
			
			// Get plugin parameters
			$phocagallery	= $matches[0][$i][0];
			preg_match($regex_one,$phocagallery,$phocagallery_parts);
			$parts			= explode("|", $phocagallery_parts[2]);
			$values_replace = array ("/^'/", "/'$/", "/^&#39;/", "/&#39;$/", "/<br \/>/");

			foreach($parts as $key => $value) {
				$values = explode("=", $value, 2);
				
				foreach ($values_replace as $key2 => $values2) {
					$values = preg_replace($values2, '', $values);
				}
				
				// Get plugin parameters from article
					 if($values[0]=='view')				{$view					= $values[1];}
				else if($values[0]=='categoryid')		{$catid					= $values[1];}
				else if($values[0]=='imageid')			{$imageid				= $values[1];}
				else if($values[0]=='imagerandom')		{$imagerandom			= $values[1];}
				else if($values[0]=='imageshadow')		{$image_background_shadow			= $values[1];}
				else if($values[0]=='limitstart')		{$limitstart			= $values[1];}
				else if($values[0]=='limitcount')		{$limitcount			= $values[1];}
				else if($values[0]=='detail')			{$tmpl['detail_window']			= $values[1];}
				else if($values[0]=='displayname')		{$tmpl['display_name']			= $values[1];}
				else if($values[0]=='displaydetail')	{$tmpl['display_icon_detail']		= $values[1];}
				else if($values[0]=='displaydownload')	{$tmpl['display_icon_download']	= $values[1];}
				else if($values[0]=='displaybuttons')	{$detail_buttons		= $values[1];}
			//	else if($values[0]=='displayratingimg')	{$tmpl['displayratingimg']	= $values[1];}
				
				else if($values[0]=='namefontsize')		{$namefontsize			= $values[1];}
				else if($values[0]=='namenumchar')		{$namenumchar			= $values[1];}
				
				else if($values[0]=='displaydescription'){$display_description	= $values[1];}
				else if($values[0]=='descriptionheight'){$description_height	= $values[1];}
				else if($values[0]=='hidecategories')	{$hide_categories		= $values[1];}
				else if($values[0]=='boxspace')			{$category_box_space	= $values[1];}
				
				// CSS
				else if($values[0]=='fontcolor')		{$font_color				= $values[1];}
				else if($values[0]=='bgcolor')			{$background_color			= $values[1];}
				else if($values[0]=='bgcolorhover')		{$background_color_hover	= $values[1];}
				else if($values[0]=='imagebgcolor')		{$image_background_color	= $values[1];}
				else if($values[0]=='bordercolor')		{$border_color				= $values[1];}
				else if($values[0]=='bordercolorhover')	{$border_color_hover		= $values[1];}
				
				else if($values[0]=='hsclass')			{$highslide_class			= $values[1];}
				else if($values[0]=='hsopacity')		{$highslide_opacity			= $values[1];}
				else if($values[0]=='hsoutlinetype')	{$highslide_outline_type	= $values[1];}
				else if($values[0]=='hsfullimg')		{$highslide_fullimg			= $values[1];}
				else if($values[0]=='hsslideshow')		{$highslide_slideshow		= $values[1];}
				else if($values[0]=='hsclosebutton')	{$highslide_close_button	= $values[1];}
				
				else if($values[0]=='float')			{$float	= $values[1];}
				
				else if($values[0]=='jakslideshowdelay')	{$tmpl['jakslideshowdelay']		= $values[1];}
				else if($values[0]=='jakorientation')		{$tmpl['jakorientation']		= $values[1];}
				else if($values[0]=='jakdescription')		{$tmpl['jakdescription']		= $values[1];}
				else if($values[0]=='jakdescriptionheight')	{$tmpl['jakdescriptionheight']	= $values[1];}
				else if($values[0]=='imageordering')		{$tmpl['imageordering']			= $values[1];}
				else if($values[0]=='pluginlink')			{$tmpl['pluginlink']			= $values[1];}
				else if($values[0]=='highslidedescription')	{$tmpl['highslidedescription']	= $values[1];}
				else if($values[0]=='type')					{$plugin_type					= $values[1];}
				else if($values[0]=='paddingmosaic')		{$padding_mosaic				= $values[1];}
			
				else if($values[0]=='minboxwidth')			{$minimum_box_width			= $values[1];}
				//Image categories
				else if($values[0]=='imagecategories')		{$img_cat				= $values[1];}
				else if($values[0]=='imagecategoriessize')	{$img_cat_size			= $values[1];}
				else if($values[0]=='switchwidth')			{$switch_width			= $values[1];}
				else if($values[0]=='switchheight')			{$switch_height			= $values[1];}
				else if($values[0]=='basicimageid')			{$basic_image_id		= $values[1];}
				else if($values[0]=='enableswitch')			{$enable_switch			= $values[1];}
				else if($values[0]=='switchfixedsize')		{$switch_fixed_size		= $values[1];}
				
				else if($values[0]=='piclens')				{$enable_piclens				= $values[1];}
				else if($values[0]=='overlib')				{$enable_overlib				= $values[1];}
				else if($values[0]=='enablecustomcss')			{$tmpl['enablecustomcss']					= $values[1];}
			}
			
		
	
			
			// If Module link is to category or categories, the detail window method needs to be set to no popup
			if ((int)$tmpl['pluginlink'] > 0) {
				$tmpl['detail_window'] = 7;
			}
			// Every loop of plugin has own number
			// Add custom CSS for every image (every image can have other CSS, Hover doesn't work in IE6)
			
			$iCss = $this->_plugin_number;
			if ($tmpl['enablecustomcss'] == 1) {} else {
				
				$cssPgPlugin	.= " .pgplugin".$iCss." {border:1px solid $border_color ; background: $background_color ;}\n"
								." .pgplugin".$iCss.":hover, .pgplugin".$i.".hover {border:1px solid $border_color_hover ; background: $background_color_hover ;}\n";
								
			}
			
			
			$tmpl['formaticon'] 		= $paramsC->get( 'icon_format', 'gif' );
			
			$tmpl['imagewidth']			= $medium_image_width 		= $paramsC->get( 'medium_image_width', 100 );
			$tmpl['imageheight']		= $medium_image_height 		= $paramsC->get( 'medium_image_height', 100 );
			$popup_width 				= $paramsC->get( 'front_modal_box_width', 680 );
			$popup_height 				= $paramsC->get( 'front_modal_box_height', 560 );
			$small_image_width 			= $paramsC->get( 'small_image_width', 50 );
			$small_image_height 		= $paramsC->get( 'small_image_height', 50 );
			$large_image_width 			= $paramsC->get( 'large_image_width', 640 );
			$large_image_height 		= $paramsC->get( 'large_image_height', 480 );
			
			
			$tmpl['enable_multibox']			= $paramsC->get( 'enable_multibox', 0);
			$tmpl['multibox_height']			= (int)$paramsC->get( 'multibox_height', 560 );	
			$tmpl['multibox_width']				= (int)$paramsC->get( 'multibox_width', 980 );
			
			// Multibox
			if ($tmpl['enable_multibox']	== 1) {
				$popup_width 							= $tmpl['multibox_width'];
				$popup_height 							= $tmpl['multibox_height'];
			}
			

			
			// Correct Picasa Images - get Info
			switch($img_cat_size) {
				// medium
				case 1:
				case 5:
					$tmpl['picasa_correct_width']	= (int)$paramsC->get( 'medium_image_width', 100 );	
					$tmpl['picasa_correct_height']	= (int)$paramsC->get( 'medium_image_height', 100 );
				break;
				
				case 0:
				case 4:
				default:
					$tmpl['picasa_correct_width']	= (int)$paramsC->get( 'small_image_width', 50 );	
					$tmpl['picasa_correct_height']	= (int)$paramsC->get( 'small_image_height', 50 );
				break;
			}
			
			if ($plugin_type == 1) {
				$imgSize	= 'small';
			} else if ($plugin_type == 2) {
				$imgSize	= 'large';
			} else {
				$imgSize	= 'medium';
			}
			
			if ($display_description == 1) {
				$popup_height	= $popup_height + $description_height;
			}
			
			// Detail buttons in detail view
			if ($detail_buttons != 1) {
				$popup_height	= $popup_height - 45;
			}
			$popup_height_rating = $popup_height;
			if ($tmpl['displayratingimg'] == 1) {
				$popup_height_rating	= $popup_height + 35;
			}
			
			$modal_box_overlay_color 	= $paramsC->get( 'modal_box_overlay_color','#000000' );
			$modal_box_overlay_opacity 	= $paramsC->get( 'modal_box_overlay_opacity', 0.3 );
			$modal_box_border_color 	= $paramsC->get( 'modal_box_border_color', '#6b6b6b' );
			$modal_box_border_width 	= $paramsC->get( 'modal_box_border_width', 2 );
			
			$tmpl['olbgcolor']				= $paramsC->get( 'ol_bg_color', '#666666' );
			$tmpl['olfgcolor']				= $paramsC->get( 'ol_fg_color', '#f6f6f6' );
			$tmpl['oltfcolor']				= $paramsC->get( 'ol_tf_color', '#000000' );
			$tmpl['olcfcolor']				= $paramsC->get( 'ol_cf_color', '#ffffff' );
			$tmpl['overliboverlayopacity']	= $paramsC->get( 'overlib_overlay_opacity', 0.7 );
			
			
			
			// =======================================================
			// DIFFERENT METHODS OF DISPLAYING THE DETAIL VIEW
			// =======================================================
			// MODAL - will be displayed in case e.g. highslide or shadowbox too, because in there are more links 
			JHtml::_('behavior.modal', 'a.pg-modal-button');

			$btn = new PhocaGalleryRenderDetailWindow();
			$btn->popupWidth 			= $popup_width;
			$btn->popupHeight 			= $popup_height;
			$btn->mbOverlayOpacity		= $modal_box_overlay_opacity;
			$btn->sbSlideshowDelay		= $paramsC->get( 'sb_slideshow_delay', 5 );
			$btn->sbSettings			= $paramsC->get( 'sb_settings', "overlayColor: '#000',overlayOpacity:0.5,resizeDuration:0.35,displayCounter:true,displayNav:true" );
			$btn->hsSlideshow			= $highslide_slideshow;
			$btn->hsClass				= $highslide_class;
			$btn->hsOutlineType			= $highslide_outline_type;
			$btn->hsOpacity				= $highslide_opacity;
			$btn->hsCloseButton			= $highslide_close_button;
			$btn->hsFullImg				= $highslide_fullimg;
			$btn->jakDescHeight			= $tmpl['jakdescriptionheight'];
			$btn->jakDescWidth			= '';
			$btn->jakOrientation		= $tmpl['jakorientation'];
			$btn->jakSlideshowDelay		= $tmpl['jakslideshowdelay'];
			$btn->bpTheme 				= $paramsC->get( 'boxplus_theme', 'lightsquare');
			$btn->bpBautocenter 		= (int)$paramsC->get( 'boxplus_bautocenter', 1);	
			$btn->bpAutofit 			= (int)$paramsC->get( 'boxplus_autofit', 1);
			$btn->bpSlideshow 			= (int)$paramsC->get( 'boxplus_slideshow', 0);
			$btn->bpLoop 				= (int)$paramsC->get( 'boxplus_loop', 0);
			$btn->bpCaptions 			= $paramsC->get( 'boxplus_captions', 'bottom');
			$btn->bpThumbs 				= $paramsC->get( 'boxplus_thumbs', 'inside');
			$btn->bpDuration 			= (int)$paramsC->get( 'boxplus_duration', 250);
			$btn->bpTransition 			= $paramsC->get( 'boxplus_transition', 'linear');
			$btn->bpContextmenu 		= (int)$paramsC->get( 'boxplus_contextmenu', 1);
			$btn->extension				= 'Pl';
			
			
			
			// Random Number - because of more modules on the site
			$randName	= 'PhocaGalleryPl' . substr(md5(uniqid(time())), 0, 8);
			//$randName2	= 'PhocaGalleryRIM2' . substr(md5(uniqid(time())), 0, 8);
			$btn->jakRandName 			= 'optgjaksPl'.$randName;

			$btn->setButtons($tmpl['detail_window'], $libraries, $library);
			$button = $btn->getB1();
			$button2 = $btn->getB2();
			$buttonOther = $btn->getB3();

			//krumo($tmpl['detail_window']);
			$tmpl['highslideonclick']	= '';// for using with highslide
			if (isset($button->highslideonclick)) {
				$tmpl['highslideonclick'] = $button->highslideonclick;// TODO
			}
			$tmpl['highslideonclick2']	= '';
			if (isset($button->highslideonclick2)) {
				$tmpl['highslideonclick2'] = $button->highslideonclick2;// TODO
			}

			$folderButton = new JObject();
			$folderButton->set('name', 'image');
			$folderButton->set('options', "");		
			
			$output	='';
			$output .= '<div class="phocagallery">' . "\n";
						
			$hideCat		= trim( $hide_categories );
			$hideCatArray	= explode( ',', $hide_categories );
			$hideCatSql		= '';
			if (is_array($hideCatArray)) {
				foreach ($hideCatArray as $value) {
					$hideCatSql .= ' AND cc.id != '. (int) trim($value) .' ';
				}
			}
			// by vogo
			$uniqueCatSql	= '';
			if ($catid > 0) {
				$uniqueCatSql	= ' AND cc.id = '. $catid .'';	
			}
			
			if ($view == 'categories') {
				//CATEGORIES
				$queryc = 'SELECT cc.*, a.catid, COUNT(a.id) AS numlinks,'
				. ' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(\':\', cc.id, cc.alias) ELSE cc.id END as slug'
				. ' FROM #__phocagallery_categories AS cc'
				. ' LEFT JOIN #__phocagallery AS a ON a.catid = cc.id'
				. ' WHERE a.published = 1'
				. ' AND cc.published = 1'
				. ' AND cc.approved = 1'
				. ' AND a.approved = 1'
				. $hideCatSql
				. $uniqueCatSql
				. ' GROUP BY cc.id'
				. ' ORDER BY cc.ordering';

				$querysc = 'SELECT cc.title AS text, cc.id AS value, cc.parent_id as parentid'
				. ' FROM #__phocagallery_categories AS cc'
				. ' WHERE cc.published = 1'
				. ' AND cc.approved = 1'
				. ' ORDER BY cc.ordering';

				$data_outcome 		= '';
				$data_outcome_array = '';
			
				$db->setQuery($queryc);
				$outcome_data = $db->loadObjectList();
			
				$db->setQuery($querysc);
				$outcome_subcategories = $db->loadObjectList();
			
				$tree = array();
				$text = '';
				$tree = PhocaGalleryCategory::CategoryTreeOption($outcome_subcategories, $tree, 0, $text, -1);
				
				foreach ($tree as $key => $value) {
					foreach ($outcome_data as $key2 => $value2) {
						if ($value->value == $value2->id) {
							
							$data_outcome 					= new JObject();
							$data_outcome->id				= $value2->id;
							$data_outcome->parent_id		= $value2->parent_id;
							$data_outcome->title			= $value->text;
							$data_outcome->name				= $value2->name;
							$data_outcome->alias			= $value2->alias;
							$data_outcome->image			= $value2->image;
							$data_outcome->section			= $value2->section;
							$data_outcome->image_position	= $value2->image_position;
							$data_outcome->description		= $value2->description;
							$data_outcome->published		= $value2->published;
							$data_outcome->editor			= $value2->editor;
							$data_outcome->ordering			= $value2->ordering;
							$data_outcome->access			= $value2->access;
							$data_outcome->accessuserid		= $value2->accessuserid;
							$data_outcome->uploaduserid		= $value2->uploaduserid;
							$data_outcome->deleteuserid		= $value2->deleteuserid;
							$data_outcome->count			= $value2->count;
							$data_outcome->params			= $value2->params;
							$data_outcome->catid			= $value2->catid;
							$data_outcome->numlinks			= $value2->numlinks;
							$data_outcome->slug				= $value2->slug;
							$data_outcome->link				= '';
							$data_outcome->filename			= '';
							$data_outcome->linkthumbnailpath= '';
							$data_outcome->extm				= '';
							$data_outcome->exts				= '';
							$data_outcome->extw				= '';
							$data_outcome->exth				= '';
							$data_outcome->extid			= '';
							
							//FILENAME
							$queryfn = 'SELECT filename, extm, exts, extw, exth, extid'
							.' FROM #__phocagallery'
							.' WHERE catid='.$value2->id
							.' AND published = 1'
							.' AND approved = 1'
							.' ORDER BY ordering LIMIT 1';
							$db->setQuery($queryfn);
							$outcome_filename	    = $db->loadObjectList();
							$data_outcome->filename	= $outcome_filename[0]->filename;
							$data_outcome->extm		= $outcome_filename[0]->extm;
							$data_outcome->exts		= $outcome_filename[0]->exts;
							$data_outcome->extw		= $outcome_filename[0]->extw;
							$data_outcome->exth		= $outcome_filename[0]->exth;
							$data_outcome->extid	= $outcome_filename[0]->extid;
							
							$data_outcome_array[] 	= $data_outcome;
						}	
					}
				}
			
				if ($img_cat == 1) {
					$medium_image_height	= $medium_image_height + 18;
					$medium_image_width 	= $medium_image_width + 18;
					$small_image_width		= $small_image_width +18;
					$small_image_height		= $small_image_height +18;
						
					$output .= '<table border="0">';
					foreach ($data_outcome_array as $category) {
						// ROUTE
						$category->link = JRoute::_(PhocaGalleryRoute::getCategoryRoute($category->id, $category->alias));
						
						$imgCatSizeHelper = 'small';
						
						$mediumCSS 	= '';//'background: url(\''.JURI::base(true).'/media/com_phocagallery/images/shadow1.png\') 50% 50% no-repeat;height:'.$medium_image_height	.'px;width:'.$medium_image_width.'px;';
						$smallCSS	= '';//'background: url(\''.JURI::base(true).'/media/com_phocagallery/images/shadow3.png\') 50% 50% no-repeat;height:'.$small_image_height	.'px;width:'.$small_image_width.'px;';
						
						switch ($img_cat_size) {	
							case 7:
							case 5:							
								$imageBg = $mediumCSS;
							break;
							case 6:
							case 4:							
								$imageBg = $smallCSS;
							break;
							default:
								$imageBg = '';
							break;
						}
						
						// Display Key Icon (in case we want to display unaccessable categories in list view)
						$rightDisplayKey  = 1;
						
						// we simulate that we want not to display unaccessable categories
						// so we get rightDisplayKey = 0 then the key will be displayed
						if (isset($category)) {
							//$rightDisplayKey = PhocaGalleryAccess::getUserRight ('accessuserid', $category->accessuserid ,$category->access, $user->get('aid', 0), $user->get('id', 0), 0);
							$rightDisplayKey = PhocaGalleryAccess::getUserRight('accessuserid', $category->accessuserid, $category->access, $user->getAuthorisedViewLevels(), $user->get('id', 0), 0);
						}
						
						
						if (isset($category->extid) && $category->extid != '') {
								
							$file_thumbnail = PhocaGalleryImageFront::displayCategoriesExtImgOrFolder($category->exts, $category->extm, $category->extw, $category->exth,(int)$img_cat_size, $rightDisplayKey);
							$category->linkthumbnailpath	= $file_thumbnail->rel;
							$category->extw					= $file_thumbnail->extw;
							$category->exth					= $file_thumbnail->exth;
							$category->extpic				= $file_thumbnail->extpic;
						} else {
							$file_thumbnail = PhocaGalleryImageFront::displayCategoriesImageOrFolder($category->filename, (int)$img_cat_size, $rightDisplayKey);
							$category->linkthumbnailpath = $file_thumbnail->rel;
						}

						
						//Output
						$output .= '<tr>'
							.'<td align="center" valign="middle" style="'.$imageBg.'"><a href="'.$category->link.'">';
							
							if (isset($category->extpic) && $category->extpic != '') {
								$correctImageRes = PhocaGalleryPicasa::correctSizeWithRate($category->extw, $category->exth, $tmpl['picasa_correct_width'], $tmpl['picasa_correct_height']);
							
								$output .='<img class="pg-image" src="'.$category->linkthumbnailpath.'" alt="'.$category->title.'" style="border:0" width="'. $correctImageRes['width'].'" height="'.$correctImageRes['height'].'" />';
							} else {
								$output .='<img class="pg-image" src="'.JURI::base(true).'/'.$category->linkthumbnailpath.'" alt="'.$category->title.'" style="border:0" />';
							}
							$output .='</a></td>'
							.'<td><a href="'.$category->link.'" class="category'.$this->params->get( 'pageclass_sfx' ).'">'.$category->title.'</a>&nbsp;'
							.'<span class="small">('.$category->numlinks.')</span></td>'
							.'</tr>';
					}
					$output .= '</table>';
				
				} else {
					$output .= '<ul>';
					
					foreach ($data_outcome_array as $category) {
						// ROUTE
						$category->link = JRoute::_(PhocaGalleryRoute::getCategoryRoute($category->id, $category->alias));
					
						$output .='<li>'
								 .'<a href="'.$category->link.'" class="category'.$this->params->get( 'pageclass_sfx' ).'">'
								 . $category->title.'</a>&nbsp;<span class="small">('.$category->numlinks.')</span>'
								 .'</li>';
					}
					$output .= '</ul>';
				}
			}
			if ($view == 'category') {
				
				$where = '';
				
				// Only one image
				if ($imageid > 0) {
					$where = ' AND a.id = '. $imageid;
				}
				
				// Random image
				if ($imagerandom == 1 && $catid > 0) {
					
					$query = 'SELECT id'
					.' FROM #__phocagallery'
					.' WHERE catid = '.(int) $catid
					.' AND published = 1'
					.' AND approved = 1'
					.' ORDER BY RAND()';
			
					$db->setQuery($query);
					$idQuery =& $db->loadObject();
					if (!empty($idQuery)) {
						$where = ' AND a.id = '. $idQuery->id;
					}
				}
				
				$limit = '';
				
				// Count of images (LIMIT 0, 20)
				if ($limitcount > 0) {
					$limit = ' LIMIT '.$limitstart.', '.$limitcount;
				}
								
				if ($tmpl['imageordering'] == 9) {
					$imageOrdering = ' ORDER BY RAND()'; 
				} else {
					$iOA = PhocaGalleryOrdering::getOrderingString($tmpl['imageordering']);
					$imageOrdering = $iOA['output'];
				}
				
				
				$query = 'SELECT cc.id, cc.alias as catalias, a.id, a.catid, a.title, a.alias, a.filename, a.description, a.extm, a.exts, a.extw, a.exth, a.extid, a.extl, a.exto,'
				. ' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(\':\', cc.id, cc.alias) ELSE cc.id END as catslug, '
				. ' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END as slug'
				. ' FROM #__phocagallery_categories AS cc'
				. ' LEFT JOIN #__phocagallery AS a ON a.catid = cc.id'
				. ' WHERE a.catid = '.(int) $catid
				. ' AND a.published = 1'
				. ' AND a.approved = 1'
				. ' AND cc.published = 1'
				. ' AND cc.approved = 1'
				. $where
				. $imageOrdering
				. $limit;
			
				$db->setQuery($query);
				$category =& $db->loadObjectList();
				$output = '<div class="gallery">';
					if(isset($category[0])){
						$output .= '<div class="gallery-main-image">';
							$output .= '<img src="images/phocagallery/'.$category[0]->filename.'"/>';
						$output .= '</div>';
					}
					$output .= '<div class="gallery-thumbnails">';
						$output .= '<ul>';
						foreach ($category as $image) {
							
							$output .= '<li><img class="pg-image"  src="images/phocagallery/'.$image->filename.'" alt="'.$image->title.'" /></li>';
							
						}
						$output .= '</ul>';
					$output .= '</div>';
					$output .= '<a href="" class="prev control"></a>';
					$output .= '<a href="" class="next control"></a>';
				$output .= '</div>';
			}
			
			
			if ($float == '') {
				$output .= '<div style="clear:both"> </div>';
			}
			
			if ($tmpl['detail_window'] == 6) {
				$output .= '<script type="text/javascript">'
				.'var gjaksPl'.$randName.' = new SZN.LightBox(dataJakJsPl'.$randName.', optgjaksPl'.$randName.');'
				.'</script>';
			}
			
			$article->text = preg_replace($regex_all, $output, $article->text, 1);
			
			// ADD JAK DATA CSS style

			if ( $tmpl['detail_window'] == 6 ) {
				$scriptJAK = '<script type="text/javascript">'
				. 'var dataJakJsPl'.$randName.' = [';
				if (!empty($tmpl['jakdatajs'])) {
					$scriptJAK .= implode($tmpl['jakdatajs'], ',');
				}
				$scriptJAK .= ']'
				. '</script>';
				$document->addCustomTag($scriptJAK);
			}
			
		}
		
		// CUSTOM CSS - For all items it will be the same
		if ( $libraries['pg-css-sbox-plugin']->value == 0 ) {
			$document->addCustomTag( "<style type=\"text/css\">\n" . $cssSbox . "\n" . " </style>\n");
			$library->setLibrary('pg-css-sbox-plugin', 1);
		}
		// All custom CSS tags will be added into one CSS area
	//	if ( $libraries['pg-css-pg-plugin']->value == 0 ) {
			$document->addCustomTag( "<style type=\"text/css\">\n" . $cssPgPlugin . "\n" . " </style>\n");
			$library->setLibrary('pg-css-pg-plugin', 1);
	//	}
		
		
	  } // end if count_matches
		return true;
	}
}
?>