<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

$this->agents                 = ipropertyHTML::getAvailableAgents($this->p->id);
$this->openhouses             = ipropertyHTML::getOpenHouses($this->p->id);
$this->property_full_address  = ipropertyHTML::getFullAddress($this->p);
?>
<?php if ($this->print): ?>
<div class="ip-print-icon pull-right">
    <?php echo JHtml::_('icon.print_screen'); ?>
</div>
<?php endif; ?>

<h1><?php echo $this->escape($this->iptitle); ?> <small class="ip-detail-price"><?php echo $this->p->formattedprice; ?></small></h1>
<table class="ip-print-table">
    <tr>
        <td class="ip-print-leftcol">
            <?php echo ipropertyHTML::getThumbnail($this->p->id, '', '', 500, '', '', false); ?>
            <hr />
            <table width="100%" class="ip-print-desc-table">
                <tr>
                    <td>
                        <?php echo JHTML::_('content.prepare', $this->p->description ); ?>
                        <hr />
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php
                            if($this->amenities)
                            {
                                $amenities = array(
                                    'general' => array(),
                                    'interior' => array(),
                                    'exterior' => array()
                                );
                                foreach ($this->amenities as $amen)
                                {
                                    switch ($amen->cat)
                                    {
                                        case 0:
                                            $amenities['general'][] = $amen;
                                            break;
                                        case 1:
                                            $amenities['interior'][] = $amen;
                                            break;
                                        case 2:
                                            $amenities['exterior'][] = $amen;
                                            break;
                                        default:
                                            $amenities['general'][] = $amen;
                                            break;
                                    }
                                }

                                foreach($amenities as $k => $a)
                                {
                                    $amen_n     = (count($a));
                                    if($amen_n > 0) 
                                    {
                                        switch($k)
                                        {
                                            case 'general':
                                                $amen_label = JText::_('COM_IPROPERTY_GENERAL_AMENITIES');
                                                break;
                                            case 'interior':
                                                $amen_label = JText::_('COM_IPROPERTY_INTERIOR_AMENITIES');
                                                break;
                                            case 'exterior':
                                                $amen_label = JText::_('COM_IPROPERTY_EXTERIOR_AMENITIES');
                                                break;
                                        }
                                        $amen_left  = '';
                                        $amen_right = '';

                                        for ($i = 0; $i < $amen_n; $i++)
                                        {
                                            if ($i < ($amen_n/2))
                                            {
                                                $amen_left  = $amen_left.'<li class="ip_checklist"><span class="icon-ok"></span> '.$a[$i]->title.'</li>';
                                            }
                                            elseif ($i >= ($amen_n/2))
                                            {
                                                $amen_right = $amen_right.'<li class="ip_checklist"><span class="icon-ok"></span> '.$a[$i]->title.'</li>';
                                            }
                                        }

                                        echo '
                                        <h5><strong>'.$amen_label.'</strong></h5>
                                        <table width="100%" class="ip-print-amens-table">                                        
                                            <tr>
                                                <td width="50%">
                                                    <ul class="nav nav-stacked ip-amen-left">'.$amen_left.'</ul>
                                                </td>
                                                <td width="50%">
                                                    <ul class="nav ip-amen-right">'.$amen_right.'</ul>
                                                </td>
                                            </tr>
                                        </table>';
                                    }
                                }
                            }                            
                        ?>
                    </td>
                </tr>
            </table>
            <hr />
            <table width="100%" class="ip-print-details-table">
                <tr>
                    <td width="50%">
                        <?php if ($this->p->beds): ?>
                        <div><strong><?php echo JText::_('COM_IPROPERTY_BEDS'); ?>:</strong> <?php echo $this->p->beds; ?></div>
                        <?php endif; ?>
                        <?php if ($this->p->baths && $this->p->baths != '0.00'): ?>
                        <div><strong><?php echo JText::_('COM_IPROPERTY_BATHS'); ?>:</strong> <?php echo $this->p->baths; ?></div>
                        <?php endif; ?>
                        <?php if ($this->p->sqft): ?>
                        <div><strong><?php echo (!$this->settings->measurement_units) ? JText::_('COM_IPROPERTY_SQFT' ) : JText::_('COM_IPROPERTY_SQM'); ?>:</strong> <?php echo is_numeric($this->p->sqft) ? number_format($this->p->sqft) : $this->p->sqft; ?></div>
                        <?php endif; ?>
                        <?php if ($this->p->lotsize): ?>
                        <div><strong><?php echo JText::_('COM_IPROPERTY_LOT_SIZE' ); ?>:</strong> <?php echo is_numeric($this->p->lotsize) ? number_format($this->p->lotsize) : $this->p->lotsize; ?></div>
                        <?php endif; ?>
                        <?php 
                        $misc_details_left = array('lot_acres','lot_type','heat','cool','fuel','siding','roof','reception','tax','income');

                        foreach ($misc_details_left as $mdl)
                        {
                            if ($this->p->$mdl)
                            {
                                ?>
                                <div><strong><?php echo JText::_('COM_IPROPERTY_'.strtoupper($mdl)); ?>:</strong> <?php echo $this->p->$mdl; ?></div>
                                <?php
                            }
                        }
                        ?>
                    </td>
                    <td width="50%">
                        <?php 
                        $misc_details_right = array('yearbuilt','zoning','propview','school_district','style','garage_type','garage_size');
                        foreach ($misc_details_right as $mdr)
                        {
                            if ($this->p->$mdr)
                            {
                                if($mdr == 'yearbuilt'){
                                    $tag = 'year_built';
                                } else {
                                    $tag = $mdr;
                                }
                                ?>
                                <div><strong><?php echo JText::_('COM_IPROPERTY_'.strtoupper($tag)); ?>:</strong> <?php echo $this->p->$mdr; ?></div>
                                <?php
                            }
                        }
                        ?>
                        <?php if($this->settings->adv_show_wf && $this->p->frontage): ?>
                            <div><strong><?php echo JText::_('COM_IPROPERTY_FRONTAGE'); ?>:</strong> <?php echo JText::_('COM_IPROPERTY_YES'); ?></div>
                        <?php endif; ?>
                        <?php if($this->settings->adv_show_reo && $this->p->reo): ?>
                            <div><strong><?php echo JText::_('COM_IPROPERTY_REO'); ?>:</strong> <?php echo JText::_('COM_IPROPERTY_YES'); ?></div>
                        <?php endif; ?>
                        <?php if($this->settings->adv_show_hoa && $this->p->hoa): ?>
                            <div><strong><?php echo JText::_('COM_IPROPERTY_HOA'); ?>:</strong> <?php echo JText::_('COM_IPROPERTY_YES'); ?></div>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </td>
        <td class="ip-print-rightcol">
            <?php echo $this->loadTemplate('sidebar'); ?>              
        </td>
    </tr>
</table>
<?php if($this->p->terms) echo '<div class="ip-terms">'.$this->p->terms.'</div>'; ?>