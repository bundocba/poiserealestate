<?php
/**
 * @version 3.1 2013-04-26
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
$document 	   = JFactory::getDocument();
$document->setMimeEncoding('application/xml');

$config = JFactory::getConfig();
$settings = $this->settings;

/////////////////////
/* EDIT BELOW HERE */
////////////////////

// default language used in Joomla site-- note Kyero expects
// amenity data to be in English or Spanish!
define('LANG', 'en');
define('POOL', 'Pool');

// default currency used in Joomla site. Must be in:
// AED, ARS, AUD, BHD, BND, BRL, BWP, CAD, CHF, CLP, CNY, CYP, CZK,
// DKK, EUR, GBP, HUF, ILS, INR, ISK, JPY, KWD, LKR, LYD, MTL, MUR,
// MXN, MYR, NOK, NPR, NZD, OMR, PKR, PLN, QAR, SAR, SEK, SGD, SIT,
// THB, TTD, USD or ZAR
define('CURRENCY', 'USD');

// id of lease stype in IProperty
define('LEASE', 2);

// NOTE: THE JOOMLA SYSTEM EMAIL CLOAK PLUGIN MAY BREAK THE FEED BY CHANGING EMAIL
// ADDRESSES INTO document.write BLOCKS, WHICH KYERO WILL REJECT AS INVALID.
// TURN OFF THE SYSTEM EMAIL CLOAKING PLUGIN IF THIS IS AN ISSUE.

// create a case for each category you have defined in IP
function getPtype($type){
    // if your property categories are NOT in english,
	// provide translations here (Kyero requires english)
	if (LANG == 'en') return $type;
    switch ($type){
        case 'My Category one in other language':
            $kyero_type = 'My Category One in ENGLISH';
        break;
        case 'My Category two in other language':
            $kyero_type = 'My Category Two in ENGLISH';
        break;
        // add more as required
    }
    return $kyero_type;
}

// convert sqft to sqm if needed
function convertUnit($value, &$settings){
	if ($settings->measurement_units) return $value;
	if ( is_numeric($value) && $value > 0 ) {
		return round(($value / 10.764));
	} else {
		return 0;
	}
}

// get price freq
function priceFreq($value){
	// replace "Week" and "Month" with local lang value
	switch ($value){
		case 'Week':
			$freq = 'week';
			break;
		case 'Month':
			$freq = 'month';
			break;
		default:
			$freq = 'sale';
			break;
	}
	return $freq;
}

////////////////////////////
/* DO NOT EDIT BELOW HERE */
////////////////////////////

################################################################################
# WRITTEN FOR KYERO FEED SPECS AS OF V 2_1
################################################################################

$xml = new XMLWriter();
$xml->openURI('php://output');
$xml->startDocument("1.0");
$xml->setIndent(true);
$xml->startElement("root");
	$xml->startElement("kyero");
		$xml->writeElement("feed_version", "2_1");
	$xml->endElement();

if($this->properties && $this->settings->feed_show){

	// start listings
	foreach ($this->properties as $property){
		$haspool	= 0; // default value for pool

		$images  	= ipropertyModelFeed::getImages($property['id']);
		$type		= ipropertyModelFeed::getType($property['id']);
		$amens		= ipropertyModelFeed::getFeatures($property['id']);

		$street         = '';
		if($property['street_num']) $street .= $property['street_num'];
		if($property['street']) $street .= ' ' . $property['street'];
		if($property['street2']) $street .= ' ' . $property['street2'];

		$xml->startElement("property");
			$xml->writeElement("id", $property['id']);
			$xml->writeElement("date", $property['modified']);
			$xml->writeElement("ref", $property['property_name']);
			$xml->writeElement("price", round($property['price']));
            $xml->writeElement("currency", CURRENCY);
			$xml->writeElement("price_freq", priceFreq($property['stype_freq']));
			if ($property['stype'] == LEASE && priceFreq($property['stype_freq']) == 'sale') $xml->writeElement("leashold", 1);
			$xml->startElement("type");					
				$xml->writeElement("en", getPtype($type));
			$xml->endElement();

			// location section
			$xml->writeElement("town", $property['city']);
			$xml->writeElement("province", $property['province']);
			$xml->writeElement("location_detail", $property['region']);

			// details section
			$xml->writeElement("beds", $property['beds']);
			$xml->writeElement("baths", round($property['baths']));

			// area section
			$xml->startElement("surface_area");
				$xml->writeElement("build", convertUnit($property['sqft'], $settings));
				$xml->writeElement("plot", convertUnit($property['lotsize'], $settings));
			$xml->endElement();
			$xml->startElement("desc");
				$xml->writeElement(LANG, strip_tags($property['description']));
			$xml->endElement();

			// landing page
			$xml->writeElement("url", JURI::root() . "index.php?option=com_iproperty&view=property&id=" . $property['id']);

			// features section
			$xml->startElement("features");
				foreach ($amens as $amen){
					$xml->writeElement("feature", $amen['title']);
					if ($amen['title'] === POOL) $haspool = 1;
				}
			$xml->endElement();

            $xml->writeElement ("pool", $haspool);

			// pictures
			if($images){
				$xml->startElement("images");
					foreach($images as $image){
						if ($image['remote'] == 1){
							$img_path = $image['path'] . $image['fname'] . $image['type'];
						} else {
							$path = $image['path'] ? $image['path'] : "/media/com_iproperty/pictures";
							$img_path = rtrim(JURI::ROOT(), '/') . $path . $image['fname'] . $image['type'];
						}
						$xml->startElement("image");
						$xml->writeAttribute("id", $image['ordering']);
							$xml->writeElement("url", $img_path);
						$xml->endElement();
					}
				$xml->endElement();
			}
		// end listing data
		$xml->endElement(); // property
	}
}
$xml->endElement(); // root
$xml->endDocument();
$xml->flush();
?>
