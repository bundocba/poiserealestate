<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');

$mapsurl = 'https://maps.googleapis.com/maps/api/js?sensor=false';
// set locale
$mapsurl .= $this->settings->map_locale ? '&language='.$this->settings->map_locale : '';
if ($this->params->get('adv_show_shapetools', $this->settings->adv_show_shapetools)) $mapsurl .= '&libraries=drawing';

// include map scripts
if ($this->params->get('adv_show_clusterer', $this->settings->adv_show_clusterer)) $this->document->addScript(JURI::root(true).'/components/com_iproperty/assets/advsearch/markerclusterer_packed.js');
$this->document->addScript($mapsurl);
$this->document->addScript(JURI::root(true).'/components/com_iproperty/assets/advsearch/gmap.js');
if ($this->params->get('adv_show_shapetools', $this->settings->adv_show_shapetools)) $this->document->addScript(JURI::root(true).'/components/com_iproperty/assets/advsearch/gmaptools.js');

echo '<div id="ip-map-canvas" class="ip-map-div"></div>';
?>
