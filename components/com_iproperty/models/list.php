<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
jimport('joomla.application.component.model');

class IpropertyModelList extends JModelList
{    
    protected $ipsettings;
    protected $hotsheet;
	protected $api;
    
    public function __construct($config = array())
	{
		parent::__construct($config);
	}	
}