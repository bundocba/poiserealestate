<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Iproperty
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license see LICENSE.php
 */

//no direct access
defined('_JEXEC') or die('Restricted access');

$cat                = (int) $params->get('cat_id', 0);
$county             = (string) $params->get('county', 0);
$region             = (string) $params->get('region', 0);
$province           = (string) $params->get('province', 0);
$locstate           = (int) $params->get('locstate', 0);
$country            = (int) $params->get('country', 0);
$count              = (int) $params->get('show_count', 0);
$pretext            = $params->get('pretext');
$posttext           = $params->get('posttext');
$moduleclass_sfx    = ($params->get('moduleclass_sfx')) ? ' '.htmlspecialchars($params->get('moduleclass_sfx')) : '';

if($cat){
    $db     = JFactory::getDbo();
    $query  = $db->getQuery(true);
    
    $query->select('alias')
            ->from('#__iproperty_categories')
            ->where('id = '.(int)$cat);
    
    $db->setQuery($query, 0, 1);
    $cat_alias = $db->loadResult();
}
?>

<div class="ip-city-links<?php echo $moduleclass_sfx; ?>" style="height: <?php echo $params->get('height', 400); ?>px; overflow: auto;">
    <ul class="ip-city-links-ul<?php echo ($params->get('ul_class')) ? ' '.$params->get('ul_class') : ''; ?>">
        <?php
        foreach ($items as $item)
        {
            if($falang){
                $db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $query->select('city')->from('#__iproperty')->where('id = '.(int)$item->id);
                $db->setQuery($query, 0, 1);
                $item->urlcity = $db->loadResult();
            }else{
                $item->urlcity = $item->city;
            }
            
            if($cat){
                $link = ipropertyHelperRoute::getCatRoute($cat.':'.$cat_alias);
                $link .= '&ipquicksearch=1';
                $link .= '&filter_city='.urlencode($item->urlcity);
            }else{
                $link = ipropertyHelperRoute::getAllPropertiesRoute();
                $link .= '&ipquicksearch=1';
                $link .= '&filter_city='.urlencode($item->urlcity);
            }
            if($county){
                $link .= '&filter_county='.urlencode($county);
            }
            if($region){
                $link .= '&filter_region='.urlencode($region);
            }
            if($province){
                $link .= '&filter_province='.urlencode($province);
            }
            if($locstate){
                $link .= '&filter_locstate='.urlencode($locstate);
            }
            if($country){
                $link .= '&filter_country='.urlencode($country);
            }
            $link       = JRoute::_($link);
            $cityword   = ($params->get('clean_city')) ? ucwords(strtolower($item->city)) : $item->city;
            $cityword   = ($pretext) ? $pretext.' '.$cityword : $cityword;
            $cityword   = ($posttext) ? $cityword.' '.$posttext : $cityword;
            $cityword   = ($count) ? $cityword.' ('.$item->count.')' : $cityword;

            echo '<li><a href="'.stripslashes($link).'">'.$cityword.'</a></li>';
        }
        ?>
    </ul>
</div>