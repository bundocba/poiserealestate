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

JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/html');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', '.ipform select');

// Create shortcut to parameters.
$params = $this->state->get('params');
?>

<!-- add custom css for time picker -->
<style type="text/css">
    tr.time{background: #D9EDF7; border-top: 1px solid #3A87AD; font-size: 14px;}
    tr.time td{padding: 7px;}
    td.time span.hour, td.time span.minute{padding: 3px; border: solid 1px #3A87AD; background: #fff;}
</style>

<div class="edit item-page<?php echo $this->pageclass_sfx; ?>">
    <?php if ($this->params->get('show_page_heading', 1)) : ?>
        <h1>
            <?php echo $this->escape($this->params->get('page_heading')); ?>
        </h1>
    <?php endif; ?>
    <div class="ip-mainheader">
        <h2><?php echo $this->iptitle; ?></h2>
    </div>

    <form action="<?php echo JRoute::_('index.php?option=com_iproperty&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate ipform form-horizontal">
        <div class="btn-toolbar">
			<div class="btn-group">
                <button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('openhouseform.apply')">
                    <?php echo JText::_('COM_IPROPERTY_APPLY') ?>
                </button>
                <button type="button" class="btn" onclick="Joomla.submitbutton('openhouseform.save')">
                    <?php echo JText::_('JSAVE') ?>
                </button>
                <button type="button" class="btn" onclick="Joomla.submitbutton('openhouseform.cancel')">
                    <?php echo JText::_('JCANCEL') ?>
                </button>
            </div>
        </div>
        <fieldset>
            <legend><?php echo JText::_('COM_IPROPERTY_DETAILS'); ?></legend>
            <div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('name'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('name'); ?>
                </div>
            </div>
            <div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('prop_id'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('prop_id'); ?>
                </div>
            </div>
            <div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('openhouse_start'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('openhouse_start'); ?>
                </div>
            </div>
            <div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('openhouse_end'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('openhouse_end'); ?>
                </div>
            </div>
            <div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('comments'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('comments'); ?>
                </div>
            </div>
            <div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('state'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('state'); ?>
                </div>
            </div>
        </fieldset>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="return" value="<?php echo $this->return_page;?>" />
        <?php echo JHtml::_('form.token'); ?>
    </form>
</div>
<div class="clearfix"></div>
<?php if ($this->settings->footer == 1) echo ipropertyHTML::buildThinkeryFooter(); ?>