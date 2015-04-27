<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');

class IpropertyTablePropertyagent extends JTable
{
    protected $tagsHelper = null;
    
	public function __construct(&$_db)
	{
		parent::__construct('#__ipropertyagent', 'id', $_db);
        // @TODO: Add Joomla tags
        //$this->tagsHelper = new JHelperTags();
        //$this->tagsHelper->typeAlias = 'com_iproperty.property';
	}

	public function check()
	{
	
        jimport('joomla.filter.output');
        $settings   = ipropertyAdmin::config();
        $ipauth     = new IpropertyHelperAuth(array('msg'=>false));
        $date       = JFactory::getDate();
        $user       = JFactory::getUser();
        
        // new debug option
        $debug      = false;
        if ($debug) JLog::addLogger( array('text_file' => 'geocode.log.php'));

        // do geocode 
        if((!$this->latitude|| !$this->longitude) && $settings->map_provider && function_exists('curl_init')){
            switch ($settings->map_provider){
                case 1: // GOOGLE
                    $this->doGeoGoogle($debug);
                break;
                case 2: // BING
                    $this->doGeoBing($settings->map_credentials, $debug);
                break;
            }
        }
        
		// Set name
        $this->title    = htmlspecialchars_decode($this->title, ENT_QUOTES);
		$this->street   = htmlspecialchars_decode($this->street, ENT_QUOTES);
        $this->street2  = htmlspecialchars_decode($this->street2, ENT_QUOTES);
        
        // Set alias
		$this->alias = JApplication::stringURLSafe($this->alias);
		if (empty($this->alias)) {
            $ptitle         = ($this->title) ? $this->title : $this->street_num.' '.$this->street.' '.$this->street2;
			$this->alias    = JApplication::stringURLSafe($ptitle.' '.$this->city);
		}

		// Check the publish down date is not earlier than publish up.
		if ($this->publish_down > $this->_db->getNullDate() && $this->publish_down < $this->publish_up) {
			// Swap the dates.
			$temp = $this->publish_up;
			$this->publish_up = $this->publish_down;
			$this->publish_down = $temp;
		}	
        
        // Clean up keywords -- eliminate extra spaces between phrases
		// and cr (\r) and lf (\n) characters from string
        if (!empty($this->metakey)) {
			// Only process if not empty
			$bad_characters = array("\n", "\r", "\"", "<", ">"); // array of characters to remove
			$after_clean = JString::str_ireplace($bad_characters, "", $this->metakey); // remove bad characters
			$keys = explode(',', $after_clean); // create array using commas as delimiter
			$clean_keys = array();

			foreach($keys as $key) {
				if (trim($key)) {
					// Ignore blank keywords
					$clean_keys[] = trim($key);
				}
			}
			$this->metakey = implode(", ", $clean_keys); // put array back together delimited by ", "
		}

        if(!$ipauth->canFeatureProp($this->id, $this->featured)){
            unset($this->featured);
        }
        if(!$ipauth->canPublishProp($this->id, $this->state)){
            unset($this->state);
        }

		return true;
	}

	public function bind($array, $ignore = array())
	{   
            
        if (isset($array['params']) && is_array($array['params'])) {
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = (string)$registry;
		}       
        
		return parent::bind($array, $ignore);
	}

	public function store($updateNulls = false)
	{
		$date	= JFactory::getDate();
		$user	= JFactory::getUser();
        
        // if modified, set modified date and user, else set created date
        if ($this->id) {
			// Existing item
			$this->modified		= $date->toSql();
			$this->modified_by	= $user->get('id');
		} else {
			if (!intval($this->created)) {
				$this->created = $date->toSql();
			}
            if (!intval($this->publish_up)) {
				$this->publish_up = $date->toSql();
			}
            if (empty($this->created_by)) {
				$this->created_by = $user->get('id');
			}
		}
        
		// Verify that the alias is unique
		$table = JTable::getInstance('Propertyagent', 'IpropertyTable');
		if ($table->load(array('alias'=>$this->alias)) && ($table->id != $this->id || $this->id == 0)) {
			//$this->setError(JText::_('COM_IPROPERTY_ERROR_UNIQUE_ALIAS'));
			//return false;
            $this->alias = $this->alias.rand();
		}
        
        // @TODO: store Joomla tags        
        //$this->tagsHelper->preStoreProcess($this);

		// Attempt to store the data.
		return parent::store($updateNulls);
        
        // @TODO: store Joomla tags
        //return $result && $this->tagsHelper->postStoreProcess($this);
	}

