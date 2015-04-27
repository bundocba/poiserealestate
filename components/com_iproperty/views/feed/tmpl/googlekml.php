<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
$document 	   = JFactory::getDocument();
$document->setMimeEncoding('application/vnd.google-earth.kml+xml');
$app = JFactory::getApplication();

################################################################################
# WRITTEN FOR GOOGLE KML 2.2 SPECS (US VERSION)
# http://code.google.com/apis/kml/documentation/kmlreference.html
################################################################################

$xml = new XMLWriter();
$xml->openURI('php://output');
$xml->startDocument('1.0');
$xml->setIndent(true);

$xml->startElement('kml');
	$xml->writeAttribute('xmlns', 'http://www.opengis.net/kml/2.2');
	$xml->writeAttribute('xmlns:gx', 'http://www.google.com/kml/ext/2.2');
	$xml->writeAttribute('xmlns:atom', 'http://www.w3.org/2005/Atom');
	
$xml->startElement('Document');
$xml->startElement('atom:author');
	$xml->writeElement('atom:name', 'Intellectual Property-- the Thinkery LLC');
$xml->endElement();

$xml->writeElement('name', $app->getCfg('sitename'));

$xml->startElement('Style');
	$xml->startElement('IconStyle');
		$xml->writeElement('href', 'http://maps.google.com/mapfiles/kml/pal4/icon46.png');
	$xml->endElement();
	$xml->writeElement('BalloonStyle', '');
$xml->endElement();

if($this->properties && $this->settings->feed_show){

	// start listings
	foreach ($this->properties as $property){

		$images		= ipropertyModelFeed::getImages($property['id'], 1);
		$state          = ipropertyHTML::getStateCode($property['locstate']);
		
		// create photo link
		if($images){
            $photo		= ($images[0]['remote']) ?  $images[0]['path'] . $images[0]['fname'] . $images[0]['type'] : JURI::root() . $images[0]['path'] . $images[0]['fname'] . $images[0]['type'];
        }else{
            $photo      = '';
        }

		$address = '';
			if($property['street_num']) $address    .= $property['street_num'];
			if($property['street']) $address        .= " " . $property['street'];
			if($property['apt']) $address           .= " " . $property['apt'];
			if($property['city']) $address          .= ", " . $property['city'];
			$address .= $state ? ", " . $state : ", " . $property['county'];
			if($property['postcode']) $address      .= ", " . $property['postcode'];
			$address .= $state ? '' : ", " . $property['country'];

                if($property['gbase_address']) $address = $property['gbase_address'];

			$title = $property['title'] ? $property['title'] : $address;
			
			
	// define vars
	$agent_image = JURI::ROOT() . "media/com_iproperty/agents/" . $property['agent_photo'];		
			
	// build the balloon_text object here.
	$balloon_text = '<div style="width: 670px;">
						<table width="100%" cellspacing="0" cellpadding="5">
						<tr>
						<td valign="top" style="width: 180px; border-right: solid 1px #ccc;">
						<div style="padding-bottom: 5px;"><img src="' . JURI::root() . 'media/com_iproperty/agents/' . $property['agent_photo'] . '" alt="' . $property['agent_name'] .'" width="78" style="border: solid 1px #666; margin-bottom: 5px;" />
						</div>
						<div style="font-size: 11px; padding-top: 5px; border-top: solid 1px #ccc;">
						<a href="' . JURI::root() . 'index.php?option=com_iproperty&view=agentproperties&id=' . $property['agent_id'] . '" style="color: #ff0000; text-decoration: none; font-size: 12px; font-weight: bold;">' . $property['agent_name'] . '</a><br />';
						
						if($property['agent_email']) $balloon_text .= '<img src="' . JURI::root() . 'components/com_iproperty/assets/images/icon-email.gif" />' . $property['agent_email'] . '<br />';
						if($property['agent_phone']) $balloon_text .= '<img src="' . JURI::root() . 'components/com_iproperty/assets/images/icon-phone.gif" />' . $property['agent_phone'] . '<br />';
						
						$balloon_text .= '</div>
						</td>
						<td valign="top" style="width: 470px;">
						<div style="border-bottom: solid 1px #ccc; padding: 0 10px 5px 10px; margin-bottom: 5px; font-size: 16px; font-weight: bold; text-transform: uppercase;">
						<a href="' . JURI::root() . 'index.php?option=com_iproperty&view=property&id=' . $property['id'] . '">' . $address . '</a>
						</div>
						<div>';
						
						if($property['property_name']) $balloon_text .= '<b>MLS-ID:</b> ' . $property['property_name'] . '<br />';
						if($property['beds']) $balloon_text .= '<b>Bedrooms:</b> ' . $property['beds'] . '<br />';
						if($property['baths']) $balloon_text .= '<b>Bathrooms:</b> ' . $property['baths'] . '<br />';
						if($property['sqft']) $balloon_text .= '<b>Square FT:</b> ' . $property['sqft'] . '<br />';
						if($property['lot_acres']) $balloon_text .= '<b>Lot Acres:</b> ' . $property['lot_acres'] . '<br />';
						if($property['heat']) $balloon_text .= '<b>Heat:</b> ' . $property['heat'] . '<br />';
						if($property['price']) $balloon_text .= '<br /><span style="font-size: 14px; font-weight: bold;">Listing Price:</span><br /><span style="font-size: 24px; font-weight: bold; color: #ff0000;"> ' . ipropertyHTML::getFormattedPrice($property['price'], $property['stype_freq'], false, $property['call_for_price']) . '</span>';
						
						$balloon_text .= '</div>
						<div style="padding-top: 10px; clear: both;">
						<b>Property Description:</b><br /> ' . $property['description'] . '<br />
						</div>
						</td>
						</tr>
						</table>
						</div>';			
			
			
		####################################################################
		# THIS IS WHERE THE ACTUAL PLACEMARK STARTS GETTING BUILT
		####################################################################	

		$xml->startElement('Placemark');
			$xml->writeAttribute('id', $property['id']);
			// location section
			$xml->writeElement("name", $title);
			
			$xml->startElement("description");
				$xml->writeCData($property['description']);
			$xml->endElement();

			$xml->startElement("Point");
				$xml->writeElement("coordinates", $property['longitude'] . "," . $property['latitude'] . ",0");
			$xml->endElement();		

			$xml->startElement("Style");
				$xml->startElement("IconStyle");
					$xml->startElement("Icon");
						$xml->writeElement("href", $photo );
					$xml->endElement();	
				$xml->endElement();	
				$xml->startElement("BalloonStyle");
					$xml->startElement("text");
						$xml->writeCData($balloon_text);
					$xml->endElement();	
				$xml->endElement();
			$xml->endElement();	
			
		// end listing data	
		$xml->endElement(); // item
	}
}
$xml->endElement(); // rss
$xml->endDocument();
$xml->flush();
?>
