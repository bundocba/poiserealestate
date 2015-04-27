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

<div class="ip-agentlist<?php echo $this->pageclass_sfx;?>">
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
    // load company overview tmpl
    if( $this->company ){
        $this->k = 1;
        echo $this->loadTemplate('company');
    }
    
    // featured agents top position
    if( $this->featured && $this->enable_featured && $this->settings->agent_feat_pos == 0 )
    {
        echo '
        <h2 class="ip-agent-header">'.JText::_( 'COM_IPROPERTY_FEATURED_AGENTS' ).'</h2>';
        $this->k = 0;
        foreach( $this->featured as $f ){
            $this->agent = $f;
            echo $this->loadTemplate('agent');
            $this->k = 1 - $this->k;
        }
    }
    
    // load quick search tmpl
    echo $this->loadTemplate('quicksearch');
    
    // display results for agents
    if ($this->items)
    {
        echo 
            '<h2 class="ip-agent-header">'.JText::_('COM_IPROPERTY_AGENTS_OF' ) . ' ' . ipropertyHTML::getCompanyName($this->company->id).'</h2><span class="pull-right small ip-pagination-results">'.$this->pagination->getResultsCounter().'</span>';
            $this->k = 0;
            foreach($this->items as $a) :
                $this->agent = $a;
                echo $this->loadTemplate('agent');
                $this->k = 1 - $this->k;
            endforeach;
        echo
            '<div class="pagination">
                '.$this->pagination->getPagesLinks().'<br />'.$this->pagination->getPagesCounter().'
             </div>';
    } else {
        echo $this->loadTemplate('noresult');
    }
    
    // featured agents bottom position
    if( $this->featured && $this->enable_featured && $this->settings->agent_feat_pos == 1 )
    {
        echo '
        <h2 class="ip-agent-header">'.JText::_( 'COM_IPROPERTY_FEATURED_AGENTS' ).'</h2>';
        $this->k = 0;
        foreach( $this->featured as $f ){
            $this->agent = $f;
            echo $this->loadTemplate('agent');
            $this->k = 1 - $this->k;
        }
    }
    // display footer if enabled
    if ($this->settings->footer == 1) echo ipropertyHTML::buildThinkeryFooter(); 
    ?>
</div>
