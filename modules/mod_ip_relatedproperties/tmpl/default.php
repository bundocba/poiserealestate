<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Iproperty
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$moduleclass_sfx    = ($params->get('moduleclass_sfx')) ? ' '.htmlspecialchars($params->get('moduleclass_sfx')) : '';
?>

<div class="ip-relatedproperties-mod<?php echo $moduleclass_sfx; ?>">
    <ul class="ip-relatedproperties-list<?php echo ($params->get('ul_class')) ? ' '.$params->get('ul_class') : ''; ?>">
        <?php
        foreach($items as $item)
        {
            $available_cats = ipropertyHTML::getAvailableCats($item->id);
            $first_cat      = $available_cats[0];
            
            $item->street_address   = ipropertyHTML::getPropertyTitle($item->id);
            $item->link             = JRoute::_(ipropertyHelperRoute::getPropertyRoute($item->id.':'.$item->alias, $first_cat, true));
            
            echo '<li><a href="'.$item->link.'">'.$item->street_address.'</a></li>';
        }
        ?>
    </ul>
</div>