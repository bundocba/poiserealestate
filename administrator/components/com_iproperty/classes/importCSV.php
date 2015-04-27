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

class importCSV extends importBase
{
    private $csv_columns;
    private $agent_columns      = array();
    private $image_columns      = array();
	private $document_columns      = array();
    private $company_columns    = array();
    private $listing_columns    = array();
    private $openhouse_columns  = array();
    private $cat_column;
    private $amenity_columns    = array();

    public function doImport()
    {
        $inc = 0;
        if (($file_handle = fopen($this->datafile, "r"))!== false) {
			// file exists and is readable so clean mids
			$this->cleanMids();
		
            // read first row (column names) into array)
            $this->csv_columns = fgetcsv($file_handle, '', ',', '"');
            foreach ($this->csv_columns as $key => $value){
                $cols = explode('|', $value);
                $this->csv_columns[$key] = $cols;
                switch($cols[0]){
                    case 'iproperty':
                        $this->listing_columns[] = $key;
                        break;
                    case 'iproperty_agents':
                        $this->agent_columns[] = $key;
                        break;
                    case 'iproperty_companies':
                        $this->company_columns[] = $key;
                        break;
                    case 'iproperty_images':
                        $this->image_columns[] = $key;
                        break;
					case 'iproperty_documents':
                        $this->document_columns[] = $key;
                        break;
                    case 'iproperty_openhouses':
                        $this->openhouse_columns[] = $key;
                        break;
                }
                // catch the category and amenity columns
                if ($cols[1] == 'category'){
                    $this->cat_column = $key;
                } else if ($cols[1] == 'generalamenities'){
                    $this->amenity_columns[0] = $key;
                } else if ($cols[1] == 'interioramenities'){
                    $this->amenity_columns[1] = $key;
                } else if ($cols[1] == 'exterioramenities'){
                    $this->amenity_columns[2] = $key;
                }
            }
            // now do the import work
            while (($row = fgetcsv($file_handle, '', ',', '"')) !== FALSE) {
                // import company
                $company = $this->importCompany($row);
                if ($company) $this->company_id = $company;
                // import agents
                $agents = $this->importAgents($row);
                // import listing
                $this->importListing($row, $company);
				// cleanup mid table
				$this->cleanMidSingle($this->prop_id);
                // import agentmids
                foreach ($agents as $key => $value){
                    $ag_array = array();
                    $ag_array['ip_source']  = $key;
                    $ag_array['agent_id']   = $value;
                    $ag_array['prop_id']    = $this->prop_id;
                    $this->insertData("iproperty_agentmid", $ag_array);
                }
                // import amenities & categories
                $this->importPropmids($row);
                // import images
                $this->importImages($row);
				// import images
                $this->importDocs($row);
                // import openhouses
                $this->importOpenhouses($row);

                // reset the property ID
                $this->prop_id      = false;
                $this->company_id   = false;
                $this->ip_source    = false;
                $inc++;
            }
        } else {
            // failed to open CSV file
            $msg = JText::_('COM_IPROPERTY_READ_FAILED').' IP Error8';
            $type = 'notice';
            if ($this->debug) JLog::add(JText::_('COM_IPROPERTY_READ_FAILED'));
            $this->app->redirect('index.php?option=com_iproperty&view=bulkimport', $msg, $type);
        }
        // finished import with no major issues
        $msg = sprintf(JText::_('COM_IPROPERTY_BULKIMPORT_SUCCESS'), $inc);
        $type = 'message';
        if ($this->debug) JLog::add(sprintf(JText::_('COM_IPROPERTY_BULKIMPORT_SUCCESS'), $inc));
        $this->app->redirect('index.php?option=com_iproperty&view=bulkimport', $msg, $type);
    }

