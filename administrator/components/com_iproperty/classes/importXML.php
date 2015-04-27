<?php

/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

require_once JPATH_COMPONENT_ADMINISTRATOR.'/classes/importBase.php';

jimport('joomla.application.component.controlleradmin');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.archive');
jimport('joomla.log.log');
ini_set('auto_detect_line_endings',TRUE);

class importXML extends importBase 
{

    public function doImport()
    {
        $inc = 0;
        if (($reader = XMLReader::open($this->datafile))!== false) {
			// file exists and is readable so clean mids
			$this->cleanMids();
			
            while ($reader->read()) {
                if ( $reader->nodeType == XMLREADER::ELEMENT &&  $reader->localName == 'listing' ) {
                    $node   = $reader->expand();
                    $dom    = new DomDocument();
                    $n      = $dom->importNode($node,true);

                    $dom->appendChild($n);
                    $xml    = simplexml_import_dom($n);

                    // import company
                    $company = $this->importCompany($xml->company);
                    // import agents
                    $agents = $this->importAgents($xml->agents);
                    // import listing
                    $this->importListing($xml, $company);
					// cleanup mid tables
					$this->cleanMidSingle($this->prop_id);
                    // import agentmids
                    foreach ($agents as $key => $value){
                        if ($this->debug) JLog::add("");
                        if ($this->debug) JLog::add("********************* Importing agentmids *******************");
                        $ag_array = array();
                        $ag_array['ip_source']  = $key;
                        $ag_array['agent_id']   = $value;
                        $ag_array['prop_id']    = $this->prop_id;
                        $this->insertData("iproperty_agentmid", $ag_array);
                    }
                    // import amenities & categories
                    $this->importPropmids($xml);
                    // import images
                    $this->importImages($xml->photos);
					// import docs
                    $this->importDocs($xml->documents);
                    // import openhouses
                    $this->importOpenhouses($xml->openhouses);
                    $inc++;
                }
                unset($node);
                unset($dom);
                unset($xml);
                // reset the property ID
                $this->prop_id      = false;
                $this->ip_source    = false;
            }
        } else {
            // failed to open XML file
            $msg = JText::_('COM_IPROPERTY_XMLREAD_FAILED').' IP Error8';
            $type = 'notice';
            if($this->debug) JLog::add(JText::_('COM_IPROPERTY_XMLREAD_FAILED'));
            $this->app->redirect('index.php?option=com_iproperty&view=bulkimport', $msg, $type);
        }
        // finished import with no major issues
        $msg = sprintf(JText::_('COM_IPROPERTY_BULKIMPORT_SUCCESS'), $inc);
        $type = 'message';
        if($this->debug) JLog::add(sprintf(JText::_('COM_IPROPERTY_BULKIMPORT_SUCCESS'), $inc));
        $this->app->redirect('index.php?option=com_iproperty&view=bulkimport', $msg, $type);
    }

    function importCompany($data)
    {
        if ($this->debug) JLog::add("Importing company: ".$data->name);
        $icon = $this->doAgentThumb_gd($data->icon, 'companies', $data->icon_remote);
        $company_array = array (
            'name'              => $data->name,
            'description'       => $data->description,
            'street'            => $data->street,
            'city'              => $data->city,
            'province'          => $data->province,
            'locstate'          => $this->state_array[trim(strtolower($data->locstate))],
            'postcode'          => $data->postcode,
            'fax'               => $data->fax,
            'phone'             => $data->phone,
            'email'             => $data->email,
            'website'           => $data->website,
            'icon'              => $icon,
            'clicense'          => $data->clicense,
            'ip_source'         => $data->id,
            'featured'          => $data->featured
        );
        if ($this->debug) JLog::add("");
        if ($this->debug) JLog::add("********************* Importing company ".$company_array['ip_source']." *******************");
        return $this->insertData("iproperty_companies", $company_array);
    }

