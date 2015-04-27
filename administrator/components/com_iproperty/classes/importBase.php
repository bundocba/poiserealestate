<?php

/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controlleradmin');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.archive');
jimport('joomla.log.log');
ini_set('auto_detect_line_endings',TRUE);

abstract class importBase 
{
    protected $database;
    protected $suppress;
    protected $img_path;
    protected $amenities_array     = array();
    protected $state_array         = array();
    protected $category_array      = array();
    protected $country_array       = array();
    protected $tables              = array('iproperty', 'iproperty_companies', 'iproperty_agents', 'iproperty_images');
    protected $stype_array;
    protected $settings;
    protected $datafile;
    protected $prop_id;
    protected $company_id;
    protected $ip_source;
    protected $debug                = false;
    protected $dumptables           = false; 
    protected $create_no_match      = false;

    public function __construct($datafile, $debug = false, $empty = false, $create_no_match = true) 
	{
        $this->app          = JFactory::getApplication();
        $this->database     = JFactory::getDBO();
        $this->datafile     = $datafile;
        $this->create_no_match = $create_no_match;
        $this->settings     = ipropertyAdmin::config();
        if($debug){
            $this->debug = true;
            JLog::addLogger( array('text_file' => 'iproperty_import.log.php'));
            JLog::add("");
            JLog::add("********************* BEGINNING BULK IMPORT  *******************");
            JLog::add("");
        }
		if($empty){
            $this->dumptables = true;
        }
		
		// check the file
		if (!JFile::exists($this->datafile)){
			$msg = JText::_( 'COM_IPROPERTY_IMPORT_FILE_DOES_NOT_EXIST' ).' FILE NOT FOUND - '.__LINE__;
            $type = 'notice';
            if($this->debug) JLog::add($this->datafile." NOT FOUND");
            $this->app->redirect('index.php?option=com_iproperty&view=bulkimport', $msg, $type);
        }    

        // get image path
        if(($this->img_path = JRequest::getString('img_path')) !== false){
            if(!$this->img_path = JPath::clean(JPATH_SITE.'/'.$this->img_path)){
                $msg = JText::_('IMG_PATH ERROR').' INVALID IMAGE PATH: ' . $this->img_path;
                $type = 'notice';
                if($this->debug) JLog::add('Img_path not found or not in site root:' . $this->img_path);
                $this->app->redirect('index.php?option=com_iproperty&view=bulkimport', $msg, $type);
            }
        }

        $this->stype_array  = ipropertyHTML::get_stypes();

        // create amen array
        $query = "SELECT * FROM #__iproperty_amenities WHERE 1";
        $this->database->setQuery($query);
        if(!$amenities = $this->database->loadObjectList()){
            $msg = JText::_( 'COM_IPROPERTY_QUERIES_EXECUTION_FAILED' ).' AMENITIES NOT FOUND - '.__LINE__.': ' . $this->database->getErrorMsg();
            $type = 'notice';
            if($this->debug) JLog::add($this->database->getErrorMsg());
            $this->app->redirect('index.php?option=com_iproperty&view=bulkimport', $msg, $type);
        } else {
            foreach($amenities as $am){
                $this->amenities_array[trim(strtolower($am->title))] = $am->id;
            }
        }

        // create cat array
        $query = "SELECT id, title FROM #__iproperty_categories WHERE 1";
        $this->database->setQuery($query);
        if(!$cats = $this->database->loadObjectList()){
            $msg = JText::_( 'COM_IPROPERTY_QUERIES_EXECUTION_FAILED' ).' ADD CATEGORIES BEFORE IMPORTING -  '.__LINE__.': ' . $this->database->getErrorMsg();
            $type = 'notice';
            if($this->debug) JLog::add($this->database->getErrorMsg());
            $this->app->redirect('index.php?option=com_iproperty&view=bulkimport', $msg, $type);
        } else {
            foreach($cats as $am){
                $this->category_array[trim(strtolower($am->title))] = $am->id;
            }
        }

        // create stype array
        $query = "SELECT id, name FROM #__iproperty_stypes WHERE 1";
        $this->database->setQuery($query);
        if(!$stypes = $this->database->loadObjectList()){
            $msg = JText::_( 'COM_IPROPERTY_QUERIES_EXECUTION_FAILED' ).' NO SALE TYPES FOUND -  '.__LINE__.': ' . $this->database->getErrorMsg();
            $type = 'notice';
            if($this->debug) JLog::add($this->database->getErrorMsg());
            $this->app->redirect('index.php?option=com_iproperty&view=bulkimport', $msg, $type);
        } else {
            foreach($stypes as $st){
                $this->stype_array[trim(strtolower($st->name))] = $st->id;
            }
        }

        // create state array
        $query = "SELECT id, mc_name FROM #__iproperty_states WHERE 1";
        $this->database->setQuery($query);
        if(!$states = $this->database->loadObjectList()){
            $msg = JText::_( 'COM_IPROPERTY_QUERIES_EXECUTION_FAILED' ).' NO STATES FOUND -  '.__LINE__.': ' . $this->database->getErrorMsg();
            $type = 'notice';
            if($this->debug) JLog::add($this->database->getErrorMsg());
            $this->app->redirect('index.php?option=com_iproperty&view=bulkimport', $msg, $type);
        } else {
            foreach($states as $am){
                $this->state_array[trim(strtolower($am->mc_name))] = $am->id;
            }
        }

        // create country array
        $query = "SELECT id, title FROM #__iproperty_countries WHERE 1";
        $this->database->setQuery($query);
        if(!$countries = $this->database->loadObjectList()){
            $msg = JText::_( 'COM_IPROPERTY_QUERIES_EXECUTION_FAILED' ).' NO COUNTRIES FOUND -  '.__LINE__.': ' . $this->database->getErrorMsg();
            $type = 'notice';
            if($this->debug) JLog::add($this->database->getErrorMsg());
            $this->app->redirect('index.php?option=com_iproperty&view=bulkimport', $msg, $type);
        } else {
            foreach($countries as $am){
                $this->country_array[trim(strtolower($am->title))] = $am->id;
            }
        }
        
        // clean out existing data
        $this->cleanMids($this->dumptables);
        $this->doImport();
    }

