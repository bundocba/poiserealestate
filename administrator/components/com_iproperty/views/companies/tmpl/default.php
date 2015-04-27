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
JHtml::_('behavior.modal');
JHtml::_('bootstrap.tooltip');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');

$user       = JFactory::getUser();
$userId     = $user->get('id');

$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$colspan    = 11;
$saveOrder	= $listOrder == 'ordering';
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_iproperty&task=companies.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'companyList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

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

<form action="<?php echo JRoute::_('index.php?option=com_iproperty&view=companies'); ?>" method="post" name="adminForm" id="adminForm">
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

        <table class="table table-striped" id="companyList">
            <thead>
                <tr>
                    <th width="1%" class="nowrap center hidden-phone">
                        <?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
                    </th>
                    <th width="1%" class="hidden-phone">
                        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                    </th>
                    <th width="1%" class="nowrap hidden-phone">
                        <?php echo JText::_('COM_IPROPERTY_IMAGE'); ?>
                    </th>
                    <th width="20%" class="nowrap">
                        <?php echo JHTML::_('grid.sort', JText::_('COM_IPROPERTY_TITLE' ), 'c.name', $listDirn, $listOrder); ?>
                    </th>
                    <th width="20%" class="nowrap">
                        <?php echo JHTML::_('grid.sort', JText::_('COM_IPROPERTY_EMAIL' ), 'c.email', $listDirn, $listOrder); ?>
                    </th>
                    <th width="15%" class="nowrap hidden-phone">
                        <?php echo JHTML::_('grid.sort', JText::_('COM_IPROPERTY_PHONE' ), 'c.phone', $listDirn, $listOrder); ?>
                    </th>
                    <th width="10%" class="nowrap hidden-phone">
                        <?php echo JHTML::_('grid.sort', JText::_('COM_IPROPERTY_WEBSITE' ), 'c.website', $listDirn, $listOrder); ?>
                    </th>
                    <th width="5%" class="nowrap hidden-phone">
                        <?php echo JHtml::_('grid.sort', 'JSTATUS', 'state', $listDirn, $listOrder); ?>
                    </th>
                    <th width="5%" class="nowrap hidden-phone">
                        <?php echo JHtml::_('grid.sort', 'COM_IPROPERTY_AGENTS', 'agent_count', $listDirn, $listOrder); ?>
                    </th>
                    <th width="5%" class="nowrap hidden-phone">
                        <?php echo JHtml::_('grid.sort', 'COM_IPROPERTY_PROPERTIES', 'prop_count', $listDirn, $listOrder); ?>
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

            <tbody>
                <?php
                if(count($this->items) > 0):
                    foreach ($this->items as $i => $item) :
                        $canEdit        = $this->ipauth->canEditCompany($item->id) && !$item->checked_out;
                        $canPublish     = $this->ipauth->canPublishCompany($item->id) && !$item->checked_out;
                        $canFeature     = $this->ipauth->canFeatureCompany($item->id) && !$item->checked_out;
                        $canOrder       = $this->ipauth->getAdmin() && !$item->checked_out;
                        $canCheckin     = $user->authorise('core.manage',       'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
                        $icon           = ipropertyHTML::getIconpath($item->icon, 'company');
                        ?>
                        <tr class="row<?php echo $i % 2; ?>">
                            <td class="order nowrap center hidden-phone">
                            <?php if ($canOrder) :
                                $disableClassName = '';
                                $disabledLabel	  = '';

                                if (!$saveOrder) :
                                    $disabledLabel    = JText::_('JORDERINGDISABLED');
                                    $disableClassName = 'inactive tip-top';
                                endif; ?>
                                <span class="sortable-handler <?php echo $disableClassName?>" title="<?php echo $disabledLabel?>" rel="tooltip">
                                    <i class="icon-menu"></i>
                                </span>
                                <input type="text" style="display:none"  name="order[]" size="5" value="<?php echo $item->ordering;?>" class="width-20 text-area-order " />
                            <?php else : ?>
                                <span class="sortable-handler inactive" >
                                    <i class="icon-menu"></i>
                                </span>
                            <?php endif; ?>
                            </td>
                            <td class="center hidden-phone">
                                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                            </td>
                            <td class="center hidden-phone"><?php echo ($item->icon) ? '<a href="'.$icon.'" class="modal"><img src="'.$icon.'" width="20" style="border: solid 1px #377391 !important;" /></a>' : '--'; ?>
                            <td class="nowrap has-context">
                                <div class="pull-left">
                                    <?php if ($item->checked_out) : ?>
                                        <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'companies.', $canCheckin); ?>
                                    <?php endif; ?>
                                    <?php if ($canEdit) : ?>
                                        <a href="<?php echo JRoute::_('index.php?option=com_iproperty&task=company.edit&id='.(int) $item->id); ?>">
                                            <?php echo $this->escape($item->name); ?>
                                        </a>
                                    <?php else : ?>
                                        <?php echo $this->escape($item->name); ?>
                                    <?php endif; ?>
                                    <p class="small">
                                        <?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?>
                                    </p>                                
                                </div>
                                <div class="pull-right">
                                    <?php
                                        // Create dropdown items
                                        if($canEdit):
                                            JHtml::_('dropdown.edit', $item->id, 'company.');
                                            JHtml::_('dropdown.divider');
                                        endif;                                   

                                        if($canPublish):
                                            if ($item->state) :
                                                JHtml::_('dropdown.unpublish', 'cb' . $i, 'companies.');
                                            else :
                                                JHtml::_('dropdown.publish', 'cb' . $i, 'companies.');
                                            endif;
                                            JHtml::_('dropdown.divider');
                                        endif;                                    

                                        if($canFeature):
                                            if ($item->featured) :
                                                JHtml::_('dropdown.addCustomItem', JText::_('COM_IPROPERTY_UNFEATURE'), '', 'onclick="contextAction(\'cb'.$i.'\', \'companies.unfeature\')"');
                                            else :
                                                JHtml::_('dropdown.addCustomItem', JText::_('COM_IPROPERTY_FEATURE'), '', 'onclick="contextAction(\'cb'.$i.'\', \'companies.feature\')"');
                                            endif;
                                            JHtml::_('dropdown.divider');
                                        endif;                                    

                                        if ($item->checked_out && $canCheckin) :
                                            JHtml::_('dropdown.checkin', 'cb' . $i, 'companies.');
                                        endif;

                                        // Render dropdown list
                                        echo JHtml::_('dropdown.render');
                                    ?>
                                </div>
                            </td>
                            <td class="small"><?php echo ($item->email) ? $item->email : '--'; ?>&nbsp;</td>
                            <td class="small hidden-phone"><?php echo ($item->phone) ? $item->phone : '--'; ?>&nbsp;</td>
                            <td class="small hidden-phone"><?php echo ($item->website) ? $item->website : '--'; ?>&nbsp;</td>
                            <td class="small hidden-phone center">
                                <div class="btn-group">
                                    <?php echo JHtml::_('jgrid.published', $item->state, $i, 'companies.', $canPublish, 'cb'); ?>
                                    <?php echo JHtml::_('ipadministrator.featured', $item->featured, $i, $canFeature, 'companies'); ?>
                                </div>
                            </td>
                            <td class="small hidden-phone center">
                                <?php echo $item->agent_count; ?>
                            </td>
                            <td class="small hidden-phone center">
                                <?php echo $item->prop_count; ?>
                            </td>
                            <td class="hidden-phone center">
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
