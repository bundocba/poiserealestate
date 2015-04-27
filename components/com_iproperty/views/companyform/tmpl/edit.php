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

JHtml::_('behavior.modal');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', '.ipform select');

// Create shortcut to parameters.
$params = $this->state->get('params');
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'companyform.cancel'){
			Joomla.submitform(task);
        }else if(document.formvalidator.isValid(document.id('adminForm'))) {
			Joomla.submitform(task);
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

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
                <button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('companyform.apply')">
                    <?php echo JText::_('COM_IPROPERTY_APPLY') ?>
                </button>
                <button type="button" class="btn" onclick="Joomla.submitbutton('companyform.save')">
                    <?php echo JText::_('JSAVE') ?>
                </button>
                <button type="button" class="btn" onclick="Joomla.submitbutton('companyform.cancel')">
                    <?php echo JText::_('JCANCEL') ?>
                </button>
            </div>
        </div>
        <ul class="nav nav-tabs">
            <li class="active"><a href="#codetails" data-toggle="tab"><?php echo JText::_('COM_IPROPERTY_DETAILS') ?></a></li>
            <?php if ($this->ipauth->getAdmin()): ?>
                <li><a href="#coother" data-toggle="tab"><?php echo JText::_('COM_IPROPERTY_OTHER') ?></a></li>
            <?php endif; ?>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="codetails">
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
                    <?php if (is_null($this->item->id) || $this->ipauth->getAdmin()):?>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('alias'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('alias'); ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('alias'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getValue('alias'); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('email'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('email'); ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('phone'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('phone'); ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('fax'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('fax'); ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('clicense'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('clicense'); ?>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend><?php echo JText::_('COM_IPROPERTY_ADDRESS'); ?></legend>
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('street'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('street'); ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('city'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('city'); ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('locstate'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('locstate'); ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('province'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('province'); ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('postcode'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('postcode'); ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('country'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('country'); ?>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend><?php echo JText::_('COM_IPROPERTY_WEB'); ?></legend>
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('website'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('website'); ?>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend><?php echo JText::_('COM_IPROPERTY_IMAGE'); ?></legend>
                    <div class="control-group">
                        <div class="controls">
                            <?php echo $this->form->getInput('icon'); ?>
                        </div>
                    </div>
                </fieldset>
            </div>
            <!-- admin can edit params -->
            <?php if ($this->ipauth->getAdmin()): ?>
                <div class="tab-pane" id="coother">
                    <fieldset class="adminform">
                        <legend><?php echo JText::_('JPUBLISHED'); ?></legend>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('state'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('state'); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('featured'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('featured'); ?>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="adminform admin_params">
                        <legend><?php echo JText::_('COM_IPROPERTY_COMPANY_PARAMETERS'); ?></legend>
                        <?php foreach($this->form->getFieldset('admin_params') as $field) :?>
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $field->label; ?>
                                </div>
                                <div class="controls">
                                    <?php echo $field->input; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </fieldset>
                </div>
            <?php endif; ?>
        </div>       
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="return" value="<?php echo $this->return_page;?>" />
        <?php echo JHtml::_( 'form.token'); ?>
    </form>
</div>