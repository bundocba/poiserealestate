<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Iproperty
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');
JFactory::getLanguage()->load('com_iproperty', JPATH_ADMINISTRATOR);
JHtml::_('bootstrap.framework');

$document   = JFactory::getDocument();
$document->addScript(JURI::root(true).'/modules/mod_ip_quicksearch/js/jcombo_qs.js');
$parent_field   = '';

// Build cascading drop downs depending on settings and menu params
// hierarchy ==> country, state, province, county, region, city
$jcomboscript   = "
var globalParentQs = null;
jQuery(document).ready(function($){
    var url = '".JURI::root(true)."/index.php?option=com_iproperty&format=raw&task=ajax.getLocOptions&".JSession::getFormToken()."=1';";
    if($params->get('show_country', 0)){
        $jcomboscript .= "
        $('#ip-qsmod-country').jCombo(url+'&loctype=country', {
            first_optval : '',
            initial_text: '".addslashes(JText::_('COM_IPROPERTY_COUNTRY'))."'
        });";
        $parent_field   = '#ip-qsmod-country';
    }
    if($params->get('show_state', 1)){
        $jcomboscript .= "
        $('#ip-qsmod-locstate').jCombo(url+'&loctype=locstate&id=', { 
            first_optval : '',
            initial_text: '".addslashes(JText::_('COM_IPROPERTY_STATE'))."',";
            if($parent_field) $jcomboscript .= "\n\t\t\tparentField: (globalParentQs != null) ? globalParentQs : '".$parent_field."'";
            $jcomboscript .= "
        });";
        $parent_field   = '#ip-qsmod-locstate';
    }
    if($params->get('show_province', 0)){
        $jcomboscript .= "
        $('#ip-qsmod-province').jCombo(url+'&loctype=province&id=', { 
            first_optval : '',
            initial_text: '".addslashes(JText::_('COM_IPROPERTY_PROVINCE'))."',";
            if($parent_field) $jcomboscript .= "\n\t\t\tparentField: (globalParentQs != null) ? globalParentQs : '".$parent_field."'";
            $jcomboscript .= "
        });";
        $parent_field   = '#ip-qsmod-province';
    }
    if($params->get('show_county', 0)){
        $jcomboscript .= "
        $('#ip-qsmod-county').jCombo(url+'&loctype=county&id=', { 
            first_optval : '',
            initial_text: '".addslashes(JText::_('COM_IPROPERTY_COUNTY'))."',";
            if($parent_field) $jcomboscript .= "\n\t\t\tparentField: (globalParentQs != null) ? globalParentQs : '".$parent_field."'";
            $jcomboscript .= "
        });";
        $parent_field   = '#ip-qsmod-county';
    }
    if($params->get('show_region', 0)){
        $jcomboscript .= "
        $('#ip-qsmod-region').jCombo(url+'&loctype=region&id=', { 
            first_optval : '',
            initial_text: '".addslashes(JText::_('COM_IPROPERTY_REGION'))."',";
            if($parent_field) $jcomboscript .= "\n\t\t\tparentField: (globalParentQs != null) ? globalParentQs : '".$parent_field."'";
            $jcomboscript .= "
        });";
        $parent_field   = '#ip-qsmod-region';
    }
    if($params->get('show_city', 1)){
        $jcomboscript .= "
        $('#ip-qsmod-city').jCombo(url+'&loctype=city&id=', {
            first_optval : '',
            initial_text: '".addslashes(JText::_('COM_IPROPERTY_CITY'))."',";
            if($parent_field) $jcomboscript .= "\n\t\t\tparentField: (globalParentQs != null) ? globalParentQs : '".$parent_field."'";
            $jcomboscript .= "  
        });";            
    }
$jcomboscript .= "
})";
        
$document->addScriptDeclaration($jcomboscript);

// require the IP fields
require_once JPATH_ADMINISTRATOR .'/components/com_iproperty/models/fields/ipcategory.php';
require_once JPATH_ADMINISTRATOR .'/components/com_iproperty/models/fields/stypes.php';
require_once JPATH_ADMINISTRATOR .'/components/com_iproperty/models/fields/beds.php';
require_once JPATH_ADMINISTRATOR .'/components/com_iproperty/models/fields/baths.php';
require_once JPATH_ADMINISTRATOR .'/components/com_iproperty/models/fields/sortby.php';
require_once JPATH_ADMINISTRATOR .'/components/com_iproperty/models/fields/orderby.php';

// build filter lists from fields
$tmpstypes      = new JFormFieldStypes();
$tmpcats        = new JFormFieldIpCategory();
$tmpbeds        = new JFormFieldBeds();
$tmpbaths       = new JFormFieldBaths();
$tmpsort        = new JFormFieldSortBy();
$tmporder       = new JFormFieldOrderBy();

$munits         = ($params->get('sqft_units')) ? JText::_('COM_IPROPERTY_SQFT2' ) : JText::_('COM_IPROPERTY_SQM2');
?>

