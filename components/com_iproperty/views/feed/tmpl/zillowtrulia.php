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
$document->setMimeEncoding('application/xml');

$config = JFactory::getConfig();

/////////////////////
/* EDIT BELOW HERE */
////////////////////

// NOTE: THE JOOMLA SYSTEM EMAIL CLOAK PLUGIN MAY BREAK THE FEED BY CHANGING EMAIL 
// ADDRESSES INTO document.write BLOCKS, WHICH TRULIA WILL REJECT AS INVALID.
// TURN OFF THE SYSTEM EMAIL CLOAKING PLUGIN IF THIS IS AN ISSUE.

// create a case for each category you have defined in IP
function getPtype($type){
    // valid Trulia types:
    // Apartment/Condo/Townhouse, Condo, Townhouse, Coop, Apartment, Loft, TIC,
    // Mobile/Manufactured, Farm/Ranch, Multi-Family, Income/Investment, Houseboat,
    // Lot/Land, Single-Family Home

    switch ($type){
        case 'My Category one':
            $trulia_type = 'Apartment/Condo/Townhouse';
        break;
        case 'My Category two':
            $trulia_type = 'Condo';
        break;
        case 'My Category three':
            $trulia_type = 'Townhouse';
        break;
        case 'My Category four':
            $trulia_type = 'Coop';
        break;
        case 'My Category five':
            $trulia_type = 'Apartment';
        break;
        case 'My Category six':
            $trulia_type = 'Loft';
        break;
        case 'My Category seven':
            $trulia_type = 'TIC';
        break;
        case 'My Category eight':
            $trulia_type = 'Mobile/Manufactured';
        break;
        case 'My Category nine':
            $trulia_type = 'Farm/Ranch';
        break;
        case 'My Category tem':
            $trulia_type = 'Single-Family Home';
        break;
        default:
            $trulia_type = 'Single-Family Home';
        break;
    }
    return $trulia_type;
}

// create a case for each garage you have defined in IP
function getGarage($garage){
    // valid Trulia types:
    // surface lot | garage lot | covered lot | street | carport | none | other

    switch ($garage){
        case 'My Garage one':
            $trulia_type = 'surface lot';
        break;
        case 'My Garage two':
            $trulia_type = 'garage lot';
        break;
        case 'My Garage three':
            $trulia_type = 'covered lot';
        break;
        case 'My Garage four':
            $trulia_type = 'street';
        break;
        case 'My Garage five':
            $trulia_type = 'carport';
        break;
        case 'My Garage six':
            $trulia_type = 'none';
        break;
        case 'My Garage seven':
        default:
            $trulia_type = 'other';
        break;
    }
    return $trulia_type;
}

// create a case for each heat type you use
function getHeat($heat){
    // valid Trulia types:
    // gas | electric | radiant | other

    switch ($heat){
        case 'My Heat one':
            $trulia_type = 'gas';
        break;
        case 'My Heat two':
            $trulia_type = 'electric';
        break;
        case 'My Heat three':
            $trulia_type = 'radiant';
        break;
        case 'My Heat four':
        default:    
            $trulia_type = 'other';
        break;
    }
    return $trulia_type;
}

////////////////////////////
/* DO NOT EDIT BELOW HERE */
////////////////////////////


################################################################################
# WRITTEN FOR TRULIA FEED SPECS AS OF FEB 1 2009
# TRULIA FEED FORMAT IS ACCEPTED BY ZILLOW
# NOTE: COMMERCIAL PROPERTY TYPES ARE NOT ACCEPTED BY TRULIA!!
################################################################################

$xml = new XMLWriter();
$xml->openURI('php://output');
$xml->startDocument("1.0");
$xml->setIndent(true);
$xml->startElement("properties");