    function importAgents($data)
    {
        $agentreturn = array();
        foreach ($data->agent as $listing_agent){
            $a_id = (string) $listing_agent->id;
            $icon = $this->doAgentThumb_gd($listing_agent->icon, 'agents', $listing_agent->icon_remote);
            if ($this->debug) JLog::add("Importing agent: ".$listing_agent->fname." ".$listing_agent->lname);
            $agent_array = array (
                'fname'             => $listing_agent->fname,
                'lname'             => $listing_agent->lname,
                'company'           => $this->getObjectID($listing_agent->company, 'company'),
                'phone'             => $listing_agent->phone,
                'mobile'            => $listing_agent->mobile,
                'email'             => $listing_agent->email,
                'fax'               => $listing_agent->fax,
                'street'            => $listing_agent->street,
                'street2'           => $listing_agent->street2,
                'city'              => $listing_agent->city,
                'locstate'          => $this->state_array[trim(strtolower($listing_agent->locstate))],
                'province'          => $listing_agent->province,
                'postcode'          => $listing_agent->postcode,
                'country'           => $this->country_array[trim(strtolower($listing_agent->country))],
                'website'           => $listing_agent->website,
                'bio'               => $listing_agent->bio,
                'featured'          => $listing_agent->featured,
                'icon'              => $icon,
                'msn'               => $listing_agent->msn,
                'skype'             => $listing_agent->skype,
                'gtalk'             => $listing_agent->gtalk,
                'linkedin'          => $listing_agent->linkedin,
                'facebook'          => $listing_agent->facebook,
                'twitter'           => $listing_agent->twitter,
                'social1'           => $listing_agent->social1,
                'alicense'          => $listing_agent->alicense,
                'ip_source'         => $listing_agent->id
            );
            if ($this->debug) JLog::add("");
            if ($this->debug) JLog::add("********************* Importing agent ".$agent_array['ip_source']." *******************");
            $return = $this->insertData("iproperty_agents", $agent_array);
            if ($return) $agentreturn[$a_id] = $return;
        }
        return $agentreturn;
    }

    function importListing($data, $company)
    {
        // do geocode if required
        if (!$data->latitude || !$data->longitude){
            $country    = $data->country;
            $state      = $data->locstate;
            $province   = $data->province;
            $prov_state = $state ? $state : $province;
            $address    = $data->street_num.' '.$data->street.' '.$data->street2.' '.$data->city.' '.$prov_state.' '.$data->postcode.' '.$country;
            if($this->debug) JLog::add("No coordinates found. Doing geocode for " . $address);
            if (($location = $this->doGeocode($address)) !== false){
                $data->latitude  = $location[0];
                $data->longitude = $location[1];
            }
        }


        $property_array = array (
                'property_name'            => $data->property_name,
                'stype'             => $this->stype_array[trim(strtolower($data->stype))],
                'stype_freq'        => $data->stype_freq,
                'listing_office'    => (int) $company,
                'street_num'        => $data->street_num,
                'street'            => $data->street,
                'street2'           => $data->street2,
                'apt'               => $data->apt,
                'city'              => $data->city,
                'title'             => $data->title,
                'alias'             => $data->alias,
                'hide_address'      => $data->hide_address,
                'show_map'          => $data->show_map,
                'short_description' => $data->short_description,
                'description'       => $data->description,
                'terms'             => $data->terms,
                'agent_notes'       => $data->agent_notes,
                'locstate'          => $this->state_array[trim(strtolower($data->locstate))],
                'province'          => $data->province,
                'postcode'          => $data->postcode,
                'region'            => $data->region,
                'county'            => $data->county,
                'country'           => $this->country_array[trim(strtolower($data->country))],
                'latitude'          => $data->latitude,
                'longitude'         => $data->longitude,
                'price'             => $data->price,
                'price2'            => $data->price2,
                'call_for_price'    => $data->call_for_price,
                'beds'              => $data->beds,
                'baths'             => $data->baths,
                'reception'         => $data->reception,
                'tax'               => $data->tax,
                'income'            => $data->income,
                'sqft'              => $data->sqft,
                'lotsize'           => $data->lotsize,
                'lot_acres'         => $data->lot_acres,
                'yearbuilt'         => $data->yearbuilt,
                'heat'              => $data->heat,
                'cool'              => $data->cool,
                'fuel'              => $data->fuel,
                'garage_type'       => $data->garage_type,
                'garage_size'       => $data->garage_size,
                'zoning'            => $data->zoning,
                'frontage'          => $data->frontage,
                'siding'            => $data->siding,
                'roof'              => $data->roof,
                'propview'          => $data->propview,
                'school_district'   => $data->school_district,
                'lot_type'          => $data->lot_type,
                'style'             => $data->style,
                'hoa'               => $data->hoa,
                'reo'               => $data->reo,
                'vtour'             => $data->vtour,
                'video'             => $data->video,
                'featured'          => $data->featured,
                'available'         => $this->formatDate($data->available, 2),
                'metadesc'          => $data->metadesc,
                'metakey'           => $data->metakey,
                'created'           => $this->formatDate($data->created, 1),
                'access'            => 1,
                'publish_up'        => $data->publish_up,
                'publish_down'      => $data->publish_down,
                'state'             => 1,
                'approved'          => 1,
                'ip_source'         => $data->id
        );

        if ($this->debug) JLog::add("");
        if ($this->debug) JLog::add("********************* Importing property ".$property_array['ip_source']." *******************");

        $this->ip_source    = $data->id;
        $this->prop_id      = $this->insertData("iproperty", $property_array);
    }

