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

if(!$this->requestform->getValue('sender_name')) $this->requestform->setValue('sender_name', '', ($this->user && $this->user->username) ? $this->user->username : '');

if(!$this->requestform->getValue('sender_email')) $this->requestform->setValue('sender_email', '', ($this->user && $this->user->email) ? $this->user->email : '');

?>



<?php if (isset($this->error)): ?>

	<div class="iproperty-error">

		<?php echo $this->error; ?>

	</div>

<?php endif; ?>



<div class="row-fluid">

    <div class="span12">

        <div class="span8 pull-left ip-request-wrapper">

            <p><?php echo JText::_('COM_IPROPERTY_REQUEST_SHOWING_TEXT' ); ?></p>

            <form name="sendRequest" method="post" action="<?php echo JRoute::_('index.php', true); ?>" id="adminForm" class="form-horizontal form-validate">

                <fieldset>

                    <div class="control-group">

                        <div class="control-label"><?php echo $this->requestform->getLabel('sender_name'); ?></div>

                        <div class="controls"><?php echo $this->requestform->getInput('sender_name'); ?></div>

                    </div>

                    <div class="control-group">

                        <div class="control-label"><?php echo $this->requestform->getLabel('sender_email'); ?></div>

                        <div class="controls"><?php echo $this->requestform->getInput('sender_email'); ?></div>

                    </div>

                    <div class="control-group">

                        <div class="control-label"><?php echo $this->requestform->getLabel('sender_dphone'); ?></div>

                        <div class="controls"><?php echo $this->requestform->getInput('sender_dphone'); ?></div>

                    </div>

                    <div class="control-group">

                        <div class="control-label"><?php echo $this->requestform->getLabel('sender_ephone'); ?></div>

                        <div class="controls"><?php echo $this->requestform->getInput('sender_ephone'); ?></div>

                    </div>

                    <div class="control-group">

                        <div class="control-label"><?php echo $this->requestform->getLabel('sender_preference'); ?></div>

                        <div class="controls"><?php echo $this->requestform->getInput('sender_preference'); ?></div>

                    </div>

                    <div class="control-group">

                        <div class="control-label"><?php echo $this->requestform->getLabel('sender_ctime'); ?></div>

                        <div class="controls"><?php echo $this->requestform->getInput('sender_ctime'); ?></div>

                    </div>

                    <div class="control-group">

                        <div class="control-label"><?php echo $this->requestform->getLabel('special_requests'); ?></div>

                        <div class="controls"><?php echo $this->requestform->getInput('special_requests'); ?></div>

                    </div>

                    <div class="control-group">

                        <div class="control-label"><?php echo $this->requestform->getLabel('copy_me'); ?></div>

                        <div class="controls"><?php echo $this->requestform->getInput('copy_me'); ?></div>

                    </div>

                    <div id="ip-dynamic-fields">

                        <?php //Dynamically load any additional fields from plugins. ?>

                        <?php foreach ($this->requestform->getFieldsets() as $fieldset) : ?>

                            <?php if ($fieldset->name != 'request'):?>

                                <?php $fields = $this->requestform->getFieldset($fieldset->name);?>

                                <?php foreach ($fields as $field) : ?>

                                    <div class="control-group ip-dynamic-field">

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

                    </div>

                    <div class="control-group">

                        <div class="control-label">&#160;</div>

                        <div class="controls"><input type="submit" class="btn btn-primary" alt="<?php echo JText::_('COM_IPROPERTY_SUBMIT_REQUEST'); ?>" title="<?php echo JText::_('COM_IPROPERTY_SUBMIT_REQUEST'); ?>" value="<?php echo JText::_('COM_IPROPERTY_SUBMIT_REQUEST'); ?>" /></div>

                    </div>

                </fieldset>

                <input type="hidden" name="option" value="com_iproperty" />

                <input type="hidden" name="view" value="property" />

                <input type="hidden" name="prop_id" value="<?php echo $this->p->id; ?>" />

                <input type="hidden" name="company_id" value="<?php echo $this->p->listing_office; ?>" />

                <input type="hidden" name="task" value="property.sendRequest" />

                <input type="hidden" name="layout" value="default" />

                <?php echo JHTML::_('form.token'); ?>

            </form>

        </div>

        <div class="span4 ip-summary-sidecol">

            <?php echo $this->loadTemplate('sidebar'); ?>

        </div>

    </div>

</div>