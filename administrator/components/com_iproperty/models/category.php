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

class IpropertyModelCategory extends JModelAdmin
{
    protected $text_prefix  = 'COM_IPROPERTY';

    public function getTable($type = 'Category', $prefix = 'IpropertyTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

    public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_iproperty.category', 'category', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		// Modify the form based on access controls.
		if (!$this->canEditState((object) $data)) {
			// Disable fields for display.
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('published', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is a record you can edit.
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('published', 'filter', 'unset');
		}

		return $form;
	}

    protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_iproperty.edit.category.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

    protected function getReorderConditions($table)
	{
		$condition = array();
		$condition[] = 'parent = '.(int) $table->parent;

		return $condition;
	}

    public function performRemove($cids, $subcats = '')
	{
        if(empty($cids)){            
            $this->setError(JText::_('COM_IPROPERTY_NO_ITEM_SELECTED'));
            return false; 
        }        
		
		$db   = JFactory::getDbo();
        $successful = 0;

        function deleteCat($id, &$successful = 0){
			$cat = JTable::getInstance('Category', 'IpropertyTable');
			$cat->load($id);            

			//Then recurse to subcats
			$scats = IpropertyModelCategory::getSubcategories($id);
			foreach($scats as $sc){
                deleteCat($sc->id, $successful);
			}

			//delete self
			$parent = $cat->parent;
			if(!IpropertyModelCategory::delete_cat($id)){
                $this->setError(JText::_('COM_IPROPERTY_CAT_NOT_DELETED' ).': '.$id.' Error: '.$this->getError());
                return false;
			}
            $successful++;
			$cat->reorder( 'parent = '.(int)$parent );
            return $successful;
		}

        $cat = JTable::getInstance('Category', 'IpropertyTable');
		foreach($cids as $c){
			$cat->load($c);
            
            //Move to parent level if told to do so
			if(!$subcats){
				$parent = $cat->parent;
                $query = $db->getQuery(true);
                $query->update('#__iproperty_categories')
                        ->set('parent = '.(int)$parent)
                        ->where('parent = '.(int)$cat->id);
                
				$db->setQuery($query);
				if(!$db->execute()){
                    $this->setError($db->getErrorMsg());
                    return false;
				}	
			}
            
			//Delete recursively, if !$subcats, no recursion occurs.
            if($count = deleteCat($cat->id)){
                $successful = $successful + $count;
            }else{
                $this->setError($this->getError());
                return false;
            }
		}
		return $successful;
	}
    
    public function getSubcategories($id = null)
    {
		$db = JFactory::getDbo();
        
        $query = $db->getQuery(true);
        $query->select('*')
                ->from('#__iproperty_categories');
        if($id){
            $query->where('parent = '.(int)$id);
        }
        $query->order('ordering ASC');
   
		$db->setQuery($query);
        $result = $db->loadObjectList();
		
		return $result;
	}
    
    public function delete_cat($id = null)
    {
		$db = JFactory::getDbo();
        
        $query = $db->getQuery(true);
        $query->delete('#__iproperty_categories')
                ->where('id = '.(int)$id);

        $db->setQuery( $query );
        if(!$db->execute()){
            $this->setError($db->getErrorMsg());
            return false;
        }
        
        $query = $db->getQuery(true);
        $query->delete('#__iproperty_propmid')
                ->where('cat_id = '.(int)$id);

        $db->setQuery( $query );
        if(!$db->execute()){
            $this->setError($db->getErrorMsg());
            return false;
        }
        return true;	
	}
}
?>