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

// uncomment this line if you want the featured listing to only show on the first page of results
// @Todo: possibly add this as a menu item parameter
//$this->enable_featured = ($this->state->get('list.start')) ? false : true;
?>

<div class="ip-proplist<?php echo $this->pageclass_sfx;?>">
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
    <?php if ($this->params->get('show_ip_map', 0) && $this->items) {
        switch($this->settings->map_provider)
        {
            case '1': //google
            default:
                echo $this->loadTemplate('gmap');
                break;
            case '2': //bing
                echo $this->loadTemplate('bing');
                break;
        }
    }
    ?>
    <?php
    // load agent overview tmpl
    if( $this->agent ){
        $this->k = 1;
        echo $this->loadTemplate('agent');
    }
    
    // featured properties top position
    if( $this->featured && $this->enable_featured && $this->settings->featured_pos == 0 ){
        echo '
        <h2 class="ip-property-header">'.JText::_( 'COM_IPROPERTY_FEATURED_PROPERTIES' ).'</h2>';
        $this->k = 0;
        foreach( $this->featured as $f ){
            $this->p = $f;
            echo $this->loadTemplate('property');
            $this->k = 1 - $this->k;
        }
    }
    
    // load quick search tmpl
    if ($this->params->get('qs_show_quicksearch', 1)){
        echo $this->loadTemplate('quicksearch');
    }

    // display results for properties
    if ($this->items)
    {
        echo 
            '<h2 class="ip-property-header">'.JText::_('COM_IPROPERTY_PROPERTIES_HANDLED_BY' ).' '.ipropertyHTML::getAgentName($this->agent->id).' </h2><span class="pull-right small ip-pagination-results">'.$this->pagination->getResultsCounter().'</span>';
            $this->k = 0;
            foreach($this->items as $p) :
                $this->p = $p;
                echo $this->loadTemplate('property');
                $this->k = 1 - $this->k;
            endforeach;
        echo
            '<div class="pagination">
                '.$this->pagination->getPagesLinks().'<br />'.$this->pagination->getPagesCounter().'
             </div>';
    } else { // no results tmpl
        echo $this->loadTemplate('noresult');
    }
    
    // featured properties bottom position
    if( $this->featured && $this->enable_featured && $this->settings->featured_pos == 1 ){
        echo '
        <h2 class="ip-property-header">'.JText::_( 'COM_IPROPERTY_FEATURED_PROPERTIES' ).'</h2>';
        $this->k = 0;
        foreach( $this->featured as $f ){
            $this->p = $f;
            echo $this->loadTemplate('property');
            $this->k = 1 - $this->k;
        }
    }
    // display disclaimer if set in params
    if ($this->params->get('show_ip_disclaimer') && $this->settings->disclaimer)
    {
        echo '<div class="well well-small" id="ip-disclaimer">'.$this->settings->disclaimer.'</div>';
    }
    // display footer if enabled
    if ($this->settings->footer == 1) echo ipropertyHTML::buildThinkeryFooter(); 
    ?>
</div>