	public function publish($pks = null, $state = 1, $userId = 0)
	{
		// Initialise variables.
		$k = $this->_tbl_key;       

		// Sanitize input.
		JArrayHelper::toInteger($pks);
		$state      = (int) $state;

		// If there are no primary keys set check to see if the instance key is set.
		if (empty($pks))
		{
			if ($this->$k) {
				$pks = array($this->$k);
			}
			// Nothing to set publishing state on, return false.
			else {
				$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
				return false;
			}
		}

		// Get an instance of the table
		$table = JTable::getInstance('Propertyagent','IpropertyTable');

		// For all keys
		foreach ($pks as $pk)
		{
            // Load the banner
            if(!$table->load($pk)){
                $this->setError($table->getError());
            }

            // Change the state
            $table->state = $state;

            // Check the row
            $table->check();

            // Store the row
            if (!$table->store()){
                $this->setError($table->getError());
            }
		}
		return count($this->getErrors())==0;
	}
    
    public function feature($pks = null, $state = 0)
	{
		// Initialise variables.
		$k = $this->_tbl_key;      

		// Sanitize input.
		JArrayHelper::toInteger($pks);
		$state      = (int) $state;

		// If there are no primary keys set check to see if the instance key is set.
		if (empty($pks))
		{
			if ($this->$k) {
				$pks = array($this->$k);
			}
			// Nothing to set publishing state on, return false.
			else {
				$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
				return false;
			}
		}

		// Get an instance of the table
		$table = JTable::getInstance('Propertyagent','IpropertyTable');

		// For all keys
		foreach ($pks as $pk)
		{
            // Load the banner
            if(!$table->load($pk)){
                $this->setError($table->getError());
            }

            // Change the state
            $table->featured = $state;

            // Check the row
            $table->check();

            // Store the row
            if (!$table->store()){
                $this->setError($table->getError());
            }
		}
		return count($this->getErrors())==0;
	}
    
    public function delete($pks = null)
	{
        // Initialise variables.
		$k = $this->_tbl_key;      

		// Sanitize input.
		JArrayHelper::toInteger($pks);

		// If there are no primary keys set check to see if the instance key is set.
		if (empty($pks))
		{
			if ($this->$k) {
				$pks = array($this->$k);
			}
			// Nothing to set publishing state on, return false.
			else {
				$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
				return false;
			}
		}

		try
		{			
			// delete from properties table
            $query = $this->_db->getQuery(true);
            $query->delete();
            $query->from('#__ipropertyagent');
            $query->where('id IN ('.implode(',', $pks).')');
            $this->_db->setQuery($query);
            
			// Check for a database error.
            if (!$this->_db->execute()) {
				throw new Exception($this->_db->getErrorMsg());
			}
            
            // delete from agent mid table
            $query = $this->_db->getQuery(true);
            $query->delete();
            $query->from('#__iproperty_agentmid');
            $query->where('prop_id IN ('.implode(',', $pks).')');
            $this->_db->setQuery($query);

			// Check for a database error.
            if (!$this->_db->execute()) {
				throw new Exception($this->_db->getErrorMsg());
			}
            
            // delete from property mid table
            $query = $this->_db->getQuery(true);
            $query->delete();
            $query->from('#__iproperty_propmid');
            $query->where('prop_id IN ('.implode(',', $pks).')');
            $this->_db->setQuery($query);

			// Check for a database error.
            if (!$this->_db->execute()) {
				throw new Exception($this->_db->getErrorMsg());
			}
            
            // delete from openhouse table
            $query = $this->_db->getQuery(true);
            $query->delete();
            $query->from('#__iproperty_openhouses');
            $query->where('prop_id IN ('.implode(',', $pks).')');
            $this->_db->setQuery($query);

			// Check for a database error.
            if (!$this->_db->execute()) {
				throw new Exception($this->_db->getErrorMsg());
			}
            
            // @TODO: delete Joomla tags
            /*foreach($pks as $pk){
                $this->tagsHelper->deleteTagData($this, $pk);
            }*/
            
            // Delete image rows and files if applicable         
            if(!$this->_deleteImageFiles($pks)){
				throw new Exception($this->getError());
			}
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		}
        
		return count($this->getErrors())==0;
	}

