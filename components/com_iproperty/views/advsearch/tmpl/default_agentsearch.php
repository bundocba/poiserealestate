<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');

// require the IP fields
require_once JPATH_COMPONENT_ADMINISTRATOR .'/models/fields/company.php';
require_once JPATH_COMPONENT_ADMINISTRATOR .'/models/fields/agentsortby.php';
require_once JPATH_COMPONENT_ADMINISTRATOR .'/models/fields/orderby.php';

// build filter lists from fields
$tmpcos     = new JFormFieldCompany();
$tmpsort    = new JFormFieldAgentSortBy();
$tmporder   = new JFormFieldOrderBy();
?>

<div id="ip-agentsearchfilter-wrapper" class="well hidden-phone">
    <form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="ip_agent_search" class="ip-agentsearch-form form-inline">
        <div class="ip-quicksearch-optholder">
            <input type="text" class="inputbox ip-agentsearch-keyword" placeholder="<?php echo JText::_('COM_IPROPERTY_SEARCH'); ?>" name="filter_search" value="<?php echo $this->state->get('filter.search'); ?>" />
            <?php if (JFactory::getApplication()->input->get('view') != 'companyagents'): ?>
                <select name="filter_company_id" class="input-medium">
                    <option value=""><?php echo JText::_('COM_IPROPERTY_COMPANY'); ?></option>
                    <?php echo JHTML::_('select.options', $tmpcos->getOptions(), 'value', 'text', $this->state->get('filter.company_id')); ?>
                </select>
            <?php endif; ?>
        </div>
        <div class="ip-quicksearch-sortholder pull-right">                
            <select name="filter_order" class="input-medium">
                <option value=""><?php echo JText::_('COM_IPROPERTY_SORTBY'); ?></option>
                <?php echo JHTML::_('select.options', $tmpsort->getOptions(), 'value', 'text', $this->state->get('list.ordering')); ?>
            </select> 
            <select name="filter_order_Dir" class="input-medium">
                <option value=""><?php echo JText::_('COM_IPROPERTY_ORDERBY'); ?></option>
                <?php echo JHTML::_('select.options', $tmporder->getOptions(), 'value', 'text', $this->state->get('list.direction')); ?>
            </select> 
            <div class="btn-group">
                <button class="btn" onclick="clearForm(this.form);" type="button"><?php echo JText::_('COM_IPROPERTY_RESET'); ?></button>
                <button class="btn btn-primary" name="commit" type="submit"><?php echo JText::_('JSUBMIT'); ?></button>
            </div>
        </div>
    </form>
    <div class="clearfix"></div>
</div>
<div class="clearfix"></div>