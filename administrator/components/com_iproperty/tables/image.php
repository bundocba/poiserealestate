<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
jimport( 'joomla.filesystem.file');
jimport( 'joomla.filesystem.folder');

class IpropertyTableImage extends JTable
{
	public function __construct(&$_db)
	{
		parent::__construct('#__iproperty_images', 'id', $_db);
	}

	public function check()
	{
		jimport('joomla.filter.output');
        $ipauth = new IpropertyHelperAuth(array('msg'=>false));

		// Set name
		$this->title = htmlspecialchars_decode($this->title, ENT_QUOTES);

		// Set ordering
		if (empty($this->ordering)) {
			// Set ordering to last if ordering was 0
			$this->ordering = self::getNextOrder('`propid` = '.$this->_db->Quote($this->propid));
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
		if (empty($this->id)){
			parent::store($updateNulls);
		}
		else
		{
			// Get the old row
			$oldrow = JTable::getInstance('Image', 'IpropertyTable');
			if (!$oldrow->load($this->id) && $oldrow->getError()){
				$this->setError($oldrow->getError());
			}

			// Store the new row
			parent::store($updateNulls);

			// Need to reorder ?
			if ($oldrow->state >= 0){
				// Reorder the oldrow
				$this->reorder('`propid`=' . $this->_db->Quote($this->propid).' AND state>=0');
			}
		}
		return count($this->getErrors())==0;
	}

	public function publish($pks = null, $state = 1, $userID = 0)
	{
		// Initialise variables.
		$k = $this->_tbl_key;       

		// Sanitize input.
		JArrayHelper::toInteger($pks);
		$state      = (int) $state;

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
		$table = JTable::getInstance('Image','IpropertyTable');

		// For all keys
		foreach ($pks as $pk)
		{
            // Load the banner
            if(!$table->load($pk)){
                $this->setError($table->getError());
            }

            // Change the state
            $table->state = $state;

            // Check the row
            $table->check();

            // Store the row
            if (!$table->store()){
                $this->setError($table->getError());
            }
		}
		return count($this->getErrors())==0;
	}
    
    public function delete($pks = null)
	{
        // Initialise variables.
		$k = $this->_tbl_key;      

		// Sanitize input.
		JArrayHelper::toInteger($pks);

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
        
        foreach($pks as $pk){

            try
            {			
                $table = JTable::getInstance('Image','IpropertyTable');
                $table->load($pk);
                $fname = $table->fname;
                $type  = $table->type;
                $path  = $table->path;

                // is the image local?
                if (!$table->remote){
                    // is the image file linked to another object?
                    $query = $this->_db->getQuery(true);
                    $query->select("id");
                    $query->from("#__iproperty_images");
                    $query->where("fname = ".$this->_db->Quote($fname));
                    $query->where("id != ".(int)$pk);
                    
                    $this->_db->setQuery($query);
                    if($this->_db->loadResult() == null) {
                        // the image is not used anywhere else, so delete the actual file
                        $path		= JPATH_SITE.$path;
                        $img_d		= $path.$fname.$type;
                        $thumb_d 	= $path.$fname."_thumb".$type;
                        JFile::delete($img_d);
                        JFile::delete($thumb_d);
                    }
                }
                // now delete the reference to the image from the images table
                $query = $this->_db->getQuery(true);
                $query->delete();
                $query->from("#__iproperty_images");
                $query->where("id = ".(int)$pk);
                
                $this->_db->setQuery( $query );

                // Check for a database error.
                if (!$this->_db->execute()) {
                    throw new Exception($this->_db->getErrorMsg());
                }
            }
            catch (Exception $e)
            {
                $this->setError($e->getMessage());
                return false;
            }
        }
		return count($this->getErrors())==0;
	}
}
?>