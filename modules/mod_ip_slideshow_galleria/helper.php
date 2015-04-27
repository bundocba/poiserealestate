<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Iproperty
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_SITE.'/components/com_iproperty/helpers/html.helper.php');
require_once(JPATH_SITE.'/components/com_iproperty/helpers/property.php');
require_once(JPATH_SITE.'/components/com_iproperty/helpers/route.php');
require_once(JPATH_SITE.'/components/com_iproperty/helpers/query.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_iproperty/classes/admin.class.php');

class modIPSlideshowGalleriaHelper
{
    public static function loadScripts($params, $items)
    {
        switch ($params->get('slideshow_type', 0)){
            case 0:
            default:
                self::_loadGalleria($params, $items);
                break;
            case 1:
                self::_loadOriginal($params, $items);
                break;
        }       
    }

    public static function getList(&$params)
    {
        $db     = JFactory::getDbo();
		$count  = (int) $params->get('count', 5);

        // Ordering
		switch ($params->get( 'ordering' ))
		{
			case '1':
				$sort           = 'price';
                $order          = 'ASC';
				break;
            case '2':
                $sort           = 'price';
                $order          = 'DESC';
                break;
			case '3':
				$sort           = 'p.street';
                $order		    = 'ASC';
				break;
            case '4':
				$sort           = 'p.street';
                $order		    = 'DESC';
				break;
            case '5':
            default:
                $sort           = 'RAND()';
                $order          = '';
                break;
            case '6':
                $sort           = 'p.created';
                $order          = 'DESC';
                break;
		}

        $where  = array();        
        
        // pull sale types if specified
        if ($params->get('prop_stype')) $where['property']['stype'] = $params->get('prop_stype');
        
        // update 2.0.1 - new option to select subcategories as well
        if ($params->get('cat_id') && $params->get('cat_subcats'))
        {            
            $cats_array = array( $params->get('cat_id') );
            $squery     = $db->setQuery(IpropertyHelperQuery::getCategories($params->get('cat_id')));
            $subcats    = $db->loadObjectList();
            
            foreach ($subcats as $s)
            {
                $cats_array[] = (int)$s->id;
            }
            $where['categories'] = $cats_array;
        } elseif ($params->get('cat_id')){
            $where['categories'] = $params->get('cat_id');
        }
        if( $params->get('featured') ) $where['property']['featured'] = 1;
        $where['searchfields']  = array('title','street','street2','short_description','description');
        
        // get items using query helper
        $pquery = new IpropertyHelperQuery($db, $sort, $order);
        $query  = $pquery->buildPropertyQuery($where, 'advsearch');
        $db->setQuery($query, 0, $count);      
        
        $items = ipropertyHelperProperty::getPropertyItems($db->loadObjectList(), true); 
        $thumbdata = array(); 
        foreach ($items as $item){
			$i = new stdClass();
			$i->thumb		= ipropertyHTML::getThumbnail($item->id, '', '', '', '', '', true, false);
			$i->full		= ipropertyHTML::getThumbnail($item->id, '', '', '', '', '', false, false);
			$i->desc		= ipropertyHTML::snippet($item->short_description);
			$i->proplink 	= str_replace('&amp;', '&', trim($item->proplink));
			$i->title		= $params->get( 'price', 1 ) ? $item->street_address.' - '.$item->formattedprice : $item->street_address;
			// remove any with array item with no pic
			if (strpos($item->thumb, 'nopic') !== false) continue;
			$thumbdata[] = $i; 			
		}
		return json_encode($thumbdata);
    }
    
    protected static function _loadGalleria($params, $items)
    {
        $doc = JFactory::getDocument();

        $showCaption        = ($params->get( 'showCaption', 1 )) ? 'true' : 'false';
        $imageDuration      = $params->get( 'imageDuration', 5000 );
		$transDuration      = $params->get( 'transDuration', 2000 );
        
        $transType          = $params->get( 'galleria_transType', 'slide' );
        $showCount          = ($params->get( 'galleria_showCount', 0 )) ? 'true' : 'false';         
        $thumbDisplay       = ($params->get( 'galleria_thumbDisplay' )) ? ', thumbnails: "'.$params->get('galleria_thumbDisplay').'"' : '';
        $theme              = $params->get( 'galleria_theme', 'think' );
        
        JHtml::_('bootstrap.framework');

        if(!defined('_IPGALLERIA')){
            define('_IPGALLERIA', true);
            $doc->addScript(JURI::root(true).'/modules/mod_ip_slideshow_galleria/assets/galleria-1.3.3.min.js');
        }

        $theme_path = JURI::root(true).'/modules/mod_ip_slideshow_galleria/assets/themes/'.$theme.'/galleria.'.$theme.'.min.js';

        $galleriaScript = 'jQuery(document).ready(function($){'."\n";
        $galleriaScript .= '  ipGalleriadata = [];'."\n";
        $galleriaScript .= "  var jsondata = $items;"."\n";
        $galleriaScript .= '  $.each( jsondata, function( i, v ){'."\n";
        $galleriaScript .= '  	ipGalleriadata.push({thumb: v.thumb, image: v.full, title: v.title, description: v.desc, link: v.proplink});'."\n";
        $galleriaScript .= '  });'."\n";
        $galleriaScript .= '  Galleria.loadTheme("'.$theme_path.'");'."\n";
        $galleriaScript .= '  Galleria.run("#ip-galleria", '
                                . '{'
                                . '_toggleInfo: false, '
                                . 'height: 0.5625, '
                                . 'dataSource: ipGalleriadata, '
                                . 'transition: "'.$transType.'", '
                                . 'autoplay: '.$imageDuration.', '
                                . 'transitionSpeed: '.$transDuration.', '
                                . 'showCounter: '.$showCount.', '
                                . 'showInfo: '.$showCaption
                                . $thumbDisplay
                                . '});'."\n";
        $galleriaScript .= '});'."\n";

        $doc->addScriptDeclaration($galleriaScript);
    }
    
