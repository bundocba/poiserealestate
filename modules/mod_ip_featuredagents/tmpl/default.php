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
        $item->alink = JRoute::_(ipropertyHelperRoute::getAgentPropertyRoute($item->id.':'.$item->alias));
        $item->clink = JRoute::_(ipropertyHelperRoute::getCompanyPropertyRoute($item->companyid.':'.$item->co_alias));
        $item->img  = $item->icon ?  IpropertyHTML::getIconpath($item->icon, 'agent') :  IpropertyHTML::getIconpath('nopic.png', 'agent');
        ?>
        <div class="ip-featuredagents-holder span<?php echo $rowspan; ?>">
            <?php if($iplayout == 'rows') echo '<div class="span3">'; ?>
                <div class="ip-mod-thumb ip-featuredagents-thumb-holder">
                    <a href="<?php echo $item->alink; ?>">
                        <img src="<?php echo $item->img; ?>" alt="<?php echo htmlspecialchars($item->name); ?>" class="thumbnail ip-featured-agent-thumb" />
                    </a>
                </div>
            <?php 
            if($iplayout == 'rows'){
                echo '</div>';
            }else{
                echo '<div class="clearfix"></div>';
            }
            ?>
            <div class="ip-mod-desc ip-featuredagents-desc-holder span9">
                <a href="<?php echo $item->alink; ?>" class="ip-mod-title">
                    <?php echo $item->name; ?>
                </a>
                <?php if ($item->companyname): ?>
                <br />
                <em>
                    <a href="<?php echo $item->clink; ?>" class="ip-mod-subtitle">
                        <?php echo $item->companyname; ?>
                    </a>
                </em>
                <?php endif; ?>
                <?php if($item->bio && $show_desc): ?>
                    <p><?php echo ipropertyHTML::snippet($item->bio, $params->get('preview_count', 200)) ?></p>
                <?php endif; ?>
            </div>
            <?php if($params->get('show_readmore', 1)): ?>
                <div class="ip-mod-readmore ip-featuredagents-readmore">
                    <a href="<?php echo $item->alink; ;?>" class="btn btn-primary readon"><?php echo JText::_('MOD_IP_FEATUREDAGENT_READ_MORE'); ?></a>
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