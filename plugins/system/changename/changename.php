<?php
/**
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Joomla! System Logging Plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	System.log
 */
class  plgSystemChangename extends JPlugin
{
	public function onAfterRender()
	{
		/*$app = JFactory::getApplication();
		$user = JFactory::getUser();

		$option = JRequest::getVar("option","");
		$view = JRequest::getVar("view","");
		$document = JFactory::getDocument();*/
		$buffer = JResponse::getBody();
		$buffer1=str_replace('Portman', 'POISE', $buffer);
		$buffer2=str_replace('portman', 'POISE', $buffer);
		$buffer3=str_replace('Portmanproperties', 'POISE Real Estate Pte Ltd', $buffer);
		$buffer4=str_replace('portmanproperties', 'POISE Real Estate Pte Ltd', $buffer);
		$buffer5=str_replace('admin@POISE Real Estate Pte Ltd.com.sg', 'admin@poiserealestate.com.sg', $buffer);
		JResponse::setBody($buffer1);
		JResponse::setBody($buffer2);
		JResponse::setBody($buffer3);
		JResponse::setBody($buffer4);
		JResponse::setBody($buffer5);
	}
}
