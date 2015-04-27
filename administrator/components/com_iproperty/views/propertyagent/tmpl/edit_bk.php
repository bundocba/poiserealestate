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

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');
JHtml::_('behavior.formvalidation');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');

$app        = JFactory::getApplication();
$document   = JFactory::getDocument();
$curr_lang  = JFactory::getLanguage();

// change measurement units label depending on settings
$this->form->setFieldAttribute('sqft', 'label', (!$this->settings->measurement_units) ? JText::_('COM_IPROPERTY_SQFT') : JText::_('COM_IPROPERTY_SQM'));

// check if maps are enabled, use google to geocode
if($this->settings->map_provider)
{
    // set defaults from item properties or default settings
    $lat        = ($this->item->latitude) ? $this->item->latitude : $this->settings->adv_default_lat;
    $lon        = ($this->item->longitude) ? $this->item->longitude : $this->settings->adv_default_long;
    $start_zoom = ($this->item->latitude) ? '13' : $this->settings->adv_default_zoom;

    $map_script = "
		var map_options = {
			startLat: '".$lat."',
			startLon: '".$lon."',
			startZoom: ".(int)$start_zoom.",
			mapDiv: 'ip-map-canvas',
			clickResize: '#proplocation',
			credentials: '".$this->settings->map_credentials."'
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
            $mapurl = "//maps.google.com/maps/api/js?sensor=false";
            $mapurl .= $this->settings->map_locale ? '&language='.$this->settings->map_locale : '';
            $document->addScript( $mapurl );
            $document->addScript( JURI::root(true).'/components/com_iproperty/assets/js/fields/gmapField.js' );
        break;
        case 2: // BING
            $document->addScript( "//ecn.dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=7.0&mkt=".$curr_lang->get('tag') );
            $document->addScript( JURI::root(true).'/components/com_iproperty/assets/js/fields/bingmapField.js' );
        break;
    }
}
?>

