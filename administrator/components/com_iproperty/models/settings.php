<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
jimport('joomla.application.component.modeladmin');

class IpropertyModelSettings extends JModelAdmin
{
    protected $text_prefix = 'COM_IPROPERTY';

	public function getTable($type = 'Settings', $prefix = 'IpropertyTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_iproperty.settings', 'settings', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		return $form;
	}

	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_iproperty.edit.settings.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}    

    public function getStypes()
    {
        $query = 'SELECT * FROM #__iproperty_stypes WHERE 1 ORDER BY id ASC';
        $this->_db->setQuery($query);
        $stypes = $this->_db->loadObjectList();
        return $stypes;
    }

    public function saveStypes($data)
    {
        foreach($data as $d){
            if(is_numeric($d[0])){ // this is an existing stype
                $stype          = (int) $d[0];
                $name           = $this->_db->Quote($d[1]);
                $bann           = $this->_db->Quote($d[2]);
                $bann_color     = $this->_db->Quote($d[3]);
                $bann_pub       = (int) $d[4];
                $bann_show      = (int) $d[5];
                $request_form   = (int) $d[6];

                $query = "UPDATE #__iproperty_stypes SET name = " . $name . ", banner_image = " . $bann . ", banner_color = " . $bann_color . ", state = " . $bann_pub . ", show_banner = " . $bann_show . ", show_request_form = " . $request_form . " WHERE id = " . $stype;

            } elseif($d[0] == 'new' && $d[1]){ // this is a new one
                $name           = $this->_db->Quote($d[1]);
                $bann           = $this->_db->Quote($d[2]);
                $bann_color     = $this->_db->Quote($d[3]);
                $bann_pub       = (int) $d[4];
                $bann_show      = (int) $d[5];
                $request_form   = (int) $d[6];

                $query = "INSERT INTO #__iproperty_stypes (name, banner_image, banner_color, state, show_banner, show_request_form) VALUES(" . $name . "," . $bann . "," . $bann_color . "," . $bann_pub . "," . $bann_show . ", " . $request_form . ")";

            }

            $this->_db->setQuery($query);
            if(!$this->_db->execute()){
                $this->setError($this->_db->getErrorMsg());
                return false;
            }
        }		
        return true;
    }

    public function deleteStype($stypeid)
    {
        $query = 'DELETE FROM #__iproperty_stypes WHERE id = '.(int)$stypeid.' LIMIT 1';
        $this->_db->setQuery($query);
        if(!$this->_db->execute()){
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        return true;
    }
}

?>