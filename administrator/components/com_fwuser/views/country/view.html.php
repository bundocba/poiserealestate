<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
class fwuserViewcountry extends JViewLegacy{
	function display($tpl = null){
		$items = $this->get('Items');
		$pagination = $this->get('Pagination');

		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		$this->rows = $items;
		$this->pagination = $pagination;

		
		$this->addToolBar();
		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
		$this->setDocument();
	}	
	protected function addToolBar(){
		
		require_once JPATH_COMPONENT .'/helpers/fwuser.php';
		
		fwuserHelper::addSubmenu('fwuser');
		
		JToolBarHelper::title(JText::_('COM_FWUSER_COUNTRY_TITLE'), 'fwuser');
		
		$document = JFactory::getDocument();
		$document->addStyleDeclaration('.icon-48-fwuser {background-image: url(../administrator/components/com_fwuser/images/icon-48-country.png);}');
		
		$canDo = JHelperContent::getActions('com_fwuser');
		if ($canDo->get('core.create')){
			JToolBarHelper::addNew('countryedit.add');
		}
		if ($canDo->get('core.edit')){
			JToolBarHelper::editList('countryedit.edit');
		}
		if ($canDo->get('core.delete')){
			JToolBarHelper::deleteList('', 'country.delete');
		}
	}	
	protected function setDocument(){
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_FWUSER_COUNTRY_TITLE'));
	}
}