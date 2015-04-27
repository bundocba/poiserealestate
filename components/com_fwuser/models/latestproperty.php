<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modellist');

require_once(JPATH_SITE.'/components/com_iproperty/helpers/html.helper.php');
require_once(JPATH_SITE.'/components/com_iproperty/helpers/property.php');
require_once(JPATH_SITE.'/components/com_iproperty/helpers/route.php');
require_once(JPATH_SITE.'/components/com_iproperty/helpers/query.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_iproperty/classes/admin.class.php');

class fwuserModellatestproperty extends JModelLegacy{
	
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

        $db     = JFactory::getDbo();
        $sort           = 'RAND()';
        $order          = '';

        $pquery = new IpropertyHelperQuery($db, $sort, $order);
        $query  = $pquery->buildPropertyQuery($where, 'properties');
        $db->setQuery($query, 0, $count);
        $hidenopic = true;
        $items = ipropertyHelperProperty::getPropertyItems($db->loadObjectList(), true, false, $hidenopic);

	return $items;
    }
	
  
	
}