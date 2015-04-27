<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
JHtml::_('bootstrap.tooltip');
JHtml::_('formbehavior.chosen', 'select');

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$colspan    = 6;

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

<form action="<?php echo JRoute::_('index.php?option=com_iproperty&view=amenities'); ?>" method="post" name="adminForm" id="adminForm">
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

         <table class="table" id="amenityList">
            <thead>
                <tr>               
                    <th width="1%" class="hidden-phone">
                        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                    </th>
                    <th width="25%"><?php echo JHtml::_('grid.sort',  'COM_IPROPERTY_TITLE', 'title', $listDirn, $listOrder); ?></th>
                    <th width="24%" class="hidden-phone"><?php echo JHtml::_('grid.sort',  'COM_IPROPERTY_CATEGORY', 'cat', $listDirn, $listOrder); ?></th>
                    <th width="1%" class="hidden-phone">&nbsp;</th>
                    <th width="25%" class="hidden-phone"><?php echo JHtml::_('grid.sort',  'COM_IPROPERTY_TITLE', 'title', $listDirn, $listOrder); ?></th>
                    <th width="24%" class="hidden-phone"><?php echo JHtml::_('grid.sort',  'COM_IPROPERTY_CATEGORY', 'cat', $listDirn, $listOrder); ?></th>
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
                $amenity_cats = array(0 => JText::_('COM_IPROPERTY_GENERAL_AMENITIES'), 1 => JText::_('COM_IPROPERTY_INTERIOR_AMENITIES'), 2 => JText::_('COM_IPROPERTY_EXTERIOR_AMENITIES'));
                if(count($this->items) > 0):
                    echo '<tr>
                            <td colspan="3" style="border-right: solid 1px #d6d6d6;" width="50%" valign="top">
                                <table class="table table-striped">';  
                                    $x = 0;
                                    foreach ($this->items as $i => $item) : ?>
                                        <tr class="row<?php echo $i % 2; ?>">
                                            <td width="1%"><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
                                            <td width="25%" align="left">
                                                <a href="<?php echo JRoute::_('index.php?option=com_iproperty&task=amenity.edit&id='.(int) $item->id); ?>"><?php echo $item->title; ?></a>
                                            </td>
                                            <td width="24%" class="center hidden-phone">
                                                <select name="amen_cat_<?php echo $item->id; ?>" class="inputbox" onchange="document.getElementById('cb<?php echo $i; ?>').checked = true;">
                                                    <?php echo JHtml::_('select.options', $this->catfield->getOptions(), 'value', 'text', $item->cat);?>
                                                </select>                            
                                            </td>
                                        </tr>
                                        <?php 
                                        $x++;
                                        if($x == 10 && $x != count($this->items)){
                                            echo '</table>
                                                </td>
                                                <td colspan="3" width="50%" valign="top">
                                                    <table class="table table-striped">';
                                        }
                                    endforeach;
                        echo '</table>
                            </td>
                        </tr>';
                else:
                ?>
                    <tr>
                        <td colspan="<?php echo $colspan; ?>" class="center">
                            <?php echo JText::_('COM_IPROPERTY_NO_RESULTS'); ?>
                        </td>
                    </tr>
                <?php
                endif; ?>
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