<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Iproperty
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license see LICENSE.php
 */

//no direct access
defined('_JEXEC') or die('Restricted access');

// get module params
$pinned         = $params->get('pinned_position', 'top');
$bgcolor        = $params->get('bgcolor', '#ededed');
$bdcolor        = $params->get('bdcolor', '#dddddd');
$bootstrap      = $params->get('bootstrap_css', false);

// load bootstrap css if param is enabled
if($bootstrap){
    $lang = JFactory::getLanguage();
    $lang_direction = $lang->isRTL() ? 'rtl' : 'ltr';
    JHtml::_('bootstrap.loadCss', true, $lang_direction);
}

// add style for pinned div
$pinned_css = '#agenttoolstatus {background: '.$bgcolor.'; padding: 4px 20px; border-'.$pinned.': 1px solid '.bdcolor.';';
// if top position, add a drop shadow
$pinned_css .= ($pinned == 'top') ? '-moz-box-shadow: 0 10px 10px -7px DarkGray; -webkit-box-shadow: 0 10px 10px -7px DarkGray; box-shadow: 0 10px 10px -7px DarkGray;' : ''; 
// end style and add to head
$pinned_css .= '}';
$pinned_css .= '#agenttoolstatus li{border-left: solid 1px '.$bdcolor.';}';
$doc->addStyleDeclaration($pinned_css);

// get basic agent and super agent profile and company edit links
$edit_profile_link = 'index.php?option=com_iproperty&view=agentform&task=agentform.edit&id='.$ipauth->getUagentId().'&return='.base64_encode(JURI::getInstance());
$edit_company_link = 'index.php?option=com_iproperty&view=companyform&task=companyform.edit&id='.$ipauth->getUagentCid().'&return='.base64_encode(JURI::getInstance());
?>

<div id="agenttoolstatus" class="navbar navbar-fixed-<?php echo $pinned; ?> hidden-phone">
    <a class="brand" href="#"><?php echo JText::_('MOD_IP_AGENTTOOLBAR_TITLE'); ?></a>
    <ul class="nav">
        <li><a href="<?php echo JRoute::_(ipropertyHelperRoute::getManageRoute()); ?>"><?php echo JText::_('MOD_IP_AGENTTOOLBAR_MANAGE_PROPERTIES'); ?></a></li>
        
        <?php 
        // Agent list or edit profile links
        if($ipauth->getSuper() || $ipauth->getAdmin()): // super agent or admins should see the link to manage all agents
        ?>
            <li><a href="<?php echo JRoute::_(ipropertyHelperRoute::getManageRoute().'&layout=agentlist'); ?>"><?php echo JText::_('MOD_IP_AGENTTOOLBAR_MANAGE_AGENTS'); ?></a></li>
        <?php 
        else: // basic agent should only see a link to edit own profile 
        ?>
            <li><a href="<?php echo $edit_profile_link; ?>"><?php echo JText::_('MOD_IP_AGENTTOOLBAR_EDIT_PROFILE'); ?></a></li>
        <?php 
        endif; 
        ?>
        
        <?php 
        // Company list or edit company links
        if($ipauth->getAdmin()): // admin should see the link to manage all companies 
        ?>
            <li><a href="<?php echo JRoute::_(ipropertyHelperRoute::getManageRoute().'&layout=companylist'); ?>"><?php echo JText::_('MOD_IP_AGENTTOOLBAR_MANAGE_COMPANIES'); ?></a></li>
        <?php 
        elseif($ipauth->getSuper()): // super agent should see a link to edit own company
        ?>
            <li><a href="<?php echo $edit_company_link; ?>"><?php echo JText::_('MOD_IP_AGENTTOOLBAR_EDIT_COMPANY'); ?></a></li>
        <?php 
        endif; 
        ?>
        <li><a href="<?php echo JRoute::_('index.php?option=com_users&view=login'); ?>"><?php echo JText::_('MOD_IP_AGENTTOOLBAR_LOGOUT'); ?></a></li>
    </ul>
</div>