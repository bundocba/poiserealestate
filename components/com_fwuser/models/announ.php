<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modellist');
class fwuserModelannoun extends JModelLegacy{
	
	function __construct()
    {
        parent::__construct();
        $array = JRequest::getVar('cid',  0, '', 'array');
        $this->setId((int)$array[0]);
    }
	
	function setId($id)
    {
        // Set id and wipe data
        $this->_id        = $id;
        $this->_data    = null;
    }
	
	
	
    function getItem(){

            $db = JFactory::getDbo();
            $query = $db->getQuery(true);

            $query->select($db->quoteName(array('datenow', 'name', 'description')));
            $query->from($db->quoteName('#__fwuser_announ','a'));

            // Reset the query using our newly populated query object.
            $db->setQuery($query);
            $result = $db->loadObjectList();

            
            return $result;
    }
	
    function store($data)
    {
        //$row =& $this->getTable();
		
		$row = JTable::getInstance('users', 'fwuserTable');
	
		if(!$row->bind($data))	
		{
			$this->setError($this->_db->getErrorMsg);
			return false;
		}
		//ki?m tra giá tr? các trý?ng trong form
		if(!$row->check())
		{
			$this->setError($this->_db->getErrorMsg);
			return false;
		}
	
        if (!$row->store($data)) {
            $this->setError( $row->getErrorMsg() );
            return false;
        }

        return true;
    }

	function splitArray($array){
		
		for ($i=0; $i<count($array); $i++) {
			$result .= $array[$i].',';
		}
		
		return $result;
	}
	
}