<div class="ip_qsmod_holder">
    <form action="<?php echo JRoute::_(ipropertyHelperRoute::getAllPropertiesRoute().'&ipquicksearch=1'); ?>" method="<?php echo $params->get('form_method', 'post'); ?>" name="ip_searchmod" class="ip_quicksearch_form">
        <div class="control-group">
            <?php if($params->get('show_keyword', 1)): ?>
                <div class="controls"><input type="text" class="input-medium ip-qssearch" placeholder="<?php echo JText::_('COM_IPROPERTY_KEYWORD'); ?>" name="filter_keyword" /></div>
            <?php endif; ?>
            <?php if ($params->get('show_cat', 1)): ?>
                <div class="controls">
                    <select name="filter_cat" class="input-medium">
                        <option value=""><?php echo JText::_('COM_IPROPERTY_CATEGORY'); ?></option>
                        <?php echo JHTML::_('select.options', $tmpcats->getOptions(), 'value', 'text', $params->get('qs_ptype', '')); ?>
                    </select>
                </div>
            <?php endif; ?>  
            <?php if($params->get('show_stype', 1)): ?>
                <div class="controls">
                    <select name="filter_stype" class="input-medium">
                        <option value=""><?php echo JText::_('COM_IPROPERTY_STYPE'); ?></option>
                        <?php echo JHTML::_('select.options', $tmpstypes->getOptions(true), 'value', 'text'); ?>
                    </select>
                </div>
            <?php endif; ?>
        </div>
        <div class="control-group" id="ip-location-filters">
            <?php if($params->get('show_country', 0)): ?>
                <div class="controls">
                    <select name="filter_country" class="input-medium" id="ip-qsmod-country"></select>
                </div>
            <?php endif; ?>
            <?php if($params->get('show_state', 1)): ?>
                <div class="controls">
                    <select name="filter_locstate" class="input-medium" id="ip-qsmod-locstate"></select>
                </div>
            <?php endif; ?>
            <?php if($params->get('show_province', 0)): ?>
                <div class="controls">
                    <select name="filter_province" class="input-medium" id="ip-qsmod-province"></select>
                </div>
            <?php endif; ?>
            <?php if($params->get('show_county', 0)): ?>
                <div class="controls">
                    <select name="filter_county" class="input-medium" id="ip-qsmod-county"></select>
                </div>
            <?php endif; ?>
            <?php if($params->get('show_region', 0)): ?>  
                <div class="controls">
                    <select name="filter_region" class="input-medium" id="ip-qsmod-region"></select>
                </div>
            <?php endif; ?>
            <?php if($params->get('show_city', 1)): ?>
                <div class="controls">
                    <select name="filter_city" class="input-medium" id="ip-qsmod-city"></select>
                </div>
            <?php endif; ?>
        </div>
        <div class="control-group">
            <?php if($params->get('show_beds', 1)): ?>
                <div class="controls">
                    <select name="filter_beds" class="input-small">
                        <option value=""><?php echo JText::_('COM_IPROPERTY_BEDS'); ?></option>
                        <?php echo JHTML::_('select.options', $tmpbeds->getOptions(), 'value', 'text'); ?>
                    </select>
                </div>
            <?php endif; ?>
            <?php if($params->get('show_baths', 1)): ?>
                <div class="controls">
                    <select name="filter_baths" class="input-small">
                        <option value=""><?php echo JText::_('COM_IPROPERTY_BATHS'); ?></option>
                        <?php echo JHTML::_('select.options', $tmpbaths->getOptions(false), 'value', 'text'); ?>
                    </select>
                </div>
            <?php endif; ?>
            <?php if($params->get('show_price', 1) && $params->get('price_dropdowns')): ?>
                <div class="controls">
                    <?php echo ipropertyHTML::priceSelectList('filter_price_low', 'class="input-medium"', '', '', false, $params->get('price_interval', false)); ?>
        </div>
                <div class="controls">
                    <?php echo ipropertyHTML::priceSelectList('filter_price_high', 'class="input-medium"', '', '', true, $params->get('price_interval', false)); ?>
                </div>
            <?php endif; ?>
            <?php if($params->get('show_price', 1) && !$params->get('price_dropdowns')): ?>
                <div class="controls">
                    <input type="text" class="input-small ip-qssearch" placeholder="<?php echo JText::_('COM_IPROPERTY_MIN_PRICE'); ?>" name="filter_price_low" />
                </div>
                <div class="controls">
                    <input type="text" class="input-small ip-qssearch" placeholder="<?php echo JText::_('COM_IPROPERTY_MAX_PRICE'); ?>" name="filter_price_high" />
                </div>
            <?php endif; ?>
            <?php if($params->get('show_sqft', 1)): ?>
                <div class="controls">
                    <input type="text" class="input-small ip-qssearch" placeholder="<?php echo sprintf(JText::_('COM_IPROPERTY_MIN_RANGE'), $munits); ?>" name="filter_sqft_low" />
                </div>
                <div class="controls">
                    <input type="text" class="input-small ip-qssearch" placeholder="<?php echo sprintf(JText::_('COM_IPROPERTY_MAX_RANGE'), $munits); ?>" name="filter_sqft_high" />
                </div>
            <?php endif; ?>
        </div>
        
        <div class="ip-quicksearch-sortholder"> 
            <div class="btn-group">
                <?php if ($params->get('show_advsearch', 1)) : ?>
                    <a class="btn btn-info" href="<?php echo JRoute::_(ipropertyHelperRoute::getAdvsearchRoute()); ?>">
                        <?php echo JText::_('MOD_IP_QUICKSEARCH_ADVANCED_SEARCH'); ?>
                    </a>
                <?php endif; ?>
                <button class="btn btn-primary" name="commit" type="submit"><?php echo JText::_('JSUBMIT'); ?></button>                
            </div>
        </div>
            
        <?php if($params->get('form_method', 'post') == 'get') : ?>
            <input type="hidden" name="option" value="com_iproperty" />
            <input type="hidden" name="view" value="allproperties" />
            <input type="hidden" name="Itemid" value="<?php echo $params->get('form_itemid'); ?>" />
            <input type="hidden" name="ipquicksearch" value="1" />
        <?php endif; ?>
    </form>
</div>