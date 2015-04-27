<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Iproperty
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license see LICENSE.php
 */
 
defined('_JEXEC') or die('Restricted access'); 

$todayRates         = $Mrates['today'];
$lastweekRates      = $Mrates['lastWeek'];

$moduleclass_sfx    = ($params->get('moduleclass_sfx')) ? ' '.htmlspecialchars($params->get('moduleclass_sfx')) : '';
$menuclass_sfx      = htmlspecialchars($params->get('menuclass_sfx'));
?>

<div class="ip-zillowmrates-mod<?php echo $moduleclass_sfx; ?>">
    <h5><?php echo JText::_('TODAY RATE'); ?></h5>
    <ul class="<?php echo $menuclass_sfx; ?>">
        <li><strong><?php echo JText::_('MOD_IP_ZILLOW_30_FIXED'); ?>:</strong> <?php echo $todayRates['thirtyYearFixed']; ?>%</li>
        <li><strong><?php echo JText::_('MOD_IP_ZILLOW_15_FIXED'); ?>:</strong> <?php echo $todayRates['fifteenYearFixed']; ?>%</li>
        <li><strong><?php echo JText::_('MOD_IP_ZILLOW_51_ARM'); ?>:</strong> <?php echo $todayRates['fiveOneARM']; ?>%</li>
    </ul>

    <h5><?php echo JText::_('MOD_IP_ZILLOW_LAST_WEEK_RATE'); ?></h5>
    <ul class="<?php echo $menuclass_sfx; ?>">
        <li><strong><?php echo JText::_('MOD_IP_ZILLOW_30_FIXED'); ?>:</strong> <?php echo $lastweekRates['thirtyYearFixed']; ?>%</li>
        <li><strong><?php echo JText::_('MOD_IP_ZILLOW_15_FIXED'); ?>:</strong> <?php echo $lastweekRates['fifteenYearFixed']; ?>%</li>
        <li><strong><?php echo JText::_('MOD_IP_ZILLOW_51_ARM'); ?>:</strong> <?php echo $lastweekRates['fiveOneARM']; ?>%</li>
    </ul>

    <div class="small center">
        <?php echo JText::_('MOD_IP_ZILLOW_PROVIDED_BY'); ?>
        <a href="http://www.zillow.com" target="_blank"><img src="<?php echo JURI::root(true); ?>/modules/mod_ip_zillowmrates/zillow_logo.gif" align="middle" border="0" alt="Zillow" /></a>
    </div>
</div>