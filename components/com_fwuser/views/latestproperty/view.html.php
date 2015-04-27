<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
class fwuserViewlatestproperty extends JViewLegacy{
    
    	
        
	public function display($tpl = null){
		if (count($errors = $this->get('Errors'))){
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		$active = JFactory::getApplication()->getMenu()->getActive();
		if (isset($active->query['layout'])){
			$this->setLayout($active->query['layout']);
		}				
                
                $items = $this->get("Item");
                $this->assignRef("items", $items);
                $pagination	= $this->get('Pagination');
                 $this->assignRef("pagination", $pagination);
                
		parent::display($tpl);
	}
}
