<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_login
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
?>
<div style='display:none'>
	<div id="agent-form" class="popup-form">
		<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form" class="form-vertical">
		<?php if ($params->get('greeting')) : ?>
			<div class="login-greeting">
			<?php if ($params->get('name') == 0) : {
				echo JText::sprintf('MOD_LOGIN_HINAME', htmlspecialchars($user->get('name')));
				echo " you are currently logged in. Would you like to log out?";
			} else : {
				echo JText::sprintf('MOD_LOGIN_HINAME', htmlspecialchars($user->get('username')));
				echo " you are currently logged in. Would you like to log out?";
			} endif; ?>
			</div>
		<?php endif; ?>
			<div class="logout-button" style="margin-top: 20px;">
				<input type="submit" name="Submit" class="btn btn-primary" value="<?php echo JText::_('JLOGOUT'); ?>" />
				<span style="background: #032D5F;padding: 9px;border-radius: 5px;"><a style="color: #fff;" href="<?php echo JRoute::_('index.php?option=com_iproperty&view=file&Itemid=224&lang=en&id='.$agent_id);?>">Cancel</a></span>
				<input type="hidden" name="option" value="com_users" />
				<input type="hidden" name="task" value="user.logout" />
				<input type="hidden" name="return" value="<?php echo JRoute::_('index.php'); ?>" />
				<?php echo JHtml::_('form.token'); ?>
			</div>
		</form>
	</div>
</div>