    protected function _deleteImageFiles($cid = array())
    {
        //delete images where propid in array of ids
        $cids       = implode( ',', $cid );
        $imgtable   = JTable::getInstance('Image','IpropertyTable');
        
        $query = $this->_db->getQuery(true);
        $query->select('*');
        $query->from('#__iproperty_images');
        $query->where('propid IN ('.$cids.')');
        $this->_db->setQuery($query);
        
        // Images for each listing being deleted
        $imgs       = $this->_db->loadObjectList();

        foreach($imgs as $img){
            // is the image file linked to another object?
            $query = $this->_db->getQuery(true);
            $query->select('id');
            $query->from('#__iproperty_images');
            $query->where('fname = '.$this->_db->Quote($img->fname));
            $query->where('id != '.(int)$img->id);
            $query->limit('1');
            $this->_db->setQuery($query);
            
            // Delete the image files if they are not linked to other listings
            if($this->_db->loadResult() == null) {
                if(chdir(JPATH_SITE.$img->path)) {
                    $dimg   = $img->fname.$img->type;
                    $dthumb = $img->fname . "_thumb".$img->type;
                    @chmod($dimg, 0777);
                    @chmod($dthumb, 0777);
                    unlink($dimg);
                    unlink($dthumb);
                }
            }
            
            // delete from images table
            $query = $this->_db->getQuery(true);
            $query->delete();
            $query->from('#__iproperty_images');
            $query->where('id = '.(int)$img->id);
            $this->_db->setQuery($query);
            
            if(!$this->_db->execute()){
                $this->setError($this->_db->getErrorMsg());
                return false;
            }            
            $imgtable->reorder('propid = '.(int)$img->propid.' AND type = '.$this->_db->Quote($img->type));            
        }
        return true;
    }
    
    public function deletePropMids($propid)
    {
        try{
            // delete from propmid table
            $query = $this->_db->getQuery(true);
            $query->delete();
            $query->from('#__iproperty_propmid');
            $query->where('prop_id = '.(int)$propid);
            $this->_db->setQuery($query);
            
            if (!$this->_db->execute()) {
                throw new Exception($this->_db->getErrorMsg());
            }
        }
        catch (Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		} 
        return true;
    }
    
    public function deleteAgentMids($propid)
    {
        try{            
            // delete from agentmid table
            $query = $this->_db->getQuery(true);
            $query->delete();
            $query->from('#__iproperty_agentmid');
            $query->where('prop_id = '.(int)$propid);
            $this->_db->setQuery($query);
            
            if (!$this->_db->execute()) {
                throw new Exception($this->_db->getErrorMsg());
            }
        }
        catch (Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		} 
        return true;
    }  
    
    public function storeCatMids($propid, $cats)
    {
        try
		{        
            foreach( $cats as $cat ){
                $query = 'INSERT INTO #__iproperty_propmid (prop_id, cat_id) VALUES ('.(int)$propid.','.(int)$cat.')';
                $this->_db->setQuery($query);
                
                if (!$this->_db->execute()) {
                    throw new Exception($this->_db->getErrorMsg());
                }
            }
        }
        catch (Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		}
        return true;
    }
    
    public function storeAgentMids($propid, $agents)
    {        
        try
		{        
            foreach( $agents as $agent ){
                $query = 'INSERT INTO #__iproperty_agentmid (prop_id, agent_id) VALUES ('.(int)$propid.','.(int)$agent.')';
                $this->_db->setQuery($query);
                
                if (!$this->_db->execute()) {
                    throw new Exception($this->_db->getErrorMsg());
                }
            }
        }
        catch (Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		}
        return true;
    }
    
    public function storeAmenities($propid, $amens)
    {       
        try
		{        
            foreach( $amens as $amen ){
                $query = 'INSERT INTO #__iproperty_propmid (prop_id, amen_id) VALUES ('.(int)$propid.','.(int)$amen.')';
                $this->_db->setQuery($query);
                
                if (!$this->_db->execute()) {
                    throw new Exception($this->_db->getErrorMsg());
                }
            }
        }
        catch (Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		}
        return true;
    }
    
