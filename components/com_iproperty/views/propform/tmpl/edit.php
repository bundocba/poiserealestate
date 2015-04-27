<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.modal');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', '.ipform select');

// Create shortcut to parameters.
$params = $this->state->get('params');

// Change header element background to match template per IP settings
$headers_array = array('description', 'geocode', 'general_amen', 'exterior_amen', 'interior_amen');
foreach($headers_array as $h){
    $this->form->setFieldAttribute($h.'_header', 'color', $this->settings->accent);
    $this->form->setFieldAttribute($h.'_header', 'tcolor', $this->settings->secondary_accent);
}

// change measurement units label depending on settings
$this->form->setFieldAttribute('sqft', 'label', (!$this->settings->measurement_units) ? JText::_('COM_IPROPERTY_SQFT') : JText::_('COM_IPROPERTY_SQM'));

$app        = JFactory::getApplication();
$document   = JFactory::getDocument();
$curr_lang  = JFactory::getLanguage();
$languages  = JLanguageHelper::getLanguages('lang_code'); 
$languageCode = $languages[ $curr_lang->getTag() ]->sef;
         
// check if maps are enabled, and use google for geocoding
if($this->settings->map_provider)
{
    // set defaults from item properties or default settings
    $lat        = ($this->item->latitude) ? $this->item->latitude : $this->settings->adv_default_lat;
    $lon        = ($this->item->longitude) ? $this->item->longitude : $this->settings->adv_default_long;
    $start_zoom = ($this->item->latitude) ? '13' : $this->settings->adv_default_zoom;
    $kml        = ($this->item->kml) ? JURI::root(true).'/media/com_iproperty/kml/'.$this->item->kml : false;

    $map_script = "    
      var map_options = {
            startLat: '".$lat."',
            startLon: '".$lon."',
            startZoom: ".(int)$start_zoom.",
            mapDiv: 'ip-map-canvas',
            clickResize: '#proplocation',
            credentials: '".$this->settings->map_credentials."',
            kml: '".$kml."'
       };
       
       jQuery(document).ready(function($){
			$('#clear_geopoint').click(function(){
				$('#jform_latitude').val('');
				$('#jform_longitude').val('');
			});
		});";
    $document->addScriptDeclaration($map_script);
    
    // add map scripts
    switch ($this->settings->map_provider){
        case 1: // GOOGLE
            $document->addScript( "//maps.google.com/maps/api/js?sensor=false" ); 
            $document->addScript( JURI::root(true).'/components/com_iproperty/assets/js/fields/gmapField.js' );
        break;
        case 2: // BING
            $document->addScript( "//ecn.dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=7.0&mkt=".$curr_lang->get('tag') ); 
            $document->addScript( JURI::root(true).'/components/com_iproperty/assets/js/fields/bingmapField.js' );
        break;
    }
}
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'propform.cancel'){
            <?php echo $this->form->getField('description')->save(); ?>
			Joomla.submitform(task);
        }else if(document.formvalidator.isValid(document.id('adminForm'))) {
            <?php if($this->ipauth->getAdmin()): //only confirm company if admin user ?>
                if(document.id('jform_listing_office').selectedIndex == ''){
                    alert('<?php echo $this->escape(JText::_('COM_IPROPERTY_SELECT_COMPANIES')); ?>');
                    return false;
                }
            <?php endif; ?>
            <?php if($this->ipauth->getAdmin() || $this->ipauth->getSuper()): //only confirm agnets if admin or super agent ?>
                if(document.id('jform_agents').selectedIndex == ''){
                    alert('<?php echo $this->escape(JText::_('COM_IPROPERTY_SELECT_AGENT')); ?>');
                    return false;
                }
            <?php endif; ?>
            if(document.id('jform_stype').selectedIndex == ''){
                alert('<?php echo $this->escape(JText::_('COM_IPROPERTY_SELECT_STYPE')); ?>');
                return false;
            }else if(document.id('jform_categories').selectedIndex == ''){
                alert('<?php echo $this->escape(JText::_('COM_IPROPERTY_SELECT_CATEGORY')); ?>');
                return false;
            }
			<?php echo $this->form->getField('description')->save(); ?>
			Joomla.submitform(task);
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
    
	// coordinate validation routine
	window.addEvent('domready', function(){
		// coordinate validation
		document.formvalidator.setHandler('coord', function(value) {
			if (isNaN(value) == false) return true;
			return false;
	    });
	});    
</script>

<div class="edit item-page<?php echo $this->pageclass_sfx; ?>">
    <?php if ($this->params->get('show_page_heading', 1)) : ?>
        <h1>
            <?php echo $this->escape($this->params->get('page_heading')); ?>
        </h1>
    <?php endif; ?>
    <div class="ip-mainheader">
        <h2><?php echo $this->iptitle; ?></h2>
    </div>

    <form action="<?php echo JRoute::_('index.php?option=com_iproperty&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate ipform form-horizontal" enctype="multipart/form-data">
        <div class="btn-toolbar">
			<div class="btn-group">
                <button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('propform.apply')">
                    <i class="icon-edit"></i> <?php echo JText::_('COM_IPROPERTY_APPLY') ?>
                </button>
                <button type="button" class="btn" onclick="Joomla.submitbutton('propform.save')">
                    <i class="icon-ok"></i> <?php echo JText::_('JSAVE') ?>
                </button>
                <?php if ($this->item->id && $this->ipauth->canAddProp()): ?>
                    <button type="button" class="btn" onclick="Joomla.submitbutton('propform.save2copy')">
                        <i class="icon-copy"></i> <?php echo JText::_('COM_IPROPERTY_CLONE') ?>
                    </button>
                <?php endif; ?>
                <button type="button" class="btn" onclick="Joomla.submitbutton('propform.cancel')">
                    <i class="icon-cancel"></i> <?php echo JText::_('JCANCEL') ?>
                </button>
            </div>
        </div>
        <?php 
        echo JHtmlBootstrap::startTabSet('ip-propview', array('active' => 'propgeneral'));
        echo JHtmlBootstrap::addTab('ip-propview', 'propgeneral', JText::_('COM_IPROPERTY_DESCRIPTION')); ?>
            <fieldset>
                <legend><?php echo JText::_('COM_IPROPERTY_DETAILS'); ?></legend>
                <?php if($this->settings->showtitle): ?>
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('title'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('title'); ?>
                        </div>
                    </div>
                <?php endif; ?>       

                <?php if (is_null($this->item->id) || $this->ipauth->getAdmin()):?>
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('alias'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('alias'); ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('alias'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getValue('alias'); ?>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('property_name'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('property_name'); ?>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('available'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('available'); ?>
                    </div>
                </div>                                    
                <?php if($this->ipauth->getAdmin()): //only show company if admin user ?>
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('listing_office'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('listing_office'); ?>
                        </div>
                    </div>
                <?php else: //if not admin, set the listing office as the user agent company id ?>
                    <?php if($this->form->getValue('listing_office')): ?>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('listing_office'); ?>
                            </div>
                            <div class="controls">
                                <?php echo ipropertyHTML::getCompanyName($this->form->getValue('listing_office')); ?>
                            </div>
                        </div>
                        <input type="hidden" name="jform[listing_office]" value="<?php echo $this->form->getValue('listing_office'); ?>" />
                    <?php else: ?>
                        <input type="hidden" name="jform[listing_office]" value="<?php echo $this->ipauth->getUagentCid(); ?>" />
                    <?php endif; ?>
                <?php endif; ?>            
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('stype'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('stype'); ?>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('price'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('price'); ?>
                        &nbsp;<?php echo JText::_('COM_IPROPERTY_PER'); ?>&nbsp;
                        <?php echo $this->form->getInput('stype_freq'); ?>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('price2'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('price2'); ?>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('call_for_price'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('call_for_price'); ?>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('vtour'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('vtour'); ?>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('categories'); ?>                            
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('categories'); ?>                            
                    </div>
                </div>
                <?php if($this->ipauth->getAdmin() || $this->ipauth->getSuper()): //only show agents if admin or super agent user ?>
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('agents'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('agents'); ?>
                        </div>
                    </div>
                <?php else: ?>
                    <input type="hidden" name="jform[agents][]" value="" />
                <?php endif; ?>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('short_description'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('short_description'); ?>
                    </div>
                </div>
                <div class="control-group form-vertical">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('description_header'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('description'); ?>
                    </div>
                </div>           
            </fieldset>
        <?php
        echo JHtmlBootstrap::endTab();
        echo JHtmlBootstrap::addTab('ip-propview', 'proplocation', JText::_('COM_IPROPERTY_LOCATION'));
        ?>
            <fieldset>
                <legend><?php echo JText::_('COM_IPROPERTY_LOCATION'); ?></legend>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('hide_address'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('hide_address'); ?>
                    </div>
                </div> 
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('street_num'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('street_num'); ?>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('street'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('street'); ?>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('street2'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('street2'); ?>
                    </div>
                </div> 
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('apt'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('apt'); ?>
                    </div>
                </div> 
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('city'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('city'); ?>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('county'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('county'); ?>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('region'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('region'); ?>
                    </div>
                </div> 
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('locstate'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('locstate'); ?>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('province'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('province'); ?>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('postcode'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('postcode'); ?>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('country'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('country'); ?>
                    </div>
                </div> 
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('condition'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('condition'); ?>
                    </div>
                </div> 
                
                 <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('tenure'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('tenure'); ?>
                    </div>
                </div> 
                <div class="clearfix"></div>

                <?php if($this->settings->map_provider): ?>
                    <?php echo $this->form->getLabel('geocode_header'); ?>                        
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('show_map'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('show_map'); ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('latitude'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('latitude'); ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('longitude'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('longitude'); ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('kml'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('kml'); ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('kmlfile'); ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            &nbsp;
                        </div>
                        <div class="controls">
                            <button id="clear_geopoint" class="btn btn-warning" type="button"><?php echo JText::_('JSEARCH_FILTER_CLEAR');?></button>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="control-group">
                        <div class="controls">
                            <?php echo $this->form->getInput('map'); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </fieldset>
        <?php
        echo JHtmlBootstrap::endTab();
        echo JHtmlBootstrap::addTab('ip-propview', 'propdetails', JText::_('COM_IPROPERTY_DETAILS'));
        ?>
            <fieldset>
                <legend><?php echo JText::_('COM_IPROPERTY_DETAILS'); ?></legend>
                <div class="row-fluid">
                    <div class="span6 pull-left form-vertical">
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('beds'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('beds'); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('baths'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('baths'); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('total_units'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('total_units'); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('sqft'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('sqft'); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('lotsize'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('lotsize'); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('lot_acres'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('lot_acres'); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('lot_type'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('lot_type'); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('heat'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('heat'); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('cool'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('cool'); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('fuel'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('fuel'); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('garage_type'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('garage_type'); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('garage_size'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('garage_size'); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('siding'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('siding'); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('roof'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('roof'); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('reception'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('reception'); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('tax'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('tax'); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('income'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('income'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="span6 pull-right form-vertical">
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('yearbuilt'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('yearbuilt'); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('zoning'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('zoning'); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('propview'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('propview'); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('school_district'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('school_district'); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('style'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('style'); ?>
                            </div>
                        </div>
                        <?php if($this->settings->adv_show_wf): ?>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('frontage'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('frontage'); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if($this->settings->adv_show_reo): ?>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('reo'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('reo'); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if($this->settings->adv_show_hoa): ?>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('hoa'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('hoa'); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </fieldset>
        <?php
        echo JHtmlBootstrap::endTab();
        echo JHtmlBootstrap::addTab('ip-propview', 'propamens', JText::_('COM_IPROPERTY_AMENITIES'));
        ?>
            <div class="row-fluid">
                <div class="span4 pull-left">
                    <?php echo $this->form->getLabel('general_amen_header'); ?>
                    <?php echo $this->form->getInput('general_amens'); ?>
                </div>
                <div class="span4 pull-left">
                    <?php echo $this->form->getLabel('interior_amen_header'); ?>
                    <?php echo $this->form->getInput('interior_amens'); ?>
                </div>
                <div class="span4 pull-left">
                    <?php echo $this->form->getLabel('exterior_amen_header'); ?>
                    <?php echo $this->form->getInput('exterior_amens'); ?>
                </div>
            </div>
			<div class="row-fluid">
                    <div class="span4 pull-left">
                        <?php echo $this->form->getLabel('accessibility_amen_header'); ?>
                        <?php echo $this->form->getInput('accessibility_amens'); ?>
                    </div>
                    <div class="span4 pull-left">
                        <?php echo $this->form->getLabel('green_amen_header'); ?>
                        <?php echo $this->form->getInput('green_amens'); ?>
                    </div>
                    <div class="span4 pull-left">
                        <?php echo $this->form->getLabel('security_amen_header'); ?>
                        <?php echo $this->form->getInput('security_amens'); ?>
                    </div>
                </div>
				<div class="row-fluid">
                    <div class="span4 pull-left">
                        <?php echo $this->form->getLabel('landscape_amen_header'); ?>
                        <?php echo $this->form->getInput('landscape_amens'); ?>
                    </div>
                    <div class="span4 pull-left">
                        <?php echo $this->form->getLabel('community_amen_header'); ?>
                        <?php echo $this->form->getInput('community_amens'); ?>
                    </div>
                    <div class="span4 pull-left">
                        <?php echo $this->form->getLabel('appliance_amen_header'); ?>
                        <?php echo $this->form->getInput('appliance_amens'); ?>
                    </div>
                </div>
        <?php
        echo JHtmlBootstrap::endTab();
        echo JHtmlBootstrap::addTab('ip-propview', 'propimages', JText::_('COM_IPROPERTY_IMAGES').' / '.JText::_('COM_IPROPERTY_VIDEO'));
        ?>
            <div class="row-fluid">
                <ul class="nav nav-tabs ip-vid-tab">
                    <li class="active"><a href="#imgtab" data-toggle="tab"><?php echo JText::_('COM_IPROPERTY_IMAGES');?></a></li>
                    <li><a href="#vidtab" data-toggle="tab"><?php echo JText::_('COM_IPROPERTY_VIDEO');?></a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="imgtab">
                        <div class="clearfix"></div>                        
                        <?php if($this->item->id): ?>
                            <?php echo $this->form->getInput('gallery'); ?>
                        <?php else: ?>
                            <div class="alert alert-info"><?php echo JText::_('COM_IPROPERTY_SAVE_BEFORE_IMAGES'); ?></div>
                        <?php endif; ?> 
                    </div>
                    <div class="tab-pane" id="vidtab">
                        <div class="control-group form-vertical">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('video'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('video'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        echo JHtmlBootstrap::endTab();
        echo JHtmlBootstrap::addTab('ip-propview', 'propother', JText::_('COM_IPROPERTY_OTHER'));
        ?>
            <fieldset class="adminform">
                <legend><?php echo JText::_('COM_IPROPERTY_NOTES'); ?></legend>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('agent_notes'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('agent_notes'); ?>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('terms'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('terms'); ?>
                    </div>
                </div>
            </fieldset>
            <fieldset class="adminform">
                <legend><?php echo JText::_('COM_IPROPERTY_PUBLISHING'); ?></legend>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('access'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('access'); ?>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('publish_up'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('publish_up'); ?>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('publish_down'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('publish_down'); ?>
                    </div>
                </div>
            </fieldset>
            <fieldset class="adminform">
                <legend><?php echo JText::_('COM_IPROPERTY_META_INFO'); ?></legend>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('metakey'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('metakey'); ?>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('metadesc'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('metadesc'); ?>
                    </div>
                </div>
            </fieldset>
        <?php
        echo JHtmlBootstrap::endTab();
        $this->dispatcher->trigger('onAfterRenderPropertyEdit', array($this->item, $this->settings ));
        echo JHtmlBootstrap::endTabSet();
        ?>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="return" value="<?php echo $this->return_page; ?>" />
        <?php echo JHtml::_( 'form.token'); ?>
    </form>
</div>

<?php if($this->item->id): 
    
    jimport('joomla.filesystem.file');
    
    // plupload scripts
	$document->addStyleSheet( "//ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" );
    $document->addStyleSheet( JURI::root(true)."/components/com_iproperty/assets/js/plupload/js/jquery.plupload.queue/css/jquery.plupload.queue.css" );
	$document->addScript( "//ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js" );
    $document->addScript( JURI::root(true)."/components/com_iproperty/assets/js/plupload/js/plupload.full.min.js" );
    $document->addScript( JURI::root(true)."/components/com_iproperty/assets/js/plupload/js/jquery.plupload.queue/jquery.plupload.queue.js" );
    // include language file for uploader if it exists
    if(JFile::exists(JPATH_SITE.'/components/com_iproperty/assets/js/plupload/js/i18n/'.$languageCode.'.js')){
        $document->addScript( JURI::root(true)."/components/com_iproperty/assets/js/plupload/js/i18n/".$languageCode.".js" );
    }

    // sortable tables
    $document->addScript( JURI::root(true).'/components/com_iproperty/assets/js/ipsortables.js');
    $document->addScript( JURI::root(true).'/components/com_iproperty/assets/js/ipsortables_docs.js');
	
	// ****************************
	// not sure if this should stay -- hosted version of the FULL bootstrap so icons etc. work
	//$document->addScript( JURI::root(true).'/components/com_iproperty/assets/js/image.resize.js');
	$document->addScript( JURI::root(true).'/components/com_iproperty/assets/js/bootbox.min.js');
    
    ?>
    <script type="text/javascript">                                   
        var ipbaseurl		= '<?php echo rtrim(JURI::root(), '/'); ?>';
        var pluploadpath	= '<?php echo JURI::root().'/components/com_iproperty/assets/js'; ?>';
        var ipmaximagesize  = '<?php echo $this->settings->maximgsize; ?>';
        var ipfilemaxupload = 0;
		var ipGalleryOptions = false;

        (function($) {  
			ipGalleryOptions = {
                propid: <?php echo (int)$this->item->id; ?>,
                iptoken: '<?php echo JSession::getFormToken(); ?>',
                ipbaseurl: '<?php echo JURI::root(); ?>',
				ipthumbwidth: '<?php echo $this->settings->thumbwidth; ?>',
                //ipthumbwidth: 150,
                iplimitstart: 0,
                iplimit: 50,
                debug: false,
                language: {
                    save: '<?php echo addslashes(JText::_('COM_IPROPERTY_SAVE')); ?>',
                    del: '<?php echo addslashes(JText::_('COM_IPROPERTY_DELETE')); ?>',
					edit: '<?php echo addslashes(JText::_('COM_IPROPERTY_EDIT')); ?>',
					add: '<?php echo addslashes(JText::_('COM_IPROPERTY_ADD')); ?>',
					confirm: '<?php echo addslashes(JText::_('COM_IPROPERTY_CONFIRM')); ?>',
					ok: '<?php echo addslashes(JText::_('JYES')); ?>',
					cancel: '<?php echo addslashes(JText::_('JCANCEL')); ?>',
                    iptitletext: '<?php echo addslashes(JText::_('COM_IPROPERTY_TITLE')); ?>',
                    ipdesctext: '<?php echo addslashes(JText::_('COM_IPROPERTY_DESCRIPTION')); ?>',
                    noresults: '<?php echo addslashes(JText::_('COM_IPROPERTY_NO_RESULTS')); ?>',
                    updated: '<?php echo addslashes(JText::_('COM_IPROPERTY_UPDATED')); ?>',  
                    notupdated: '<?php echo addslashes(JText::_('COM_IPROPERTY_NOT_UPDATED')); ?>',
                    previous: '<?php echo addslashes(JText::_('COM_IPROPERTY_PREVIOUS')); ?>',
                    next: '<?php echo addslashes(JText::_('COM_IPROPERTY_NEXT')); ?>',
                    of: '<?php echo addslashes(JText::_('COM_IPROPERTY_OF')); ?>',
                    fname: '<?php echo addslashes(JText::_('COM_IPROPERTY_FNAME')); ?>',
                    overlimit: '<?php echo addslashes(JText::_('COM_IPROPERTY_OVERIMGLIMIT')); ?>',
					warning: '<?php echo addslashes(JText::_('COM_IPROPERTY_WARNING')); ?>',
					uploadcomplete: '<?php echo addslashes(JText::_('COM_IPROPERTY_UPLOAD_COMPLETE')); ?>'
                },
                client: '<?php echo $app->getName(); ?>',
                allowedFileTypes: [{title : "Files", extensions : "jpg,gif,png,pdf,doc,txt,jpeg"}]
            };
            
            // create auto complete             
            $.each(['city','province','region','county'], function(index, value){
                var url = '<?php echo JURI::base('true'); ?>/index.php?option=com_iproperty&task=ajax.ajaxAutocomplete&format=raw&field='+value+'&<?php echo JSession::getFormToken(); ?>=1';
                $.getJSON(url).done(function( data ){
                    $('#jform_'+value).typeahead({source: data, items:5});
                });  
            });
        })(jQuery);
    </script>
<?php endif; ?>