    // abstract functions to import the data
    abstract function doImport();
    abstract function importCompany($data);
    abstract function importAgents($data);
    abstract function importListing($data, $company);
    abstract function importPropmids($data);
    abstract function importImages($data);
    abstract function importOpenhouses($data);

    ########################################################
    # HELPER FUNCTIONS                                     #
    ########################################################
    
    protected function getAmenity($name, $type)
    {
        if (($amen = $this->amenities_array[trim(strtolower($name))]) !== null) {
            if ($this->debug) JLog::add('Amen found for: '.$name.' Local ID: '.$amen);
            return $amen;
        } else {
            if ($this->create_no_match){
                if ($this->debug) JLog::add('Amen not found, setting it: '.$name);
                $values = array('title' => $name, 'cat' => $type);
                $amen = $this->setAmenity($values);
                return $amen;
            } else {
                return false;
            }
        }
    }

    protected function setAmenity($values)
    {
        if(is_array($values) && $values['title']){
            $query = 'INSERT INTO #__iproperty_amenities (title, cat) VALUES ('.$this->database->Quote(trim($values['title'])).','.$values['cat'].')';
            $this->database->setQuery($query);
            if($this->database->execute()){
                if ($this->debug) JLog::add('Added amenity '.$values['title']);
                // get the new amen ID
                $newid = $this->database->insertid();
                $this->amenities_array[trim(strtolower($values['title']))] = $newid;
                return $newid;
            } else {
                if ($this->debug) JLog::add('Failed to add amenity '.$values['title'].' - '.$this->database->getErrorMsg());
                return false;
            }
        }
    }
    
    protected function getCategory($name)
    {
        if (($cat = $this->category_array[trim(strtolower($name))]) !== null) {
            if ($this->debug) JLog::add('Cat found for: '.$name.' Local ID: '.$cat);
            return $cat;
        } else {
            if ($this->create_no_match){
                if ($this->debug) JLog::add('Cat not found, setting it: '.$name);
                $cat = $this->setCategory((string) $name);
                return $cat;
            } else {
                return false;
            }
        }
    }

    protected function setCategory($name)
    {
        if($name){
            $query = 'INSERT INTO #__iproperty_categories (title, ordering, alias, access) SELECT '.$this->database->Quote(trim($name)).', IFNULL(MAX(ordering),0)+1,'.$this->database->Quote(JApplication::stringURLSafe($name)).', 1 FROM #__iproperty_categories';
            $this->database->setQuery($query);
            if($this->database->execute()){
                if ($this->debug) JLog::add('Added category '.$name);
                // get the new cat ID
                $newid = $this->database->insertid();
                $this->category_array[trim(strtolower($name))] = $newid;
                return $newid;
            } else {
                if ($this->debug) JLog::add('Failed to add category '.$name.' - '.$this->database->getErrorMsg());
                return false;
            }
        }
    }