    public function cloneImages($old_id, $new_id)
    {
        //die('here - '.$old_id.'=='.$new_id);
        $query = "SELECT * FROM #__iproperty_images WHERE propid = ".(int)$old_id;
        $this->_db->setQuery($query);
        $images = $this->_db->LoadObjectList();


        for($x = 0; $x < count($images); $x++) {
            $currimg = JTable::getInstance('Image','IpropertyTable');
            $currimg->load($images[$x]->id);

            $copyimg = JTable::getInstance('Image','IpropertyTable');
            $copyimg->propid            = $new_id;
            $copyimg->title             = $currimg->title;
            $copyimg->description       = $currimg->description;
            $copyimg->fname             = $currimg->fname;
            $copyimg->type              = $currimg->type;
            $copyimg->path              = $currimg->path;
            $copyimg->remote            = $currimg->remote;
            $copyimg->owner             = $currimg->owner;
            $copyimg->state             = $currimg->state;

            if (!$copyimg->check()) {
                $this->setError($this->_db->getErrorMsg());
                return false;
            }
            if (!$copyimg->store()) {
                $this->setError($this->_db->getErrorMsg());
                return false;
            }

            $copyimg->checkin();
            $copyimg->reorder( "propid = ".(int)$propid." AND type = '$copyimg->type'" );
        }
        return true;
    }
    
    public function clearHits($pks)
    {
        // Initialise variables.
		$k = $this->_tbl_key;       

		// Sanitize input.
		JArrayHelper::toInteger($pks);
		$state      = (int) $state;

		// If there are no primary keys set check to see if the instance key is set.
		if (empty($pks))
		{
			if ($this->$k) {
				$pks = array($this->$k);
			}
			// Nothing to set publishing state on, return false.
			else {
				$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
				return false;
			}
		}

		// Get an instance of the table
		$table = JTable::getInstance('Propertyagent','IpropertyTable');

		// For all keys
		foreach ($pks as $pk)
		{
            // Load the banner
            if(!$table->load($pk)){
                $this->setError($table->getError());
            }

            // Change the state
            $table->hits = '0';

            // Check the row
            $table->check();

            // Store the row
            if (!$table->store()){
                $this->setError($table->getError());
            }
		}
		return count($this->getErrors())==0;
    }
    
    public function approve($pks = null, $state = 0)
	{
		// Initialise variables.
		$k = $this->_tbl_key;      

		// Sanitize input.
		JArrayHelper::toInteger($pks);
		$state      = (int) $state;

		// If there are no primary keys set check to see if the instance key is set.
		if (empty($pks))
		{
			if ($this->$k) {
				$pks = array($this->$k);
			}
			// Nothing to set publishing state on, return false.
			else {
				$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
				return false;
			}
		}

		// Get an instance of the table
		$table = JTable::getInstance('Propertyagent','IpropertyTable');

		// For all keys
		foreach ($pks as $pk)
		{
            // Load the banner
            if(!$table->load($pk)){
                $this->setError($table->getError());
            }

            // Change the state
            $table->approved = $state;

            // Check the row
            $table->check();

            // Store the row
            if (!$table->store()){
                $this->setError($table->getError());
            }
            
            // Send approval notification to agents
            if($state == 1){
                $this->_notifyApproval($table->id);
            }
		}
		return count($this->getErrors())==0;
	}
    
    protected function _notifyApproval($propid)
    {
        //send notification of approval to agents
        require_once (JPATH_COMPONENT_SITE.'/helpers/route.php');
        $app  = JFactory::getApplication();

        $settings      = ipropertyAdmin::config();
        $admin_from    = $app->getCfg('fromname');
        $admin_email   = $app->getCfg('mailfrom');
        $property_path = JURI::base().ipropertyHelperRoute::getPropertyRoute($propid);

        $agents        = ipropertyHTML::getAvailableAgents($propid);
        $property      = ipropertyHTML::getPropertyTitle($propid);

		$subject        = sprintf(JText::_('COM_IPROPERTY_APPROVAL_SUBJECT' ), $property);
		$date           = JHTML::_('date','now',JText::_('DATE_FORMAT_LC4'));
        $fulldate       = JHTML::_('date','now',JText::_('DATE_FORMAT_LC2'));

        //check who admin wants to send the requests to
        $recipients = array();
        foreach($agents as $a){
            $recipients[] = $a->email;
        }     
        
		$body = sprintf(JText::_('COM_IPROPERTY_APPROVAL_BODY'), $property, $admin_from)."\n\n";
        $body .= JText::_('COM_IPROPERTY_FOLLOW_LINK' ) . ":\n"
                . $property_path . "\n\n"
                . JText::_('COM_IPROPERTY_GENERATED_BY_INTELLECTUAL_PROPERTY' ) . " " . $fulldate;

        $sento = '';
        $mail = JFactory::getMailer();
        $mail->addRecipient( $recipients );
        $mail->addReplyTo(array($admin_email, $admin_from));
        $mail->setSender( array( $admin_email, $admin_from ));
        $mail->setSubject( $subject );
        $mail->setBody( $body );
        $sento = $mail->Send();

		if( $sento ){
            return true;
		}else{
			return false;
		}
    }
    
