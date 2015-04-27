<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.tooltip');

// set user name and email if available in session or user object
if(!$this->form->getValue('sender_name')) $this->form->setValue('sender_name', '', (isset($this->user) && $this->user->username) ? $this->user->username : '');
if(!$this->form->getValue('sender_email')) $this->form->setValue('sender_email', '', (isset($this->user) && $this->user->email) ? $this->user->email : '');

if (isset($this->error)) : ?>
	<div class="contact-error">
		<?php echo $this->error; ?>
	</div>
<?php endif; ?>

<div class="well">
    <form name="contactForm" method="post" action="<?php echo JRoute::_('index.php', true); ?>" id="ipCform" class="offset1 form-horizontal form-validate">
        <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('sender_name'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('sender_name'); ?></div>
        </div>
        <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('sender_email'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('sender_email'); ?></div>
        </div>
        <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('sender_dphone'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('sender_dphone'); ?></div>
        </div>
        <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('sender_ephone'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('sender_ephone'); ?></div>
        </div>
        <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('sender_preference'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('sender_preference'); ?></div>
        </div>
        <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('sender_requests'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('sender_requests'); ?></div>
        </div>
        <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('sender_copy'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('sender_copy'); ?></div>
        </div>
        <?php //Dynamically load any additional fields from plugins. ?>
        <?php foreach ($this->form->getFieldsets() as $fieldset) : ?>
            <?php if ($fieldset->name != 'contact'):?>
                <?php $fields = $this->form->getFieldset($fieldset->name);?>
                <?php foreach ($fields as $field) : ?>
                    <div class="control-group">
                        <?php if ($field->hidden) : ?>
                            <div class="controls">
                                <?php echo $field->input;?>
                            </div>
                        <?php else:?>
                            <div class="control-label">
                                <?php echo $field->label; ?>
                                <?php if (!$field->required && $field->type != "Spacer") : ?>
                                    <span class="optional"><?php echo JText::_('COM_IPROPERTY_OPTIONAL');?></span>
                                <?php endif; ?>
                            </div>
                            <div class="controls"><?php echo $field->input;?></div>
                        <?php endif;?>
                    </div>
                <?php endforeach;?>
            <?php endif ?>
        <?php endforeach;?>
        <div class="form-actions">
            <button class="btn btn-primary validate" type="submit"><?php echo JText::_('JSUBMIT'); ?></button>
            <input type="hidden" name="option" value="com_iproperty" />
            <input type="hidden" name="task" value="contact.contactForm" />
            <input type="hidden" name="id" value="<?php echo $this->state->get('contact.id'); ?>" />
            <input type="hidden" name="remote_addr" value="<?php echo $_SERVER['REMOTE_ADDR']; ?>" />
            <input type="hidden" name="return" value="<?php echo $this->return_page;?>" />
            <input type="hidden" name="jform[ctype]" value="<?php echo $this->ctype; ?>" />
            <?php echo JHtml::_('form.token'); ?>
        </div>
    </form>
</div>