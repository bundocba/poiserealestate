<?php
/**
* @version 1.4.0
* @package RSform!Pro 1.4.0
* @copyright (C) 2007-2013 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class RSFormControllerDirectory extends RSFormController
{
	public function __construct() {
		parent::__construct();
	}
	
	public function download() {
		$app 		= JFactory::getApplication();
		$model		= $this->getModel('directory');
		$directory	= $model->getDirectory();
		
		if (!$directory->enablecsv || !$model->isValid()) {
			JError::raiseWarning(500, JText::_('JERROR_ALERTNOAUTHOR'));
			$app->redirect(JURI::root());
		}
		
		$db 	= JFactory::getDBO();
		$params = $app->getParams('com_rsform');
		$menu	= $app->getMenu();
		$active = $menu->getActive();
		$formId = $params->get('formId');
		$cids 	= JRequest::getVar('cid');
		JArrayHelper::toInteger($cids);
		
		$fields = RSFormProHelper::getDirectoryFields($formId);
		$downloadableFields = array();
		$downloadableFieldCaptions = array();
		foreach ($fields as $field) {
			if ($field->incsv) {
				$downloadableFields[] = $field->FieldName;
				$downloadableFieldCaptions[] = $field->FieldCaption;
			}
		}

		list($multipleSeparator, $uploadFields, $multipleFields, $secret) = RSFormProHelper::getDirectoryFormProperties($formId);
		
		// submissions
		$submissions = array();
		$db->setQuery("SELECT * FROM #__rsform_submission_values WHERE FormId='".(int) $formId."' AND SubmissionId IN (".implode(',', $cids).")");
		$list = $db->loadObjectList();
		foreach ($list as $item) {
			// process here
			if (in_array($item->FieldName, $uploadFields)) {
				$item->FieldValue = '<a href="'.JURI::root().'index.php?option=com_rsform&amp;task=submissions.view.file&amp;hash='.md5($item->SubmissionId.$secret.$item->FieldName).'">'.basename($item->FieldValue).'</a>';
			} elseif (in_array($item->FieldName, $multipleFields)) {
				$item->FieldValue = str_replace("\n", $multipleSeparator, $item->FieldValue);
			}
			$submissions[$item->SubmissionId][$item->FieldName] = $item->FieldValue;
		}
		
		$enclosure = '"';
		$delimiter = ',';
		
		$download_name = $active->alias.'.csv';
		header('Cache-Control: public, must-revalidate');
		header('Cache-Control: pre-check=0, post-check=0, max-age=0');
		if (!preg_match('#MSIE#', $_SERVER['HTTP_USER_AGENT']))
			header("Pragma: no-cache");
		header("Expires: 0"); 
		header("Content-Description: File Transfer");
		header("Expires: Sat, 01 Jan 2000 01:00:00 GMT");
		if (preg_match('#Opera#', $_SERVER['HTTP_USER_AGENT']))
			header("Content-Type: application/octetstream"); 
		else 
			header("Content-Type: application/octet-stream");
		header('Content-Disposition: attachment; filename="'.$download_name.'"');
		header("Content-Transfer-Encoding: binary\n");
		
		ob_end_clean();
		echo $enclosure.implode($enclosure.$delimiter.$enclosure, $downloadableFieldCaptions).$enclosure."\n";
		foreach ($cids as $cid) {
			$row = array();
			foreach ($downloadableFields as $field) {
				if (isset($submissions[$cid][$field])) {
					$row[] = str_replace($enclosure, $enclosure.$enclosure, $submissions[$cid][$field]);
				} else {
					$row[] = '';
				}
			}
			
			echo $enclosure.implode($enclosure.$delimiter.$enclosure, $row).$enclosure."\n";
		}
		
		jexit();
	}
	
	public function save() {
		$app 	= JFactory::getApplication();
		$formId	= $app->input->getInt('formId',0);
		$id		= $app->input->getInt('id',0);
		
		// Get the model
		$model = $this->getModel('directory');
		
		// Save
		if (RSFormProHelper::canEdit($formId,$id)) {
			if ($model->save()) {
				$this->setMessage(JText::_('RSFP_SUBM_DIR_SAVE_OK'));
				$this->setRedirect(JRoute::_('index.php?option=com_rsform&view=directory',false));
			} else {
				$app->enqueueMessage(JText::_('RSFP_SUBM_DIR_SAVE_ERROR'),'error');
				JRequest::setVar('view','directory');
				JRequest::setVar('layout','edit');
				JRequest::setVar('id',$id);
				
				parent::display();
			}
		} else {
			$this->setMessage(JText::_('JERROR_ALERTNOAUTHOR'),'error');
			$this->setRedirect(JRoute::_('index.php?option=com_rsform&view=directory',false));
		}
		
		
	}
}