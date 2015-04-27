<?php
/**
* @version 1.4.0
* @package RSform!Pro 1.4.0
* @copyright (C) 2007-2013 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$out	='<div class="rsform-table" id="rsform-table2">'."\n";
foreach ($quickfields as $quickfield) {	
	$out .= "\t".'<div class="rsform-table-item">'."\n";
	$out .= "\t\t".'<div class="rsform-field-title">{'.$quickfield.':caption}</div>'."\n";
	$out .= "\t\t".'<div class="rsform-field-value">{'.$quickfield.':value}</div>'."\n";
	$out .= "\t".'</div>'."\n";
}
$out.='</div>'."\n";

if ($out != $this->_directory->ViewLayout && $this->_directory->formId) {
	// Clean it
	// Update the layout
	$db = JFactory::getDBO();
	$db->setQuery("UPDATE #__rsform_directory SET ViewLayout='".$db->escape($out)."' WHERE formId=".$this->_directory->formId);
	$db->execute();
}
	
return $out;
?>