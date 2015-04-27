<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Iproperty
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license see LICENSE.php
 * @notes  based on Joomla mod_popular module
 */

defined('_JEXEC') or die;

require_once(JPATH_SITE.'/components/com_iproperty/helpers/html.helper.php');
require_once(JPATH_SITE.'/components/com_iproperty/helpers/query.php');
JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_iproperty/models', 'IpropertyModel');

abstract class modIpPopularHelper
{
	public static function getList(&$params)
	{
		$user = JFactory::getuser();

		// Get an instance of the generic articles model
		$model = JModelLegacy::getInstance('Properties', 'IpropertyModel', array('ignore_request' => true));

		// Set List SELECT
		$model->setState('list.select', 'p.id, p.title, p.checked_out, p.checked_out_time, ' .
				' p.created, p.hits, CONCAT_WS(" ", p.street_num, p.street, p.street2) AS street_address');

		// Set Ordering filter
		$model->setState('list.ordering', 'p.hits');
		$model->setState('list.direction', 'DESC');

		// Set Category Filter
		$categoryId = $params->get('catid');
		if (is_numeric($categoryId)){
			$model->setState('filter.cat_id', $categoryId);
		}

		// Set the Start and Limit
		$model->setState('list.start', 0);
		$model->setState('list.limit', $params->get('count', 5));

		$items = $model->getItems();

		if ($error = $model->getError()) {
			JError::raiseError(500, $error);
			return false;
		}

		// Set the links
		foreach ($items as &$item) 
        {
			if ($user->authorise('core.edit', 'com_iproperty.property.'.$item->id)){
				$item->link = JRoute::_('index.php?option=com_iproperty&task=property.edit&id='.$item->id);
			} else {
				$item->link = '';
			}
		}

		return $items;
	}
}