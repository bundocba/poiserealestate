<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Iproperty
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license see LICENSE.php
 */
 
defined('_JEXEC') or die('Restricted access');

class modIPMratesHelper 
{
	public static function MRatesCall(&$params)
    {
		$query_string = "";
		
		$Zparams = array(
			'zws-id' => $params->get('zillow_id'),
			'output' => 'json'
		);
		
		foreach ($Zparams as $key => $value) {
			$query_string .= "$key=" . urlencode($value) . "&";
		}
		
		$url = "http://www.zillow.com/webservice/GetRateSummary.htm?$query_string";
		
		$output = file_get_contents($url);
		$mrates = json_decode($output,true);
		$mrates = $mrates['response'];
		return $mrates;
	}	
}