    function importCompany($data)
    {
        if ($this->debug) JLog::add("Importing company.");
        $company_array = array();
        foreach($this->company_columns as $c){
            $company_array[$this->csv_columns[$c][1]] = $data[$c];
        }

        if ($this->debug) JLog::add("");
        if ($this->debug) JLog::add("********************* Importing company ".$company_array['id']." *******************");

        // get locstate
        $company_array['locstate'] = $this->state_array[trim(strtolower($company_array['locstate']))];
        // get country
        $company_array['country'] = $this->country_array[trim(strtolower($company_array['country']))];
        // set state and access
        $company_array['state']    = 1;
        // set ip_source and reset ID
        $company_array['ip_source']      = $company_array['id'];
        $company_array['id']             = '';

        return $this->insertData("iproperty_companies", $company_array);
    }

    function importAgents($data)
    {
        $agentreturn = array();
        $agentinsert = array();

        foreach ($this->agent_columns as $c){
            //if ($this->debug) JLog::add("Importing photo: ".$picture->fname);
            $agentdata = $this->csv_columns[$c];
            $agentinsert[$agentdata[2]][$agentdata[1]] = $data[$c];
        }

        foreach ($agentinsert as $agent_array){
            if ($agent_array['id']){

                if ($this->debug) JLog::add("");
                if ($this->debug) JLog::add("********************* Importing agent ".$agent_array['id']." *******************");

                // get locstate
                $agent_array['locstate'] = $this->state_array[trim(strtolower($agent_array['locstate']))];
                // get country
                $agent_array['country'] = $this->country_array[trim(strtolower($agent_array['country']))];
                // get company ID
                $agent_array['company'] = $this->getObjectID($this->company_id, 'company');
                // set state and access
                $agent_array['state']    = 1;
                // set ip_source and reset ID
                $agent_array['ip_source']      = $agent_array['id'];
                $agent_array['id']             = '';

                $agentreturn[$agent_array['ip_source']] = $this->insertData("iproperty_agents", $agent_array);
            }
        }
        return $agentreturn;
    }

    function importListing($data, $company)
    {
        $property_array = array();
        foreach($this->listing_columns as $p){
            $property_array[$this->csv_columns[$p][1]] = $data[$p];
        }

        if ($this->debug) JLog::add("");
        if ($this->debug) JLog::add("********************* Importing property ".$property_array['id']." *******************");

        // remove the columns we don't use in the table
		unset($property_array['category']);
		unset($property_array['generalamenities']);
		unset($property_array['interioramenities']);
		unset($property_array['exterioramenities']);
        
        // do geocode if required
        if (!$property_array['latitude'] || !$property_array['longitude']){
            if($this->debug) JLog::add("No coordinates found. Doing geocode for " . $address);
            $country    = $property_array['country'];
            $state      = $property_array['locstate'];
            $province   = $property_array['province'];
            $prov_state = $state ? $state : $province;
            $address    = $property_array['street_num'].' '.$property_array['street'].' '.$property_array['street2'].' '.$property_array['city'].' '.$prov_state.' '.$property_array['postcode'].' '.$country;
            
            if (($location = $this->doGeocode($address)) !== false){
                $property_array['latitude']  = $location[0];
                $property_array['longitude'] = $location[1];
            }
        }
        
        // add listing office
        $property_array['listing_office'] = (int) $company;
        // get stype
        $property_array['stype']    = $this->stype_array[trim(strtolower($property_array['stype']))];
        // get locstate
        $property_array['locstate'] = $this->state_array[trim(strtolower($property_array['locstate']))];
        // get country
        $property_array['country']  = $this->country_array[trim(strtolower($property_array['country']))];
        // set state and access
        $property_array['state']    = 1;
        $property_array['access']   = 1;
        // set ip_source and reset ID
        $property_array['ip_source']    = $property_array['id'];
        $property_array['id']           = '';

        // format dates
        if (isset($property_array['created'])) {
            $property_array['created'] = $this->formatDate($property_array['created']);
        }

        if (isset($property_array['available'])) {
            $property_array['available'] = $this->formatDate($property_array['available'], 2);
        }

        $this->ip_source = $property_array['ip_source'];
        $this->prop_id   = $this->insertData("iproperty", $property_array);
    }

