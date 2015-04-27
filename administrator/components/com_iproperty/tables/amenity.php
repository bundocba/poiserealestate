<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');

class IpropertyTableAmenity extends JTable
{
	public function __construct(&$_db)
	{
		parent::__construct('#__iproperty_amenities', 'id', $_db);
	}

	public function check()
	{
		jimport('joomla.filter.output');

		// Set name
		$this->title = htmlspecialchars_decode($this->title, ENT_QUOTES);
		return true;
	}

    public function bind($array, $ignore = '')
	{
		return parent::bind($array, $ignore);
	}

    public function store($data = false)
	{
		
        if (!empty($this->id)){ // Store single amenity when editing
			return parent::store($data);
		}
		else // Store all posted amenities from add amenities layout
		{
            $post   = JRequest::get('post');
            $db     = JFactory::getDbo();

            for($i = 0; $i < sizeof($post['title']); $i++){
                if($post['title'][$i]){
                    $newamen = new StdClass();
                    $newamen->title = $post['title'][$i];
                    $newamen->cat   = $post['cat'][$i];

                    if(!$db->insertObject('#__iproperty_amenities', $newamen)){
                        $this->setError($db->getErrorMsg());
                        return false;
                    }
                }
            }
        }
		return true;
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

		try
		{			
			// delete from amenities table
            $query = $this->_db->getQuery(true);
            $query->delete();
            $query->from('#__iproperty_amenities');
            $query->where('id IN ('.implode(',', $pks).')');
            $this->_db->setQuery($query);
            
			// Check for a database error.
            if (!$this->_db->execute()) {
				throw new Exception($this->_db->getErrorMsg());
			}
            
            // delete from property mid table
            $query = $this->_db->getQuery(true);
            $query->delete();
            $query->from('#__iproperty_propmid');
            $query->where('amen_id IN ('.implode(',', $pks).')');
            $this->_db->setQuery($query);
            
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
		return count($this->getErrors())==0;
	}
    
    public function saveCats($pks, $post)
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

		try
		{
            foreach($pks as $p){
                // change categories
                $query = 'UPDATE #__iproperty_amenities SET cat = '.(int)$post['amen_cat_'.$p].' WHERE id = '.(int)$p;
                $this->_db->setQuery($query);
                
                // Check for a database error.
                if (!$this->_db->execute()) {
                    throw new Exception($this->_db->getErrorMsg());
                }
            }		
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		}
		return count($this->getErrors())==0;
    }
}
?>