if($this->properties && $this->settings->feed_show){

	// start listings
	foreach ($this->properties as $property){

		$images  	= ipropertyModelFeed::getImages($property['id']);
		$type		= ipropertyModelFeed::getType($property['id']);
		$state          = ipropertyHTML::getStateCode($property['locstate']);

                $street         = '';
                if($property['street_num']) $street .= $property['street_num'];
                if($property['street']) $street .= ' ' . $property['street'];
                if($property['street2']) $street .= ' ' . $property['street2'];

		$xml->startElement("property");
			// location section
			$xml->startElement("location");
				if ($property['apt']) $xml->writeElement("unit-number", $property['apt']);
				$xml->writeElement("street-address", $street);
				if ($property['city']) $xml->writeElement("city-name", $property['city']);
				$xml->writeElement("state-code", $state);
				$xml->writeElement("zipcode", $property['postcode']);
				$xml->writeElement("county", $property['county']);
				$xml->writeElement("subdivision", '');
				if ($property['latitude']) $xml->writeElement("latitude", $property['latitude']);
				if ($property['longitude']) $xml->writeElement("longitude", $property['longitude']);
				$xml->writeElement("geocode-type", "exact");
				$xml->writeElement("display-address", "yes");
			$xml->endElement(); // location
			
			// details section
			$xml->startElement("details");
				$xml->writeElement("listing-title", $property['title']);
				$xml->writeElement("price", $property['price']);
				$xml->writeElement("year-built", $property['yearbuilt']);
				$xml->writeElement("num-bedrooms", $property['beds']);
				$xml->writeElement("num-bathrooms", $property['baths']);
				$xml->writeElement("lot-size", $property['lotsize']);
				$xml->writeElement("square-feet", $property['sqft']);
				$xml->writeElement("date-listed", $property['created']);
					$xml->startElement("description");
						$xml->text(strip_tags($property['description']));
					$xml->endElement();	
				$xml->writeElement("mlsId", $property['property_name']);
				$xml->writeElement("provider-listingid", $property['id']);
				$xml->writeElement("property-type", getPtype($type));
			$xml->endElement(); // details
			
			// landing page
			$xml->startElement("landing-page");
				$xml->writeElement("lp-url", JURI::root() . "index.php?option=com_iproperty&view=property&id=" . $property['id']);
			$xml->endElement();
			
			// property status
			if ($property['stype'] == 1) {
				$xml->writeElement("status", "For Sale");
			} elseif ($property['stype'] == 4) {
				$xml->writeElement("status", "For Rent");
			}
			if ($property['reo']) $xml->writeElement("foreclosure-status", 'REO - Bank Owned');
			
			// site details
			$xml->startElement("site");
				$xml->writeElement("site-url", JURI::root());
				$xml->writeElement("site-name", $config->get('sitename'));
			$xml->endElement();	
			
			// pictures
			if($images){
				$xml->startElement("pictures");
					foreach($images as $image){
						if ($image['remote'] == 1){ 
							$img_path = $image['path'] . $image['fname'] . $image['type'];
						} else {
							$path = $image['path'] ? $image['path'] : "/media/com_iproperty/pictures";
							$img_path = rtrim(JURI::ROOT(), '/') . $path . $image['fname'] . $image['type'];
						}
						$xml->startElement("picture");
							$xml->writeElement("picture-url", $img_path);
							if($image['title']) $xml->writeElement("picture-caption", $image['title']);
							if($image['description']) $xml->writeElement("picture-description", $image['description']);
							$xml->writeElement("picture-seq-number", $image['ordering']);
						$xml->endElement();	
					}
				$xml->endElement();
			}
			
			// vtours
			$xml->startElement("virtual-tours");
				if ($property['vtour']) {
					$xml->writeElement("virtual-tour-url", $property['vtour']);
				}
			$xml->endElement();
			
			// agent
			$xml->startElement("agent");
				if ($property['agent_name']) {
					$xml->writeElement("agent-name", $property['agent_name']);
					$xml->writeElement("agent-phone", $property['agent_phone']);
					$xml->writeElement("agent-email", $property['agent_email']);
					if($property['agent_photo']) $xml->writeElement("agent-picture-url", JURI::root() . "media/com_iproperty/agents/" . $property['agent_photo']);	
				}
			$xml->endElement();	
			
			// company
			$xml->startElement("office");
				if ($property['company_name']) {
					$xml->writeElement("office-id", $property['company_id']);
					$xml->writeElement("office-name", $property['company_name']);
					$xml->writeElement("office-phone", $property['company_phone']);
					$xml->writeElement("office-email", $property['company_email']);
				}	
			$xml->endElement();	
			
			// schools
			$xml->startElement("schools");
				if ($property['school_district']) {
					$xml->startElement("school-district");
						$xml->writeElement("district-name", $property['school_district']);
					$xml->endElement();	
				}
			$xml->endElement();
			
			// detailed characteristics
			$xml->startElement("detailed-characteristics");
				if ($property['heat']) {
					$xml->startElement("heating-systems");
						$xml->writeElement("heating-system", getHeat($property['heat']));
					$xml->endElement();	
				}
                if ($property['cool']) {
					$xml->startElement("cooling-systems");
						$xml->writeElement("other-cooling", $property['cool']);
					$xml->endElement();	
				}
				if ($property['garage_type']) {
					$xml->startElement("parking-types");
						$xml->writeElement("parking-type", getGarage($property['garage_type']));
					$xml->endElement();	
                }    
                $xml->writeElement("roof-type", $property['roof']);
                $xml->writeElement("exterior-type", $property['siding']);
                $xml->writeElement("architecture-style", $property['style']);	
				
			$xml->endElement();
		// end listing data	
		$xml->endElement(); // property
	}
}
$xml->endElement(); // properties
$xml->endDocument();
$xml->flush();

?>
