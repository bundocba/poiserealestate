<?php
/**
* @version 1.4.0
* @package RSform!Pro 1.4.0
* @copyright (C) 2007-2013 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$out ='<div class="rsform-table" id="rsform-table3">'."\n";

if (!empty($imagefields)) {
	$quickfields = array_diff($quickfields,$imagefields);
}

$titleValues = count($quickfields) > 2 ? array_slice($quickfields,0,3) : $quickfields;
$otherValues = count($quickfields) > count($titleValues) ? array_slice($quickfields,3) : array();

if (!empty($titleValues)) {
	foreach ($titleValues as $i => $title) {
		if ($i == 0) {
			$out .= "\t".'<p class="rsform-main-title rsform-title">{'.$title.':value}</p>'."\n";
		} elseif ($i == 1) {
			$out .= "\t".'<p class="rsform-big-subtitle rsform-title">{'.$title.':value}</p>'."\n";
		} elseif ($i == 2) {
			$out .= "\t".'<p class="rsform-small-subtitle rsform-title">{'.$title.':value}</p>'."\n";
		}
	}
}

if (!empty($imagefields)) {
	$out .= "\t".'<ul class="rsform-gallery">'."\n";
	
	foreach ($imagefields as $image) {
		$out .= "\t\t".'{if {'.$image.':value}}<li><a href="javascript:void(0)" class="modal" rel="{handler: \'clone\'}"><img src="{'.$image.':path}" alt="" /></a></li>{/if}'."\n";
	}
	
	$out .= "\t".'</ul>'."\n";
}

if (!empty($otherValues)) {
	$out .= "\t".'<div class="rsfp-table">'."\n";
	
	foreach ($otherValues as $other) {
		$out .= "\t\t".'<div class="rsform-table-row">'."\n";
		$out .= "\t\t\t".'<div class="rsform-left-col">{'.$other.':caption}</div>'."\n";
		$out .= "\t\t\t".'<div class="rsform-right-col">{'.$other.':value}</div>'."\n";
		$out .= "\t\t".'</div>'."\n";
	}
	
	$out .= "\t".'</div>'."\n";
}

$out .= '</div>';

if ($out != $this->_directory->ViewLayout && $this->_directory->formId) {
	// Clean it
	// Update the layout
	$db = JFactory::getDBO();
	$db->setQuery("UPDATE #__rsform_directory SET ViewLayout='".$db->escape($out)."' WHERE formId=".$this->_directory->formId);
	$db->execute();
}
	
return $out;
?>