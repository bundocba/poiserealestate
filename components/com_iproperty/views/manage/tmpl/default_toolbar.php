<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

$layout = JRequest::getWord('layout', 'proplist');
?>

<div class="ip-manage-login pull-right">
    <?php echo sprintf(JText::_('COM_IPROPERTY_MANAGE_LOGIN'), $this->user->get('username')); ?> |
    <a href="<?php echo JRoute::_('index.php?option=com_users&view=login'); ?>"><?php echo JText::_('COM_IPROPERTY_LOGOUT'); ?></a>
</div>
<div class="clearfix"> </div>
<div class="navbar">
    <div class="navbar-inner">
        <a class="brand" href="<?php echo JRoute::_(ipropertyHelperRoute::getManageRoute()); ?>"><?php echo JText::_('COM_IPROPERTY_MANAGE'); ?></a>
        <ul class="nav">
            <li<?php if($layout == 'proplist') echo ' class="active"'; ?>><a href="<?php echo JRoute::_('index.php'); ?>"><?php echo JText::_('COM_IPROPERTY_PROPERTIES'); ?></a></li>
            <?php if($this->ipauth->getSuper() || $this->ipauth->getAdmin() || $this->ipauth->getAuthLevel() == 3): ?>
                <li<?php if($layout == 'agentlist') echo ' class="active"'; ?>><a href="<?php echo JRoute::_('&layout=agentlist'); ?>"><?php echo JText::_('COM_IPROPERTY_AGENTS'); ?></a></li>
                <li<?php if($layout == 'companylist') echo ' class="active"'; ?>><a href="<?php echo JRoute::_('&layout=companylist'); ?>"><?php echo JText::_('COM_IPROPERTY_COMPANIES'); ?></a></li>
            <?php endif; ?>
            <li<?php if($layout == 'openhouselist') echo ' class="active"'; ?>><a href="<?php echo JRoute::_('&layout=openhouselist'); ?>"><?php echo JText::_('COM_IPROPERTY_OPENHOUSES'); ?></a></li>
        </ul>
    </div>
</div>
<div class="clearfix"> </div>