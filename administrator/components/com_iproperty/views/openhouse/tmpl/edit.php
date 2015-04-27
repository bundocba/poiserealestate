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
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
?>
<!-- add custom css for time picker -->
<style type="text/css">
    tr.time{background: #D9EDF7; border-top: 1px solid #3A87AD; font-size: 14px;}
    tr.time td{padding: 7px;}
    td.time span.hour, td.time span.minute{padding: 3px; border: solid 1px #3A87AD; background: #fff;}
</style>

<form action="<?php echo JRoute::_('index.php?option=com_iproperty&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
    <div class="row-fluid">
        <div class="span9 form-horizontal">
            <h4><?php echo JText::_('COM_IPROPERTY_DETAILS'); ?></h4>
            <hr />
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
        </div>
        <div class="span3 form-vertical">
            <div class="alert alert-info">
                <h4><?php echo JText::_('COM_IPROPERTY_PUBLISHING');?></h4>
                <hr />            
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('state'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('state'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
<div class="clearfix"></div>
<?php echo ipropertyAdmin::footer( ); ?>