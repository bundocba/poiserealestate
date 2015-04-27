<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
jimport('joomla.application.component.view');

class IpropertyViewAdvsearch extends JViewLegacy
{
	function display()
	{        
        $document       = JFactory::getDocument();
		$model          = $this->getModel();
		$properties		= $this->get('data');
        
		$this->assignRef('properties', $properties);        
        return $this->properties;
	}
}
?>