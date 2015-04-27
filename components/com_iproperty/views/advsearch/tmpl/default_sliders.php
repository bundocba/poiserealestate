<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
jimport('joomla.application.web.client');
$client = new JApplicationWebClient();

$theme = $this->params->get('adv_map_slider_theme', 'ui-lightness');

// load jquery UI for sliders
$this->document->addScript(JURI::root(true).'/components/com_iproperty/assets/advsearch/jquery-slider.min.js');
if ($client->mobile) $this->document->addScript(JURI::root(true).'/components/com_iproperty/assets/js/jquery.ui.touch-punch.min.js');
$this->document->addStyleSheet('//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/themes/'.$theme.'/jquery-ui.css');

// load ip slider script
$this->document->addScript(JURI::root(true).'/components/com_iproperty/assets/advsearch/sliders.js');
?>
<div id="mapSliders" class="ip-adv-slidercontainer"></div>