    protected static function _loadOriginal($params, $items)
    {
        $doc = JFactory::getDocument();

        if(!defined('_IPSLIDESCRIPTS')){
            define('_IPSLIDESCRIPTS', true);
            $doc->addScript(JURI::root(true).'/modules/mod_ip_slideshow_galleria/assets/themes/original/js/slideshow.js');
            $doc->addStyleSheet(JURI::root(true).'/modules/mod_ip_slideshow_galleria/assets/themes/original/css/slideshow.css');
        }

        $showCaption        = $params->get( 'showCaption', 1 );
        $imageDuration      = $params->get( 'imageDuration', 5000 );
		$transDuration      = $params->get( 'transDuration', 2000 );        
                		
		$transType          = $params->get( 'orig_transType', 'kenburns' );
        $controller         = $params->get( 'orig_showController', true );
        $loop               = $params->get( 'orig_loopShow', true );
        $thumbnails         = $params->get( 'orig_showThumbnails', true );
        $suffix             = htmlspecialchars( $params->get('moduleclass_sfx') );
        $cleansuffix        = str_replace(' ', '_', $suffix); 
        
        // depending on which transition type selected, load proper script
        $sstype = '';
        switch ($transType){
            case 'none':
            default:
                //no special effects
            break;
            case 'flash':
                if(!defined('_IPSLIDESCRIPTS_FLASH')){
                    define('_IPSLIDESCRIPTS_FLASH', true);
                    $doc->addScript(JURI::root(true).'/modules/mod_ip_slideshow_galleria/assets/themes/original/js/slideshow.flash.js');
                }
                $sstype = ".Flash";
            break;
            case 'kenburns':
                if(!defined('_IPSLIDESCRIPTS_KB')){
                    define('_IPSLIDESCRIPTS_KB', true);
                    $doc->addScript(JURI::root(true).'/modules/mod_ip_slideshow_galleria/assets/themes/original/js/slideshow.kenburns.js');
                }
                $sstype = ".KenBurns";
            break;
            case 'push':
                if(!defined('_IPSLIDESCRIPTS_PUSH')){
                    define('_IPSLIDESCRIPTS_PUSH', true);
                    $doc->addScript(JURI::root(true).'/modules/mod_ip_slideshow_galleria/assets/themes/original/js/slideshow.push.js');
                }
                $sstype = ".Push";
            break;
            case 'fold':
                if(!defined('_IPSLIDESCRIPTS_FOLD')){
                    define('_IPSLIDESCRIPTS_FOLD', true);
                    $doc->addScript(JURI::root(true).'/modules/mod_ip_slideshow_galleria/assets/themes/original/js/slideshow.fold.js');
                }
                $sstype = ".Fold";
            break;
        }
		
		$origScript          = '//<![CDATA['."\n";
        $origScript         .= "window.addEvent('domready', function() {\n";
        $origScript 		.= "	var jsondata = $items;"."\n";
		$origScript         .= "	var imgs".$cleansuffix." = {};"."\r\n";
		
		if ($showCaption == 1) {       
			$origScript .= '  jQuery.each( jsondata, function( i, v ){'."\n";
			$origScript .= "	imgs[v.full] = { caption: v.title+' - '+v.desc, href: v.proplink }"."\n";
			$origScript .= '  });'."\n";
		} else {
			$origScript .= '  jQuery.each( jsondata, function( i, v ){'."\n";
			$origScript .= '  	imgs[v.full] = { href: v.proplink };'."\n";
			$origScript .= '  });'."\n";
		}

        $origScript    .= "	var myshow".$cleansuffix." = new Slideshow".$sstype."( 'ip_slideshow".$cleansuffix."', imgs".$cleansuffix.", {"."\r\n";
        // options array
        $origScript    .= "        delay: $imageDuration,"."\r\n"
                    .  "        duration: $transDuration,"."\r\n"
                    .  "        controller: $controller,"."\r\n"
                    .  "        loop: $loop,"."\r\n"
                    .  "        thumbnails: $thumbnails,"."\r\n"
                    .  "        captions: $showCaption,"."\r\n"
                    .  "        replace:[/(\.[^\.]+)$/, '_thumb$1']"."\r\n" // this line will append the normal IP thumb suffix
                    .  "    });"."\r\n";
        
        // any classes need to be added to "classes" array in options
        //$origScript .= "myshow.h2.setStyles({color: '$titleColor', fontSize: '$titleSize'});";
        //        myshow.caps.p.setStyles({color: '$descColor', fontSize: '$descSize'});
        $origScript .= "	});"."\r\n";
        $origScript .= "//]]>"."\r\n";
					
		$doc->addScriptDeclaration($origScript);
    }
}
