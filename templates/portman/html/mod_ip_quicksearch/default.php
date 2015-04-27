
<?php

/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Iproperty
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');
$lang = JFactory::getLanguage();
$lang->load('com_iproperty', JPATH_ADMINISTRATOR);

require_once JPATH_ADMINISTRATOR .'/components/com_iproperty/models/fields/stypes.php';
require_once JPATH_ADMINISTRATOR .'/components/com_iproperty/models/fields/beds.php';

$db = JFactory::getDBO();
$query = "SELECT distinct city as value,city as text FROM #__iproperty WHERE language='".$lang->getTag()."' AND state=1";
$db->setQuery($query);
$arr_city = $db->loadAssocList();
// build filter lists from fields


$tmpstypes      = new JFormFieldStypes();
 $tmpbeds        = new JFormFieldBeds();


$munits         = ($params->get('sqft_units')) ? JText::_('COM_IPROPERTY_SQFT2' ) : JText::_('COM_IPROPERTY_SQM2');
?>

<div id="properties-search">	
	<h2 class="col-sm-offset-1">Property Search</h2>	
	<form action="index.php" name="ip_searchmod" class="ip_quicksearch_form">		
<div class="row clearfix">			
	<div class="col-sm-4 col-sm-offset-1">				
		<div class="row">
			<div class="col-sm-6">	
				<label for="type">Property Type:</label>				
			</div>
			<div class="col-sm-6">
				<input type="text" name="property_type" id="type">			
			</div>
		</div>
	</div>			
<div class="col-sm-4">		
	<div class="row">
		<div class="col-sm-5">	
			<label for="district">District:</label>	
		</div>		
		<div class="col-sm-7">	
			<select name="filter_city" class="custom" id="ip-qsmod-city">
				<option value=""><?php echo JText::_('COM_IPROPERTY_CITY'); ?></option>					
				<?php echo JHTML::_('select.options', $arr_city, 'value', 'text'); ?>				
			</select>	 
		</div>
	</div>
</div>
<div class="col-sm-3">				
	<div class="row">
		<div class="col-sm-5">	
			<label for="numberbeds">Bedrooms:</label>	
		</div>		
		<div class="col-sm-7">	
			<select name="filter_beds" class="custom" id="ip-qsmod-numberbeds">
				<option value=""><?php echo JText::_('COM_IPROPERTY_BEDS'); ?></option>	

				<?php echo JHTML::_('select.options',  $tmpbeds->getOptions() , 'value', 'text'); ?>				
			</select>	 
		</div>
	</div>
</div>				
	
</div>		
<div class="row clearfix">			
	<div class="col-sm-4 col-sm-offset-1">	
	<div class="row">
		<div class="col-sm-6">
			<label for="salerent">For Sale or Rent:</label>	
		</div>
		<div class="col-sm-6">
			<select name="filter_stype" class="custom" id="filter_stype">					
				<option value=""><?php echo JText::_('COM_IPROPERTY_STYPE'); ?></option>					
				<?php echo JHTML::_('select.options', $tmpstypes->getOptions(true), 'value', 'text'); ?>				
			</select>		
		</div>
	</div>
</div>			
<div class="col-sm-4">				
<div class="row">					
<div class="col-sm-5">						
	<label for="price">Price Range:</label>					
</div>					
<div class="col-sm-7">						
<div class="row">							
	<div class="col-sm-6">								
		<input type="text" class="price" placeholder="<?php echo JText::_('COM_IPROPERTY_MIN_PRICE'); ?>" name="filter_price_low" />							
	</div>							
	<div class="col-sm-6">								
		<input type="text" class="price" placeholder="<?php echo JText::_('COM_IPROPERTY_MAX_PRICE'); ?>" name="filter_price_high" />							
	</div>						
</div>					
</div>				
</div>			
</div>			
<div class="col-sm-3">
	<div class="row">
		<div class="col-sm-7">
						
				<!-- <label for="keyword">Keyword:</label>	 -->			
			<input type="text" id="keyword" placeholder="<?php echo JText::_('COM_IPROPERTY_KEYWORD'); ?>" name="filter_keyword" />			
				
		</div>
		<div class="col-sm-5">
			<input type="submit" name="search" id="search" value="<?php echo JText::_('PBSEARCH'); ?>">
		</div>
		
	</div>				
				
</div>		
</div>		
	<input type="hidden" name="option" value="com_iproperty" />            
	<input type="hidden" name="view" value="allproperties" />            
	<input type="hidden" name="layout" value="search" />            
	<input type="hidden" name="Itemid" value="<?php echo $params->get('form_itemid'); ?>" />            
	<input type="hidden" name="ipquicksearch" value="1" />        
	</form>
	</div>

