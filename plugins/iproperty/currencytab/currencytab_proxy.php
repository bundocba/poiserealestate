<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

header("Content-Type: application/json; charset=utf-8");

$xml = simplexml_load_file('http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml');
$currencies = array();

foreach($xml->Cube->Cube->Cube as $a){
	foreach($a->attributes() as $b => $c){
		if ($b == 'currency'){
			$currency = (string) $c;
		} else if ($b == 'rate'){
			$rate = (float) $c;
		}
	}
	$currencies[$currency] = $rate;
}

echo json_encode($currencies);

