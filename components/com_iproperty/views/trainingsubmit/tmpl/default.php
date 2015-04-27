<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

JHtml::_('bootstrap.tooltip');
?>

<div class="ip-traininglist<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading')) : ?>
        <div class="page-header">
            <h1>
                <?php echo $this->escape($this->params->get('page_heading')); ?>
            </h1>
        </div>
    <?php endif; ?>
    <?php if ($this->params->get('show_ip_title') && $this->iptitle) : ?>
        <div class="ip-mainheader">
            <h2>
                <?php echo $this->escape($this->iptitle); ?>
            </h2>
        </div>        
    <?php endif; ?>
    <div class="clearfix"></div>
    
    <?php 
        if($this->limit == 0){
            if($this->result > 0){
                echo '<h2>Thank  for Registers</h2>';
                echo '<a href="'.JRoute::_("index.php?option=com_iproperty&view=training&Itemid=224&lang=en").'">Please come back Training Course</a>';
            }
        }else{
            echo '<h2>This Training Course is full</h2>';
             echo '<a href="'.JRoute::_("index.php?option=com_iproperty&view=training&Itemid=224&lang=en").'">Please come back Training Course to choise different Training course</a>';
        }
    ?>
</div>