    protected function getObjectID($id, $type='agent')
    {
        if(!$id) return false;
        if ($this->debug) JLog::add('Looking for ObjectID '.$id.' - '.$type);
        switch ($type){
            case 'agent':
                $table = 'agents';
                break;
            case 'company':
                $table = 'companies';
                break;
        }

        $query = "SELECT id FROM #__iproperty_".$table." WHERE ip_source = ".$this->database->Quote($id);
        $this->database->setQuery($query);

        if(($result = $this->database->loadResult()) !== false) {
            if ($this->debug) JLog::add('ObjectID '.$id.' found: local ID '.$result);
            return $result;
        }
        if ($this->debug) JLog::add('ObjectID '.$id.' not found.');
        return false;
    }

    protected function doGeocode($address)
    {
        if($this->debug) JLog::add("Doing geocode for " . $address);
        $mapaddress = urlencode( $address );

        // Desired address
        $url = "http://maps.googleapis.com/maps/api/geocode/xml?address=$mapaddress&sensor=false";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
        curl_setopt($ch, CURLOPT_URL, $url);

        $page = curl_exec($ch);
        curl_close($ch);

        // uncomment this line if geocoding fails due to frequent requests
        //sleep(1);

        // Parse the returned XML file
        try{
            $xml = @new SimpleXMLElement($page);
        } catch (Exception $e) {
            if($this->debug) JLog::add("SimpleXML error in geocode function: " . $e);
            return false;
        }
        // Parse the coordinate string
        $longitude = $xml->result->geometry->location->lng;
        $latitude = $xml->result->geometry->location->lat;
        $gaddress = addslashes($xml->result->formatted_address);
        $status = $xml->status;

        unset($xml);
        unset($page);

        if ($status == 'OK') {
            if($this->debug) JLog::add("Geocode found-- " . $gaddress . " latitude: " . $latitude . " longitude: " . $longitude);
            $location = array($latitude, $longitude);
            return $location;
        } elseif ( $status == 'ZERO_RESULTS' ) { // no address or private address
            if($this->debug) JLog::add("No geocode found for property at " . $address);
            return false;
        } else {
            if ($status == 'OVER_QUERY_LIMIT' && !$this->suppress) {
                if($this->debug) JLog::add("OUT OF GEOCODES! FURTHER ERRORS SUPPRESSED.");
                $this->suppress = true;
            } else {
                if($this->debug) JLog::add("A problem with the geocoder occured. Error: " . $status);
            }
            return false;
        }
    }

    // GD THUMB FUNCTION
    protected function doThumb_gd($img_name)
    {
        // pass in the filename
        $thumb_w = $this->settings->thumbwidth ? $this->settings->thumbwidth : 150;
        $thumb_h = $this->settings->thumbheight ? $this->settings->thumbheight : 95;
        $fullsize_w = $this->settings->imgwidth ? $this->settings->imgwidth : 710;
        $fullsize_h = $this->settings->imgheight ? $this->settings->imgheight : 450;
        $filename = $this->img_path.'/'.$img_name;

        if(!JFile::exists($filename)){
            if($this->debug) JLog::add("ERROR in GD transform: file ".$filename." does not exist.");
            return false;
        }

        try{
            $system=explode(".",$img_name);
                $shortname = JFile::makeSafe($system[0]);
                try{
                    if (preg_match("/jpg|jpeg/i",$system[1])) {
                        if (!$src_img=imagecreatefromjpeg($filename)){
                            throw new Exception('Could not create jpg');
                            return false;
                        }
                    }
                } catch (Exception $e) {
                    if($this->debug) JLog::add("ERROR 1 in GD transform: " . $e);
                    return false;
                }
                try{
                    if (preg_match("/png/i",$system[1])) {
                        if(!$src_img=imagecreatefrompng($filename)){
                            throw new Exception('Could not create png.');
                            return false;
                        }
                    }
                } catch (Exception $e) {
                    if($this->debug) JLog::add("ERROR 2 in GD transform: " . $e);
                    return false;
                }
                if($this->debug) JLog::add("Doing GD transform for " . $filename);
        } catch (Exception $e) {
            if($this->debug) JLog::add("ERROR 3 in GD transform: " . $e);
            return false;
        }

        $old_x=imageSX($src_img);
        $old_y=imageSY($src_img);

        if($this->settings->imgproportion){ // we need to keep originally proportions so get ratio
            $ratio         = $old_y / $old_x;

            $thumb_h       = $ratio * $thumb_w;
            $fullsize_h    = $ratio * $fullsize_w;

            $th_w = $thumb_w;
            $th_h = $thumb_h;
            $fs_w = $fullsize_w;
            $fs_h = $fullsize_h;
        }

        if(!$this->debug) unlink( $filename ); // delete the temp images only if not in debug mode
        // Output the image
        // create fullsize
        try{
            $dst_img = ImageCreateTrueColor($fs_w, $fs_h);
            $newfile = JPATH_SITE . $this->settings->imgpath . $shortname . ".jpg";
            imagecopyresampled($dst_img,$src_img,0,0,0,0,$fs_w,$fs_h,$old_x,$old_y);
            imagejpeg($dst_img,$newfile);
            if($this->debug) JLog::add("Created " . $newfile);
        }
        catch(Exception $e) {
            if($this->debug) JLog::add("ERROR 2 in GD transform for " . $filename . " - " . $e);
            return false;
        }
        //imagedestroy($dst_img);
        // create thumb
        try{
            $tdst_img = ImageCreateTrueColor($th_w, $th_h);
            $newfile = JPATH_SITE . $this->settings->imgpath . $shortname . "_thumb.jpg";
            imagecopyresampled($tdst_img,$src_img,0,0,0,0,$th_w,$th_h,$old_x,$old_y);
            imagejpeg($tdst_img,$newfile);
            if($this->debug) JLog::add("Created " . $newfile);
        }
        catch(Exception $e) {
            if($this->debug) JLog::add("ERROR 3 in GD transform for " . $filename . " - " . $e);
            return false;
        }

        imagedestroy($dst_img);
        imagedestroy($src_img);
        return $newfile;
    }

