<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');

if (!$this->user->id): ?>
<div class="well well-small">
    <div align="center">
        <?php echo JHtml::_('image', 'components/com_iproperty/assets/images/iproperty.png', JText::_('COM_IPROPERTY_PLEASE_LOG_IN')); ?>
        <p><?php echo JText::_('COM_IPROPERTY_LOG_IN_TO_SAVE_SEARCHES'); ?></p>
        <p><a href="<?php echo JRoute::_('index.php?option=com_users&view=login&return='.base64_encode(JURI::getInstance()->toString())); ?>"><?php echo JText::_('COM_IPROPERTY_PLEASE_LOG_IN'); ?></a></p>
        <input type="hidden" name="ipsearchstring" id="ipsavesearchstring" value="" />
    </div>
</div>
<?php else: ?>
<div class="well well-small">
    <p><?php echo JText::_('COM_IPROPERTY_SEARCH_TEXT'); ?></p>
	<form name="ipsaveProperty" action="<?php echo JRoute::_('index.php', true); ?>" method="post" class="ip-savesearch-form form-inline">
		<input type="text" class="input-large" id="notes" name="notes" placeholder="<?php echo JText::_('COM_IPROPERTY_NOTES'); ?>" />&#160;
		<?php if($this->settings->show_searchupdate): ?>
		<label class="checkbox">
			<input type="checkbox" id="email_update" name="email_update" /> <?php echo JText::_('COM_IPROPERTY_EMAIL_UPDATES'); ?>
		</label>&#160;
		<?php endif; ?>
		<button type="submit" class="btn btn-primary"><?php echo JText::_('JSAVE'); ?></button>
		<input type="hidden" name="task" value="ipuser.saveSearch" />
		<input type="hidden" name="userid" value="<?php echo $this->user->id; ?>" />
		<input type="hidden" name="ipsearchstring" id="ipsavesearchstring" value="" />
		<?php echo JHTML::_('form.token'); ?>                            
	</form>
</div>                     
<?php endif; ?>
