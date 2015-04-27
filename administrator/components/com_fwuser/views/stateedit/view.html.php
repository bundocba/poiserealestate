<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
class fwuserViewstateEdit extends JViewLegacy{
	public function display($tpl = null){	
		$form = $this->get('Form');
		$item = $this->get('Item');
		$script = $this->get('Script');

		if (count($errors = $this->get('Errors'))){
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		
		$this->form = $form;
		$this->item = $item;
		$this->script = $script;

		$this->addToolBar();
		parent::display($tpl);
		$this->setDocument();
	}
	protected function addToolBar(){
		JRequest::setVar('hidemainmenu', true);
		
		$user = JFactory::getUser();
		$userId = $user->id;
		$isNew = ($this->item->id == 0);
		$canDo = JHelperContent::getActions( 'com_fwuser','state.' . $this->item->id);
		
		JToolBarHelper::title($isNew ? JText::_('COM_FWUSER_STATE_EDIT_CREATING') : JText::_('COM_FWUSER_STATE_EDIT_EDITING'));
		
		if($isNew){
			if ($canDo->get('core.create')){
				JToolBarHelper::apply('stateedit.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('stateedit.save', 'JTOOLBAR_SAVE');
				JToolBarHelper::custom('stateedit.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			}
			JToolBarHelper::cancel('stateedit.cancel', 'JTOOLBAR_CANCEL');
			
		}else{			
			if ($canDo->get('core.edit')){
				JToolBarHelper::apply('stateedit.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('stateedit.save', 'JTOOLBAR_SAVE');
				if ($canDo->get('core.create')){
					JToolBarHelper::custom('stateedit.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
				}
			}
			if ($canDo->get('core.create')){
				JToolBarHelper::custom('stateedit.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
			}
			JToolBarHelper::cancel('stateedit.cancel', 'JTOOLBAR_CLOSE');
		}
	}
	protected function setDocument(){
		$isNew = ($this->item->id < 1);
		$document = JFactory::getDocument();
		$document->setTitle($isNew ? JText::_('COM_FWUSER_STATE_EDIT_CREATING') : JText::_('COM_FWUSER_STATE_EDIT_EDITING'));
		
		$document->addScript(JURI::root() . $this->script);
		$document->addScript(JURI::root() . "/administrator/components/com_fwuser/views/stateedit/submitbutton.js");
		JText::script('COM_FWUSER_FORM_ERROR_MESSAGE');
	}
}
