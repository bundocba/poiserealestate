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

$sortFields = $this->getSortFields('property');
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
        
    <form action="<?php echo JRoute::_(ipropertyHelperRoute::getManageRoute().'&layout='.JFactory::getApplication()->input->get('layout','proplist')); ?>" method="post" name="adminForm" id="adminForm">
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
                <select name="filter_beds" id="filter_beds" class="input-medium" onchange="this.form.submit()">
                    <option value=""><?php echo JText::_('COM_IPROPERTY_BEDS');?></option>
                    <?php echo JHtml::_('select.options', JFormFieldBeds::getOptions(), 'value', 'text', $this->state->get('filter.beds')); ?>
                </select>
            </div>
            <div class="btn-group pull-right">
                <select name="filter_baths" id="filter_baths" class="input-medium" onchange="this.form.submit()">
                    <option value=""><?php echo JText::_('COM_IPROPERTY_BATHS');?></option>
                    <?php echo JHtml::_('select.options', JFormFieldBaths::getOptions(false), 'value', 'text', $this->state->get('filter.baths')); ?>
                </select>
            </div>
            <div class="btn-group pull-right">
                <select name="filter_cat_id" id="filter_cat_id" class="input-medium" onchange="this.form.submit()">
                    <option value=""><?php echo JText::_('COM_IPROPERTY_CATEGORY');?></option>
                    <?php echo JHtml::_('select.options', JFormFieldIpCategory::getOptions(), 'value', 'text', $this->state->get('filter.cat_id')); ?>
                </select>
            </div>
        </div>
        <div class="clearfix"> </div>

        <h2><?php echo JText::_('COM_IPROPERTY_PROPERTIES'); ?></h2>    
        <div class="btn-toolbar">
            <div class="btn-group">
                <?php if ($this->ipauth->canAddProp()): ?>
                <button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('propform.add')">
                    <i class="icon-plus"></i> <?php echo JText::_('JNEW'); ?>
                </button>
                <?php endif; ?>                        
                <button type="button" class="btn hasTooltip" title="<?php echo htmlentities(JText::_('JPUBLISHED'), ENT_QUOTES, 'UTF-8'); ?>" onclick="Joomla.submitbutton('proplist.publish')">
                    <i class="icon-ok"></i>
                </button>
                <button type="button" class="btn hasTooltip" title="<?php echo htmlentities(JText::_('JUNPUBLISHED'), ENT_QUOTES, 'UTF-8'); ?>" onclick="Joomla.submitbutton('proplist.unpublish')">
                    <i class="icon-ban-circle"></i>
                </button>
                <?php if ($this->ipauth->getAdmin() || $this->ipauth->getSuper()): ?>
                <button type="button" class="btn hasTooltip" title="<?php echo htmlentities(JText::_('COM_IPROPERTY_APPROVE'), ENT_QUOTES, 'UTF-8'); ?>" onclick="Joomla.submitbutton('proplist.approve')">
                    <i class="icon-thumbs-up"></i> 
                </button>
                <button type="button" class="btn hasTooltip" title="<?php echo htmlentities(JText::_('COM_IPROPERTY_UNAPPROVE'), ENT_QUOTES, 'UTF-8'); ?>" onclick="Joomla.submitbutton('proplist.unapprove')">
                    <i class="icon-thumbs-down"></i> 
                </button>
                <button type="button" class="btn hasTooltip" title="<?php echo htmlentities(JText::_('COM_IPROPERTY_FEATURE'), ENT_QUOTES, 'UTF-8'); ?>" onclick="Joomla.submitbutton('proplist.feature')">
                    <i class="icon-star"></i> 
                </button>
                <button type="button" class="btn hasTooltip" title="<?php echo htmlentities(JText::_('COM_IPROPERTY_UNFEATURE'), ENT_QUOTES, 'UTF-8'); ?>" onclick="Joomla.submitbutton('proplist.unfeature')">
                    <i class="icon-star-empty"></i> 
                </button>
                <?php endif; ?>
                <button type="button" class="btn hasTooltip" title="<?php echo htmlentities(JText::_('COM_IPROPERTY_DELETE'), ENT_QUOTES, 'UTF-8'); ?>" onclick="if(confirm('<?php echo addslashes(JText::_('COM_IPROPERTY_CONFIRM_DELETE')); ?>')){Joomla.submitbutton('proplist.delete')}else{return false;}">
                    <i class="icon-remove"></i> 
                </button>
            </div>
        </div>
        <table class="ptable table table-striped" id="propertyList">
            <thead>
                <tr>
                    <th width="1%" class="center hidden-phone">
                        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo htmlentities(JText::_('JGLOBAL_CHECK_ALL', ENT_QUOTES, 'UTF-8')); ?>" onclick="Joomla.checkAll(this)" />
                    </th>                    
                    <th width="55%" class="nowrap">
                        <?php echo JHTML::_('grid.sort', JText::_('COM_IPROPERTY_STREET' ), 'street', $listDirn, $listOrder ); ?> /
                        <?php echo JHTML::_('grid.sort', JText::_('COM_IPROPERTY_LOCATION' ), 'city', $listDirn, $listOrder ); ?> /
                        <?php echo JHTML::_('grid.sort', JText::_('COM_IPROPERTY_TITLE' ), 'title', $listDirn, $listOrder ); ?> /
                        <?php echo JHTML::_('grid.sort', JText::_('COM_IPROPERTY_REF' ), 'property_name', $listDirn, $listOrder ); ?> /
                        <?php echo JHTML::_('grid.sort', JText::_('COM_IPROPERTY_PRICE' ), 'price', $listDirn, $listOrder ); ?>
                    </th>
                    <th width="20%" class="nowrap hidden-phone"><?php echo JText::_('COM_IPROPERTY_CATEGORIES'); ?></th>
                    <th width="20%" class="nowrap hidden-phone"><?php echo JText::_('COM_IPROPERTY_AGENT'); ?></th>
                    <th width="20%" class="nowrap center hidden-phone"><?php echo JText::_('COM_IPROPERTY_ACTION'); ?></th>
                    <th width="5%" class="nowrap center">
                        <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
                    </th>
                </tr>
            </thead>

            <tfoot>
                <tr>
                    <td class="center" colspan="6">
                        <?php echo $this->pagination->getListFooter(); ?>
                    </td>
                </tr>
            </tfoot>

            <tbody>
                <?php
                if( $this->items ):                           
                    $k = 0;
                    foreach($this->items as $i => $item){

                        $canEdit        = $this->ipauth->canEditProp($item->id) && !$item->checked_out;
                        $canPublish     = $this->ipauth->canPublishProp($item->id, ($item->state == 1) ? 0 : 1) && !$item->checked_out;
                        $canFeature     = $this->ipauth->canFeatureProp($item->id, ($item->featured == 1) ? 0 : 1) && !$item->checked_out;
                        $canOrder       = ($this->ipauth->getAdmin() || $this->ipauth->getSuper()) && !$item->checked_out;
                        $canCheckin     = $this->user->authorise('core.manage',       'com_checkin') || $item->checked_out == $this->userId || $item->checked_out == 0;
                        $canApprove     = $this->ipauth->canApproveProp($item->id, ($item->approved == 1) ? 0 : 1) && !$item->checked_out;

                        $linktitle      = ipropertyHTML::getStreetAddress($this->settings, $item);
                        if($item->city) $linktitle .= ', '.$item->city;
                        if($item->locstate) $linktitle .= ' - '.ipropertyHTML::getStateName($item->locstate);
                        $edit_link      = JRoute::_('index.php?option=com_iproperty&view=propform&task=propform.edit&id='.(int)$item->id.'&Itemid='.JRequest::getInt('Itemid').'&return='.$this->return);
                        ?>
                        <tr class="row<?php echo $i % 2; ?>">
                            <td class="center hidden-phone">
                                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                            </td>                            
                            <td class="has-context">
                                <div class="pull-left">
                                    <?php if ($item->checked_out) : ?>
                                        <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'proplist.', $canCheckin); ?>
                                    <?php endif; ?>
                                    <?php echo '<span class="hasTooltip" title="'.htmlentities(ipropertyHTML::getThumbnail($item->id, '', $linktitle, 100)).'"><i class="icon-camera"></i></span>'; ?> |                                 
                                    <?php if ($canEdit) : ?>
                                        <a href="<?php echo $edit_link; ?>">
                                            <?php echo $this->escape($linktitle); ?>
                                        </a>
                                    <?php else : ?>
                                        <?php echo $this->escape($linktitle); ?>                                    
                                    <?php endif; ?>
                                    <?php if ($this->settings->showtitle && $item->title) echo '<br /><span class="small">'.$item->street_address.'</span>'; ?>
                                    <?php if ($item->property_name) echo '<br /><b>'.JText::_('COM_IPROPERTY_property_name').':</b> '.$item->property_name; ?> 
                                    <?php if ($item->price) echo '<br /><b>'.JText::_('COM_IPROPERTY_PRICE').':</b> '.ipropertyHTML::getFormattedPrice($item->price, $item->stype_freq, '', '', $item->price2, true); ?>
                                </div>
                                <div class="pull-right">
                                    <?php
                                    // Create dropdown items
                                    if($canPublish):
                                        if ($item->state) :
                                            JHtml::_('dropdown.unpublish', 'cb' . $i, 'proplist.');
                                        else :
                                            JHtml::_('dropdown.publish', 'cb' . $i, 'proplist.');
                                        endif;
                                        JHtml::_('dropdown.divider');
                                    endif;    

                                    if($this->settings->edit_rights && $canApprove):
                                        if ($item->approved) :
                                            JHtml::_('dropdown.addCustomItem', JText::_('COM_IPROPERTY_UNAPPROVE'), '', 'onclick="contextAction(\'cb'.$i.'\', \'proplist.unapprove\')"');
                                        else :
                                            JHtml::_('dropdown.addCustomItem', JText::_('COM_IPROPERTY_APPROVE'), '', 'onclick="contextAction(\'cb'.$i.'\', \'proplist.approve\')"');
                                        endif;
                                        JHtml::_('dropdown.divider');
                                    endif;

                                    if($canFeature):
                                        if ($item->featured) :
                                            JHtml::_('dropdown.addCustomItem', JText::_('COM_IPROPERTY_UNFEATURE'), '', 'onclick="contextAction(\'cb'.$i.'\', \'proplist.unfeature\')"');
                                        else :
                                            JHtml::_('dropdown.addCustomItem', JText::_('COM_IPROPERTY_FEATURE'), '', 'onclick="contextAction(\'cb'.$i.'\', \'proplist.feature\')"');
                                        endif;
                                        JHtml::_('dropdown.divider');
                                    endif;                                    

                                    if ($item->checked_out && $canCheckin) :
                                        JHtml::_('dropdown.checkin', 'cb' . $i, 'proplist.');
                                    endif;

                                    // Render dropdown list
                                    echo JHtml::_('dropdown.render');
                                    ?>
                                </div>
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
                            <td class="small hidden-phone center">
                                <div class="btn-group">
                                    <?php if($this->settings->edit_rights && $canApprove): ?>
                                        <?php echo JHtml::_('icon.approved', $item->approved, $i, $canApprove, 'proplist'); ?>
                                    <?php endif; ?>
                                    <?php echo JHtml::_('jgrid.published', $item->state, $i, 'proplist.', $canPublish, 'cb'); ?>
                                    <?php echo JHtml::_('icon.featured', $item->featured, $i, $canFeature, 'proplist'); ?>
                                </div>
                            </td>
                            <td class="center">
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