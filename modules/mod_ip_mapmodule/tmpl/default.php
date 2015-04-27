<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Iproperty
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');

$document 	= JFactory::getDocument();
$settings 	= ipropertyAdmin::config();
$data 		= json_encode((array) $items);
$map_js 	= "var mapData = ".$data.";";
$curr_lang  = JFactory::getLanguage();
$rtl		= $curr_lang->isRTL() ? 1 : 0;
// get currency options
$c_format	= $settings->nformat ? ',' : '.';
$c_symbol	= $settings->currency ?: '$';
$c_round	= $settings->currency_digits ?: '-2';
$c_position = $settings->currency_pos ? '%n %s' : '%s %n'; // TODO: not sure if supported by currency plugin

// load the langfile for the component
JFactory::getLanguage()->load('com_iproperty', JPATH_SITE);

// load currency format and correct language file if it exists
$document->addScript(JURI::root(true).'/components/com_iproperty/assets/js/currency/jquery.formatCurrency-1.4.0.pack.js');
$currencyformat = '{ digitGroupSymbol: "'.$c_format.'", symbol: "'.$c_symbol.'", roundToDecimalPlace: '.$c_round.', positiveFormat: "'.$c_position.'" }';

// check for template map and property preview icons
$map_house_icon         = '/components/com_iproperty/assets/images/map/icon56.png';

// load the appropriate script
$mapscript  = JURI::root(true).'/modules/mod_ip_mapmodule/';
$mapscript .= $params->get('maptype', 0) ? 'bing.js' : 'google.js'; 
$document->addScript( $mapscript );
if (!defined('IPGMAP')){
	$mapsurl = $params->get('maptype', 0) ? '//ecn.dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=7.0&mkt='.$curr_lang->get('tag') : '//maps.googleapis.com/maps/api/js?sensor=false';
	$document->addScript( $mapsurl );
}

$map_js .="
var mapModOptions = {
	ipbaseurl: '".JURI::root()."',
	currencyFormat: ".$currencyformat.",
	marker: '".$map_house_icon."',
	zoom: ".$settings->adv_default_zoom.",
	maxZoom: ".$settings->max_zoom.",
	lat: '".$settings->adv_default_lat."',
	lng: '".$settings->adv_default_long."',
	maptype: '".$params->get('mapstyle', 'ROADMAP')."',
	credentials: '".$settings->map_credentials."'
};

var langOptions = {
	tprop:'".addslashes(JText::_('COM_IPROPERTY_RESULTS'))."',
	price:'".addslashes(JText::_('COM_IPROPERTY_PRICE'))."',
	pid: '".addslashes(JText::_('COM_IPROPERTY_PROPERTY_ID'))."',
	street: '".addslashes(JText::_('COM_IPROPERTY_STREET'))."',
	beds: '".addslashes(JText::_('COM_IPROPERTY_BEDS'))."',
	baths: '".addslashes(JText::_('COM_IPROPERTY_BATHS'))."',
	sqft: '".addslashes($munits)."',
	preview: '".addslashes(JText::_('COM_IPROPERTY_PREVIEW'))."',
	more: '".addslashes(JText::_('COM_IPROPERTY_MORE' ))."',
	inputText: '".addslashes(JText::_('COM_IPROPERTY_INPUT_TIP'))."',
	isRTL: ".$rtl."
};

jQuery(function($) {
    $(document).ready(function(){
		// set the map divs height explicitly
		$('#ip-map-mod-canvas').height(".$params->get('height', 250).");
		ipMapModFunctions.buildMap(mapModOptions);
		$.each(mapData, function(index, el){
            ipMapModFunctions.addMarker(el);
		});
	});
});
";

//$document->addStyleDeclaration('.ip-map-div img{max-width: inherit;}');
$document->addScriptDeclaration( $map_js );

echo '
    <div id="ip-map-mod-canvas" class="ip-map-div"></div>
    <div class="clearfix"></div>
    ';
?>
