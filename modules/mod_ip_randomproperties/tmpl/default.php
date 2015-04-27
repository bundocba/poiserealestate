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

// Initialize variables
$usethumb           = $params->get('usethumb', 1);
$iplayout           = $params->get('iplayout', 'rows');
$rowspan            = ($iplayout == 'rows') ? 12 : (12 / $params->get('columns', '12'));
$moduleclass_sfx    = ($params->get('moduleclass_sfx')) ? ' '.htmlspecialchars($params->get('moduleclass_sfx')) : '';
?>

<div class="row-fluid<?php echo $moduleclass_sfx; ?>">
    <?php
    $colcount = 0;
    foreach($items as $item)
    {
        $item->proplink = JRoute::_(ipropertyHelperRoute::getPropertyRoute($item->id.':'.$item->alias, '', true));
        ?>
        <div class="ip-randomproperties-holder span<?php echo $rowspan; ?>">
            <?php if($iplayout == 'rows') echo '<div class="span3">'; ?>
                <div class="ip-mod-thumb ip-randomproperties-thumb-holder">
                    <?php echo ipropertyHTML::getThumbnail($item->id, $item->proplink, $item->street_address, '', 'class="ip-randomproperties-thumb thumbnail"', '', $usethumb); ?>
                    <?php if($params->get('show_banners', 1)) echo $item->banner; ?>
                </div>
            <?php 
            if($iplayout == 'rows'){
                echo '</div>';
            }else{
                echo '<div class="clearfix"></div>';
            }
            ?>
            <div class="ip-mod-desc ip-randomproperties-desc-holder span9">
                <a href="<?php echo $item->proplink; ?>" class="ip-mod-title">
                    <?php echo $item->street_address; ?>
                </a>
                <em>
                    <?php
                    if($item->city) echo $item->city;
                    if($item->locstate) echo ', '.ipropertyHTML::getStateName($item->locstate);
                    if($item->province) echo ', '.$item->province;
                    ?>
                </em>
                <?php if($item->short_description && $params->get('show_desc', 1)): ?>
                    <p><?php echo ipropertyHTML::snippet($item->short_description, $params->get('preview_count', 200)) ?></p>
                <?php endif; ?>
                <div class="ip-mod-price"><?php echo $item->formattedprice; ?></div>
            </div>
            <?php if($params->get('show_readmore', 1)): ?>
                <div class="ip-mod-readmore ip-randomproperties-readmore">
                    <a href="<?php echo $item->proplink; ;?>" class="btn btn-primary readon"><?php echo JText::_('MOD_IP_RANDOM_READ_MORE'); ?></a>
                </div>
            <?php endif; ?>
        </div>
        <?php
        $colcount++;
        
        // we want to end div with row fluid class and start a new one if:
        // a) we are using the row layout - each row should be new
        // b) if using the column layout - if the column count has been reached
        if($iplayout == 'rows' || ($iplayout == 'columns' && $colcount == $params->get('columns')))
        {
            $colcount = 0;
            echo '</div><div class="row-fluid'.$moduleclass_sfx.'">';
        }
    }
    ?>
</div>
