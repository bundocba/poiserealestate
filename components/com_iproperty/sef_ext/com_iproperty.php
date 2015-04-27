<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.');

// ------------------  standard plugin initialize function - don't change ---------------------------
global $sh_LANG;
$sefConfig = &Sh404sefFactory::getConfig();
$shLangName = '';
$shLangIso = '';
$title = array();
$shItemidString = '';
$dosef = shInitializePlugin( $lang, $shLangName, $shLangIso, $option);
if ($dosef == false) return;
// ------------------  standard plugin initialize function - don't change ---------------------------


/********************************************************
* Utility Functions
********************************************************/


# Include the config file
require_once( sh404SEF_ABS_PATH.'components/com_iproperty/helpers/html.helper.php');
require_once( sh404SEF_ABS_PATH.'administrator/components/com_iproperty/classes/admin.class.php');
$settings       = ipropertyAdmin::config();
$app            = JFactory::getApplication();
$menu           = $app->getMenu();
$db             = JFactory::getDbo();

if (empty($Itemid) && $sefConfig->shInsertGlobalItemidIfNone && !empty($shCurrentItemid)) {
    $string .= '&Itemid='.$shCurrentItemid;  // append current Itemid
    $Itemid = $shCurrentItemid;
    shAddToGETVarsList('Itemid', $Itemid); // V 1.2.4.m
}

$mparams    = $menu->getParams($Itemid); 

// Basic requests
$view       = isset($view) ? $view : null;
$task       = isset($task) ? $task : null;
$id         = isset($id) ? $id : null;

// Pagination
$start      = isset($start) ? $start : null;
$limit      = isset($limit) ? $limit : null;
$limitstart = isset($limitstart) ? $limitstart : null;
$limitstart = ($start != '' && !$limitstart) ? $start : null;

// Layout and formats
$format     = isset($format) ? $format : null;
$type       = isset($type) ? $type : null;
$layout     = isset($layout) ? $layout : null;

// IP params or search criteria
$cat        = isset($cat) ? $cat : null;
$stype      = isset($stype) ? $stype : null;
$city       = isset($city) ? $city : null;
$locstate   = isset($locstate) ? $locstate : null;
$province   = isset($province) ? $province : null;
$county     = isset($county) ? $county : null;
$region     = isset($region) ? $region : null;
$country    = isset($country) ? $country : null;
$hoa        = isset($hoa) ? $hoa : null;
$reo        = isset($reo) ? $reo : null;
$waterfront = isset($waterfront) ? $waterfront : null;
$hotsheet   = isset($hotsheet) ? $hotsheet : null;
$ipquicksearch = isset($ipquicksearch) ? $ipquicksearch : null;

