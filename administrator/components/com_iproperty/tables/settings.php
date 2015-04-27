<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');

class IpropertyTableSettings extends JTable
{
	public function __construct(&$_db)
	{
		parent::__construct('#__iproperty_settings', 'id', $_db);
	}
}
?>