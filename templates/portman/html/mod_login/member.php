<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_login
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_SITE . '/components/com_users/helpers/route.php';

JHtml::_('behavior.keepalive');
JHtml::_('bootstrap.tooltip');
?>
<div style="display:none;">
	<div class="popup-form" id="member-form">
		<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="" class=" form-inline" autocomplete="off">
			<h3><?php echo $module->title;?></h3>
			<div class="userdata">
				<div id="form-login-username" class="control-group row">
					<div class="controls">
						<div class="col-sm-3">
							<label for="modlgn-username"><?php echo JText::_('Email:
') ?></label>
						</div>
						<div class="col-sm-9">
							<input id="modlgn-username" type="text" name="username" class="input-small" tabindex="0" size="18" placeholder="<?php echo JText::_('Email Adress:') ?>" />
						</div>
					</div>
				</div>
				<div id="form-login-password" class="control-group row">
					<div class="controls">
						<div class="col-sm-3">
							<label for="modlgn-passwd"><?php echo JText::_('JGLOBAL_PASSWORD') ?></label>
						</div>
						<div class="col-sm-9">
							<input id="modlgn-passwd" type="password" name="password" class="input-small" tabindex="0" size="18" placeholder="<?php echo JText::_('JGLOBAL_PASSWORD') ?>" />
						</div>
					</div>
				</div>
				<?php if (count($twofactormethods) > 1): ?>
				<div id="form-login-secretkey" class="control-group">
					<div class="controls">
						<?php if (!$params->get('usetext')) : ?>
							<div class="input-prepend input-append">
								<span class="add-on">
									<span class="icon-star hasTooltip" title="<?php echo JText::_('JGLOBAL_SECRETKEY'); ?>">
									</span>
										<label for="modlgn-secretkey" class="element-invisible"><?php echo JText::_('JGLOBAL_SECRETKEY'); ?>
									</label>
								</span>
								<input id="modlgn-secretkey" autocomplete="off" type="text" name="secretkey" class="input-small" tabindex="0" size="18" placeholder="<?php echo JText::_('JGLOBAL_SECRETKEY') ?>" />
								<span class="btn width-auto hasTooltip" title="<?php echo JText::_('JGLOBAL_SECRETKEY_HELP'); ?>">
									<span class="icon-help"></span>
								</span>
						</div>
						<?php else: ?>
							<label for="modlgn-secretkey"><?php echo JText::_('JGLOBAL_SECRETKEY') ?></label>
							<input id="modlgn-secretkey" autocomplete="off" type="text" name="secretkey" class="input-small" tabindex="0" size="18" placeholder="<?php echo JText::_('JGLOBAL_SECRETKEY') ?>" />
							<span class="btn width-auto hasTooltip" title="<?php echo JText::_('JGLOBAL_SECRETKEY_HELP'); ?>">
								<span class="icon-help"></span>
							</span>
						<?php endif; ?>

					</div>
				</div>
				<?php endif; ?>
				<div id="form-login-submit" class="control-group row">
					<div class="controls col-sm-12 col-sm-offset-7">
						<button type="submit" tabindex="0" name="Submit"><?php echo JText::_('JLOGIN') ?></button>
					</div>
					<div class="forgot_pass">
							<a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>">
							Forgot your password?</a>
					</div>
				</div>
				<input type="hidden" name="option" value="com_users" />
				<input type="hidden" name="task" value="user.login" />
				<input type="hidden" name="return" value="<?php echo $return; ?>" />
				<?php echo JHtml::_('form.token'); ?>
			</div>
			
		</form>
	</div>
</div>     