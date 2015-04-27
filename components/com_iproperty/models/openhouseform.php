<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

// No direct access
defined('_JEXEC') or die;

// Base this model on the backend version.
require_once JPATH_ADMINISTRATOR.'/components/com_iproperty/models/openhouse.php';

class IpropertyModelOpenhouseForm extends IpropertyModelOpenhouse
{
	protected function populateState()
	{
		$app    = JFactory::getApplication();

		// Load state from the request.
		$pk     = $app->input->getInt('id');
		$this->setState('openhouse.id', $pk);

		$return = $app->input->get('return', null, 'default', 'base64');
		$this->setState('return_page', base64_decode($return));

		// Load the parameters.
		$params	= $app->getParams();
		$this->setState('params', $params);

		$this->setState('layout', $app->input->getCmd('layout'));
	}

	public function getItem($itemId = null)
	{
		// Initialise variables.
		$itemId     = (int) (!empty($itemId)) ? $itemId : $this->getState('openhouse.id');

		// Get a row instance.
		$table      = $this->getTable();

		// Attempt to load the row.
		$return     = $table->load($itemId);

		// Check for a table object error.
		if ($return === false && $table->getError()) {
			$this->setError($table->getError());
			return false;
		}

		$properties = $table->getProperties(1);
		$item      = JArrayHelper::toObject($properties, 'JObject');
        
        if (property_exists($item, 'params'))
		{
			$registry = new JRegistry;
			$registry->loadString($item->params);
			$item->params = $registry->toArray();
		}

		return $item;
	}

	public function getReturnPage()
	{
		return base64_encode($this->getState('return_page'));
	}
}