<script language="javascript" type="text/javascript">
    Joomla.submitbutton = function(task)
	{
		// if save as copy, make alias unique
		if (task == 'property.save2copy'){
			var alias = document.id('jform_alias').value;
			document.id('jform_alias').value = alias +'_'+String.uniqueID();
            document.id('jform_state').value = 0;
		}

        if (task == 'property.cancel'){
            <?php echo $this->form->getField('description')->save(); ?>
			Joomla.submitform(task, document.getElementById('adminForm'));
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
			Joomla.submitform(task, document.id('adminForm'));
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
<style type="text/css">.ip-state-highlight{background: #D9EDF7; border: 3px dashed #74b4d4;}</style>


<form action="<?php echo JRoute::_('index.php?option=com_iproperty&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">
    <div class="row-fluid">
        <div class="span9 form-horizontal">
            <?php echo JHtmlBootstrap::startTabSet('ip-propview', array('active' => 'propgeneral'));
            echo JHtmlBootstrap::addTab('ip-propview', 'propgeneral', JText::_('COM_IPROPERTY_DESCRIPTION')); ?>
                <div class="row-fluid form-horizontal">
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
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('alias'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('alias'); ?>
                        </div>
                    </div>
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
                    </div>					<div class="control-group">                        <div class="control-label">                            <?php echo $this->form->getLabel('language'); ?>                        </div>                        <div class="controls">                            <?php echo $this->form->getInput('language'); ?>                        </div>                    </div>
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
                            <input type="hidden" name="jform[listing_office]" value="<?php echo $this->ipauth->getUagentCid(); ?>" />
                        <?php endif; ?>
                    <?php endif;?>
					<div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('property_type'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('property_type'); ?>
                        </div>
                    </div>
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
                            <?php if($this->ipauth->getAdmin() || $this->ipauth->getSuper()): //only show agents if admin or super agent user ?>
                                <?php echo $this->form->getLabel('agents'); ?>
                            <?php endif; ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('categories'); ?>
                            <?php if($this->ipauth->getAdmin() || $this->ipauth->getSuper()): //only show agents if admin or super agent user ?>
                                <?php echo $this->form->getInput('agents'); ?>
                            <?php else: ?>
                                <input type="hidden" name="jform[agents][]" value="" />
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('short_description'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('short_description'); ?>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <?php echo $this->form->getLabel('description_header'); ?>
                <div class="clearfix"></div>
            <?php
                echo $this->form->getInput('description');
                echo JHtmlBootstrap::endTab();
                echo JHtmlBootstrap::addTab('ip-propview', 'proplocation', JText::_('COM_IPROPERTY_LOCATION'));
            ?>
                <div class="row-fluid form-horizontal">
                    <h4><?php echo JText::_('COM_IPROPERTY_LOCATION'); ?></h4>
                    <hr />
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
                    <div class="clearfix"></div>

                    <?php if($this->settings->map_provider): ?>
                        <div class="clearfix"></div>
                        <?php echo $this->form->getLabel('geocode_header'); ?>
                        <div class="clearfix"></div>
                        <div class="span2 pull-left form-vertical">
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
                                    <button id="clear_geopoint" class="btn btn-warning" type="button"><?php echo JText::_('JSEARCH_FILTER_CLEAR');?></button>
                                </div>
                            </div>
                        </div>
                        <div class="span7 pull-right">
                             <?php echo $this->form->getInput('map'); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php
                echo JHtmlBootstrap::endTab();
                echo JHtmlBootstrap::addTab('ip-propview', 'propdetails', JText::_('COM_IPROPERTY_DETAILS'));
            ?>
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
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('frontage'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('frontage'); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('reo'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('reo'); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('hoa'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('hoa'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
                echo JHtmlBootstrap::endTab();
                echo JHtmlBootstrap::addTab('ip-propview', 'propamens', JText::_('COM_IPROPERTY_AMENITIES'));
            ?>
                <div class="row-fluid form-horizontal">
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
				<div class="row-fluid form-horizontal">
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
				<div class="row-fluid form-horizontal">
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
                <div class="row-fluid form-horizontal">
                    <ul class="nav nav-tabs">
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
                echo JHtmlBootstrap::addTab('ip-propview', 'propnotes', JText::_('COM_IPROPERTY_NOTES'));
            ?>
                <div class="row-fluid form-horizontal">
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
                </div>
            <?php
                echo JHtmlBootstrap::endTab();
                $this->dispatcher->trigger('onAfterRenderPropertyEdit', array($this->item, $this->settings ));
                echo JHtmlBootstrap::endTabSet();
            ?>
        </div>
        <div class="span3 form-vertical">
            <div class="alert alert-info">
                <h4><?php echo JText::_('COM_IPROPERTY_PUBLISHING'); ?></h4>
                <hr />
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
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('state'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('state'); ?>
                    </div>
                </div>
            </div>
            <?php if ($this->ipauth->getSuper() || $this->ipauth->getAdmin()): ?>
                <div class="alert alert-success">
                    <h4><?php echo JText::_('COM_IPROPERTY_DETAILS');?></h4>
                    <hr />
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('hits'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('hits'); ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('created_by'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('created_by'); ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('created'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('created'); ?>
                        </div>
                    </div>
                    <?php if( $this->item->modified && $this->item->modified != '0000-00-00 00:00:00' ): ?>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('modified'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('modified'); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('modified_by'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('modified_by'); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('featured'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('featured'); ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('approved'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('approved'); ?>
                        </div>
                    </div>
                </div>
                <div class="alert alert-notice">
                    <h4><?php echo JText::_('COM_IPROPERTY_META_INFO'); ?></h4>
                    <hr />
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
                    <!--
                    <div class="control-group">
                        <div class="control-label">
                            <?php //echo $this->form->getLabel('tags'); ?>
                        </div>
                        <div class="controls">
                            <?php //echo $this->form->getInput('tags'); ?>
                        </div>
                    </div>-->
                </div>
            <?php endif; ?>
        </div>
    </div>
    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
</form>
<div class="clearfix"></div>
<?php echo ipropertyAdmin::footer( ); ?>

<?php if($this->item->id):
    
    jimport('joomla.filesystem.file');

    // plupload scripts
	$document->addStyleSheet( "//ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" );
    $document->addStyleSheet( JURI::root(true)."/components/com_iproperty/assets/js/plupload/js/jquery.plupload.queue/css/jquery.plupload.queue.css" );
	$document->addScript( "//ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js" );
    $document->addScript( JURI::root(true)."/components/com_iproperty/assets/js/plupload/js/plupload.full.min.js" );
    $document->addScript( JURI::root(true)."/components/com_iproperty/assets/js/plupload/js/jquery.plupload.queue/jquery.plupload.queue.js" );
    // include language file for uploader if it exists
    if(JFile::exists(JPATH_SITE.'/components/com_iproperty/assets/js/plupload/js/i18n/'.$curr_lang->get('tag').'.js')){
        $document->addScript( JURI::root(true)."/components/com_iproperty/assets/js/plupload/js/i18n/".$curr_lang->get('tag').".js" );
    }

    // sortable tables
    $document->addScript( JURI::root(true).'/components/com_iproperty/assets/js/ipsortables.js');
    $document->addScript( JURI::root(true).'/components/com_iproperty/assets/js/ipsortables_docs.js');
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
