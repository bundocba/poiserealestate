<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');

class IpropertyTableCategory extends JTable
{
	public function __construct(&$_db)
	{
		parent::__construct('#__iproperty_categories', 'id', $_db);
	}

    public function check()
	{
		jimport('joomla.filter.output');
        $date       = JFactory::getDate();

		// Set name
		$this->title = htmlspecialchars_decode($this->title, ENT_QUOTES);
        
        // Set alias
		$this->alias = JApplication::stringURLSafe($this->alias);
		if (empty($this->alias)) {
			$this->alias = JApplication::stringURLSafe($this->title);
		}
        
        // Check the publish down date is not earlier than publish up.
		if ($this->publish_down > $this->_db->getNullDate() && $this->publish_down < $this->publish_up) {
			// Swap the dates.
			$temp = $this->publish_up;
			$this->publish_up = $this->publish_down;
			$this->publish_down = $temp;
		}
        
        if(!$this->id){
            if (!intval($this->publish_up)) {
				$this->publish_up = $date->toSql();
			}
        }

		// Set ordering
		if ($this->state < 0) {
			// Set ordering to 0 if state is archived or trashed
			$this->ordering = 0;
		} else if (empty($this->ordering)) {
			// Set ordering to last if ordering was 0
			$this->ordering = self::getNextOrder('`parent`=' . $this->_db->Quote($this->parent).' AND state>=0');
		}

		return true;
	}

	public function bind($array, $ignore = array())
	{
		if (isset($array['params']) && is_array($array['params'])) {
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = (string)$registry;
		}
		return parent::bind($array, $ignore);
	}

	public function store($updateNulls = false)
	{
		// Set icon as nopic if none selected
        $updateNulls['icon'] = ($updateNulls['icon'] == '') ? 'nopic.png' : $updateNulls['icon'];

        // Transform the params field
		if (is_array($this->params)) {
			$registry = new JRegistry;
			$registry->loadArray($this->params);
			$this->params = (string) $registry;
		}
        
        // Verify that the alias is unique
		$table = JTable::getInstance('Category', 'IpropertyTable');
		if ($table->load(array('alias' => $this->alias)) && ($table->id != $this->id || $this->id == 0))
		{
			$this->setError(JText::_('COM_IPROPERTY_ERROR_UNIQUE_ALIAS'));
			return false;
		}
        
		// Attempt to store the data.
		return parent::store($updateNulls);
	}

	public function publish($pks = null, $state = 1, $userID = 0)
	{
		// Initialise variables.
		$k = $this->_tbl_key;

		// Sanitize input.
		JArrayHelper::toInteger($pks);
		$state  = (int) $state;

		// If there are no primary keys set check to see if the instance key is set.
		if (empty($pks))
		{
			if ($this->$k) {
				$pks = array($this->$k);
			}
			// Nothing to set publishing state on, return false.
			else {
				$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
				return false;
			}
		}

		// Get an instance of the table
		$table = JTable::getInstance('Category','IpropertyTable');

		// For all keys
		foreach ($pks as $pk)
		{
			// Load the banner
			if(!$table->load($pk))
			{
				$this->setError($table->getError());
			}
            // Change the state
            $table->state = $state;
            
            $recurse = JRequest::getVar('catrecurse', 0 );
			if($recurse == 1){
				$query = 'UPDATE #__iproperty_categories'
				        . ' SET state = '. (int) $state
				        . ' WHERE parent IN ('.implode(',',$pks).')';
                        
				$this->_db->setQuery( $query );
				
				if (!$this->_db->execute()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}			
			}

            // Check the row
            $table->check();

            // Store the row
            if (!$table->store())
            {
                $this->setError($table->getError());
            }
		}
		return count($this->getErrors())==0;
	}
}
?>