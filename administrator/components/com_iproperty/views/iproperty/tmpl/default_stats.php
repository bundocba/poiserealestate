<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
?>

<h1><?php echo JText::_('COM_IPROPERTY_STATISTICS'); ?></h1>
<div>
    <ul class="nav nav-tabs">
        <li class="active"><a href="#iptopprops" data-toggle="tab"><?php echo JText::_('COM_IPROPERTY_TOP_PROPERTIES');?></a></li>
        <li><a href="#ipfeaturedprops" data-toggle="tab"><?php echo JText::_('COM_IPROPERTY_FEATURED_PROPERTIES');?></a></li>
        <li><a href="#ipactiveusers" data-toggle="tab"><?php echo JText::_('COM_IPROPERTY_MOST_ACTIVE_USERS');?></a></li>
    </ul>
    <div class="tab-content">
        <?php echo $this->loadTemplate('tprops'); ?>
        <?php echo $this->loadTemplate('fprops'); ?>
        <?php echo $this->loadTemplate('usersave'); ?>
    </div>
</div>