    function importPropmids($data)
    {
        if ($this->debug) JLog::add("");
        if ($this->debug) JLog::add("********************* Importing propmids *******************");
        // insert cats
        $cats = explode(',', $data[$this->cat_column]);
        foreach ($cats as $c){
            $cat_array = array();
            $cat = $this->getCategory($c);
            $cat_array['cat_id'] = $cat ?: 0;
            $cat_array['prop_id'] = $this->prop_id;
            $cat_array['ip_source'] = $this->ip_source;
            $this->insertData("iproperty_propmid", $cat_array);
        }
        // insert amens
        $amens      = array();
        $amens[]    = explode(',', $data[$this->amenity_columns[0]]);
        $amens[]    = explode(',', $data[$this->amenity_columns[1]]);
        $amens[]    = explode(',', $data[$this->amenity_columns[2]]);

        foreach ($amens as $key => $amentype){
            foreach ($amentype as $amen){
                if (strlen($amen)) $amen = $this->getAmenity($amen, $key);
                if ($amen){
                    $amen_array = array();
                    $amen_array['amen_id'] = $amen;
                    $amen_array['prop_id'] = $this->prop_id;
                    $amen_array['ip_source'] = $this->ip_source;
                    $this->insertData("iproperty_propmid", $amen_array);
                }
            }
        }
    }

    function importImages($data)
    {
        if ($this->debug) JLog::add("");
        if ($this->debug) JLog::add("********************* Importing photos *******************");
        $images = array();
        foreach ($this->image_columns as $c){
            $imagedata = $this->csv_columns[$c];
            $images[$imagedata[2]][$imagedata[1]] = $data[$c];
        }
        foreach ($images as $image){
            if($image['fname']){
                // parse out the path info
                $info   = pathinfo($image['fname']);
                if (!$image['remote']){
                    if ($this->debug) JLog::add("Doing local image import for ".$image['fname']);
                    $return = $this->doThumb_gd($image['fname']);
                    $image['fname'] = $return ? $info['filename'] : false;
                    $image['type']  = '.jpg';
                    $image['path']  = $this->settings->imgpath ?: '/media/com_iproperty/pictures/';
                } else if ($image['remote']) {
                    if ($this->debug) JLog::add("Doing remote image import for ".$image['fname']);
                    // parse out the path info
                    $info   = pathinfo($image['fname']);
                    $image['fname']  = $info['filename'];
                    $image['type']   = '.'.$info['extension'];
                    $image['path']   = $info['dirname'].'/';
                }
                $image['ip_source'] = $this->ip_source;
                $image['propid']    = $this->prop_id;
                if ($image['fname']) $this->insertData("iproperty_images", $image);
            }
        }
    }
	
    function importDocs($data)
    {
        if ($this->debug) JLog::add("");
        if ($this->debug) JLog::add("********************* Importing documents *******************");
        $docs = array();
        foreach ($this->document_columns as $c){
            $docdata = $this->csv_columns[$c];
            $docs[$docdata[2]][$docdata[1]] = $data[$c];
        }
        foreach ($docs as $doc){	
            if($doc['fname']){
                // parse out the path info
                $info   = pathinfo($doc['fname']);
                if (!$doc['remote']){
                    if ($this->debug) JLog::add("Doing local document import for ".$doc['fname']);
                    $doc['fname'] = $info['filename'];
                    $doc['type']  = '.'.$info['extension'];
                    $doc['path']  = $this->settings->imgpath ?: '/media/com_iproperty/pictures/';
					$old = $this->img_path.'/'.$info['basename'];
					$new = JPATH_SITE.$doc['path'].$info['basename'];					
					rename($old, $new);
                } else if ($doc['remote']) {
                    if ($this->debug) JLog::add("Doing remote document import for ".$doc['fname']);
                    // parse out the path info
                    $info   = pathinfo($doc['fname']);
                    $doc['fname']  = $info['filename'];
                    $doc['type']   = '.'.$info['extension'];
                    $doc['path']   = $info['dirname'].'/';
                }
                $doc['ip_source'] = $this->ip_source;
                $doc['propid']    = $this->prop_id;
                if ($doc['fname']) $this->insertData("iproperty_images", $doc);
            }
        }
    }	

    function importOpenhouses($data)
    {
        // TODO
    }
}
?>
