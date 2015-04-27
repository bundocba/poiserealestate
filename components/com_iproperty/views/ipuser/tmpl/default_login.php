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
?>
<div class="login">
	<div class="ip-mainheader">
        <h2><?php echo $this->escape(JText::_('COM_IPROPERTY_PLEASE_LOG_IN')); ?></h2>
    </div>

	<form action="<?php echo JRoute::_('index.php?option=com_users&task=user.login'); ?>" method="post" class="form-horizontal">
		<fieldset class="well">
			<div class="control-group">
                <div class="control-label">
                    <label id="username-lbl" for="username" class="required"><?php echo JText::_('COM_IPROPERTY_USERNAME'); ?><span class="star">&#160;*</span></label>
                </div>
                <div class="controls">
                    <input type="text" name="username" id="username" value="" class="validate-username required" size="25"/>
                </div>
            </div>
            <div class="control-group">
                <div class="control-label">
                    <label id="password-lbl" for="password" class=" required"><?php echo JText::_('COM_IPROPERTY_PASSWORD'); ?><span class="star">&#160;*</span></label>
                </div>
                <div class="controls">
                    <input type="password" name="password" id="password" value="" class="validate-password required" size="25"/>
                </div>
            </div>
            <div class="control-group">
				<div class="controls">
					<button type="submit" class="btn btn-primary"><?php echo JText::_('JLOGIN'); ?></button>
				</div>
			</div>
			<input type="hidden" name="return" value="<?php echo $this->return; ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</fieldset>
	</form>
</div>
<div>
	<ul class="nav nav-tabs nav-stacked">
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>">
			<?php echo JText::_('COM_IPROPERTY_FORGOT_PASSWORD'); ?></a>
		</li>
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>">
			<?php echo JText::_('COM_IPROPERTY_FORGOT_USERNAME'); ?></a>
		</li>
		<?php if ($this->allowreg) : ?>
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>">
            <?php echo JText::_('COM_IPROPERTY_CREATE_ACCOUNT'); ?></a>
		</li>
		<?php endif; ?>
	</ul>
</div>
<?php
    // display footer if enabled
    if ($this->settings->footer == 1) echo ipropertyHTML::buildThinkeryFooter(); 
?>

