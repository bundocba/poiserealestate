<?php
/**
* @version 1.4.0
* @package RSform!Pro 1.4.0
* @copyright (C) 2007-2013 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view');

class RSFormViewDirectory extends JViewLegacy
{
	public function display( $tpl = null ) {
		$this->app			= JFactory::getApplication();
		$this->doc			= JFactory::getDocument();
		$this->params		= $this->app->getParams('com_rsform');
		$this->layout		= $this->getLayout();
		$this->directory	= $this->get('Directory');
		
		if ($this->layout == 'view') {
			
			$this->doc->addStyleSheet(JURI::root(true).'/components/com_rsform/assets/css/directory.css');
			
			$this->template = $this->get('template');
			$this->canEdit	= RSFormProHelper::canEdit($this->params->get('formId'),$this->app->input->getInt('id',0));
			$this->id		= $this->app->input->getInt('id',0);
			
			// Add custom CSS and JS
			if ($this->directory->JS)
				$this->doc->addCustomTag($this->directory->JS);
			if ($this->directory->CSS)
				$this->doc->addCustomTag($this->directory->CSS);
		
		} elseif ($this->layout == 'edit') {
			if (RSFormProHelper::canEdit($this->params->get('formId'),$this->app->input->getInt('id',0))) {
				$this->doc->addStyleSheet(JURI::root(true).'/components/com_rsform/assets/css/directory.css');
				$this->fields		= $this->get('EditFields');
			} else {
				$this->app->redirect(JURI::root());
			}
		} else {
			$this->search		= $this->get('Search');
			$this->items 		= $this->get('Items');
			$this->uploadFields = $this->get('uploadFields');
			$this->multipleFields = $this->get('multipleFields');
			$this->unescapedFields = array_merge($this->multipleFields, $this->uploadFields);
			$this->fields		= $this->get('Fields');
			$this->pagination 	= $this->get('Pagination');
		
			$this->filter_search 	= $this->get('Search');
			$this->filter_order 	= $this->get('ListOrder');
			$this->filter_order_Dir = $this->get('ListDirn');
		
			$this->viewableFields = array();
			foreach ($this->fields as $field) {
				if ($field->viewable) {
					$this->viewableFields[] = $field;
				}
			}
			
			// Add custom CSS and JS
			if ($this->directory->JS)
				$this->doc->addCustomTag($this->directory->JS);
			if ($this->directory->CSS)
				$this->doc->addCustomTag($this->directory->CSS);
		}
		
		parent::display($tpl);
	}
	
	public function pdfLink($id) {
		$app		= JFactory::getApplication();
		$has_suffix = $app->getCfg('sef') && $app->getCfg('sef_suffix');
		$pdf_link = JRoute::_('index.php?option=com_rsform&view=directory&layout=view&id='.$id.'&format=pdf');
		if ($has_suffix) {
			$pdf_link .= strpos($pdf_link, '?') === false ? '?' : '&';
			$pdf_link .= 'format=pdf';
		}
		
		return $pdf_link;
	}
}