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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
JHTML::_('behavior.keepalive');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.modal');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');
JFactory::getLanguage()->load('com_iproperty', JPATH_ADMINISTRATOR);
JFactory::getLanguage()->load('', JPATH_ADMINISTRATOR);

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
        
    <form action="<?php echo JRoute::_(ipropertyHelperRoute::getManageRoute().'&layout='.JFactory::getApplication()->input->get('layout','openhouselist')); ?>" method="post" name="adminForm" id="adminForm">
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
        
        <h2><?php echo JText::_('COM_IPROPERTY_OPENHOUSES'); ?></h2>    
        <div class="btn-toolbar">
            <div class="btn-group">
                <?php if ($this->ipauth->canAddProp()): ?>
                <button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('openhouseform.add')">
                    <i class="icon-plus"></i> <?php echo JText::_('JNEW'); ?>
                </button>
                <?php endif; ?>                        
                <button type="button" class="btn hasTooltip" title="<?php echo htmlentities(JText::_('JPUBLISHED'), ENT_QUOTES, 'UTF-8'); ?>" onclick="Joomla.submitbutton('openhouselist.publish')">
                    <i class="icon-ok"></i>
                </button>
                <button type="button" class="btn hasTooltip" title="<?php echo htmlentities(JText::_('JUNPUBLISHED'), ENT_QUOTES, 'UTF-8'); ?>" onclick="Joomla.submitbutton('openhouselist.unpublish')">
                    <i class="icon-ban-circle"></i>
                </button>
                <button type="button" class="btn hasTooltip" title="<?php echo htmlentities(JText::_('COM_IPROPERTY_DELETE'), ENT_QUOTES, 'UTF-8'); ?>" onclick="if(confirm('<?php echo addslashes(JText::_('COM_IPROPERTY_CONFIRM_DELETE')); ?>')){Joomla.submitbutton('openhouselist.delete')}else{return false;}">
                    <i class="icon-remove"></i> 
                </button>
            </div>
        </div>
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
                        $edit_link      = JRoute::_('index.php?option=com_iproperty&task=openhouseform.edit&id='.(int)$item->id.'&Itemid='.JRequest::getInt('Itemid').'&return='.$this->return);
                        ?>
                        <tr class="row<?php echo $i % 2; ?>">
                            <td class="center hidden-phone">
                                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                            </td>
                            <td class="nowrap has-context">
                                <div class="pull-left">
                                    <?php if ($item->checked_out) : ?>
                                        <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'openhouselist.', $canCheckin); ?>
                                    <?php endif; ?>
                                    <?php 
                                        if($item->ohname) echo '<em>'.$this->escape($item->ohname).'</em><br />';
                                        if ($canEdit){
                                            echo '<a href="'.$edit_link.'">
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
                                            JHtml::_('dropdown.edit', $item->id, 'openhouseform.');
                                            JHtml::_('dropdown.divider');
                                        endif;                                   

                                        if($canPublish):
                                            if ($item->state) :
                                                JHtml::_('dropdown.unpublish', 'cb' . $i, 'openhouselist.');
                                            else :
                                                JHtml::_('dropdown.publish', 'cb' . $i, 'openhouselist.');
                                            endif;
                                            JHtml::_('dropdown.divider');
                                        endif;                                   

                                        if ($item->checked_out && $canCheckin) :
                                            JHtml::_('dropdown.checkin', 'cb' . $i, 'openhouselist.');
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
                                <?php echo JHtml::_('jgrid.published', $item->state, $i, 'openhouselist.', $canPublish, 'cb'); ?>
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
        <input type="hidden" name="return" value="<?php echo $this->return; ?>" />
        <?php echo JHtml::_('form.token'); ?>    
    </form>
</div>
<div class="clearfix"></div>
<?php if ($this->settings->footer == 1) echo ipropertyHTML::buildThinkeryFooter(); ?>