    // google geocoder
    private function doGeoGoogle($debug = false)
    {
        // Attempt geocode if gmaps enabled and curl exists    
        $geoadd = '';
        if($this->street_num) $geoadd .= $this->street_num;
        if($this->street)     $geoadd .= ' '.$this->street;
        if($this->street2)    $geoadd .= ' '.$this->street2;
        if($this->city)       $geoadd .= ', '.$this->city;
        if($this->locstate)   $geoadd .= ', '.ipropertyHTML::getStateCode($this->locstate);
        if($this->province)   $geoadd .= ', '.$this->province;
        if($this->postcode)   $geoadd .= ', '.$this->postcode;
        if($this->country)    $geoadd .= ', '.ipropertyHTML::getCountryName($this->country);

        $mapaddress = urlencode($geoadd);
        $url        = "http://maps.googleapis.com/maps/api/geocode/xml?address=$mapaddress&sensor=false";

        if ($debug) JLog::add('mapaddress: '.$mapaddress);

        $ch         = curl_init();
        if($ch){
            $timeout    = 10;
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
            if(curl_error($ch) && $debug) JLog::add('Curl error: '.curl_error($ch));
            $data       = curl_exec($ch);
            curl_close($ch);
        } else {
            if ($debug) JLog::add('Curl Failed');
            return false;
        }

        if($data && strlen($data)) {
            if ($debug) JLog::add('Data was returned.');
        } else {
            if ($debug) JLog::add('No data returned');
        }

        try{
            // Parse the returned XML file
            $xml = new SimpleXMLElement($data);
        }catch(Exception $e){
            if ($debug) JLog::add('SimpleXML error: '.$e);
        }

        if ($xml->status == 'OK') {
            // got legit code back, so save the lat/lon
            $this->latitude         = (string) $xml->result->geometry->location->lat;
            $this->longitude        = (string) $xml->result->geometry->location->lng;
            $this->gbase_address    = (string) $xml->result->formatted_address;
        } else {
            if ($debug) JLog::add('Google status error: '.$xml->status);
        }
    }
        
    // google geocoder
    private function doGeoBing($credentials, $debug = false)
    {
        // Attempt geocode if gmaps enabled and curl exists    
        $geoadd = '';
        if($this->street_num) $geoadd .= $this->street_num;
        if($this->street)     $geoadd .= ' '.$this->street;
        if($this->street2)    $geoadd .= ' '.$this->street2;
        if($this->city)       $geoadd .= ', '.$this->city;
        if($this->locstate)   $geoadd .= ', '.ipropertyHTML::getStateCode($this->locstate);
        if($this->province)   $geoadd .= ', '.$this->province;
        if($this->postcode)   $geoadd .= ', '.$this->postcode;
        if($this->country)    $geoadd .= ', '.ipropertyHTML::getCountryName($this->country);

        $mapaddress = urlencode($geoadd);
        $url        = "http://dev.virtualearth.net/REST/v1/Locations?q=$mapaddress&o=xml&key=$credentials";

        if ($debug) JLog::add('mapaddress: '.$mapaddress);

        $ch         = curl_init();
        if($ch){
            $timeout    = 10;
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
            if(curl_error($ch) && $debug) JLog::add('Curl error: '.curl_error($ch));
            $data       = curl_exec($ch);
            curl_close($ch);
        } else {
            if ($debug) JLog::add('Curl Failed');
            return false;
        }

        if($data && strlen($data)) {
            if ($debug) JLog::add('Data was returned.');
        } else {
            if ($debug) JLog::add('No data returned');
        }

        try{
            // Parse the returned XML file
            $xml = new SimpleXMLElement($data);
        }catch(Exception $e){
            if ($debug) JLog::add('SimpleXML error: '.$e);
        }

        if ($xml->StatusDescription == 'OK') {
            // got legit code back, so save the lat/lon
            $this->latitude         = (string) $xml->ResourceSets->ResourceSet->Resources->Location->Point->Latitude;
            $this->longitude        = (string) $xml->ResourceSets->ResourceSet->Resources->Location->Point->Longitude;
            $this->gbase_address    = (string) $xml->ResourceSets->ResourceSet->Resources->Location->Address->FormattedAddress;
        } else {
            if ($debug) JLog::add('Bing geocode status error: '.$xml->StatusCode.' - '.$xml->StatusDescription);
        }
    }
}
?>