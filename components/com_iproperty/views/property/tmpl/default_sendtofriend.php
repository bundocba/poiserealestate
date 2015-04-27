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



// check if the request form is enabled for the sale type. If so, copy the dynamic fields to the

// dynamic field copy div. Work around for multiple captchas on single page

if(ipropertyHtml::stypeRequestForm($this->p->stype)):

    // clone captcha

    JFactory::getDocument()->addScriptDeclaration(

        "jQuery(function($) {

            $('a[href=\"#propstf\"]').on('shown', function (e) {

                $('#ip-dynamic-fields-copy').empty(); //empty dynamic field copy div

                $.each($('.ip-dynamic-field'), function(){ //copy dynamic fields from request form into stf form

                    $(this).clone().appendTo('#ip-dynamic-fields-copy');

                });

            });

        });"

    );

endif;



// set user name and email if available in session or user object

if(!$this->stfform->getValue('sender_name')) $this->stfform->setValue('sender_name', '', ($this->user && $this->user->username) ? $this->user->username : '');

if(!$this->stfform->getValue('sender_email')) $this->stfform->setValue('sender_email', '', ($this->user && $this->user->email) ? $this->user->email : '');

?>



<?php if (isset($this->error)): ?>

	<div class="iproperty-error">

		<?php echo $this->error; ?>

	</div>

<?php endif; ?>



<div class="row-fluid">

    <div class="span12">

        <div class="span8 pull-left ip-request-wrapper">     

            <p><?php echo JText::_('COM_IPROPERTY_SEND_TO_FRIEND_TEXT' ); ?></p>

            <form name="sendTofriend" method="post" action="<?php echo JRoute::_( 'index.php', true ); ?>" id="adminForm" class="form-horizontal form-validate">

                <fieldset>

                    <div class="control-group">

                        <div class="control-label"><?php echo $this->stfform->getLabel('sender_name'); ?></div>

                        <div class="controls"><?php echo $this->stfform->getInput('sender_name'); ?></div>

                    </div>

                    <div class="control-group">

                        <div class="control-label"><?php echo $this->stfform->getLabel('sender_email'); ?></div>

                        <div class="controls"><?php echo $this->stfform->getInput('sender_email'); ?></div>

                    </div>

                    <div class="control-group">

                        <div class="control-label"><?php echo $this->stfform->getLabel('recipient_email'); ?></div>

                        <div class="controls"><?php echo $this->stfform->getInput('recipient_email'); ?></div>

                    </div>

                    <div class="control-group">

                        <div class="control-label"><?php echo $this->stfform->getLabel('comments'); ?></div>

                        <div class="controls"><?php echo $this->stfform->getInput('comments'); ?></div>

                    </div>

                    <div id="ip-dynamic-fields-copy"></div>

                    <?php if(!ipropertyHtml::stypeRequestForm($this->p->stype)): // request form is not enabled, show captcha field ?>

                    <div id="ip-captcha-backup">

                        <div class="control-group">

                            <div class="control-label"><?php echo $this->stfform->getLabel('captcha'); ?></div>

                            <div class="controls"><?php echo $this->stfform->getInput('captcha'); ?></div>

                        </div>

                    </div>

                    <?php endif; ?>

                    <div class="control-group">

                        <div class="control-label">&#160;</div>

                        <div class="controls"><input type="submit" class="btn btn-primary" alt="<?php echo JText::_('COM_IPROPERTY_SUBMIT_SEND'); ?>" title="<?php echo JText::_('COM_IPROPERTY_SUBMIT_SEND'); ?>" value="<?php echo JText::_('COM_IPROPERTY_SUBMIT_SEND'); ?>" /></div>

                    </div>

                </fieldset>

                <input type="hidden" name="option" value="com_iproperty" />

                <input type="hidden" name="view" value="property" />

                <input type="hidden" name="prop_id" value="<?php echo $this->p->id; ?>" />

                <input type="hidden" name="task" value="property.sendTofriend" />

                <input type="hidden" name="layout" value="default" />

                <?php echo JHTML::_('form.token'); ?>

            </form>

        </div>

        <div class="span4 ip-summary-sidecol">

            <?php echo $this->loadTemplate('sidebar'); ?>

        </div>

    </div>

</div>