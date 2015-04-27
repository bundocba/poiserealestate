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

class IpropertyTableFile extends JTable
{
	public function __construct(&$_db)
	{            
		parent::__construct('#__iproperty_file', 'id', $_db);
	}

	public function check()
	{  
		jimport('joomla.filter.output');
        $ipauth = new IpropertyHelperAuth(array('msg'=>false));

		// Set name
		$this->title = htmlspecialchars_decode($this->title, ENT_QUOTES);

		// Set ordering
		/*if (empty($this->ordering)) {
			// Set ordering to last if ordering was 0
			$this->ordering = self::getNextOrder('`propid` = '.$this->_db->Quote($this->propid));
		}*/

		return true;
	}

	public function bind($array, $ignore = array())
	{           
           
            //upload file to server
                //FILE
		if(Jfolder::exists(JPATH_ROOT.DS.'documents'.DS.'file')==false){
			Jfolder::create(JPATH_ROOT.DS.'documents'.DS.'file'.DS);
			
		}
                $file = JRequest::get( 'FILES' );
               
                
                if(!empty($file)){
                    
                    $fileDestination 	= JPATH_SITE.DS.'documents'.DS.'file'.DS.$_FILES["files"]["name"];
                    
                    /*
                    if(JFile::Exists($fileDestination)){
			JFile::Delete($fileDestination);
                    }*/
                    
                    $uploaded = JFile::upload($_FILES["files"]["tmp_name"], $fileDestination);
                    
                }
            
            $array["fname"]= $_FILES["files"]["name"];
            $array["path"]= $fileDestination;
            $user = JFactory::getUser();
            $array["owner"] = $user->id;
                    
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
			$oldrow = JTable::getInstance('File', 'IpropertyTable');
			if (!$oldrow->load($this->id) && $oldrow->getError()){
				$this->setError($oldrow->getError());
			}
                        //print_r($updateNulls);die;
			// Store the new row
			parent::store($updateNulls);

			
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
		$table = JTable::getInstance('File','IpropertyTable');

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
                $table = JTable::getInstance('File','IpropertyTable');
                $table->load($pk);
                $fname = $table->fname;
                $type  = $table->type;
                $path  = $table->path;

                // is the file local?
                if (!$table->remote){
                    // is the file file linked to another object?
                    $query = $this->_db->getQuery(true);
                    $query->select("id");
                    $query->from("#__iproperty_file");
                    $query->where("fname = ".$this->_db->Quote($fname));
                    $query->where("id != ".(int)$pk);
                    
                    $this->_db->setQuery($query);
                    if($this->_db->loadResult() == null) {
                        // the file is not used anywhere else, so delete the actual file
                        $path		= JPATH_SITE.$path;
                        $img_d		= $path.$fname.$type;
                        $thumb_d 	= $path.$fname."_thumb".$type;
                        JFile::delete($img_d);
                        JFile::delete($thumb_d);
                    }
                }
                // now delete the reference to the file from the files table
                $query = $this->_db->getQuery(true);
                $query->delete();
                $query->from("#__iproperty_file");
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