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

$show_desc          = $params->get('show_desc', 1);
$iplayout           = $params->get('iplayout', 'rows');
$rowspan            = ($iplayout == 'rows') ? 12 : (12 / $params->get('columns', '12'));
$moduleclass_sfx    = ($params->get('moduleclass_sfx')) ? ' '.htmlspecialchars($params->get('moduleclass_sfx')) : '';
?>

<div class="row-fluid<?php echo $moduleclass_sfx; ?>">
    <?php
    $colcount = 0;
    foreach($items as $item)
    {
        $item->clink = JRoute::_(ipropertyHelperRoute::getCompanyPropertyRoute($item->id.':'.$item->alias));
        $item->img  = $item->icon ?  IpropertyHTML::getIconpath($item->icon, 'company') :  IpropertyHTML::getIconpath('nopic.png', 'company');
        ?>
        <div class="ip-featuredcompanies-holder span<?php echo $rowspan; ?>">
            <?php if($iplayout == 'rows') echo '<div class="span3">'; ?>
                <div class="ip-mod-thumb ip-featuredcompanies-thumb-holder">
                    <a href="<?php echo $item->clink; ?>">
                        <img src="<?php echo $item->img; ?>" alt="<?php echo htmlspecialchars($item->name); ?>" class="thumbnail ip-featured-company-thumb" />
                    </a>
                </div>
            <?php 
            if($iplayout == 'rows'){
                echo '</div>';
            }else{
                echo '<div class="clearfix"></div>';
            }
            ?>
            <div class="ip-mod-desc ip-featuredcompanies-desc-holder span9">
                <a href="<?php echo $item->clink; ?>" class="ip-mod-title">
                    <?php echo $item->name; ?>
                </a>
            </div>
            <?php if($params->get('show_readmore', 1)): ?>
                <div class="ip-mod-readmore ip-featuredcompanies-readmore">
                    <a href="<?php echo $item->clink; ;?>" class="btn btn-primary readon"><?php echo JText::_('MOD_IP_FEATUREDCOMPANIES_READ_MORE'); ?></a>
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