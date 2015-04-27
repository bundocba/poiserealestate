<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

JHtml::_('bootstrap.tooltip');
JHtml::_('bootstrap.modal');

$this->document->addScript(JURI::root(true).'/components/com_iproperty/assets/advsearch/utility.js');
?>

<div class="ip-advsearchpage<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_savesearch')) : ?>
    <div id="save-panel">
        <?php echo $this->loadTemplate('searchsave'); ?>
    </div>
    <?php endif; ?>
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
    <a name="ipmap_top"></a>

    <?php
    // build gmap or bing map
    switch ($this->settings->map_provider)
    {
        case 1:
            echo $this->loadTemplate('map_google');
            break;
        case 2:
            echo $this->loadTemplate('map_bing');
            break;
    }
    
    echo '<div class="clearfix ip-adv-spacer"></div>';

    // build sliders or dropdowns
    if ($this->params->get('adv_map_sliders', 1))
    {
        echo $this->loadTemplate('sliders');
    } else {
        echo $this->loadTemplate('dropdowns');
    }

    // build controls
    echo $this->loadTemplate('controls');

    // build results 
    echo $this->loadTemplate('results');
    
    // display disclaimer if set in params
    if ($this->params->get('show_ip_disclaimer') && $this->settings->disclaimer)
    {
        echo '<div class="well well-small" id="ip-disclaimer">'.$this->settings->disclaimer.'</div>';
    }
    // display footer if enabled
    if ($this->settings->footer == 1) echo ipropertyHTML::buildThinkeryFooter();
    ?>
</div>
