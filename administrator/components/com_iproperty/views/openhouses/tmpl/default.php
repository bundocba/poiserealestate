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
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');

$user		= JFactory::getUser();
$userId		= $user->get('id');

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$colspan    = 9;

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

<form action="<?php echo JRoute::_('index.php?option=com_iproperty&view=openhouses'); ?>" method="post" name="adminForm" id="adminForm">
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

        <table class="table table-striped" id="openhouseList">
            <thead>
                <tr>
                    <th width="1%" class="hidden-phone">
                        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                    </th>                
                    <th width="25%" class="nowrap">
                        <?php echo JHTML::_('grid.sort', JText::_('COM_IPROPERTY_NAME' ), 'o.name', $listDirn, $listOrder ); ?> / 
                        <?php echo JHTML::_('grid.sort', JText::_('COM_IPROPERTY_STREET' ), 'p.street', $listDirn, $listOrder ); ?> /
                        <?php echo JHTML::_('grid.sort', JText::_('COM_IPROPERTY_TITLE' ), 'p.title', $listDirn, $listOrder ); ?>
                    </th>
                    <th width="10%" class="nowrap hidden-phone">
                        <?php echo JHTML::_('grid.sort', JText::_('COM_IPROPERTY_REF' ), 'p.property_name', $listDirn, $listOrder ); ?>
                    </th>
                    <th width="15%" class="nowrap">
                        <?php echo JHTML::_('grid.sort', JText::_('COM_IPROPERTY_START' ), 'o.openhouse_start', $listDirn, $listOrder ); ?>
                    </th>
                    <th width="15%" class="nowrap">
                        <?php echo JHTML::_('grid.sort', JText::_('COM_IPROPERTY_END' ), 'o.openhouse_end', $listDirn, $listOrder ); ?>
                    </th>
                    <th width="15%" class="nowrap hidden-phone">
                        <?php echo JText::_('COM_IPROPERTY_AGENT'); ?>
                    </th>
                    <th width="15%" class="nowrap hidden-phone">
                        <?php echo JHTML::_('grid.sort', JText::_('COM_IPROPERTY_COMPANY' ), 'company', $listDirn, $listOrder ); ?>
                    </th>
                    <th width="1%" class="nowrap hidden-phone">
                        <?php echo JHTML::_('grid.sort', JText::_('COM_IPROPERTY_PUBLISHED' ), 'o.state', $listDirn, $listOrder ); ?>
                    </th>
                    <th width="1%" class="nowrap hidden-phone">
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

            <?php 
                if(count($this->items) > 0):
                    foreach ($this->items as $i => $item) :
                        $canEdit        = !$item->checked_out;
                        $canPublish     = !$item->checked_out;
                        $canCheckin     = $user->authorise('core.manage',		'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
                        $expired        = ($item->openhouse_end && ($item->openhouse_end <= date('Y-m-d H:i:s'))) ? true : false;
                        $linktitle      = ipropertyHTML::getStreetAddress($this->settings, $item);
                        ?>
                        <tr class="row<?php echo $i % 2; ?>">
                            <td class="center hidden-phone">
                                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                            </td>
                            <td class="nowrap has-context">
                                <div class="pull-left">
                                    <?php if ($item->checked_out) : ?>
                                        <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'openhouses.', $canCheckin); ?>
                                    <?php endif; ?>
                                    <?php 
                                        if($item->ohname) echo '<em>'.$this->escape($item->ohname).'</em><br />';
                                        if ($canEdit){
                                            echo '<a href="'.JRoute::_('index.php?option=com_iproperty&task=openhouse.edit&id='.(int) $item->id).'">
                                                    '.$this->escape($linktitle).'
                                                  </a>';
                                        }else{ 
                                            echo $this->escape($linktitle);                                   
                                        }
                                        if($this->settings->showtitle && $item->title) echo '<br />'.$this->escape($item->street_address);
                                    ?>
                                </div>
                                <div class="pull-right">
                                    <?php
                                        // Create dropdown items
                                        if($canEdit):
                                            JHtml::_('dropdown.edit', $item->id, 'openhouse.');
                                            JHtml::_('dropdown.divider');
                                        endif;                                   

                                        if($canPublish):
                                            if ($item->state) :
                                                JHtml::_('dropdown.unpublish', 'cb' . $i, 'openhouses.');
                                            else :
                                                JHtml::_('dropdown.publish', 'cb' . $i, 'openhouses.');
                                            endif;
                                            JHtml::_('dropdown.divider');
                                        endif;                                   

                                        if ($item->checked_out && $canCheckin) :
                                            JHtml::_('dropdown.checkin', 'cb' . $i, 'openhouses.');
                                        endif;

                                        // Render dropdown list
                                        echo JHtml::_('dropdown.render');
                                    ?>
                                </div>
                            </td>
                            <td class="small center hidden-phone"><?php echo ($item->property_name) ? $item->property_name : '--'; ?></td>
                            <td class="small center"><?php echo ($item->openhouse_start) ? $item->openhouse_start : '--'; ?></td>
                            <td class="small center"><?php echo ($item->openhouse_end) ? (($expired) ? '<span class="invalid">'.$item->openhouse_end.'</span>' : $item->openhouse_end ) : '--'; ?></td>
                            <td class="small hidden-phone">
                                <?php
                                    $agents = ipropertyHTML::getAvailableAgents($item->prop_id);
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
                            <td class="hidden-phone"><?php echo ($item->company) ? ipropertyHTML::getCompanyName($item->company) : '--'; ?></td>
                            <td class="center hidden-phone">
                                <?php echo JHtml::_('jgrid.published', $item->state, $i, 'openhouses.', $canPublish, 'cb'); ?>
                            </td>
                            <td class="center hidden-phone">
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