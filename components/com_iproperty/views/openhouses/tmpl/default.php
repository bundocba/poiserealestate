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

    <?php
    //display results for properties
    if( $this->items )
    {
        $enddate    = JHTML::_('date', htmlspecialchars($this->items[0]->enddate),JText::_('COM_IPROPERTY_DATE_FORMAT_AVAILABLE'));
        $i          = false;        

        $this->k = 0;
        foreach($this->items as $p)
        {
			$tempdate = JHTML::_('date', htmlspecialchars($p->enddate),JText::_('COM_IPROPERTY_DATE_FORMAT_AVAILABLE'));			
            if($tempdate != $enddate || !$i) 
            {
                echo
                '<div class="ip-openhouse-header pull-right">
                    <span class="label label-success"><span class="icon-info-sign ip-openhouse-icon"></span> '.JText::_('COM_IPROPERTY_ENDS').' '.$tempdate.' <span class="icon-chevron-down"></span></span>
                </div>
                <div class="clearfix"></div>';
                $enddate = JHTML::_('date', htmlspecialchars($p->enddate),JText::_('COM_IPROPERTY_DATE_FORMAT_AVAILABLE'));
                $i = true;
            }

            $this->p    = $p;               
            // load list view tmpl
            echo $this->loadTemplate('openhouse');                
            $this->k = 1 - $this->k;
        }
        echo
            '<div class="pagination">
                '.$this->pagination->getPagesLinks().'<br />'.$this->pagination->getPagesCounter().'
             </div>';
    } else {
        echo $this->loadTemplate('noresult');
    }

    // display disclaimer if set in params
    if ($this->params->get('show_ip_disclaimer') && $this->settings->disclaimer){
        echo '<div class="well well-small" id="ip-disclaimer">'.$this->settings->disclaimer.'</div>';
    }
    // display footer if enabled
    if ($this->settings->footer == 1) echo ipropertyHTML::buildThinkeryFooter(); 
    ?>
</div>