    // GD THUMB FUNCTION
    protected function doAgentThumb_gd($img_name, $folder, $remote = false)
    {
        if ($this->debug) JLog::add('Doing icon transform for: '.$img_name.' - '.$folder.' remote: '.$remote);
        if ($remote == true || (strpos($img_name, 'http') !== false)){
            // it's a remote image so let's just return the path
            return $img_name;
        }
        // pass in the filename
        if ($folder == 'agents'){
            $thumb_w = $this->settings->agentphotowidth ? $this->settings->agentphotowidth : 90;
        } elseif ($folder == 'companies') {
            $thumb_w = $this->settings->company_photo_width ? $this->settings->company_photo_width : 90;
        } else {
            return false;
        }
        $filename = $this->img_path.'/'.$img_name;

        if (!JFile::exists($filename)){
            if($this->debug) JLog::add("ERROR in GD " . $folder . " transform. File does not exist: " . $filename . " - " . $img_name);
            return false;
        }

        try{
            $system=explode(".",$img_name);
                $shortname = JFile::makeSafe($system[0]);
                try{
                    if (!$src_img=imagecreatefromjpeg($filename))  throw new Exception('Could not create jpg');
                } catch (Exception $e) {
                    if ($this->debug) JLog::add("ERROR 1 in GD " . $folder . " transform: " . $e);
                    return false;
                }
                if ($this->debug) JLog::add("Doing GD " . $folder . " transform for " . $filename);
        } catch (Exception $e) {
            if ($this->debug) JLog::add("ERROR 2 in GD " . $folder . " transform: " . $e);
            return false;
        }

        $old_x=imageSX($src_img);
        $old_y=imageSY($src_img);

        $ratio         = $old_y / $old_x;
        $thumb_h       = $ratio * $thumb_w;
        $th_w = $thumb_w;
        $th_h = $thumb_h;

        if (!$this->debug) unlink( $filename ); // delete the temp images only if not in debug mode
        // Output the image
        // create thumb
        try{
            $dst_img = ImageCreateTrueColor($th_w, $th_h);
            $newfile = JPATH_SITE . '/media/com_iproperty/'.$folder .'/'. $shortname . ".jpg";
            imagecopyresampled($dst_img,$src_img,0,0,0,0,$th_w,$th_h,$old_x,$old_y);
            imagejpeg($dst_img,$newfile);
            if ($this->debug) JLog::add("Created " . $newfile);
            return $shortname.'.jpg';
        }
        catch(Exception $e) {
            if ($this->debug) JLog::add("ERROR 3 in GD " . $folder . " transform for " . $filename . " - " . $e);
            return false;
        }
        imagedestroy($dst_img);
        imagedestroy($src_img);
    }