    function importPropmids($data)
    {
        if ($this->debug) JLog::add("");
        if ($this->debug) JLog::add("********************* Importing propmids *******************");
        // insert cats
        foreach ($data->categories->category as $cat){
            $cat_array = array();
            if (($cat_code = $this->getCategory($cat)) !== false) {
                $cat_array['cat_id'] = $cat_code;
            } else {
                if ($this->debug) JLog::add('Category '.$cat.' not found, setting it to 0');
                $cat_array['cat_id'] = 0;
            }
            $cat_array['prop_id'] = $this->prop_id;
            $cat_array['ip_source'] = $this->ip_source;
            $this->insertData("iproperty_propmid", $cat_array);
        }
        // insert amens
        $amens      = array();
        $amens[]    = explode(',', $data->amenities->general);
        $amens[]    = explode(',', $data->amenities->interior);
        $amens[]    = explode(',', $data->amenities->exterior);

        foreach ($amens as $key => $amentype){
            foreach ($amentype as $amen){
                $amen_array = array();
                $amen = $this->getAmenity($amen, $key);
                if($amen){
                    $amen_array['amen_id'] = $amen;
                    $amen_array['prop_id'] = $this->prop_id;
                    $amen_array['ip_source'] = $this->ip_source;
                    $this->insertData("iproperty_propmid", $amen_array);
                }
            }
        }
    }

    function importImages($data){
        if ($this->debug) JLog::add("");
		if ($this->debug) JLog::add("********************* Importing images *******************");
		foreach ($data->photo as $picture){
			// parse out the path info
			$info   = pathinfo($picture->path);
			$path   = $info['dirname'].'/';
			$fname  = $info['filename'];
			$type   = '.'.$info['extension'];
			
			if ($this->debug) JLog::add("Importing photo: ".$fname);			
			if(!$picture->remote){
				$this->doThumb_gd($picture->fname);
				$type 	= '.jpg';
				$path	= $this->settings->imgpath ? $this->settings->imgpath : '/media/com_iproperty/pictures/';
			} 
			$pictures_array = array(
				'fname' 		=> $fname,
				'remote'		=> $picture->remote,
				'ordering'		=> $picture->ordering,
				'title'			=> $picture->title,
				'type'			=> $type,
				'path'			=> $path,
				'propid'		=> $this->prop_id,
				'description'	=> $picture->description,
				'ip_source'		=> $this->ip_source
			);
			if($pictures_array['fname']) $this->insertData("iproperty_images", $pictures_array);
		}
	}
	
    function importDocs($data){
        if ($this->debug) JLog::add("");
		if ($this->debug) JLog::add("********************* Importing docs *******************");
		foreach ($data->doc as $doc){
			// parse out the path info
			$info   = pathinfo($doc->path);
			$path   = $info['dirname'].'/';
			$fname  = $info['filename'];
			$type   = '.'.$info['extension'];
			
			if ($this->debug) JLog::add("Importing document: ".$fname);			
			if(!$doc->remote){
				// move the file but don't bother transforming
				$path	= $this->settings->imgpath ? $this->settings->imgpath : '/media/com_iproperty/pictures/';
				$old	= $this->img_path.'/'.$info['basename'];
				$new	= JPATH_SITE.$path.$info['basename']; 
				rename($old, $new);
			} 
			$doc_array = array(
				'fname' 		=> $fname,
				'remote'		=> $doc->remote,
				'ordering'		=> $doc->ordering,
				'title'			=> $doc->title,
				'type'			=> $type,
				'path'			=> $path,
				'propid'		=> $this->prop_id,
				'description'	=> $doc->description,
				'ip_source'		=> $this->ip_source
			);
			if($doc_array['fname']) $this->insertData("iproperty_images", $doc_array);
		}
	}	

    function importOpenhouses($data)
    {
        if ($this->debug) JLog::add("");
        if ($this->debug) JLog::add("********************* Importing openhouses *******************");
        foreach ($data->openhouse as $openhouse){
            $start  = $this->formatDate($openhouse->openhouse_start);
            $end    = $this->formatDate($openhouse->openhouse_end);

            $openhouse_array = array(
                'openhouse_start'   => $start,
                'openhouse_end'     => $end,
                'name'              => $openhouse->name,
                'comments'          => $openhouse->comments,
                'prop_id'           => $this->prop_id
            );

            if ($start && $end && $this->prop_id) $this->insertData("iproperty_openhouses", $openhouse_array);
        }
    }
}
?>