switch($view)
{
    case 'advsearch':
        $advtitle = "Property-Search";
        
        // see if there are multiple advanced searches
        $query = $db->getQuery(true);
        $query->select('count(id)')
                ->from('#__menu')
                ->where('link = '.$db->Quote('index.php?option=com_iproperty&view=advsearch'))
                ->where('published = 1');
        
        $db->setQuery($query);
        $result = $db->loadResult();
        
        if($result > 1){
            $advtitle .= $Itemid;
        }
        
        $title[] = $advtitle;
	break;

    case 'agentform':
        $dosef = false;
    break;

    case 'agentproperties':
		$title[]    = "Agent-Properties";
        $agent_id   = $id;
        $cat_id     = $cat;

        if(!empty($agent_id)) {
            $temp       = ipropertyHTML::getAgentName($agent_id, true);
            if(!empty($temp)) $title[] = $temp;
		}
        if(!empty($cat_id)){
            $temp       = ipropertyHTML::getCatName($cat_id, true);
            if(!empty($temp)) $title[] = $temp;
        }
    break;

    case 'agents':
		$title[] = "Agents";
	break;

    case 'allproperties':
		$title[] = "All-Properties";
	break;

    case 'cat':
		$title[] = "Property-Types";
        $cat_id = $id;

        if($cat_id == 0){
            $title[] = "All-Types";
        }else if(!empty($cat_id)) {
            $temp = ipropertyHTML::getCatName($cat_id, true);
            if(!empty($temp)) $title[] = $temp;
		}
	break;

    case 'companies':
		$title[] = "Companies";
	break;

    case 'companyagents':
		$title[] = "Company-Agents";
        $co_id = $id;

        if(!empty($co_id)) {
            $temp = ipropertyHTML::getCompanyName($co_id, true);
            if(!empty($temp)) $title[] = $temp;
		}
	break;
    
    case 'companyform':
        $dosef = false;
    break;

    case 'companyproperties':
		$title[] = "Company-Properties";
        $co_id = $id;
        $cat_id = $cat;

        if(!empty($co_id)) {
            $temp = ipropertyHTML::getCompanyName($co_id, true);
            if(!empty($temp)) $title[] = $temp;
		}
        if(!empty($cat_id)){
            $temp = ipropertyHTML::getCatName($cat_id, true);
            if(!empty($temp)) $title[] = $temp;
        }
	break;

    case 'contact':
        if($layout == 'agent'){
            $agent_id = $id;
            if(!empty($agent_id)) {
                $temp = ipropertyHTML::getAgentName($agent_id, true);
                if(!empty($temp)) $title[] = 'Contact '.$temp;
            }
        }else if($layout == 'company'){
            $co_id = $id;
            if(!empty($co_id)) {
                $temp = ipropertyHTML::getCompanyName($co_id, true);
                if(!empty($temp)) $title[] = 'Contact '.$temp;
            }
        }
	break;

    case 'feed':
		$title[] = "Feeds";
	break;

    case 'home':
		$title[] = "Iproperty";
	break;

	case 'ipuser':
		$title[] = "My-Favorites";
	break;

    case 'manage':
        $title[] = "IP-Manage";
    break;

    case 'openhouses':
		$title[] = "Open-Houses";
	break;

	case 'property':
		$title[] = "Property";
		$prop_id = $id;

		if(!empty($prop_id)) {
            $temp = ipropertyHTML::getPropertyTitle($prop_id, true);
            if(!empty($temp)) $title[] = $temp.'-'.$prop_id;
		}
	break;
    
    case 'propform':
        $dosef = false;
	break;

	default:
		  $dosef = false;
	break;
}

// Tack on additional IP related info to the url from menu parameters   
if($mparams->get('cat', ''))        $title[] = ipropertyHTML::getCatName($mparams->get('cat'), true);

//var_dump($mparams->get('stype'));
if($mparams->get('stype', '')){
    if(is_array($mparams->get('stype'))){
        foreach($mparams->get('stype') as $st){
            $title[] = ipropertyHTML::get_stype($st);
        }
    }else{
        $title[] = ipropertyHTML::get_stype($mparams->get('stype'));
    }
}
if($mparams->get('city', ''))       $title[] = $mparams->get('city');
if($mparams->get('locstate', ''))   $title[] = ipropertyHTML::getStateName($mparams->get('locstate'));
if($mparams->get('province', ''))   $title[] = $mparams->get('province');
if($mparams->get('county', ''))     $title[] = $mparams->get('county');
if($mparams->get('region', ''))     $title[] = $mparams->get('region');
if($mparams->get('country', ''))    $title[] = ipropertyHTML::getCountryName($mparams->get('country'));
if($mparams->get('beds', ''))       $title[] = 'Min-'.$mparams->get('beds');
if($mparams->get('baths', ''))      $title[] = 'Min-'.$mparams->get('baths');
if($mparams->get('price_low', ''))  $title[] = 'Above-'.$mparams->get('price_low');
if($mparams->get('price_high', '')) $title[] = 'Below-'.$mparams->get('price_high');
if($mparams->get('hoa', ''))        $title[] = 'hoa';
if($mparams->get('reo', ''))        $title[] = 'reo';
if($mparams->get('waterfront', '')) $title[] = 'Waterfront';

