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
JHTML::_('behavior.keepalive');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.modal');
JHtml::_('dropdown.init');
JFactory::getLanguage()->load('com_iproperty', JPATH_ADMINISTRATOR);
JFactory::getLanguage()->load('', JPATH_ADMINISTRATOR);

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$ordering   = ($listOrder == 'ordering');

$sortFields = $this->getSortFields('agent');
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

<div class="ip-manage-list<?php echo $this->pageclass_sfx; ?>">
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
    // main agent display
    if ($this->agent)
    {
        $this->k = 1;        
        $this->agents_folder = $this->ipbaseurl.'/media/com_iproperty/agents/';
        $this->agent_photo_width = $this->settings->agent_photo_width ? $this->settings->agent_photo_width : 90;
        // build agent display
        echo $this->loadTemplate('agent');
    } elseif (!$this->ipauth->getAdmin()){
        // redirect to home page if not admin or agent
        JFactory::getApplication()->redirect(JURI::root);
    }
    // Build toolbar
    echo $this->loadTemplate('toolbar');
    ?>
        
    <form action="<?php echo JRoute::_(ipropertyHelperRoute::getManageRoute().'&layout='.JFactory::getApplication()->input->get('layout','agentlist')); ?>" method="post" name="adminForm" id="adminForm">
        <div id="filter-bar" class="btn-toolbar">
            <div class="filter-search btn-group pull-left">
                <label class="element-invisible" for="filter_search"><?php echo htmlentities(JText::_('JSEARCH_FILTER_LABEL'), ENT_QUOTES, 'UTF-8'); ?></label>
                <input type="text" name="filter_search" class="inputbox input-medium" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>" />
            </div>
            <div class="btn-group pull-left hidden-phone">
                <button class="btn hasTooltip" type="submit" title="<?php echo htmlentities(JText::_('JSEARCH_FILTER_SUBMIT'), ENT_QUOTES, 'UTF-8'); ?>"><i class="icon-search"></i></button>
                <button class="btn hasTooltip" type="button" onclick="document.id('filter_search').value='';this.form.submit();" rel="tooltip" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
            </div>
            <div class="btn-group pull-right hidden-phone">
                <select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
                    <option value=""><?php echo JText::_('COM_IPROPERTY_ORDERBY');?></option>
                    <option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('COM_IPROPERTY_ASCENDING');?></option>
                    <option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('COM_IPROPERTY_DESCENDING');?></option>
                </select>
            </div>
            <div class="btn-group pull-right">
                <select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
                    <option value=""><?php echo JText::_('COM_IPROPERTY_SORTBY');?></option>
                    <?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
                </select>
            </div>
        </div>
        <div class="clearfix"> </div>
        
        <div id="filter-bar" class="btn-toolbar">
            <div class="btn-group pull-right">
                <select name="filter_state" id="filter_state" class="input-medium" onchange="this.form.submit()">
                    <option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
                    <?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('archived'=>false, 'trash'=>false, 'all'=>false)), 'value', 'text', $this->state->get('filter.state'), true); ?>
                </select>
            </div>
            <div class="btn-group pull-right">
                <select name="filter_company_id" id="filter_cat_id" class="input-medium" onchange="this.form.submit()">
                    <option value=""><?php echo JText::_('COM_IPROPERTY_COMPANY');?></option>
                    <?php echo JHtml::_('select.options', JFormFieldCompany::getOptions(true), 'value', 'text', $this->state->get('filter.company_id')); ?>
                </select>
            </div>
        </div>
        <div class="clearfix"> </div>

        <h2><?php echo JText::_('COM_IPROPERTY_AGENTS'); ?></h2>    
        <div class="btn-toolbar">
            <div class="btn-group">
                <?php if ($this->ipauth->canAddAgent()): ?>
                    <button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('agentform.add')">
                        <i class="icon-plus"></i> <?php echo JText::_('JNEW'); ?>
                    </button>
                <?php endif; ?>                        
                <button type="button" class="btn hasTooltip" title="<?php echo htmlentities(JText::_('JPUBLISHED'), ENT_QUOTES, 'UTF-8'); ?>" onclick="Joomla.submitbutton('agentlist.publish')">
                    <i class="icon-ok"></i>
                </button>
                <button type="button" class="btn hasTooltip" title="<?php echo htmlentities(JText::_('JUNPUBLISHED'), ENT_QUOTES, 'UTF-8'); ?>" onclick="Joomla.submitbutton('agentlist.unpublish')">
                    <i class="icon-ban-circle"></i>
                </button>
                <?php if ($this->ipauth->getAdmin() || $this->ipauth->getSuper()): ?>
                    <button type="button" class="btn hasTooltip" title="<?php echo htmlentities(JText::_('COM_IPROPERTY_FEATURE'), ENT_QUOTES, 'UTF-8'); ?>" onclick="Joomla.submitbutton('agentlist.feature')">
                        <i class="icon-star"></i> 
                    </button>
                    <button type="button" class="btn hasTooltip" title="<?php echo htmlentities(JText::_('COM_IPROPERTY_UNFEATURE'), ENT_QUOTES, 'UTF-8'); ?>" onclick="Joomla.submitbutton('agentlist.unfeature')">
                        <i class="icon-star-empty"></i> 
                    </button>
                <?php endif; ?>
                <?php if ($this->ipauth->getAdmin()): ?>
                    <button type="button" class="btn hasTooltip" title="<?php echo htmlentities(JText::_('COM_IPROPERTY_SUPER'), ENT_QUOTES, 'UTF-8'); ?>" onclick="Joomla.submitbutton('agentlist.super')">
                        <i class="icon-plus"></i> 
                    </button>
                    <button type="button" class="btn hasTooltip" title="<?php echo htmlentities(JText::_('COM_IPROPERTY_UNSUPER'), ENT_QUOTES, 'UTF-8'); ?>" onclick="Joomla.submitbutton('agentlist.unsuper')">
                        <i class="icon-minus"></i> 
                    </button>
                <?php endif; ?>
                <button type="button" class="btn hasTooltip" title="<?php echo htmlentities(JText::_('COM_IPROPERTY_DELETE'), ENT_QUOTES, 'UTF-8'); ?>" onclick="if(confirm('<?php echo addslashes(JText::_('COM_IPROPERTY_CONFIRM_DELETE')); ?>')){Joomla.submitbutton('agentlist.delete')}else{return false;}">
                    <i class="icon-remove"></i> 
                </button>
            </div>
        </div>
        <table class="ptable table table-striped" id="agentList">
            <thead>
                <tr>
                    <th width="1%" class="center hidden-phone">
                        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo htmlentities(JText::_('JGLOBAL_CHECK_ALL'), ENT_QUOTES, 'UTF-8'); ?>" onclick="Joomla.checkAll(this)" />
                    </th>  
                    <th width="3%" class="nowrap hidden-phone">
                        <?php echo JText::_('COM_IPROPERTY_IMAGE'); ?>
                    </th>
                    <th width="30%" class="nowrap">
                        <?php echo JHtml::_('grid.sort',  'COM_IPROPERTY_LNAME', 'lname', $listDirn, $listOrder); ?> / 
                        <?php echo JHtml::_('grid.sort',  'COM_IPROPERTY_FNAME', 'fname', $listDirn, $listOrder); ?>
                    </th>
                    <th width="25%" class="nowrap">
                        <?php echo JHtml::_('grid.sort',  'COM_IPROPERTY_COMPANY', 'company', $listDirn, $listOrder); ?>
                    </th>
                    <th width="25%" class="nowrap hidden-phone">
                        <?php echo JHtml::_('grid.sort',  'COM_IPROPERTY_EMAIL', 'email', $listDirn, $listOrder); ?>
                    </th>
                    <th width="15%" class="nowrap hidden-phone center">
                        <?php echo JHtml::_('grid.sort', 'JSTATUS', 'state', $listDirn, $listOrder); ?>
                    </th>
                    <th width="1%" class="nowrap hidden-phone center">
                        <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
                    </th>
                </tr>
            </thead>

            <tfoot>
                <tr>
                    <td class="center" colspan="8">
                        <?php echo $this->pagination->getListFooter(); ?>
                    </td>
                </tr>
            </tfoot>

            <tbody>
                <?php
                if( $this->items ):                           
                    $k = 0;
                    foreach($this->items as $i => $item){

                        $canEdit        = $this->ipauth->canEditAgent($item->id) && !$item->checked_out;
                        $canPublish     = $this->ipauth->canPublishAgent($item->id, ($item->state == 1) ? 0 : 1) && !$item->checked_out;
                        $canFeature     = $this->ipauth->canFeatureAgent($item->id, ($item->featured == 1) ? 0 : 1) && !$item->checked_out;
                        $canSuper       = $this->ipauth->getAdmin() && !$item->checked_out;
                        $canOrder       = ($this->ipauth->getAdmin() || $this->ipauth->getSuper()) && !$item->checked_out;
                        $canCheckin     = $this->user->authorise('core.manage',       'com_checkin') || $item->checked_out == $this->userId || $item->checked_out == 0;
                        $icon           = ipropertyHTML::getIconpath($item->icon, 'agent');
                        $edit_link      = JRoute::_('index.php?option=com_iproperty&task=agentform.edit&id='.(int)$item->id.'&Itemid='.JRequest::getInt('Itemid').'&return='.$this->return);
                        ?>
                        <tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->company; ?>">
                            <td class="center hidden-phone">
                                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                            </td>
                            <td class="center hidden-phone"><?php echo ($item->icon) ? '<img src="'.$icon.'" width="20" style="border: solid 1px #377391;" class="ip-managelist-img" alt="" />' : '--'; ?>
                            <td class="nowrap has-context">
                                <div class="pull-left">
                                    <?php if ($item->checked_out) : ?>
                                        <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'agentlist.', $canCheckin); ?>
                                    <?php endif; ?>
                                    <?php if ($canEdit) : ?>
                                        <a href="<?php echo $edit_link; ?>">
                                            <?php echo ($item->lname) ? $this->escape($item->lname).', ' : ''; ?>
                                            <?php echo ($item->fname) ? $this->escape($item->fname) : '--'; ?>
                                        </a>
                                    <?php else : ?>
                                        <?php echo ($item->lname) ? $this->escape($item->lname).', ' : ''; ?>
                                        <?php echo ($item->fname) ? $this->escape($item->fname) : '--'; ?>
                                    <?php endif; ?>                                    
                                    <p class="small">
                                        <?php echo JText::_('JFIELD_ALIAS_LABEL').': '.$this->escape($item->alias);?>
                                        <?php if ($item->user_name): ?>
                                            <?php echo '<br />'.JText::_('JGLOBAL_USERNAME').': <span class="hasTooltip" title="'.$item->user_name.'::ID: '.$item->user_id.'<br />'.JText::_('JGLOBAL_USERNAME').': '.$item->user_username.'" >'.$item->user_name.'</span>'; ?>
                                        <?php endif; ?>
                                    </p>                                    
                                </div>
                                <div class="pull-right">
                                    <?php
                                        // Create dropdown items
                                        if($canPublish):
                                            if ($item->state) :
                                                JHtml::_('dropdown.unpublish', 'cb' . $i, 'agentlist.');
                                            else :
                                                JHtml::_('dropdown.publish', 'cb' . $i, 'agentlist.');
                                            endif;
                                            JHtml::_('dropdown.divider');
                                        endif;                                    

                                        if($canFeature):
                                            if ($item->featured) :
                                                JHtml::_('dropdown.addCustomItem', JText::_('COM_IPROPERTY_UNFEATURE'), '', 'onclick="contextAction(\'cb'.$i.'\', \'agentlist.unfeature\')"');
                                            else :
                                                JHtml::_('dropdown.addCustomItem', JText::_('COM_IPROPERTY_FEATURE'), '', 'onclick="contextAction(\'cb'.$i.'\', \'agentlist.feature\')"');
                                            endif;
                                            JHtml::_('dropdown.divider');
                                        endif;                                   

                                        if($canSuper):
                                            if ($item->agent_type) :
                                                JHtml::_('dropdown.addCustomItem', JText::_('COM_IPROPERTY_UNSUPER'), '', 'onclick="contextAction(\'cb'.$i.'\', \'agentlist.unsuper\')"');
                                            else:
                                                JHtml::_('dropdown.addCustomItem', JText::_('COM_IPROPERTY_SUPER'), '', 'onclick="contextAction(\'cb'.$i.'\', \'agentlist.super\')"');
                                            endif;
                                            JHtml::_('dropdown.divider');
                                        endif;                                    

                                        if ($item->checked_out && $canCheckin) :
                                            JHtml::_('dropdown.checkin', 'cb' . $i, 'agentlist.');
                                        endif;

                                        // Render dropdown list
                                        echo JHtml::_('dropdown.render');
                                    ?>
                                </div>
                            </td>
                            <td class="small"><?php echo ($item->company_title) ? $this->escape($item->company_title) : '--'; ?></td>
                            <td class="small hidden-phone"><?php echo ($item->email) ? $item->email : '--'; ?></td>
                            <td class="center hidden-phone">
                                <div class="btn-group">
                                    <?php echo JHtml::_('jgrid.published', $item->state, $i, 'agentlist.', $canPublish, 'cb'); ?>
                                    <?php echo JHtml::_('icon.featured', $item->featured, $i, $canFeature, 'agentlist'); ?>                            
                                    <?php if ($this->ipauth->getAdmin()) echo JHtml::_('icon.super', $item->agent_type, $i, $canSuper, 'agentlist'); ?>
                                </div>
                            </td>
                            <td class="center hidden-phone">
                                <?php echo $item->id; ?>
                            </td>
                        </tr>
                          <?php

                        $k = 1 - $k;
                    }
                else:
                    echo '<td class="center" colspan="6">'.JText::_('COM_IPROPERTY_NO_RESULTS').'</td>';
                endif;
                ?>
            </tbody>
        </table>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
        <input type="hidden" name="return" value="<?php echo $this->return; ?>" />
        <?php echo JHtml::_('form.token'); ?>
    </form>
</div>