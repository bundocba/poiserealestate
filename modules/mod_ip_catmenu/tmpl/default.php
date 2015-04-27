<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Iproperty
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license see LICENSE.php
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access');


// Because this function is declared in the view, need to make sure it hasn't already been declared:
if (!function_exists('ipBuildMenu')) 
{
	function ipBuildMenu($params, $parent = 0, $level = 0, $currentCat = null) 
    {
		// prepare params:
        $db             = JFactory::getDbo();
		$levelStart     = (int) $params->get('startLevel', 0);
		$levelEnd       = (int) $params->get('endLevel', 0);
		$menuClass      = $params->get('class_sfx');
        $moduleclass_sfx = $params->get('moduleclass_sfx');
        $jinput = JFactory::getApplication()->input;

		echo '<div class="ip-catmenu'.$moduleclass_sfx.'">';
        if ( (!$levelEnd || $level < $levelEnd) && $rows = modIpCatMenu::ipGetCatSiblings($parent, $params) ) 
        {
			if ($level >= $levelStart)
            {
                echo '<ul class="ip-catmenu'.$menuClass.'">';
            }
                foreach( $rows as $row ) 
                {                    
                    $cat_active = $jinput->get('option') == 'com_iproperty' && $jinput->get('view') == 'cat' && $jinput->get('id') == $row->id;
                    $activeClass = ($cat_active) ? ' class="current active deeper parent"' : '';
                    if ($level >= $levelStart)
                    {
                        $link = JRoute::_(ipropertyHelperRoute::getCatRoute($row->id.':'.$row->alias));
                        echo '<li'.$activeClass.'>
                                <a href="'.$link.'">
                                    <span>'.htmlspecialchars(stripslashes($row->title), ENT_COMPAT, 'UTF-8');
                                    
                                    //TODO: CREATE PARAM FOR CAT COUNT - UNDER DEVELOPMENT
                                    if($params->get('show_count', 0)) echo ' - ('.$row->cat_count.')';
                                    echo '
                                    </span>
                                </a>';
                    }
                    
                    // Check for sub categories
                    ipBuildMenu($params, $row->id, $level + 1, $currentCat);
                    if ($level >= $levelStart)
                    {
                        echo '</li>';
                    }
                }
            if ($level >= $levelStart)
            {
                echo '</ul>';
            }
		}
        echo '</div>';
	}
}
// Call the function for the first menu item:
ipBuildMenu($params, 0, 0 );
?>
