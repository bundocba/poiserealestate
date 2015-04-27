<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');

require(JPATH_SITE.'/components/com_contact/models/rules/contactemailmessage.php');

/**
 * JFormRule for com_iproperty to make sure the message body contains no banned word.
 * Uses banned text list from the com_contact configuration - extend here if needed
 *
 * @package     Joomla.Site
 * @subpackage  com_iproperty
 */
class JFormRuleIpropertyEmailMessage extends JFormRuleContactEmailMessage
{
}