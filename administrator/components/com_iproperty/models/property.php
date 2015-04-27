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

class IpropertyModelProperty extends JModelAdmin
{
    protected $text_prefix = 'COM_IPROPERTY';

	public function getTable($type = 'Property', $prefix = 'IpropertyTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_iproperty.property', 'property', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		// Modify the form based on access controls.
		if (!$this->canEditState((object) $data)) {
			// Disable fields for display.
			$form->setFieldAttribute('state', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is a record you can edit.
			$form->setFieldAttribute('state', 'filter', 'unset');
		}

		return $form;
	}

	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_iproperty.edit.property.data', array());
        $user = JFactory::getUser();

		if (empty($data)) {
			$data = $this->getItem();

			// Prime some default values.
			if ($this->getState('property.id') == 0) {
                $settings   = ipropertyAdmin::config();                

                // Set defaults according to IP config
                $data->categories       = $settings->default_category;
                $data->listing_office   = $settings->default_company;
                $data->agents           = $settings->default_agent;
                $data->locstate         = $settings->default_state;
                $data->country          = $settings->default_country;              
                
                // Set other defaults
                $data->access           = 1;
                $data->created_by       = $user->get('id');
                $data->hits             = 0;
			}else{
                //populate multiple select lists
                $data->categories       = $this->_getCategories($data->id);
                $data->agents           = $this->_getAgents($data->id);
                
                //populate amenities checkbox lists
                $amenities              = $this->_getAmenities($data->id);
                $data->general_amens    = $amenities;
                $data->interior_amens   = $amenities;
                $data->exterior_amens   = $amenities;
				$data->green_amens    = $amenities;
                $data->community_amens   = $amenities;
                $data->landscape_amens   = $amenities;
				$data->appliance_amens    = $amenities;
                $data->security_amens   = $amenities;
                $data->accessibility_amens   = $amenities;
                
                // @TODO: add Joomla tags
                /*$data->tags = new JHelperTags;
                $data->tags->getTagIds($this->getState('property.id'), 'com_iproperty.property');
                $data->metadata['tags'] = $data->tags;*/
            }
		}
		return $data;
	}
    
