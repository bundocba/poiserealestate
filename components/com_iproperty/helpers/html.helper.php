<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.filesystem.file' );
require_once (JPATH_ADMINISTRATOR.'/components/com_iproperty/classes/admin.class.php' );
require_once (JPATH_SITE.'/components/com_iproperty/helpers/auth.php' );
require_once (JPATH_SITE.'/components/com_iproperty/helpers/query.php' );

abstract class IpropertyHTML
{
    public static function snippet($text, $length = 200, $tail = "(...)", $strip_tags = true)
    {
       $text = trim($text);
       $text = ($strip_tags) ? strip_tags($text) : $text;
       $txtl = strlen($text);
       if($txtl > $length) {
           for ($i = 1; $text[$length-$i] != " "; $i++) 
           {
               if ($i == $length) {
                   return substr($text, 0, $length) . $tail;
               }
           }
           $text = substr($text, 0, $length-$i+1) . $tail;
       }
	   // strip out curly bracket plugin text if it exists
	   $text = preg_replace( "/{([^:}]*):?([^}]*)}/", '', $text );
	   $text = str_replace( '  ', ' ', $text );
	   
       return $text;
    }
    
    public static function sentence_case($string)
    {
        $sentences  = preg_split('/([.?!]+)/', $string, -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
        $new_string = '';
        foreach ($sentences as $key => $sentence) 
        {
            $new_string .= ($key & 1) == 0?
            ucfirst(strtolower(trim($sentence))) :
            $sentence.' ';
        }
        return trim($new_string);
    }
    
    public function prepareContent($text, $length = 300) 
    {
		// strips tags won't remove the actual jscript
		$text = preg_replace( "'<script[^>]*>.*?</script>'si", "", $text );
		$text = preg_replace( '/{.+?}/', '', $text);
        
		// replace line breaking tags with whitespace
		$text = preg_replace( "'<(br[^/>]*?/|hr[^/>]*?/|/(div|h[1-6]|li|p|td))>'si", ' ', $text );
		$text = strip_tags( $text );
		if (strlen($text) > $length) $text = substr($text, 0, $length) . "...";
        
		return $text;
	}

    public static function buildThinkeryFooter()
    {
        $ipversion = ipropertyAdmin::_getversion();
        return '<div class="small pagination-centered property_footer">'.JText::_( 'COM_IPROPERTY_PROPERTY_AGENT_FOOTER' ).' <a href="http://www.thethinkery.net" target="_blank">theThinkery.net</a>.  v'.$ipversion['iproperty'].'</div>';
    }
    
    public static function getCountryName($country)
    {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);
        $query->select('id, title')
            ->from('#__iproperty_countries')
            ->where('id = '.(int)$country);

        $db->setQuery($query, 0, 1);
        $result = $db->loadObject();

        return $result ? $result->title : '';
    }

    public static function getStateName($state)
    {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);
        $query->select('id, title')
            ->from('#__iproperty_states')
            ->where('id = '.(int)$state);

        $db->setQuery($query, 0, 1);
        $result = $db->loadObject();

