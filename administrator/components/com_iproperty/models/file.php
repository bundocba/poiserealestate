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

class IpropertyModelFile extends JModelAdmin
{
    protected $text_prefix = 'COM_IPROPERTY';

    public function getTable($type = 'File', $prefix = 'IpropertyTable', $config = array())
    {
            return JTable::getInstance($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_iproperty.file', 'file', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		// Modify the form based on access controls.
		if (!$this->canEditState((object) $data)) {
			// Disable fields for display.
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('state', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is a record you can edit.
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('state', 'filter', 'unset');
		}

		return $form;
	}

    protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_iproperty.edit.agent.data', array());

		if (empty($data)) {
			$data = $this->getItem();

			// Prime some default values.
			if ($this->getState('file.id') == 0) {
                $settings   = ipropertyAdmin::config();

                //Set defaults according to WF config
                //$data->file      = $settings->default_file;
			}
		}

		return $data;
	}

	protected function getReorderFiles($table)
	{
		$file = array();
		$file[] = 'state >= 0';
		return $file;
	}
    
    public function publishFile($pks, $value = 0)
	{
		// Initialise variables.
		$table	= $this->getTable();
		$pks	= (array) $pks;
        $ipauth = new ipropertyHelperAuth();

		$successful = 0;
        // Access checks.
		foreach ($pks as $i => $pk) {
			if ($table->load($pk)) {
				if (!$ipauth->canPublishFile($pk)){
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
    
    public function featureFile($pks, $value = 0)
	{
		// Initialise variables.
		$table	= $this->getTable();
		$pks	= (array) $pks;
        $ipauth = new ipropertyHelperAuth();

		$successful = 0;
        // Access checks.
		foreach ($pks as $i => $pk) {
			if ($table->load($pk)) {
				if (!$ipauth->canFeatureFile($pk)){
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
    
    public function deleteFile($pks)
	{
		// Initialise variables.
		$table	= $this->getTable();
		$pks	= (array) $pks;
        $ipauth = new ipropertyHelperAuth();

		$successful = 0;
        // Access checks.
		foreach ($pks as $i => $pk) {
			if ($table->load($pk)) {
				if (!$ipauth->canDeleteFile($pk)){
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
}

?>