    public function publishProp($pks, $value = 0)
	{
		// Initialise variables.
		$table	= $this->getTable();
		$pks	= (array) $pks;
        $ipauth = new ipropertyHelperAuth();

		$successful = 0;
        // Access checks.
		foreach ($pks as $i => $pk) {
			if ($table->load($pk)) {
				if (!$ipauth->canPublishProp($pk, $value)){
					// Prune items that you can't change.
					unset($pks[$i]);
                    JError::raise(E_WARNING, 403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
				}else{
                    $successful++;
                }
			}
		}

		// Attempt to change the state of the records.
		if (!$table->publish($pks, $value)) {
			$this->setError($table->getError());
			return false;
		}

		return $successful;
	}
    
    public function featureProp($pks, $value = 0)
	{
		// Initialise variables.
		$table	= $this->getTable();
		$pks	= (array) $pks;
        $ipauth = new ipropertyHelperAuth();

		$successful = 0;
        // Access checks.
		foreach ($pks as $i => $pk) {
			if ($table->load($pk)) {
				if (!$ipauth->canFeatureProp($pk, $value)){
					// Prune items that you can't change.
					unset($pks[$i]);
                    JError::raise(E_WARNING, 403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
				}else{
                    // Attempt to change the state of the records.
                    if (!$table->feature($pk, $value)) {
                        $this->setError($table->getError());
                        return false;
                    }
                    $successful++;
                }
			}
		}
        return $successful;
	}
    
    public function deleteProp($pks)
	{
		// Initialise variables.
		$table	= $this->getTable();
		$pks	= (array) $pks;
        $ipauth = new ipropertyHelperAuth();

		$successful = 0;
        // Access checks.
		foreach ($pks as $i => $pk) {
			if ($table->load($pk)) {
				if (!$ipauth->canDeleteProp($pk)){
					// Prune items that you can't change.
					unset($pks[$i]);
                    JError::raise(E_WARNING, 403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
				}else{
                    $successful++;
                }
			}
		}

		// Attempt to change the state of the records.
		if (!$table->delete($pks)) {
			$this->setError($table->getError());
			return false;
		}

		return $successful;
	}
    
    protected function _getAmenities($prop_id)
    {
        $query = $this->_db->getQuery(true);
        
        $query->select('amen_id')
            ->from('#__iproperty_propmid')
            ->where('prop_id = '.(int)$prop_id)
            ->where('amen_id != 0')
            ->group('amen_id');
        
        $this->_db->setQuery($query);
        return $this->_db->loadColumn();
    }
    
    protected function _getCategories($prop_id)
    {
        $query = $this->_db->getQuery(true);
        
        $query->select('cat_id')
            ->from('#__iproperty_propmid')
            ->where('prop_id = '.(int)$prop_id)
            ->where('cat_id != 0')
            ->group('cat_id');
        
        $this->_db->setQuery($query);
        return $this->_db->loadColumn();
    }
    
    protected function _getAgents($prop_id)
    {
        $query = $this->_db->getQuery(true);
        
        $query->select('agent_id')
            ->from('#__iproperty_agentmid')
            ->where('prop_id = '.(int)$prop_id)
            ->group('agent_id');
        
        $this->_db->setQuery($query);
        return $this->_db->loadColumn();
    } 
    
    public function clearHits($pks)
    {
        // Initialise variables.
		$table	= $this->getTable();
		$pks	= (array) $pks;
        $ipauth = new ipropertyHelperAuth();

		$successful = 0;
        // Access checks.
		foreach ($pks as $i => $pk) {
			if ($table->load($pk)) {
				if (!$ipauth->canEditProp($pk)){
					// Prune items that you can't change.
					unset($pks[$i]);
                    JError::raise(E_WARNING, 403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
				}else{
                    $successful++;
                }
			}
		}

		// Attempt to change the state of the records.
		if (!$table->clearHits($pks)) {
			$this->setError($table->getError());
			return false;
		}

		return $successful;
    }
    
    protected function prepareTable($table)
	{
		// trim all values so we don't get multiple cities etc. with space and without
		foreach ($table as $key => $value){
			if (isset($value))	$table->$key = trim($value);
		}
		// Set the publish date to now
		if($table->state == 1 && intval($table->publish_up) == 0) {
			$table->publish_up = JFactory::getDate()->toSql();
		}
	}
    
    public function save($data)
    {
        jimport('joomla.filesystem.file');
        
        $this->setState($this->getName().'.oldid', JRequest::getInt('id'));
      
        // check for KML
        $kml        = JRequest::getVar('jform', array(), 'files', 'array');        
        $filename   = JFile::makeSafe($kml['name']['kmlfile']);
		

		if($filename != "") {
            $src        = $kml['tmp_name']['kmlfile'];
            $dest       =  JPATH_SITE."/media/com_iproperty/kml/".$filename;
			if ( JFile::upload($src, $dest) ) {
				$data['kml'] = $filename;  				
			} 
		}
        
        return parent::save($data);
    }
    
    public function saveMids($validData)
    {        
        //die($this->getState($this->getName().'.oldid').'==='.$this->getState($this->getName().'.id'));
        //die('here -- <pre>'.var_dump($validData).'</pre>');        
        // Property Id we're dealing with
        $settings   = ipropertyAdmin::config();
        $ipauth     = new ipropertyHelperAuth();
        
        $propid     = $this->getState($this->getName().'.id');        
        $isNew      = $this->getState($this->getName().'.new');
        $oldId      = $this->getState($this->getName().'.oldid');
		
		$amens		= array();
        
        // Categories
        $cats = $validData['categories'];
        
        // Agents
        $agents = $validData['agents'];
        //die('here -- <pre>'.var_dump(!empty($agents) && $agents[0] !== '').'</pre>'); 
 
        // If this is a new listing and no agent(s) have been assigned and the user is not admin
        // Set the agent as the current user agent
        // this is used on the front end when the basic agent cannot assign agents to listings
        if((empty($agents) || $agents[0] == '') && !$ipauth->getAdmin() && $isNew){
            $agents = array($ipauth->getUagentId());
        }
        
        // Amenities
        // Thanks to bianchijc for code modification to avoid notices       
        $amen_fields    = array('general_amens', 'interior_amens', 'exterior_amens', 'accessibility_amens', 'green_amens', 'security_amens', 'landscape_amens', 'community_amens', 'appliance_amens' );
        foreach ($amen_fields as $f) {
            if (array_key_exists($f, $validData) && is_array($validData[$f])) {
                $amens = array_merge($amens, $validData[$f]);
            }
        }
        
        $table	= $this->getTable();

		// Attempt to clear prop mid table in order to save new results
		if (!$table->deletePropMids($propid)) {
			$this->setError($table->getError());
			return false;
		}

		// Attempt to save categories in prop mid table
        if (!$table->storeCatMids($propid, $cats)) {
			$this->setError($table->getError());
			return false;
		}
        
        // If the agents array is not empty, clear the agent mid table for this listing
        // and save new results for agent array
        // If the array is empty it means that a non-super agent is saving an existing listing so we
        // want to keep the existing agent(s) assigned to the listing
        if(!empty($agents) && $agents[0] !== ''){
            if (!$table->deleteAgentMids($propid)) {
                $this->setError($table->getError());
                return false;
            }
            
            if (!$table->storeAgentMids($propid, $agents)) {
                $this->setError($table->getError());
                return false;
            }
        }
        
        if (!$table->storeAmenities($propid, $amens)) {
			$this->setError($table->getError());
			return false;
		}
        
        // If a new property, check if it's being saved as a copy and the old id is valid
        // If so, clone the images from the old property to the new
        if($isNew && $oldId && $oldId != 0){
            $table->cloneImages($oldId, $propid);
        }
        
        // Trigger after save edit plugin
        JPluginHelper::importPlugin( 'iproperty');        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onAfterSavePropertyEdit', array($propid, $isNew, $settings));
    }
    
    public function approveProp($pks, $value = 0)
	{
		// Initialise variables.
		$table	= $this->getTable();
		$pks	= (array) $pks;
        $ipauth = new ipropertyHelperAuth();

		$successful = 0;
        // Access checks.
		foreach ($pks as $i => $pk) {
			if ($table->load($pk)) {
				if (!$ipauth->canEditProp($pk, $value)){
					// Prune items that you can't change.
					unset($pks[$i]);
                    JError::raise(E_WARNING, 403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
				}else{
                    // Attempt to change the state of the records.
                    if (!$table->approve($pk, $value)) {
                        $this->setError($table->getError());
                        return false;
                    }
                    $successful++;
                }
			}
		}
        return $successful;
	}
}
?>
