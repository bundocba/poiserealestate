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
<div class="ip-openhouse-mod<?php echo $moduleclass_sfx; ?>">
    <ul class="ip-openhouse-list<?php echo ($params->get('ul_class')) ? ' '.$params->get('ul_class') : ''; ?>">
        <?php
        foreach($items as $item)
        {
            $available_cats = ipropertyHTML::getAvailableCats($item->prop_id);
            $first_cat      = $available_cats[0];

            $item->name     = ($item->name) ? $item->name : ipropertyHTML::getPropertyTitle($item->prop_id);
            $item->link     = JRoute::_(ipropertyHelperRoute::getPropertyRoute($item->prop_id.':'.$item->alias, $first_cat, true));
            $item->start    = JHTML::_('date', htmlspecialchars($item->openhouse_start),JText::_('DATE_FORMAT_LC2'));
            $item->end		= JHTML::_('date', htmlspecialchars($item->openhouse_end),JText::_('DATE_FORMAT_LC2'));
            ?>
                
            <li>
                <a href="<?php echo $item->link; ?>" class="ip-openhouse-title">
                    <?php echo htmlspecialchars($item->name); ?>
                </a>
                <br /><?php echo $item->start; ?>
                <br /><span class="small"><em><?php echo JText::_('MOD_IP_OPENHOUSES_THROUGH'); ?></em></span>
                <br /><?php echo $item->end; ?>
            </li>
            <?php
        }
        ?>
    </ul>
</div>