// Check if any requests in the url for IP related info, tack on if not already part of the menu param
if (!empty($cat) && !$mparams->get('cat'))                  $title[] = ipropertyHTML::getCatName($cat, true);
if (!empty($stype) && !$mparams->get('stype')){
    if(is_array($stype)){
        foreach($stype as $st){
            if($st) $title[] = ipropertyHTML::get_stype($st);
        }
    }else{
        $title[] = ipropertyHTML::get_stype($stype);
    }
}
if (!empty($city) && !$mparams->get('city'))                $title[] = $city;
if (!empty($locstate) && !$mparams->get('locstate'))        $title[] = ipropertyHTML::getStateName($locstate);
if (!empty($province) && !$mparams->get('province'))        $title[] = $province;
if (!empty($county) && !$mparams->get('county'))            $title[] = $county;
if (!empty($region) && !$mparams->get('region'))            $title[] = $region;
if (!empty($country) && !$mparams->get('country'))          $title[] = ipropertyHTML::getCountryName($country);
if (!empty($beds) && !$mparams->get('beds', ''))            $title[] = 'Min-'.$beds;
if (!empty($baths) && !$mparams->get('baths', ''))          $title[] = 'Min-'.$baths;
if (!empty($price_low) && !$mparams->get('price_low', ''))  $title[] = 'Above-'.$price_low;
if (!empty($price_high) && !$mparams->get('price_high', ''))$title[] = 'Below-'.$price_high;
if(!empty($hoa) && !$mparams->get('hoa', ''))               $title[] = 'hoa';
if(!empty($reo) && !$mparams->get('reo', ''))               $title[] = 'reo';
if(!empty($waterfront) && !$mparams->get('waterfront', '')) $title[] = 'Waterfront';
if(!empty($ipquicksearch) && $view == 'allproperties')      $title[] = 'Search';

if(!empty($hotsheet)){
    switch($hotsheet){
        case 1: //new
            $title[] = "New-Listings";
        break;
        case 2: //updated
            $title[] = "Updated-Listings";
        break;
    }
}
if (!empty($layout) && $view != 'contact')            $title[] = $layout;
if(!empty($format))             $title[] = $format;
if(!empty($type))               $title[] = $type;

/* sh404SEF extension plugin : remove vars we have used, adjust as needed --*/
if (isset($view))
    shRemoveFromGETVarsList('view');
if (isset($task))
    shRemoveFromGETVarsList('task');
if(isset($id)) 
    shRemoveFromGETVarsList('id');

if (isset($limit))
    shRemoveFromGETVarsList('limit');
if (isset($limitstart))
    shRemoveFromGETVarsList('limitstart'); // limitstart can be zero

if(isset($format) && (!sh404SEF_PROTECT_AGAINST_DOCUMENT_TYPE_ERROR || (sh404SEF_PROTECT_AGAINST_DOCUMENT_TYPE_ERROR && $format == 'html'))) 
    shRemoveFromGETVarsList('format');
if(isset($type)) 
    shRemoveFromGETVarsList('type');
if(isset($layout)) 
    shRemoveFromGETVarsList('layout');

if(isset($cat))
    shRemoveFromGETVarsList('cat');
if(isset($stype))
    shRemoveFromGETVarsList('stype');
if(isset($city))
    shRemoveFromGETVarsList('city');
if(isset($locstate))
    shRemoveFromGETVarsList('locstate');
if(isset($province))
    shRemoveFromGETVarsList('province');
if(isset($county))
    shRemoveFromGETVarsList('county');
if(isset($region))
    shRemoveFromGETVarsList('region');
if(isset($country))
    shRemoveFromGETVarsList('country');
if(isset($hoa))
    shRemoveFromGETVarsList('hoa');
if(isset($reo))
    shRemoveFromGETVarsList('reo');
if(isset($waterfront))
    shRemoveFromGETVarsList('waterfront');
if(isset($hotsheet))
    shRemoveFromGETVarsList('hotsheet');
if(isset($ipquicksearch))
    shRemoveFromGETVarsList('ipquicksearch');

//if(isset($controller)) shRemoveFromGETVarsList('controller');
shRemoveFromGETVarsList('option');
shRemoveFromGETVarsList('lang');
shRemoveFromGETVarsList('Itemid');
/* sh404SEF extension plugin : end of remove vars we have used -------------*/

// ------------------ standard plugin finalize function - don't change ---------------------------
if ($dosef){
    $string = shFinalizePlugin( $string, $title, $shAppendString, $shItemidString,
    (isset($limit) ? @$limit : null), (isset($limitstart) ? @$limitstart : null),
    (isset($shLangName) ? @$shLangName : null));
}
// ------------------ standard plugin finalize function - don't change ---------------------------

?>