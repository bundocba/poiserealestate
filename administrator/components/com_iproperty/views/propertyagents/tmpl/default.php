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

$user		= JFactory::getUser();
$userId		= $user->get('id');

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$ordering   = ($listOrder == 'ordering');
$colspan    = 12;

$sortFields = $this->getSortFields();
?>

<script type="text/javascript">
    Joomla.orderTable = function() {
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>') {
			dirn = 'asc';
		} else {
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_iproperty&view=propertyagents'); ?>" method="post" name="adminForm" id="adminForm">
<?php if (!empty( $this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
<?php else : ?>
    <div id="j-main-container">
<?php endif;?>
        <?php IpropertyAdmin::buildAdminToolbar(); ?>
        <div id="filter-bar" class="btn-toolbar">
            <div class="filter-search btn-group pull-left">
                <label class="element-invisible" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
                <input type="text" name="filter_search" class="inputbox" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>" />
            </div>
            <div class="btn-group pull-left hidden-phone">
                <button class="btn tip" type="submit" rel="tooltip" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
                <button class="btn tip" type="button" onclick="document.id('filter_search').value='';this.form.submit();" rel="tooltip" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
            </div>
            <div class="btn-group pull-right hidden-phone">
                <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
                <?php echo $this->pagination->getLimitBox(); ?>
            </div>
            <div class="btn-group pull-right hidden-phone">
                <label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
                <select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
                    <option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
                    <option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
                    <option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
                </select>
            </div>
            <div class="btn-group pull-right">
                <label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
                <select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
                    <option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
                    <?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
                </select>
            </div>
        </div>
        <div class="clearfix"> </div>

        <table class="table table-striped" id="propertyList">
            <thead>
                <tr>
                    <th width="1%" class="hidden-phone">
                        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                    </th>
                    <th width="1%" class="nowrap hidden-phone">
                        <?php echo JText::_('COM_IPROPERTY_IMAGE'); ?>
                    </th>
                    <th width="25%" class="nowrap">
                        <?php echo JHTML::_('grid.sort', JText::_('COM_IPROPERTY_STREET' ), 'street', $listDirn, $listOrder ); ?> / 
                        <?php echo JHTML::_('grid.sort', JText::_('COM_IPROPERTY_TITLE' ), 'title', $listDirn, $listOrder ); ?> /
                        <?php echo JHTML::_('grid.sort', JText::_('COM_IPROPERTY_REF' ), 'property_name', $listDirn, $listOrder ); ?>
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
                        <?php echo JText::_('COM_IPROPERTY_CATEGORIES'); ?>
                    </th>
                    <th width="5%" class="nowrap hidden-phone">
                        <?php echo JHTML::_('grid.sort', JText::_('COM_IPROPERTY_HITS' ), 'hits', $listDirn, $listOrder ); ?>
                    </th>
                    <th width="5%" class="nowrap hidden-phone">
                        <?php echo JHTML::_('grid.sort', JText::_('COM_IPROPERTY_ACCESS' ), 'access', $listDirn, $listOrder ); ?>
                    </th>
                    <th width="5%" class="nowrap hidden-phone center">
                        <?php echo JHtml::_('grid.sort', 'JSTATUS', 'state', $listDirn, $listOrder); ?>
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
                        $canEdit        = $this->ipauth->canEditProp($item->id) && !$item->checked_out;
                        $canPublish     = $this->ipauth->canPublishProp($item->id, ($item->state == 1) ? 0 : 1) && !$item->checked_out;
                        $canFeature     = $this->ipauth->canFeatureProp($item->id, ($item->featured == 1) ? 0 : 1) && !$item->checked_out;
                        $canOrder       = ($this->ipauth->getAdmin() || $this->ipauth->getSuper()) && !$item->checked_out;
                        $canCheckin     = $user->authorise('core.manage',		'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
                        $canApprove     = $this->ipauth->canApproveProp($item->id, ($item->approved == 1) ? 0 : 1) && !$item->checked_out;
                        $linktitle      = ipropertyHTML::getStreetAddress($this->settings, $item);
                        ?>
                        <tr class="row<?php echo $i % 2; ?>">
                            <td class="center hidden-phone">
                                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                            </td>
                            <td class="center hidden-phone"><?php echo ipropertyHTML::getThumbnail($item->id, '', $item->street_address, 50); ?></td>
                            <td class="nowrap has-context">
                                <div class="pull-left">
                                    <?php if ($item->checked_out) : ?>
                                        <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'properties.', $canCheckin); ?>
                                    <?php endif; ?>
                                    <?php if ($canEdit) : ?>
                                        <a href="<?php echo JRoute::_('index.php?option=com_iproperty&task=propertyagent.edit&id='.(int) $item->id); ?>">
                                            <?php echo $this->escape($linktitle); ?>
                                        </a>
                                    <?php else : ?>
                                        <?php echo $this->escape($linktitle); ?>                                    
                                    <?php endif; ?>   
                                    <?php echo ($this->settings->showtitle && $item->title) ? '<br />'.$item->street_address : ''; ?>
                                    <?php echo ($item->agent_notes) ? ' <span class="editlinktip hasTooltip" title="'.JText::_('COM_IPROPERTY_AGENT_NOTES' ).'::'.nl2br($item->agent_notes).'"><i class="icon-vcard"></i></span>' : ''; ?>
                                    <p class="small">
                                        <?php                                                                                 
                                            echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));
                                            echo ($item->property_name) ? '<br />'.JText::_('COM_IPROPERTY_REF' ).': '.$item->property_name : ''; 
                                        ?>
                                    </p>
                                </div>
                                <div class="pull-right">
                                    <?php
                                        // Create dropdown items
                                        if($canEdit):
                                            JHtml::_('dropdown.edit', $item->id, 'propertyagent.');
                                            JHtml::_('dropdown.divider');
                                        endif;                                   

                                        if($canPublish):
                                            if ($item->state) :
                                                JHtml::_('dropdown.unpublish', 'cb' . $i, 'propertyagents.');
                                            else :
                                                JHtml::_('dropdown.publish', 'cb' . $i, 'propertyagents.');
                                            endif;
                                            JHtml::_('dropdown.divider');
                                        endif;    

                                        if($this->settings->edit_rights && $canApprove):
                                            if ($item->approved) :
                                                JHtml::_('dropdown.addCustomItem', JText::_('COM_IPROPERTY_UNAPPROVE'), '', 'onclick="contextAction(\'cb'.$i.'\', \'propertyagents.unapprove\')"');
                                            else :
                                                JHtml::_('dropdown.addCustomItem', JText::_('COM_IPROPERTY_APPROVE'), '', 'onclick="contextAction(\'cb'.$i.'\', \'propertyagents.approve\')"');
                                            endif;
                                            JHtml::_('dropdown.divider');
                                        endif;

                                        if($canFeature):
                                            if ($item->featured) :
                                                JHtml::_('dropdown.addCustomItem', JText::_('COM_IPROPERTY_UNFEATURE'), '', 'onclick="contextAction(\'cb'.$i.'\', \'propertyagents.unfeature\')"');
                                            else :
                                                JHtml::_('dropdown.addCustomItem', JText::_('COM_IPROPERTY_FEATURE'), '', 'onclick="contextAction(\'cb'.$i.'\', \'propertyagents.feature\')"');
                                            endif;
                                            JHtml::_('dropdown.divider');
                                        endif;                                    

                                        if ($item->checked_out && $canCheckin) :
                                            JHtml::_('dropdown.checkin', 'cb' . $i, 'properties.');
                                        endif;

                                        // Render dropdown list
                                        echo JHtml::_('dropdown.render');
                                    ?>
                                </div>
                            </td>
                            <td class="small hidden-phone">
                                <?php
                                    $agents = ipropertyHTML::getAvailableAgents($item->id);
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
                                        echo '<span class="hasTooltip invalid" title="'.JText::_('COM_IPROPERTY_BEDS' ).' :: '.JText::_('COM_IPROPERTY_BEDS_NOT_IN_RANGE' ).'">'.$item->beds.'</span>';
                                    }else{
                                        echo $item->beds;
                                    }
                                ?>
                                /
                                <?php
                                    if(($item->baths < $this->settings->adv_baths_low) || ($item->baths > $this->settings->adv_baths_high)){
                                        echo '<span class="hasTooltip invalid" title="'.JText::_('COM_IPROPERTY_BATHS' ).' :: '.JText::_('COM_IPROPERTY_BATHS_NOT_IN_RANGE' ).'">'.$item->baths.'</span>';
                                    }else{
                                        echo $item->baths;
                                    }
                                ?>
                                /
                                <?php
                                    if(($item->sqft < $this->settings->adv_sqft_low) || ($item->sqft > $this->settings->adv_sqft_high)){
                                        echo '<span class="hasTooltip invalid" title="'.JText::_('COM_IPROPERTY_SQFT' ).' :: '.JText::_('COM_IPROPERTY_SQFT_NOT_IN_RANGE' ).'">'.$item->sqft.'</span>';
                                    }else{
                                        echo $item->sqft;
                                    }
                                ?>
                            </td>
                            <td class="small hidden-phone center">
                                <?php
                                    if((($item->price < $this->settings->adv_price_low) || ($item->price > $this->settings->adv_price_high)) && !$this->settings->adv_nolimit){
                                        echo '<span class="hasTooltip invalid" title="'.JText::_('COM_IPROPERTY_PRICE' ).' :: '.JText::_('COM_IPROPERTY_PRICE_NOT_IN_RANGE' ).'">'.ipropertyHTML::getFormattedPrice($item->price, $item->stype_freq).'</span>';
                                    }else{
                                        echo ipropertyHTML::getFormattedPrice($item->price, $item->stype_freq, '', '', $item->price2, true);
                                        if($item->call_for_price) echo ' [<span class="invalid">'.JText::_('COM_IPROPERTY_CALL_FOR_PRICE' ).'</span>]';
                                    }
                                ?>
                            </td>
                            <td class="small hidden-phone">
                            <?php
                                $cats   = ipropertyHTML::getAvailableCats($item->id);
                                if($cats){
                                    foreach( $cats as $c ){
                                        echo ipropertyHTML::getCatIcon($c, 20, true).' ';
                                    }
                                }else{
                                    echo '<span class="invalid">'.JText::_('COM_IPROPERTY_NONE' ).'</span>';
                                }
                            ?>
                            </td>
                            <td class="small hidden-phone center">
                                <?php echo $item->hits; ?>
                            </td>
                            <td class="small hidden-phone center">
                                <?php echo $item->groupname;?>
                            </td>
                            <td class="small hidden-phone center">
                                <div class="btn-group">
                                    <?php if($this->settings->edit_rights && $canApprove): ?>
                                        <?php echo JHtml::_('ipadministrator.approved', $item->approved, $i, $canApprove, 'properties'); ?>
                                    <?php endif; ?>
                                    <?php echo JHtml::_('jgrid.published', $item->state, $i, 'properties.', $canPublish, 'cb'); ?>
                                    <?php echo JHtml::_('ipadministrator.featured', $item->featured, $i, $canFeature, 'properties'); ?>
                                </div>
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
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
<div class="clearfix"></div>
<?php echo ipropertyAdmin::footer( ); ?>