<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.modal');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');

require_once JPATH_ROOT . '/components/com_iproperty/helpers/route.php';

$user		= JFactory::getUser();
$userId		= $user->get('id');

$field		= JRequest::getCmd('field');
$function	= 'ipSelectListing_'.$field;
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$colspan    = 8;

$app        = JFactory::getApplication();
// If calling from front end, restrict propert list to agent or company where applicable
if($app->getName() == 'site')
{
    $vlink = $app->input->getCmd('view');
    $vlink .= ($app->input->getInt('id')) ? '&id='.$app->input->getInt('id') : '';
    $search_filter = 'keyword';

}else{ // If calling from adim panel, use the properties view since it already uses ACL to restrict access
    $vlink = 'properties';
    $search_filter = 'search';
}
?>

<form action="<?php echo JRoute::_('index.php?option=com_iproperty&view='.$vlink.'&layout=modal&tmpl=component'); ?>" method="post" name="adminForm" id="adminForm">
    <div id="j-main-container">
        <div id="filter-bar" class="btn-toolbar">
            <div class="filter-search btn-group pull-left">
                <label class="element-invisible" for="filter_<?php echo $search_filter; ?>"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
                <input type="text" name="filter_<?php echo $search_filter; ?>" class="inputbox" id="filter_<?php echo $search_filter; ?>" value="<?php echo $this->escape($this->state->get('filter.'.$search_filter)); ?>" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>" />
            </div>
            <div class="btn-group pull-left hidden-phone">
                <button class="btn tip" type="submit" rel="tooltip" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
                <button class="btn tip" type="button" onclick="document.id('filter_<?php echo $search_filter; ?>').value='';this.form.submit();" rel="tooltip" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
            </div>
            <div class="btn-group pull-right hidden-phone">
                <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
                <?php echo $this->pagination->getLimitBox(); ?>
            </div>
        </div>
        <div class="clearfix"> </div>

        <table class="table table-striped" id="propertyList">
            <thead>
                <tr>
                    <th width="1%" class="nowrap hidden-phone">
                        <?php echo JText::_('COM_IPROPERTY_IMAGES'); ?>
                    </th>
                    <th width="25%" class="nowrap">
                        <?php echo JHTML::_('grid.sort', JText::_('COM_IPROPERTY_STREET' ), 'street', $listDirn, $listOrder ); ?> / 
                        <?php echo JHTML::_('grid.sort', JText::_('COM_IPROPERTY_TITLE' ), 'title', $listDirn, $listOrder ); ?> /
                        <?php echo JHTML::_('grid.sort', JText::_('COM_IPROPERTY_REF' ), 'mls_id', $listDirn, $listOrder ); ?>
                    </th>
                    <th width="15%" class="nowrap hidden-phone">
                        <?php echo JText::_('COM_IPROPERTY_AGENTS'); ?>
                    </th>
                    <th width="15%" class="nowrap hidden-phone">
                        <?php echo JHTML::_('grid.sort', JText::_('COM_IPROPERTY_LOCATION' ), 'city', $listDirn, $listOrder ); ?>
                    </th>
                    <th width="10%" class="nowrap hidden-phone">
                        <?php echo JHTML::_('grid.sort', JText::_('COM_IPROPERTY_BEDS' ), 'beds', $listDirn, $listOrder ); ?> /
                        <?php echo JHTML::_('grid.sort', JText::_('COM_IPROPERTY_BATHS' ), 'baths', $listDirn, $listOrder ); ?> /
                        <?php echo JHTML::_('grid.sort', (!$this->settings->measurement_units) ? JText::_('COM_IPROPERTY_SQFT' ) : JText::_('COM_IPROPERTY_SQM' ), 'sqft', $listDirn, $listOrder ); ?>
                    </th>
                    <th width="5%" class="nowrap hidden-phone">
                        <?php echo JHTML::_('grid.sort', JText::_('COM_IPROPERTY_PRICE' ), 'price', $listDirn, $listOrder ); ?>
                    </th>
                    <th width="5%" class="nowrap hidden-phone">
                        <?php echo JText::_('COM_IPROPERTY_CATEGORY'); ?>
                    </th>              
                    <th width="1%" class="nowrap">
                        <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
                    </th>
                </tr>
            </thead>

            <tfoot>
                <tr>
                    <td colspan="<?php echo $colspan; ?>">
                        <?php echo $this->pagination->getListFooter(); ?>
                    </td>
                </tr>
            </tfoot>

            <tbody>
                <?php 
                if(count($this->items) > 0):
                    foreach ($this->items as $i => $item) :
                        $agents         = ipropertyHTML::getAvailableAgents($item->id);
                        $cats           = ipropertyHTML::getAvailableCats($item->id);
                        $linktitle      = ipropertyHTML::getStreetAddress($this->settings, $item);
                        ?>
                        <tr class="row<?php echo $i % 2; ?>">
                            <td class="center hidden-phone"><?php echo ipropertyHTML::getThumbnail($item->id, '', $linktitle, 50); ?></td>
                            <td>
                                <a href="javascript:void(0);" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($linktitle)); ?>', '<?php echo $this->escape(ipropertyHelperRoute::getPropertyRoute($item->id.':'.$item->alias)); ?>');"><?php echo $this->escape($linktitle); ?></a>
                                <?php echo ($this->settings->showtitle && $item->title) ? '<br /><span class="small">'.$item->street_address.'</small>' : ''; ?>
                            </td>                            
                            <td class="small hidden-phone">
                                <?php                                    
                                    $x = 0;
                                    if($agents){
                                        foreach($agents AS $a){
                                            echo ipropertyHTML::getAgentName($a->id);
                                            $x++;
                                            if($x < count($agents)) echo '<br />';
                                        }
                                    }else{
                                        echo '--';
                                    }
                                ?>
                            </td>
                            <td class="small hidden-phone">
                                <?php
                                $location = '';
                                if($item->city) $location .= $item->city;
                                if($item->locstate) $location .= ', '.ipropertyHTML::getstatename($item->locstate);
                                if($item->province) $location .= ', '.$item->province;
                                echo ($location) ? $location : '--';
                                ?>
                            </td>
                            <td class="small hidden-phone center">
                                <?php
                                    if(($item->beds < $this->settings->adv_beds_low) || ($item->beds > $this->settings->adv_beds_high)){
                                        echo '<span class="hasTip invalid" title="'.JText::_('COM_IPROPERTY_BEDS' ).' :: '.JText::_('COM_IPROPERTY_BEDS' ).'">'.$item->beds.'</span>';
                                    }else{
                                        echo $item->beds;
                                    }
                                ?>
                                /
                                <?php
                                    if(($item->baths < $this->settings->adv_baths_low) || ($item->baths > $this->settings->adv_baths_high)){
                                        echo '<span class="hasTip invalid" title="'.JText::_('COM_IPROPERTY_BATHS' ).' :: '.JText::_('COM_IPROPERTY_BATHS' ).'">'.$item->baths.'</span>';
                                    }else{
                                        echo $item->baths;
                                    }
                                ?>
                                /
                                <?php
                                    if(($item->sqft < $this->settings->adv_sqft_low) || ($item->sqft > $this->settings->adv_sqft_high)){
                                        echo '<span class="hasTip invalid" title="'.JText::_('COM_IPROPERTY_SQFT' ).' :: '.JText::_('COM_IPROPERTY_SQFT' ).'">'.$item->sqft.'</span>';
                                    }else{
                                        echo $item->sqft;
                                    }
                                ?>
                            </td>
                            <td class="small hidden-phone center">
                                <?php
                                    if((($item->price < $this->settings->adv_price_low) || ($item->price > $this->settings->adv_price_high)) && !$this->settings->adv_nolimit){
                                        echo '<span class="hasTip invalid" title="'.JText::_('COM_IPROPERTY_PRICE' ).' :: '.JText::_('COM_IPROPERTY_PRICE' ).'">'.ipropertyHTML::getFormattedPrice($item->price, $item->stype_freq).'</span>';
                                    }else{
                                        echo ipropertyHTML::getFormattedPrice($item->price, $item->stype_freq, '', '', $item->price2, true);
                                        if($item->call_for_price) echo ' [<span class="invalid">'.JText::_('COM_IPROPERTY_CALL_FOR_PRICE' ).'</span>]';
                                    }
                                ?>
                            </td>
                            <td class="small hidden-phone">
                            <?php                                
                                if($cats){
                                    foreach( $cats as $c ){
                                        echo ipropertyHTML::getCatIcon($c, 20, true).' ';
                                    }
                                }else{
                                    echo '<span class="invalid">'.JText::_('COM_IPROPERTY_NONE' ).'</span>';
                                }
                            ?>
                            </td>                            
                            <td class="center">
                                <?php echo $item->id; ?>
                            </td>
                        </tr>
                    <?php 
                    endforeach;
                else:
                ?>
                    <tr>
                        <td colspan="<?php echo $colspan; ?>" class="center">
                            <?php echo JText::_('COM_IPROPERTY_NO_RESULTS'); ?>
                        </td>
                    </tr>
                <?php
                endif;
                ?>
            </tbody>
        </table>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="field" value="<?php echo $this->escape($field); ?>" />
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
        <input type="hidden" name="ipquicksearch" value="1" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
<div class="clearfix"></div>