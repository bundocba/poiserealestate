<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
JHtml::_('behavior.modal');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>

<form action="<?php echo JRoute::_('index.php?option=com_iproperty&view=categories'); ?>" method="post" name="adminForm" id="adminForm">
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
            <div class="filter-search btn-group pull-right">  
                <div class="span6">
                    <div>
                        <label class="filter-search-lbl" for="catrecurse" class="element-invisible"><?php echo JText::_('COM_IPROPERTY_APPLY_TO_SUBTREE'); ?></label>
                    </div>
                </div>
                <div class="span4 pull-right">
                    <fieldset class="radio btn-group">
                        <input type="radio" id="catrecurse0" name="catrecurse"<?php echo (!JRequest::getInt('catrecurse', 0)) ? ' checked="checked"' : ''; ?> value="0" />
                        <label for="catrecurse0"><?php echo JText::_('JNO'); ?></label>
                        <input type="radio" id="catrecurse1" name="catrecurse"<?php echo (JRequest::getInt('catrecurse', 0)) ? ' checked="checked"' : ''; ?> value="1" />
                        <label for="catrecurse1"><?php echo JText::_('JYES'); ?></label>
                    </fieldset>
                </div>            
            </div>
        </div>
        <div class="clearfix"> </div>

        <table class="table table-striped" id="categoryList">
            <thead>
                <tr>   
                    <th width="1%" class="nowrap center hidden-phone">
                        <?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'c.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
                    </th>
                    <th width="1%" class="hidden-phone">
                        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                    </th>               
                    <th width="5%" class="hidden-phone"><?php echo JText::_('COM_IPROPERTY_IMAGE'); ?></th>
                    <th width="30%"><?php echo JText::_('JGLOBAL_TITLE'); ?></th>
                    <th width="40%" class="hidden-phone"><?php echo JText::_('JGLOBAL_DESCRIPTION'); ?></th>
                    <th width="5%" class="center"><?php echo JText::_('JSTATUS'); ?></th>
                    <th width="10%" class="center hidden-phone"><?php echo JText::_('JGRID_HEADING_ACCESS'); ?></th>
                    <th width="5%" class="center hidden-phone"><?php echo JText::_('COM_IPROPERTY_ENTRIES'); ?></th>                
                    <th width="1%" class="center hidden-phone"><?php echo JText::_('JGRID_HEADING_ID'); ?></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td colspan="9">
                        &nbsp;
                    </td>
                </tr>
            </tfoot>
            <tbody>

                <?php

                    $model          = $this->getModel('categories');
                    $i              = 0;
                    $parent         = 0;
                    $spacer         = '';
                    $published      = 1;
                    $model->catLoop($i, $parent, $spacer, $published, $this->settings, $this->ipauth, $listOrder, $listDirn);
                ?>
            </tbody>
        </table>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
<div class="clearfix"></div>
<?php echo ipropertyAdmin::footer( ); ?>