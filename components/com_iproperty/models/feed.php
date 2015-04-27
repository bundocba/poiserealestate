<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
jimport('joomla.application.component.model');

class IpropertyModelFeed extends JModelLegacy
{
	private $limit;
	private $cat;
	private $company;
	private $agent;
	
	public function __construct()
	{
		parent::__construct();

		$app        = JFactory::getApplication();
        $option     = JRequest::getCmd('option');
        
		$settings   = ipropertyAdmin::config();

		$this->limit		= $settings->rss;
		$this->disclaimer 	= $settings->disclaimer;
		#$this->cat		= $settings->feedcat;
		#$this->company	= $settings->feedcompany;
		#$this->agent	= $settings->feedagent;
	}
	
    public function getData()
    {
		$app        = JFactory::getApplication();
        $option     = JRequest::getCmd('option');

		$db = JFactory::getDBO();
		$query = "SELECT a.*,"
					." b.id as agent_id, CONCAT(b.fname, ' ', b.lname) as agent_name, b.phone as agent_phone, b.fax as agent_fax, b.street as agent_street, b.street2 as agent_street2, b.city as agent_city, b.state as agent_state, b.postcode as agent_zip, b.email as agent_email, b.icon as agent_photo,"
					." c.name as company_name, c.id as company_id, c.phone as company_phone, c.email as company_email, c.website as company_website, c.icon as company_icon, c.street as company_address, c.city as company_city, c.state as company_state, c.postcode as company_zip,"
					." e.title as country"
					." FROM #__iproperty a";

		$query .= " JOIN #__iproperty_agentmid z ON a.id = z.prop_id";

		if($this->agent) $query .=	" AND z.agent_id = " . $this->agent;

		$query .= " JOIN #__iproperty_agents b ON b.id = z.agent_id"
					." JOIN #__iproperty_companies c ON c.id = a.listing_office";

		if($this->agent) $query .=	" AND c.id = " . $this->company;

		$query .= " JOIN #__iproperty_countries e ON a.country = e.id"
					." WHERE a.state = 1"
		      		." AND (a.publish_up = '0000-00-00' OR a.publish_up <= NOW())"
			  		." AND (a.publish_down = '0000-00-00' OR a.publish_down >= NOW())"
			  		." AND b.state = 1"
			  		." AND c.state = 1";

		if($this->company) $query .= " AND a.listing_office = " . $this->company;

		$query .= " GROUP BY a.id";

		if ($this->limit) $query .= " LIMIT " . $this->limit;

		$db->setQuery($query);
		$list = $db->loadAssocList();

		return $list;

	}

	public function getImages($id, $limit=0)
    {
		$app        = JFactory::getApplication();
        $option     = JRequest::getCmd('option');
        
		$db = JFactory::getDBO();
		$query = "SELECT path, fname, type, title, ordering, remote, description FROM #__iproperty_images"
				." WHERE propid = " . (int) $id
				." ORDER BY ordering ASC";
                if($limit) $query .= " LIMIT " . $limit;
				
		$db->setQuery($query);

		return $db->loadAssocList();
			
	}	

	public function getFeatures($id)
    {
		$app        = JFactory::getApplication();
        $option     = JRequest::getCmd('option');
        
		$db = JFactory::getDBO();
		$query = "SELECT a.title FROM #__iproperty_amenities a"
				." JOIN #__iproperty_propmid b ON a.id = b.amen_id"
				." WHERE b.prop_id = " . (int) $id;
				
		$db->setQuery($query);

		return $db->loadAssocList();
			
	}
	
	public function getType($id)
    {
		$app        = JFactory::getApplication();
        $option     = JRequest::getCmd('option');
        
		$db = JFactory::getDBO();
		$query = "SELECT a.title FROM #__iproperty_categories a"
				." JOIN #__iproperty_propmid b ON a.id = b.cat_id"
				." WHERE b.prop_id = " . (int) $id;
				
		$db->setQuery($query);

		return $db->loadResult();
			
	}
	
	public function getState($id)
    {
		$app        = JFactory::getApplication();
        $option     = JRequest::getCmd('option');
        
		$db = JFactory::getDBO();
		$query = "SELECT a.mc_name FROM #__iproperty_states a"
				." JOIN #__iproperty b ON a.id = b.locstate"
				." WHERE b.id = " . (int) $id;
				
		$db->setQuery($query);

		return $db->loadResult();
	}

	public function getCountryCode($id)
    {
		$app        = JFactory::getApplication();
        $option     = JRequest::getCmd('option');
        
		$db = JFactory::getDBO();
		$query = "SELECT a.mc_name FROM #__iproperty_countries a"
				." WHERE a.id = " . (int) $id;

		$db->setQuery($query);

		return $db->loadResult();
	}
	
	// do the google gbase address lookup
	public function getGbaseAddress($address)
    {
		$url = "http://maps.google.com/maps/api/geocode/json?address=" . urlencode($address) . "&sensor=false";
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		
		$data = curl_exec($ch);
		curl_close($ch);
		
		$data 		= json_decode($data);

		if($data->status != 'OK') return false;
		$g_address 	= $data->results[0]->formatted_address; 

		return $g_address;		
   
	}

	// grab all companies
	public function getCompanies()
    {
		$app        = JFactory::getApplication();
        $option     = JRequest::getCmd('option');
        
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__iproperty_companies a"
			." WHERE 1";

		$db->setQuery($query);

		return $db->loadAssocList();

	}
}

?>
