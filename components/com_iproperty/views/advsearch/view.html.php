<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
jimport('joomla.application.component.view');

class IpropertyViewAdvsearch extends JViewLegacy
{
	protected $ipbaseurl;
    protected $ipauth;

    function display($tpl = null)
    {
        jimport('joomla.filesystem.file');
        JHtml::_('jquery.framework'); // load jquery
        JHtml::_('jquery.ui'); // load jquery ui

        $app            = JFactory::getApplication();
        $itemid         = (JRequest::getInt('recallSearch')) ? JRequest::getInt('recallSearch') : JRequest::getInt('Itemid', 99999);
        $this->params   = $app->getParams();
        $lang 			= JFactory::getLanguage();
        $rtl            = $lang->isRTL() ? 1 : 0;

        JPluginHelper::importPlugin( 'iproperty');
        $dispatcher = JDispatcher::getInstance();

        $this->ipbaseurl    = JURI::root(true);
        $document           = JFactory::getDocument();
        $settings           = ipropertyAdmin::config();
        $user               = JFactory::getUser();
        $this->ipauth       = new ipropertyHelperAuth();

        if(!$settings->map_provider){
            $this->_displayNoAccess();
            return;
        }
		
		// if we have map, then set IPGMAP
        define('IPGMAP', 1);

		// get currency options
		$c_format	= $settings->nformat ? ',' : '.';
		$c_symbol	= $settings->currency ?: '$';
		$c_round	= $settings->currency_digits ?: '-2';
		$c_position = $settings->currency_pos ? '%n %s' : '%s %n'; // TODO: not sure if supported by currency plugin

        // check for template map and property preview icons
        $map_house_icon         = '/components/com_iproperty/assets/images/map/icon56.png';
        $templatepath           = $app->getTemplate();
        if(JFile::exists('templates'.'/'.$templatepath.'/images/iproperty/map/icon56.png')) $map_house_icon = '/templates/'.$templatepath.'/images/iproperty/map/icon56.png';

        // Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

        // load currency format and correct language file if it exists
        $document->addScript($this->ipbaseurl.'/components/com_iproperty/assets/js/currency/jquery.formatCurrency-1.4.0.pack.js');

        // load jQuery cookie plugin
        $document->addScript($this->ipbaseurl.'/components/com_iproperty/assets/js/jquery.cookie.js');
        // set currency format
        $currencyformat = '{ digitGroupSymbol: "'.$c_format.'", symbol: "'.$c_symbol.'", roundToDecimalPlace: '.$c_round.', positiveFormat: "'.$c_position.'" }';

        // create toolbar
        $dispatcher->trigger('onBeforeRenderToolbar', array($settings));

        $this->assignRef('settings'     , $settings);
        $this->assignRef('dispatcher'   , $dispatcher);
        $this->assignRef('user'         , $user);

        $munits     = (!$settings->measurement_units) ? JText::_('COM_IPROPERTY_SQFT2' ) : JText::_('COM_IPROPERTY_SQM2');

        // set layout to boolean, since pre-3.0rc1 options were 'table' and 'overview'
        $this->adv_layout = ($this->params->get('adv_layout') == 0 || $this->params->get('adv_layout') == 'table') ? 0 : 1;

        $mapscript = "
        // set global vars for functions
        var ipSliders = {}
        var tabGroups = {} // populated down in getInputs
        var amenGroups = []; // populated down in getInputs

        var langOptions = {
            tprop:'".addslashes(JText::_('COM_IPROPERTY_RESULTS'))."',
            price:'".addslashes(JText::_('COM_IPROPERTY_PRICE'))."',
            nolimit: '".addslashes(JText::_('COM_IPROPERTY_NO_LIMIT'))."',
            pid: '".addslashes(JText::_('COM_IPROPERTY_PROPERTY_ID'))."',
            street: '".addslashes(JText::_('COM_IPROPERTY_STREET'))."',
            beds: '".addslashes(JText::_('COM_IPROPERTY_BEDS'))."',
            baths: '".addslashes(JText::_('COM_IPROPERTY_BATHS'))."',
            sqft: '".addslashes($munits)."',
            preview: '".addslashes(JText::_('COM_IPROPERTY_PREVIEW'))."',
            more: '".addslashes(JText::_('COM_IPROPERTY_MORE' ))."',
            inputText: '".addslashes(JText::_('COM_IPROPERTY_INPUT_TIP'))."',
            noRecords: '".addslashes(JText::_('COM_IPROPERTY_NO_RECORDS_TEXT'))."',
            previous: '".addslashes(JText::_('COM_IPROPERTY_PREVIOUS'))."',
            next: '".addslashes(JText::_('COM_IPROPERTY_NEXT'))."',
            of: '".addslashes(JText::_('COM_IPROPERTY_OF'))."',
            searchopt: '".addslashes(JText::_('COM_IPROPERTY_SEARCH_OPTIONS'))."',
            savesearch: '".addslashes(JText::_('COM_IPROPERTY_SAVESEARCH'))."',
            clearsearch: '".addslashes(JText::_('COM_IPROPERTY_CLEARSEARCH'))."',
            asc: '".addslashes(JText::_('COM_IPROPERTY_ASC'))."',
            desc: '".addslashes(JText::_('COM_IPROPERTY_DESC'))."',
            isRTL: ".$rtl."
        };

        var searchOptions = {
            limitstart: 0,
            limit: ".$this->params->get('adv_perpage', $settings->adv_perpage).",
            maxpages: 5, // TODO: this should be set by param
            currentpage: 1,
            totallistings: 0,
            totalpages: 0,
            currentpageset: 1,
            totalpagesets: 1,
            sortby: '".$settings->default_p_sort."',
            orderby: '".$settings->default_p_order."'
        };

        var mapOptions = {
			locationFields: [],
            resultColumns: [],
            debug: false,
            currentvals : {
                'Itemid': ".$itemid.",
                'property': {},
				'location': {},
                'categories': [],
                'sliders': {},
				'amenities': []
            },
            ipbaseurl: '".JURI::root()."',
            itemid: ".$itemid.",
            currencyFormat: ".$currencyformat.",
            marker: '".$map_house_icon."',
            token: '',
            zoom: ".$this->params->get('adv_default_zoom', $settings->adv_default_zoom).",
            maxZoom: ".$this->params->get('max_zoom', $settings->max_zoom).",
            lat: '".$this->params->get('adv_default_lat', $settings->adv_default_lat)."',
            lng: '".$this->params->get('adv_default_long', $settings->adv_default_long)."',
            maptype: '".$settings->adv_maptype."',
            credentials: '".$settings->map_credentials."',
            sort: '".$settings->default_p_sort."',
            order: '".$settings->default_p_order."',
            maptools: ".$this->params->get('adv_show_shapetools', $settings->adv_show_shapetools).",
            layout: ".$this->adv_layout.",
            savesearch: ".$settings->show_savesearch.",
			mapcluster: ".$this->params->get('adv_show_clusterer', $settings->adv_show_clusterer)."
        };
        ".$this->_getInputs($settings)."\n".
        "jQuery(function($) {
            $(document).ready(function(){
				// get token
				$.get( mapOptions.ipbaseurl+'index.php?option=com_iproperty&format=raw&task=ajax.getToken', function( data ) {
					mapOptions.token = data;	
					// create initial page elements
					ipMapFunctions.handleCookie();
					ipMapFunctions.buildMap(mapOptions);
					ipMapFunctions.buildSliders(ipSliders);
					ipMapFunctions.createInputGroups(mapOptions, tabGroups, amenGroups);
					ipMapFunctions.buildInputs();
					ipMapFunctions.getAjaxResults();
				});	
            })
        });
        ";

        // set columns for results header
        if ($this->params->get('adv_column_pid', true)){
            $mapscript .= 'mapOptions.resultColumns.push("pid")'."\n";
        }
        if ($this->params->get('adv_column_price', true)){
            $mapscript .= 'mapOptions.resultColumns.push("price")'."\n";
        }
        if ($this->params->get('adv_column_street', true)){
            $mapscript .= 'mapOptions.resultColumns.push("street")'."\n";
        }
        if ($this->params->get('adv_column_beds', true)){
            $mapscript .= 'mapOptions.resultColumns.push("beds")'."\n";
        }
        if ($this->params->get('adv_column_baths', true)){
            $mapscript .= 'mapOptions.resultColumns.push("baths")'."\n";
        }
        if ($this->params->get('adv_column_sqft', true)){
            $mapscript .= 'mapOptions.resultColumns.push("sqft")'."\n";
        }
        $mapscript .= 'mapOptions.resultColumns.push("preview")'."\n";
        $mapscript .= 'mapOptions.showthumb = true;'."\n";

        $document->addScriptDeclaration($mapscript);

        $this->_prepareDocument();
        parent::display($tpl);
    }

    protected function _prepareDocument()
    {
        $app            = JFactory::getApplication();
        $menus          = $app->getMenu();
        $pathway        = $app->getPathway();
        $this->params   = $app->getParams();
        $title          = null;

        $menu = $menus->getActive();
        if ($menu) {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        } else {
            $this->params->def('page_heading', JText::_('COM_IPROPERTY_INTELLECTUAL_PROPERTY' ));
        }

        $title = (is_object($menu) && $menu->query['view'] == 'advsearch') ? $this->params->get('page_title', '') : JText::_( 'COM_IPROPERTY_ADVANCED_SEARCH' );
        $this->iptitle = $title;
        if (empty($title)) {
            $title = $app->getCfg('sitename');
        }
        elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
            $title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
        }
        elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
        }
        $this->document->setTitle($title);

        // Set meta data according to menu params
        if ($this->params->get('menu-meta_description', '')) $this->document->setDescription($this->params->get('menu-meta_description', ''));
        if ($this->params->get('menu-meta_keywords', '')) $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords', ''));
        if ($this->params->get('robots')) $this->document->setMetadata('robots', $this->params->get('robots'));

        // Breadcrumbs
        if(is_object($menu) && $menu->query['view'] != 'advsearch') {
            $pathway->addItem($this->iptitle);
        }
    }

    function _displayNoAccess($tpl = 'noaccess')
    {
        $app                = JFactory::getApplication();
        $this->ipbaseurl    = JURI::root(true);
        $document           = JFactory::getDocument();
        $settings           = ipropertyAdmin::config();
        $pathway            = $app->getPathway();

        // Get the menu item object
        $menus = $app->getMenu();
        $menu  = $menus->getActive();

        $document->setTitle( JText::_('COM_IPROPERTY_ADVANCED_SEARCH'));
        //set breadcrumbs
        if(is_object($menu) && $menu->query['view'] != 'advsearch') {
            $pathway->addItem(JText::_('COM_IPROPERTY_ADVANCED_SEARCH'), '');
        }

        $this->assignRef('settings', $settings);

        parent::display($tpl);
    }

    protected function _getInputs($settings)
    {
        $input_script = 'var inputData = {};'."\n";

        // add tab group data
        // TODO -- maybe make these depend on settings?
        $input_script .= "tabGroups.categories  = { 'title': '".addslashes(JText::_('COM_IPROPERTY_CATEGORY'))."', 'active': true }"."\n";
        $input_script .= "tabGroups.location    = { 'title': '".addslashes(JText::_('COM_IPROPERTY_LOCATION'))."', 'active': false }"."\n";
        $input_script .= "tabGroups.property    = { 'title': '".addslashes(JText::_('COM_IPROPERTY_PROPERTY'))."', 'active': false }"."\n";

        $max_price  = $this->params->get('adv_price_high'   , $settings->adv_price_high);
        $min_price  = $this->params->get('adv_price_low'    , $settings->adv_price_low);
        $max_beds   = $this->params->get('adv_beds_high'    , $settings->adv_beds_high);
        $min_beds   = $this->params->get('adv_beds_low'     , $settings->adv_beds_low);
        $max_baths  = $this->params->get('adv_baths_high'   , $settings->adv_baths_high);
        $min_baths  = $this->params->get('adv_baths_low'    , $settings->adv_baths_low);
        $max_sqft   = $this->params->get('adv_sqft_high'    , $settings->adv_sqft_high);
        $min_sqft   = $this->params->get('adv_sqft_low'     , $settings->adv_sqft_low);
        $no_limit   = $this->params->get('adv_nolimit'      , $settings->adv_nolimit);
        $filter     = $this->params->get('adv_map_filters'  , true) ? 1 : 0;
        $multiple   = $this->params->get('adv_map_multiple' , false) ? 1 : 0;

        $sqft_header = ($settings->measurement_units) ? JText::_('COM_IPROPERTY_SQMDD') : JText::_('COM_IPROPERTY_SQFTDD');

        // Price Slider
        if ($this->params->get('show_price_sliders', 1))
        {
            $input_script .=
            "ipSliders.price = {
                param: 'price',
                title: '".addslashes(JText::_('COM_IPROPERTY_PRICE'))."',
                value: { min: ".$min_price.", max: ".$max_price." },
                nolimit: ".$no_limit.",
                labelUnit: '', // leave this null for default formatting, else supply i18n value for locale (eg de-DE)
                selected: {
                    min: ".$min_price.",
                    max: ".$max_price."
                }
            }"."\n";
        }

        // Beds slider
        if ($this->params->get('show_beds_sliders', 1))
        {
            $input_script .=
            "ipSliders.beds = {
                param: 'beds',
                title: '".addslashes(JText::_('COM_IPROPERTY_BEDS'))."',
                value: { min: ".$min_beds.", max: ".$max_beds." },
                nolimit: ".$no_limit.",
                selected: {
                    min: ".$min_beds.",
                    max: ".$max_beds."
                }
            }"."\n";
        }

        // Baths slider
        if ($this->params->get('show_baths_sliders', 1))
        {
            $input_script .=
            "ipSliders.baths = {
                param: 'baths',
                title: '".addslashes(JText::_('COM_IPROPERTY_BATHS'))."',
                value: { min: ".$min_baths.", max: ".$max_baths." },
                nolimit: ".$no_limit.",
                selected: {
                    min: ".$min_baths.",
                    max: ".$max_baths."
                }
            }"."\n";
        }

        // Sqft/M slider
        if ($this->params->get('show_sqft_sliders', 1))
        {
            $input_script .=
            "ipSliders.sqft = {
                param: 'sqft',
                title: '".addslashes($sqft_header)."',
                value: { min: ".$min_sqft.", max: ".$max_sqft." },
                nolimit: ".$no_limit.",
                selected: {
                    min: ".$min_sqft.",
                    max: ".$max_sqft."
                }
            }"."\n";
        }

        // Sale types
        if ($this->params->get('adv_show_stype', $settings->adv_show_stype))
        {
            if($this->params->get('default_stype')){
                $stype_default  = (int)$this->params->get('default_stype');
                $stype_disabled = 'true';
				$input_script .= 'mapOptions.currentvals.property["stype"] = '.$stype_default.';'."\n";
            }else{
                $stype_default  = 'false';
                $stype_disabled = 'false';
            }
            $input_script .= "inputData.stype = {
                                    default: ".$stype_default.",
                                    disabled: ".$stype_disabled.",
                                    title: '".addslashes(JText::_('COM_IPROPERTY_SALE_TYPE'))."',
                                    datagroup: 'property',
                                    tab: 'property',
                                    multiple: ".$multiple.",
                                    type: 'select',
                                    lookup: true,
                                    param: 'stype',
                                    selected: '".$stype_default."',
                                }"."\n";
        }

        // Countries
        if ($this->params->get('adv_show_country', $settings->adv_show_country))
        {
			$input_script .= 'mapOptions.locationFields.push("country");'."\n";
            if($this->params->get('default_country')){
                $country_default  = (int)$this->params->get('default_country');
                $country_disabled = 'true';
				$input_script .= 'mapOptions.currentvals.location["country"] = '.$country_default.';'."\n";
            }else{
                $country_default  = 'false';
                $country_disabled = 'false';
            }
            $input_script .= "inputData.country = {
                                    default: '".$country_default."',
                                    disabled: ".$country_disabled.",
                                    title: '".addslashes(JText::_('COM_IPROPERTY_COUNTRY'))."',
                                    datagroup: 'property',
                                    tab: 'location',
                                    multiple: ".$multiple.",
                                    filter: ".$filter.",
                                    type: 'select',
                                    lookup: true,
									param: 'country',
                                    selected: '".$country_default."',
                                }"."\n";
        }

        // States
        if ($this->params->get('adv_show_locstate', $settings->adv_show_locstate))
        {
			$input_script .= 'mapOptions.locationFields.push("locstate");'."\n";
            if($this->params->get('default_locstate')){
                $state_default  = (int)$this->params->get('default_locstate');
                $state_disabled = 'true';
				$input_script .= 'mapOptions.currentvals.location["locstate"] = '.$state_default.';'."\n";
            }else{
                $state_default  = 'false';
                $state_disabled = 'false';
            }
            $input_script .= "inputData.locstate = {
                                    default: ".$state_default.",
                                    disabled: ".$state_disabled.",
                                    title: '".addslashes(JText::_('COM_IPROPERTY_STATE'))."',
                                    datagroup: 'property',
                                    tab: 'location',
                                    multiple: ".$multiple.",
                                    filter: ".$filter.",
                                    type: 'select',
                                    lookup: true,
									param: 'locstate',
                                    selected: '".$state_default."'
                                }"."\n";
        }

        // Provinces
        if ($this->params->get('adv_show_province', $settings->adv_show_province))
        {
			$input_script .= 'mapOptions.locationFields.push("province");'."\n";
            if($this->params->get('default_province')){
                $province_default  = "'".addslashes($this->params->get('default_province'))."'";
                $province_disabled = 'true';
				$input_script .= 'mapOptions.currentvals.location["province"] = '.$province_default.';'."\n";
            }else{
                $province_default  = 'false';
                $province_disabled = 'false';
            }
            $input_script .= "inputData.province = {
                                    default: ".$province_default.",
                                    disabled: ".$province_disabled.",
                                    title: '".addslashes(JText::_('COM_IPROPERTY_PROVINCE'))."',
                                    datagroup: 'property',
                                    tab: 'location',
                                    multiple: ".$multiple.",
                                    filter: ".$filter.",
                                    type: 'select',
                                    lookup: true,
									param: 'province',
                                    selected: ".$province_default."
                                }"."\n";
        }

        // Regions
        if ($this->params->get('adv_show_region', $settings->adv_show_region))
        {
			$input_script .= 'mapOptions.locationFields.push("region");'."\n";
            if($this->params->get('default_region')){
                $region_default  = "'".addslashes($this->params->get('default_region'))."'";
                $region_disabled = 'true';
				$input_script .= 'mapOptions.currentvals.location["region"] = '.$region_default.';'."\n";
            }else{
                $region_default  = 'false';
                $region_disabled = 'false';
            }
            $input_script .= "inputData.region = {
                                    default: ".$region_default.",
                                    disabled: ".$region_disabled.",
                                    title: '".addslashes(JText::_('COM_IPROPERTY_REGION'))."',
                                    datagroup: 'property',
                                    tab: 'location',
                                    multiple: ".$multiple.",
                                    filter: ".$filter.",
                                    type: 'select',
                                    lookup: true,
									param: 'region',
                                    selected: ".$region_default."
                                }"."\n";
        }

        // Counties
        if ($this->params->get('adv_show_county', $settings->adv_show_county))
        {
			$input_script .= 'mapOptions.locationFields.push("county");'."\n";
            if($this->params->get('default_county')){
                $county_default  = "'".addslashes($this->params->get('default_county'))."'";
                $county_disabled = 'true';
				$input_script .= 'mapOptions.currentvals.location["county"] = '.$county_default.';'."\n";
            }else{
                $county_default  = 'false';
                $county_disabled = 'false';
            }
            $input_script .= "inputData.county = {
                                    default: ".$county_default.",
                                    disabled: ".$county_disabled.",
                                    title: '".addslashes(JText::_('COM_IPROPERTY_COUNTY'))."',
                                    datagroup: 'property',
                                    tab: 'location',
                                    multiple: ".$multiple.",
                                    filter: ".$filter.",
                                    type: 'select',
                                    lookup: true,
									param: 'county',
                                    selected: ".$county_default."
                               }"."\n";
        }

        // Cities
        if ($this->params->get('adv_show_city', $settings->adv_show_city))
        {
			$input_script .= 'mapOptions.locationFields.push("city");'."\n";
            if($this->params->get('default_city')){
                $city_default  = "'".addslashes($this->params->get('default_city'))."'";
                $city_disabled = 'true';
				$input_script .= 'mapOptions.currentvals.location["city"] = '.$city_default.';'."\n";
            }else{
                $city_default  = 'false';
                $city_disabled = 'false';
            }
            $input_script .= "inputData.city = {
                                    default: ".$city_default.",
                                    disabled: ".$city_disabled.",
                                    title: '".addslashes(JText::_('COM_IPROPERTY_CITY'))."',
                                    datagroup: 'property',
                                    tab: 'location',
                                    multiple: ".$multiple.",
                                    filter: ".$filter.",
                                    type: 'select',
                                    lookup: true,
									param: 'city',
                                    selected: ".$city_default."
                                }"."\n";
        }

        // REO
        if ($this->params->get('adv_show_reo', $settings->adv_show_reo))
        {
            $reo_checked = ($this->params->get('default_reo')) ? 1 : 0;
			$input_script .= $reo_checked ? 'mapOptions.currentvals.property["reo"] = 1;'."\n" : '';
            $input_script .=
            "inputData.reo = {
                title: '".addslashes(JText::_('COM_IPROPERTY_REO'))."',
                type: 'checkbox',
                param: 'reo',
                disabled: ".$reo_checked.",
                checked: ".$reo_checked.",
                datagroup: 'property',
                tab: 'property',
                lookup: false
            }"."\n";
        }

        // HOA
        if ($this->params->get('adv_show_hoa', $settings->adv_show_hoa))
        {
            $hoa_checked = ($this->params->get('default_hoa')) ? 1 : 0;
			$input_script .= $hoa_checked ? 'mapOptions.currentvals.property["hoa"] = 1;'."\n" : '';
            $input_script .=
            "inputData.hoa = {
                title: '".addslashes(JText::_('COM_IPROPERTY_HOA'))."',
                type: 'checkbox',
                param: 'hoa',
                disabled: ".$hoa_checked.",
                checked: ".$hoa_checked.",
                datagroup: 'property',
                tab: 'property',
                lookup: false
            }"."\n";
        }

        // Waterfront
        if ($this->params->get('adv_show_wf', $settings->adv_show_wf))
        {
            $wf_checked = ($this->params->get('default_waterfront')) ? 1 : 0;
			$input_script .= $wf_checked ? 'mapOptions.currentvals.property["frontage"] = 1;'."\n" : '';
            $input_script .=
            "inputData.waterfront = {
                title: '".addslashes(JText::_('COM_IPROPERTY_WATER_FRONT'))."',
                type: 'checkbox',
                param: 'frontage',
                disabled: ".$wf_checked.",
                checked: ".$wf_checked.",
                datagroup: 'property',
                tab: 'property',
                lookup: false
            }"."\n";
        }

        // Amenities
        if ($this->params->get('adv_show_amen', $settings->adv_show_amen))
        {
            $input_script .=
            "tabGroups.amenities = { 'title': '".addslashes(JText::_('COM_IPROPERTY_AMENITIES'))."', 'active': false };
            amenGroups = new Array( '".addslashes(JText::_('COM_IPROPERTY_AMEN0'))."', '".addslashes(JText::_('COM_IPROPERTY_AMEN1'))."', '".addslashes(JText::_('COM_IPROPERTY_AMEN2'))."', '".addslashes(JText::_('COM_IPROPERTY_AMEN3'))."', '".addslashes(JText::_('COM_IPROPERTY_AMEN4'))."', '".addslashes(JText::_('COM_IPROPERTY_AMEN5'))."', '".addslashes(JText::_('COM_IPROPERTY_AMEN6'))."', '".addslashes(JText::_('COM_IPROPERTY_AMEN7'))."', '".addslashes(JText::_('COM_IPROPERTY_AMEN8'))."' );
            inputData.amenities = {
                title: '".addslashes(JText::_('COM_IPROPERTY_AMENITIES'))."',
                type: 'checkbox',
                param: 'amenities',
                datagroup: 'amenities',
                tab: 'amenities',
                lookup: true
            }"."\n";
        }

        // Categories -- no option to disable
		$input_script .= $this->params->get('default_cat') ? 'mapOptions.currentvals.categories.push("'.$this->params->get('default_cat').'");'."\n" : '';
        $input_script .=
        "inputData.categories = {
            title: '".addslashes(JText::_('COM_IPROPERTY_CATEGORY'))."',
            default: ".(int)$this->params->get('default_cat').",
            param: 'categories',
            type: 'checkbox',
            datagroup: 'categories',
            tab: 'categories',
            lookup: true
        }"."\n";

        return $input_script;
    }
}
?>