        return $result ? $result->title : '';
    }

    public static function getStateCode($state)
    {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);
        $query->select('mc_name')
            ->from('#__iproperty_states')
            ->where('id = '.(int)$state);

        $db->setQuery($query, 0, 1);
        $result = $db->loadResult();

        return $result ? $result : '';
    }

    public static function getCompanyName($co, $alias = false)
    {
        $db     = JFactory::getDbo();
        $query 	= $db->getQuery(true);
        $query->select($db->quoteName(array('id', 'name', 'alias')))
			->from($db->quoteName('#__iproperty_companies'))
			->where($db->quoteName('id').' = '.$db->quote($co));

        $db->setQuery($query, 0, 1);
        $result = $db->loadObject();
        
        if($alias && $result->alias){
            return $result->alias;
        }else{        
            return $result ? $result->name : '';
        }
    }

    public static function getCompanyEmail($co)
    {
        $db     = JFactory::getDbo();
        $query 	= $db->getQuery(true);
        $query->select($db->quoteName(array('email')))
			->from($db->quoteName('#__iproperty_companies'))
			->where($db->quoteName('id').' = '.$db->quote($co));

        $db->setQuery($query, 0, 1);
        $result = $db->loadObject();
        
        return $result->email;
    }

    public static function getAvailableCats( $id = null )
    {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);
        $query->select('cat_id')
            ->from('#__iproperty_propmid')
            ->where('amen_id = 0')
            ->where('prop_id = '.(int)$id);

        $db->setQuery($query);
        $result = $db->loadColumn();

        return $result ? $result : '';
    }

    public static function getCatIcon($cat, $width = '', $admin = false, $text_only = false)
    {
        $db         = JFactory::getDbo();
        $caticon    = '';

        $query = $db->getQuery(true);
        $query->select('id, icon, title, alias')
            ->from('#__iproperty_categories')
            ->where('id = '.(int)$cat);

        $db->setQuery($query, 0, 1);

        if (($result = $db->loadObject()) !== null) //thanks bianchijc for null vs boolean bug
        {
            if ($text_only){
                $caticon = $result->title;
            }else if (isset($result->icon) && $admin){
                $caticon = '<img src="'.JURI::root(true).'/media/com_iproperty/categories/'.$result->icon.'" alt="'.$result->title.'" width="'.$width.'" class="hasTooltip caticon" title="'.$result->title.'" />';
            }else if (isset($result->icon) && $result->icon != "nopic.png"){
                $caticon = '<img src="'.JURI::root(true).'/media/com_iproperty/categories/'.$result->icon.'" alt="'.$result->title.'" width="'.$width.'" class="hasTooltip caticon" title="'.$result->title.'" />';
            }else{
                $caticon = $result->title;
            }

            if(!$admin)
            {
                $catlink = ipropertyHelperRoute::getCatRoute($result->id.':'.$result->alias);
                $caticon = '<a href="'.JRoute::_($catlink).'">'.$caticon.'</a>';
            }
        }
        return $caticon;
    }

    public static function getIconpath($icon, $type)
    {
        $folder = false;
        switch ($type) {
            case 'agent':
                $folder = 'media/com_iproperty/agents/';
                break;
            case 'company':
                $folder = 'media/com_iproperty/companies/';
                break;
            case 'category':
                $folder = 'media/com_iproperty/categories/';
                break;
        }
        if (!$folder) return false;

        $folderpath = (substr($icon, 0, 4) == 'http') ? '' : JURI::root().$folder;
        $iconpath   = $folderpath.$icon;

        return $iconpath;
    }
    
    public static function get_stype($stype)
    {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);
        $query->select('id, name')
            ->from('#__iproperty_stypes')
            ->where('id = '.(int)$stype);

        $db->setQuery($query, 0, 1);
        $result = $db->loadObject();

        return $result ? JText::_($result->name) : '';
    }

    public static function get_stypes()
    {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);
        $query->select('id, name')
            ->from('#__iproperty_stypes');

        $db->setQuery($query);
        $result = $db->loadObjectList();

        $stypes = array();
        foreach ($result as $r){
            $stypes[$r->id] = $r->name;
        }
        return $stypes;
    }       

    public static function checkbox( $name, $tag_attribs, $value, $text = '', $showtext = 0, $checked = null )
    {
        $text       = ($showtext == 1) ? '&#160;'.$text : '';
        $checked    = ($checked == 1) ? ' checked="checked"' : '';
        return '<input type="checkbox" name="'.$name.'" '.$tag_attribs.' value="'.$value.'"'.$checked.' />'.$text;
    }    

	public static function getAgentName($agent_id, $alias = false)
    {
        $db 	= JFactory::getDbo();
        $query 	= $db->getQuery(true);
        $query->select($db->quoteName(array('id', 'fname', 'lname', 'alias')))
			->from($db->quoteName('#__iproperty_agents'))
			->where($db->quoteName('id').' = '.$db->quote($agent_id));

        $db->setQuery($query, 0, 1);
        $result = $db->loadObject();
        
        if($alias && $result->alias){
            return $result->alias;
        }else{        
            return $result ? $result->fname.' '.$result->lname : '';
        }
    }    

    public static function getAgentEmail($agent_id)
    {
        $db     = JFactory::getDbo();
        $query 	= $db->getQuery(true);
        $query->select($db->quoteName(array('email')))
			->from($db->quoteName('#__iproperty_agents'))
			->where($db->quoteName('id').' = '.$db->quote($agent_id));

        $db->setQuery($query, 0, 1);
        $result = $db->loadResult();

        return $result ? $result->email : '';
    }  

    public static function getAvailableAgents( $id = null, $order = 'lname ASC', $limit = 999999 )
    {
        $db = JFactory::getDbo();
        $where = array();
        $query  = IpropertyHelperQuery::buildAgentsQuery($db, $where, 'lname', 'ASC', false);

        $query->leftJoin('#__iproperty_agentmid AS am ON am.agent_id = a.id')
            ->where('am.prop_id = '.(int)$id);

        $db->setQuery($query, 0, $limit);
        $result = $db->loadObjectList();

        return $result ? $result : '';
    }   

    public static function isNew($created, $days = 7)
    {
        $stamp      = strtotime("-$days days");
        $created    = strtotime($created);
        $new        = ( $created >= $stamp ) ? true : false;
        return $new;
    }

    public static function getThumbnail($prop_id, $link = '', $alt = '', $width = '', $imgattributes = '', $linkattributes = '', $suffix = true, $tags = true, $rel_path = true)
    {
        $db             = JFactory::getDbo();
        $settings       = ipropertyAdmin::config();        

        $query = $db->getQuery(true);
        $query->select('path, type, fname, remote')
            ->from('#__iproperty_images')
            ->where('propid = '.(int)$prop_id)
            ->where('( type = ".jpg" OR type = ".jpeg" OR type = ".gif" OR type = ".png")')
            ->order('ordering ASC');

        $db->setQuery($query, 0, 1);
        $thumb         = $db->loadObject();
        $imgsuffix     = ($suffix) ? '_thumb' : '';
        $root_path     = ($rel_path) ? JURI::root(true) : substr(JURI::root(), 0, -1);

        //add appropriate path to thumbnail file
        if ( $thumb ) {
            $path      = ($thumb->remote == 1) ? $thumb->path : $root_path.$settings->imgpath;
            $thumbnail = ($thumb->remote == 1) ? $path.$thumb->fname.$thumb->type : $path.$thumb->fname.$imgsuffix.$thumb->type;
        } else { //no filename found - return nopic img
            $path      = $root_path.$settings->imgpath;
            $thumbnail = $path.'nopic.png';
        }

        if ($tags){
            //create thumbnail image with link if applicable
            $thumbimg = '';
            $thumbimg .= ($link && $linkattributes) ? '<a href="'.$link.'" '.$linkattributes.'>' : ($link && !$linkattributes) ? '<a href="'.$link.'">' : '';
            $thumbimg .= '<img src="'.$thumbnail.'"';
            $thumbimg .= ($alt) ? ' alt="'.$alt.'"' : '';
            $thumbimg .= ($width) ? ' width="'.$width.'"' : '';
            $thumbimg .= ($imgattributes) ? ' '.$imgattributes : '';
            $thumbimg .= ' />';
            $thumbimg .= ($link) ? '</a>' : '';
            return $thumbimg;
        } else {
            return $thumbnail;
        }
    }

    public static function getFormattedPrice($price='', $stype_freq='', $advsearch = false, $call = false, $price2 = false, $newline = false)
    {
        if ($call == true){ //call for price flag
            $formattedprice = JText::_( 'COM_IPROPERTY_CALL_FOR_PRICE' );
        } else if ($price != 0 || $advsearch == true){ //if valid price & not using advanced search
            $settings  = ipropertyAdmin::config();

            $nformat            = $settings->nformat;
            $currency_digits    = $settings->currency_digits;
            $currency           = $settings->currency;
            $currency_pos       = $settings->currency_pos;

            if($stype_freq == '') $currency_digits = 0;
            $before_curr    = ($currency_pos == 0) ? $currency : ''; //currency before price
            $after_curr     = ($currency_pos == 1) ? ' '.$currency : ''; //currency after price

            $payments       = ($stype_freq) ? '/'.$stype_freq : '';
            $format         = ($nformat == 1) ? number_format($price, $currency_digits) : number_format($price,  $currency_digits, ',', '.');
            $formattedprice = $before_curr.$format.$after_curr.$payments;

            if($price2 && $price2 != '0.00'){
                $p2format           = ($nformat == 1) ? number_format($price2, $currency_digits) : number_format($price2,  $currency_digits, ',', '.');
                $oldprice           = '<span class="ip-slashprice">'.$before_curr.$p2format.$after_curr.'</span>';
                $oldprice           .= ($newline) ? '<br />' : ' ';
                $formattedprice     = $oldprice.'<span class="ip-newprice">'.$formattedprice.'</span>';
            }
        } else { //there was no price set
            $formattedprice = JText::_( 'COM_IPROPERTY_CALL_FOR_PRICE' );
        }
        return $formattedprice;
    }

    public static function displayBanners($stype = '', $new = '', $path = '', $settings = '', $updated = '')
    {
        if ($settings->banner_display == 1){ //image banners
            $banner_display = '';
            if ( $new == 1 && $settings->new_days ){
                $banner_display .= '
                    <div class="ip-bannerbotleft">
                        <img src="'.$path.'/components/com_iproperty/assets/images/banners/banner_new.png" alt="'.JText::_( 'COM_IPROPERTY_NEW' ).'" title="'.JText::_( 'COM_IPROPERTY_NEW' ).'" />
                    </div>';
            } else if ( $updated == 1 && $settings->updated_days ){
                $banner_display .= '
                    <div class="ip-bannerbotleft">
                        <img src="'.$path.'/components/com_iproperty/assets/images/banners/banner_updated.png" alt="'.JText::_( 'COM_IPROPERTY_UPDATED' ).'" title="'.JText::_( 'COM_IPROPERTY_UPDATED' ).'" />
                    </div>';
            }
            // dynamic sale type banners v1.5.5
            $banner_display .= ipropertyHTML::displayStypeBanner($stype, 1, $path);
        }else if ($settings->banner_display == 2){ //css banners
            $banner_display = '';
            if( $new == 1 && $settings->new_days ){
                $banner_display .= '
                    <div class="ip-bannercsstop ip-banner-new">
                        '.JText::_( 'COM_IPROPERTY_NEW' ).'
                    </div>';
            } else if ( $updated == 1 && $settings->updated_days ){
                $banner_display .= '
                    <div class="ip-bannercsstop ip-banner-updated">
                        '.JText::_( 'COM_IPROPERTY_UPDATED_LISTING' ).'
                    </div>';
            }
            // dynamic sale type banners v1.5.5
            $banner_display .= ipropertyHTML::displayStypeBanner($stype, 2);
        }else{ //no banners
            $banner_display = '';
        }
        return $banner_display;
    }

    public static function displayStypeBanner($stype, $type, $path = null)
    {
        // load stype object from db
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);
        $query->select('*, id')
                ->from('#__iproperty_stypes')
                ->where('id = '.(int)$stype);

        $db->setQuery($query, 0, 1);
        $result = $db->loadObject();

        $stype_banner = '';
        if ($result->show_banner && $result->state){
            // banner image
            if ($type == 1 && $result->banner_image){
                $stype_banner = '<div class="ip-bannertopright"><img src="'.$path.'/'.$result->banner_image.'" alt="'.JText::_($result->name).'" /></div>';
            } elseif ($type == 2 && $result->banner_color){
                $stype_banner = '<div class="ip-bannercssbot" style="background: '.$result->banner_color.';">'.JText::_($result->name).'</div>';
            }
        }
        return $stype_banner;
    }

    public static function getStreetAddress($settings, $property, $force_show = false, $pview = false)
    {
        $street_address = '';
        if ($settings->showtitle && $property->title && !$pview){
            $street_address = $property->title;
        } else {
            if ($property->hide_address && !$force_show){
                $street_address = JText::_( 'COM_IPROPERTY_ADDRESS_HIDDEN' );
            } else {
                if (!$settings->street_num_pos){ //street number before street
                    $street_address .= $property->street_num.' '.$property->street;
                    $street_address .= ($property->street2) ? ' '.$property->street2 : '';
                    $street_address .= ($property->apt) ? ' '.$property->apt : '';
                } else { //street number after street
                    $street_address .= $property->street;
                    $street_address .= ($property->street2) ? ' '.$property->street2 : '';
                    $street_address .= ($property->street_num) ? ' '.$property->street_num : '';
                    $street_address .= ($property->apt) ? ' '.$property->apt : '';
                }
            }
        }
        return (trim($street_address) != '') ? $street_address : $property->property_name;
    }

    public static function getFullAddress($property, $saddress = false)
    {        
		$address = $saddress ?: $property->street_address;
        $fulladdress = '<address>';
            $fulladdress .= ($address) ? '<strong>'.$address.'</strong><br />' : '';
            $fulladdress .= ($property->city) ? $property->city : '';
            $fulladdress .= ($property->locstate) ? ', '.ipropertyHTML::getstatename($property->locstate) : '';
            $fulladdress .= ($property->province) ? ', '.$property->province : '';
            $fulladdress .= ($property->postcode) ? ' '.$property->postcode : '';
            $fulladdress .= ($property->country) ? '<br />'.ipropertyHTML::getcountryname($property->country) : '';
        $fulladdress .= '</address>';
        return $fulladdress;
    }

    public static function getCatName($catid, $alias = false)
    {
        $db   = JFactory::getDbo();

        $query = $db->getQuery(true);
        $query->select('id, alias, title')
                ->from('#__iproperty_categories')
                ->where('id = '.(int)$catid);

        $db->setQuery($query, 0, 1);
        $result     = $db->loadObject();
        
        if($alias && $result->alias){
            return $result->alias;
        }else{        
            return $result ? $result->title : '';
        }
    }

    /**
     * Basically same as getStreetAddress(), but can be called with property id instead
     * of property object. If $alias is true, returns alias if available
     * 
     * @param   int     $prop_id    Property id
     * @param   boolean $alias      Boolean - optional, true if only want the alias returned
     * 
     * @return  string      The language HTML
     */
    public static function getPropertyTitle($prop_id, $alias = false)
    {
        $db         = JFactory::getDbo();
        $settings   = ipropertyAdmin::config();

        $query = $db->getQuery(true);
        $query->select('id, alias, street_num, street, street2, apt, title, hide_address, property_name')
                ->from('#__iproperty')
                ->where('id = '.(int)$prop_id);

        $db->setQuery($query, 0, 1);

        if (($p = $db->loadObject()) !== false)
        {
            if($alias && $p->alias){
                $prop_title = $p->alias;
            }else if($alias && !$p->alias){
                $prop_title = JApplication::stringURLSafe(self::getStreetAddress($settings, $p));
            }else{
                $prop_title = self::getStreetAddress($settings, $p);
            }
            return $prop_title;
        } else {
            return JText::_( 'COM_IPROPERTY_PROPERTY_NOT_FOUND' );
        }
    }
    
    public static function getOpenHouses($prop_id)
    {
        $db     = JFactory::getDbo();

        // Filter by start and end dates.
        $nullDate   = $db->Quote($db->getNullDate());
        $date       = JFactory::getDate();
        $nowDate    = $db->Quote($date->toSql());

        $query = $db->getQuery(true);
        $query->select('id, name, openhouse_start AS startdate, openhouse_end AS enddate, comments AS comments')
                ->from('#__iproperty_openhouses')
                ->where('prop_id = '.(int)$prop_id)
                ->where('state = 1')
                ->where('openhouse_end >= '.$nowDate)
                ->order('openhouse_start DESC');

        $db->setQuery($query);
        return $db->loadObjectList();
    }   
    
    public static function includeIpScripts($css = true, $js = true)
    {       
        $app        = JFactory::getApplication();
        $document   = JFactory::getDocument();
        $settings   = ipropertyAdmin::config();
        
        jimport('joomla.filesystem.file');
        
        // check if IP css files exist in template css folder
        // if so, use template css instead of IP css
        $templatepath   = $app->getTemplate();
        $ipcss          = (JFile::exists('templates/'.$templatepath.'/css/iproperty.css')) ? '/templates/'.$templatepath.'/css/iproperty.css' : '/components/com_iproperty/assets/css/iproperty.css';
        $ipadvcss       = (JFile::exists('templates/'.$templatepath.'/css/advsearch.css')) ? '/templates/'.$templatepath.'/css/advsearch.css' : '/components/com_iproperty/assets/css/advsearch.css';

        if($css){
            $document->addStyleSheet(JURI::root(true).$ipcss);
            if($app->input->get('view') == 'advsearch'){
                $document->addStyleSheet(JURI::root(true).$ipadvcss);
            }
            if($settings->force_accents){
                $document->addStyleDeclaration('
                .ip-openhouse-info{border-color: '.$settings->accent.' !important;}
                .ip-row0{background-color: '.$settings->secondary_accent.' !important;}');
            }
        }
        if($js){
            $document->addScript(JURI::root(true).'/components/com_iproperty/assets/js/ipcommon.js');
        }
    }

    public static function getListingAgent($property_id)
    {
        if(!$property_id) return '';
        
        $db     = JFactory::getDbo();
        
        $query = $db->getQuery(true);
        $query->select('a.id, a.fname, a.lname')
                ->from('#__iproperty_agents AS a')
                ->leftJoin('#__iproperty_agentmid AS am ON am.agent_id = a.id')
                ->where('am.prop_id = '.(int)$property_id);
        
        $db->setQuery($query, 0, 1);
        $result = $db->loadObject();
        
        return $result->fname.' '.$result->lname;        
    }

    public static function getListingInfo($property, $params)
    {       
        // get listing info params
        $show_list_ag 	= $params->get('show_list_ag', false);
        $show_list_co 	= $params->get('show_list_co', false);
        $show_created 	= $params->get('show_created', false);
        $show_modified 	= $params->get('show_modified', false);
        $show_stype 	= $params->get('show_stype', false);
        $agent          = false;

        if($show_list_ag){
            $agent = ipropertyHTML::getListingAgent($property->id);
        }

        // Get listing info display
		$created 	= ($show_created && $property->created && $property->created != '0000-00-00 00:00:00') ? JHTML::_('date', htmlspecialchars($property->created),JText::_('DATE_FORMAT_LC1')) : false;
		$modified 	= ($show_modified && $property->modified && $property->modified != '0000-00-00 00:00:00') ? JHTML::_('date', htmlspecialchars($property->modified),JText::_('DATE_FORMAT_LC1')) : false;
		
        // begin listing info output
        $output = '';
		
        // if imported data and has own listing info, show this by default
        if (isset($property->listing_info)) {
            $output .= JText::_( 'COM_IPROPERTY_LISTED_BY' ).' '.$property->listing_info;
			if( $created ) $output .= ' '.JText::_( 'COM_IPROPERTY_ON' ).' '.$created;
			if( $property->stype && $show_stype ) $output .= ' ['.ipropertyHTML::get_stype($property->stype).']';
			if( $modified )	$output .= ' '.JText::_( 'COM_IPROPERTY_LAST_MODIFIED' ).' '.$modified;
        } 
        else 
        {
            if ($show_list_ag || $show_list_co)
            {
                $output .= JText::_( 'COM_IPROPERTY_LISTED_BY' ).' ';
                $output .= ($show_list_ag && $agent) ? $agent : '';
                $output .= ($show_list_ag && $show_list_co) ? ', ' : ''; 
                $output .= ($show_list_co && $property->listing_office) ? ipropertyHTML::getCompanyName($property->listing_office).' ' : '';
            }
	
			if( $created ) $output .= ' '.JText::_( 'COM_IPROPERTY_ON' ).' '.$created;
			if( $property->stype && $show_stype ) $output .= ' ['.ipropertyHTML::get_stype($property->stype).']';
			if( $modified )	$output .= ' '.JText::_( 'COM_IPROPERTY_LAST_MODIFIED' ).' '.$modified;
        }
        return $output;
    }
    
    public static function getCatChildren($id)
    {
        $db     = JFactory::getDbo();
        $user   = JFactory::getUser();
        $groups = $user->getAuthorisedViewLevels();
        
        // Filter by start and end dates.
        $nullDate   = $db->Quote($db->getNullDate());
        $date       = JFactory::getDate();
        $nowDate    = $db->Quote($date->toSql());
        
        $query = $db->getQuery(true);
        $query->select('*, id')
                ->from('#__iproperty_categories')
                ->where('(publish_up = '.$nullDate.' OR publish_up <= '.$nowDate.')')
                ->where('(publish_down = '.$nullDate.' OR publish_down >= '.$nowDate.')')
                ->where('state = 1');
        if (is_numeric($id)){
            $query->where('parent = '.(int)$id);
        }
        if (is_array($groups) && !empty($groups)){
            $query->where('access IN ('.implode(",", $groups).')');
        }
        $query->order('ordering ASC');
        
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public static function countCatObjects($cat = "")
    {
        $db             = JFactory::getDbo();
        $subcat_count   = ipropertyHTML::getCatChildren($cat);
        
        if ($subcat_count){
            $child_array = array();
            foreach( $subcat_count as $c )
            {
                $child_array[] = $c->id;
            }
            $child_array = implode(',', $child_array);
            $where = 'pm.cat_id IN ('.(int)$cat.','.$child_array.')';
        } else {
            $where = 'pm.cat_id = '.(int)$cat;
        }

        $query = $db->getQuery(true);
        $query->select('COUNT(pm.id)')
                ->from('#__iproperty_propmid AS pm')
                ->leftJoin('#__iproperty AS p ON p.id = pm.prop_id')
                ->where($where)
                ->where('p.approved = 1')
                ->where('p.state = 1');

        $db->setQuery($query);
        return $db->loadResult();
    }
    
    public static function buildAgent($agent_id)
    {
        $where  = array ('a.id = '.(int)$agent_id);
        $db     = JFactory::getDbo();
        $query  = IpropertyHelperQuery::buildAgentsQuery($db, $where);

        $db->setQuery($query, 0, 1);
        return $db->loadObject();
    }

    public static function buildCompany($co_id)
    {
        $db     = JFactory::getDbo();
        $where  = array('c.id = '.(int)$co_id);
        $query  = IpropertyHelperQuery::buildCompaniesQuery($db, $where);

        $db->setQuery($query, 0, 1);
        return $db->loadObject();
    }
    
    // generic ajax return object creator
    public static function createReturnObject($status, $message, $data = false)
    {
        $return = new StdClass();
        $return->status = $status;
        $return->message = $message;
        $return->data = $data;
        return $return;
    }
    
    public static function priceSelectList($tag, $attrib, $sel = null, $listonly = false, $do_high = false, $increment = false)
    {
        $settings   = ipropertyAdmin::config();
        $high       = $settings->adv_price_high;
        $low        = $settings->adv_price_low;
        $steps      = $increment ? ($do_high ? ceil(($high - $low) / $increment) : floor(($high - $low) / $increment) ) : 10; // you can edit this to make more or fewer steps
        $increment  = $increment ? $increment : ($high - $low) / $steps;

        $i = 0;
        $t_price = $low;
        $temp_price = '';

        $prices = array();
        if ($do_high){
            $prices[] = JHTML::_('select.option', '', JText::_( 'COM_IPROPERTY_MAX_PRICE' ), "value", "text" );
        } else {
            $prices[] = JHTML::_('select.option', '', JText::_( 'COM_IPROPERTY_MIN_PRICE' ), "value", "text" );
        }

        while ($i <= $steps) {
            if ($i == 0 && $settings->adv_nolimit && !$do_high ) {
                $temp_value = '';
                $temp_price = ipropertyHTML::getFormattedPrice(0, '', true);
            } else if ($i == $steps && $settings->adv_nolimit && $do_high) {
                $temp_value = '';
                $temp_price = ipropertyHTML::getFormattedPrice($high, '', true) . '+';
            } else {
                $temp_value = $t_price;
                $temp_price = ipropertyHTML::getFormattedPrice($t_price, '', true);
            }
            $prices[]   = JHTML::_('select.option', $temp_value, $temp_price, "value", "text" );
            $t_price    = $t_price + $increment;
            $i++;
        }

        if($listonly){
            return $prices;
        }else{
            return JHTML::_('select.genericlist', $prices, $tag, $attrib, "value", "text", $sel);
        }
    }
    
    public static function stypeRequestForm($stype)
    {
        $db     = JFactory::getDbo();
        $query  = $db->getQuery(true);
        
        $query->select('show_request_form')
                ->from('#__iproperty_stypes')
                ->where('id = '.(int)$stype);
        
        $db->setQuery($query);
        return $db->loadResult();
    }
    
    public static function getAgentCompany($agentid)
    {
        $db     = JFactory::getDbo();
        $query  = $db->getQuery(true);
        
        $query->select('company')
                ->from('#__iproperty_agents')
                ->where('id = '.(int)$agentid);
        
        $db->setQuery($query, 0, 1);
        return $db->loadResult();
    }
    
    //@TODO - move this back to associations helper when stable. for now we can leave it here
    public static function getFalangAssociations($reftable, $id, $reffield = 'alias')
	{        
        $associations   = array();
        
        if (!file_exists(JPATH_ADMINISTRATOR . '/components/com_falang/falang.php') || !JComponentHelper::isEnabled('com_falang', true))
        {
            // Falang is either not installed or is disabled. Return emtpy array
            return $associations;
        }
        
		// Assuming Falang is installed and enabled, let's find the associations
        $db             = JFactory::getDbo();
		$query          = $db->getQuery(true);
        
        $query->select('l.lang_code, f.value')
                ->from('#__falang_content AS f')
                ->leftJoin('#__languages AS l ON l.lang_id = f.language_id')
                ->where('f.reference_table = '.$db->quote($reftable))
                ->where('f.reference_field = '.$db->quote($reffield))
                ->where('f.reference_id = '.(int)$id)
                ->where('f.published = 1')
                ->where('l.published = 1');

		$db->setQuery($query);

		try
		{
			$items = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			throw new Exception($e->getMessage(), 500);
		}

		if ($items)
		{
			foreach ($items as $item)
			{
                $associations[$item->lang_code] = $item->value;
			}
		}          

		return $associations;
	}
}