    protected function insertData($tab_name, $arr)
    {
        $query_string = '';
        foreach ($arr as $column => $value) {
            if ($value) $query_string .= $this->database->quoteName($column). "=" . $this->database->Quote(trim($value)) . ",";
        }
        $query_string = rtrim($query_string, ',');
        $query = "INSERT INTO " . $this->database->quoteName('#__'.$tab_name) . " SET " . $query_string . " ON DUPLICATE KEY UPDATE " . $query_string . ",id=LAST_INSERT_ID(id)";
        if ($this->debug) JLog::add("Inserting into: ".$tab_name." - ".$query);
        $this->database->setQuery($query);
        if (!$this->database->execute()){
            if ($this->debug) JLog::add("Insert failed: ".$this->database->getErrorMsg());
        } else {
            if ($this->debug) JLog::add("Query: ".$query." -SUCCESS. ID ASSIGNED: ".$this->database->insertid());
            return $this->database->insertid();
        }
        return false;
    }

    protected function cleanMids($dump = false)
    {
        // remove all data from mid tables with ip source set
        $tables = array('#__iproperty_propmid', '#__iproperty_agentmid', '#__iproperty_images');
		
        if ($this->dumptables){ // we want to dump the DB so add other IP tables
            if($this->debug) JLog::add('DUMPING ALL IPROPERTY TABLES!');
            $dumptables  = array('#__iproperty','#__iproperty_agents','#__iproperty_companies');
            $tables      = array_merge($tables, $dumptables); 

			foreach ($tables as $table) {
				$query = 'DELETE FROM '.$table.' WHERE ip_source IS NOT NULL';
				if ($this->debug) JLog::add("Cleaning mid_tables: ".$query);
				$this->database->setQuery($query);
				if(!$this->database->execute()){
					if ($this->debug) JLog::add("Cleanup failed: ".$this->database->getErrorMsg());
					return false;
				}
			}
			if ($this->debug) JLog::add("Pre-import cleanup completed.");
			return true;
		}
    }
	
	protected function cleanMidSingle($id)
	{
		// remove all data from mid tables with ip source set
        $tables = array('#__iproperty_propmid', '#__iproperty_agentmid', '#__iproperty_images');
		foreach ($tables as $table) {
			$propid = 'prop_id';
			if ($table == '#__iproperty_images') $propid = 'propid'; // stupid hack since the propid column is named diff in images
			$query = 'DELETE FROM '.$table.' WHERE '.$this->database->quoteName($propid).' = '.$this->database->Quote($id);
			if ($this->debug) JLog::add("Cleaning mid_tables for ID: ".$query);
			$this->database->setQuery($query);
			if(!$this->database->execute()){
				if ($this->debug) JLog::add("Cleanup failed: ".$this->database->getErrorMsg());
				return false;
			}
		}
		if ($this->debug) JLog::add("Mid cleanup completed for id ".$id);
		return true;
	}

    protected function formatDate($date, $format = 1)
    {
        $date = strtotime($date);
        switch ($format){
            case 1: // datetime
                $newdate = date("Y-m-d H:i:s", $date);
                break;
            case 2: // date
                $newdate = date("Y-m-d", $date);
                break;
            case 3: // timestamp
                $newdate = $date;
                break;
            default: // datetime
                $newdate = date("Y-m-d H:i:s", $date);
                break;
        }
        return $newdate;
    }
	
	protected function getFileFTP($filename, $host, $username, $password, $directory = false)
	{
		$config         = JFactory::getConfig();
		$tmp            = $config->get('tmp_path');
        $server_file    = $directory.'/'.$filename;
        $local_file     = $tmp.'/'.$filename;
        
        // set up basic ftp connection
        $conn_id = ftp_connect($host);

        // login with username and password
        if(!$login_result = ftp_login($conn_id, $username, $password)){
            if ($this->debug) JLog::add("Unable to connect to ".$host." via FTP-- exiting.");
            return false;
        }
        
        // try to download $server_file and save to $local_file
        if (ftp_get($conn_id, $local_file, $server_file, FTP_BINARY)) {
            if ($this->debug) JLog::add("Wrote ftp download to " . $local_file);
            return $local_file;
        } else {
            if ($this->debug) JLog::add("Problem with FTP download of $filename. Process failed.");
            return false;
        }
        
	}
	
	protected function extractFile($filename)
	{
		$config 		= JFactory::getConfig();
		$tmp 			= $config->get('tmp_path');
		$extractpath 	= $tmp.'/ip_xml'.time().'/';
        // unzip archive
		$zip = new ZipArchive;
		$res = $zip->open($filename);
		if ($res === TRUE) {
			$zip->extractTo($extractpath);
			$zip->close();
			if ($this->debug) JLog::add("Successfully extracted ZIP Archive ".$filename);
			return $extractpath.$filename;
		} else {
			if ($this->debug) JLog::add("Failed to open ZIP Archive ".$filename);
			return false;
		}
	}
}
?>
