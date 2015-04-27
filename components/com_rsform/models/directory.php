<?php
/**
* @version 1.4.0
* @package RSform!Pro 1.4.0
* @copyright (C) 2007-2013 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');

class RSFormModelDirectory extends JModelLegacy
{
	protected $fields;
	
	/**
	 *	Main constructor
	 */
	public function __construct($config = array()) {
		$this->_app		= JFactory::getApplication();
		$this->_db		= JFactory::getDbo();
		$this->params	= $this->_app->getParams('com_rsform');
		$this->itemid	= $this->getItemid();
		$this->context	= 'com_rsform.directory'.$this->itemid;
		
		// Check for a valid form
		if (!$this->isValid()) {
			JError::raiseWarning(500, JText::_('JERROR_ALERTNOAUTHOR'));
			$this->_app->redirect(JURI::root());
		}
		
		$this->getFields();
		parent::__construct();
	}
	
	/**
	 *	Check if we are allowed to show content
	 */
	public function isValid() {
		$formId = $this->params->get('formId',0);
		
		if (!$this->params->get('enable_directory', 0)) {
			return false;
		}
		
		// Do we have a valid formId
		if (empty($formId)) {
			return false;
		}
		
		// Check if the directory exists
		$this->_db->setQuery('SELECT COUNT('.$this->_db->qn('formId').') FROM '.$this->_db->qn('#__rsform_directory').' WHERE '.$this->_db->qn('formId').' = '.(int) $formId.'');
		if ($count = (int) $this->_db->loadResult())
			return true;
		else 
			return false;
	}
	
	/**
	 *	Get directory fields
	 */
	public function getFields() {
		if (!is_array($this->fields)) {
			$this->fields = RSFormProHelper::getDirectoryFields($this->params->get('formId'));
		}
		
		return $this->fields;
	}
	
	/**
	 *	Submissions query
	 */
	public function getListQuery() {
		$query 	= 'SELECT s.SubmissionId';
		$fields = $this->getFields();
		
		$viewable	= array();
		$searchable	= array();
		
		foreach ($fields as $field) {
			if ($field->viewable)
				$viewable[] = $field->FieldName;
			if ($field->searchable)
				$searchable[] = $field->FieldName;
		}
		
		$diff = array_diff($searchable, $viewable);
		
		$selectFields = array();
		foreach ($fields as $field) {
			if ($field->viewable) {
				$selectFields[] = 'GROUP_CONCAT(IF(sv.FieldName='.$this->_db->q($field->FieldName).', sv.FieldValue, NULL)) AS '.$this->_db->qn($field->FieldName);
			}
		}
		
		if ($diff) {
			foreach ($diff as $value) {
				$selectFields[] = 'GROUP_CONCAT(IF(sv.FieldName='.$this->_db->q($value).', sv.FieldValue, NULL)) AS '.$this->_db->qn($value);
			}
		}
		
		if ($selectFields) {
			$query .= ', '.implode(',', $selectFields);
		}
		
		$query .= ' FROM #__rsform_submission_values sv LEFT JOIN #__rsform_submissions s ON sv.SubmissionId = s.SubmissionId  WHERE s.FormId = '.(int) $this->params->get('formId').' ';
		
		$confirmed = $this->params->get('show_confirmed', 0);
		if ($confirmed)
			$query .= ' AND s.confirmed = 1 ';
		
		$lang = $this->params->get('lang', '');
		if ($lang)
			$query .= ' AND s.Lang = '.$this->_db->q($lang).' ';
		
		$userId = $this->params->def('userId', 0);
		if ($userId == 'login')
		{
			$user = JFactory::getUser();
			if ($user->get('guest'))
				$query .= " AND 1>2";
			
			$query .= " AND s.UserId='".(int) $user->get('id')."'";
		}
		elseif ($userId == 0)
		{
			// Show all submissions
		}
		else
		{
			$userId = explode(',', $userId);
			JArrayHelper::toInteger($userId);
			
			$query .= " AND s.UserId IN (".implode(',', $userId).")";
		}	
		
		$query .= ' GROUP BY s.SubmissionId ';
		
		if ($search = $this->getSearch()) {
			$having = array();
			foreach ($fields as $field) {
				if ($field->searchable) {
					$having[] = $this->_db->qn($field->FieldName).' LIKE '.$this->_db->q('%'.$this->_db->escape($search, true).'%', false);
				}
			}
			
			if ($having) {
				$query .= ' HAVING ('.implode(' OR ', $having).')';
			}
			
		}
		
		$query .= ' ORDER BY '.$this->_db->qn($this->getListOrder()).' '.$this->_db->escape($this->getListDirn());
		return $query;
	}
	
	/**
	 *	Get Submissions
	 */
	public function getItems() {
		$query	= $this->getListQuery();
		
		$this->_db->setQuery($query, $this->getStart(), $this->getLimit());
		$items	= $this->_db->loadObjectList();
		
		list($multipleSeparator, $uploadFields, $multipleFields, $secret) = RSFormProHelper::getDirectoryFormProperties($this->params->get('formId'));
		$this->uploadFields 	= $uploadFields;
		$this->multipleFields 	= $multipleFields;
		
		if ($items) {
			foreach ($items as $i => $item) {
				foreach ($uploadFields as $field) {
					if (isset($item->$field)) {
						$item->$field = '<a href="'.JRoute::_('index.php?option=com_rsform&task=submissions.view.file&hash='.md5($item->SubmissionId.$secret.$field)).'">'.htmlspecialchars(basename($item->$field)).'</a>';
					}
				}
				foreach ($multipleFields as $field) {
					if (isset($item->$field)) {
						$item->$field = str_replace("\n", $multipleSeparator, htmlentities($item->$field, ENT_COMPAT, 'utf-8'));
					}
				}
				$items[$i] = $item;
			}
		}
		
		return $items;
	}
	
	/**
	 *	Get directory details
	 */
	public function getDirectory() {
		$formId = $this->params->get('formId',0);
		$table 	= JTable::getInstance('RSForm_Directory', 'Table');
		$table->load($formId);
		
		return $table;
	}
	
	public function getTemplate() {
		$cid		= $this->_app->input->getInt('id',0);
		$format		= $this->_app->input->get('format');
		$user		= JFactory::getUser();
		$userId		= $this->params->def('userId', 0);
		$directory	= $this->getDirectory();
		$template	= $directory->ViewLayout;
	
		if ($userId != 'login' && $userId != 0) {
			$userId = explode(',', $userId);
			JArrayHelper::toInteger($userId);
		}
		
		$this->_db->setQuery("SELECT * FROM #__rsform_submissions WHERE SubmissionId='".$cid."'");
		$submission = $this->_db->loadObject();
		
		if (!$submission || ($submission->FormId != $this->params->get('formId')) || ($userId == 'login' && $submission->UserId != $user->get('id')) || (is_array($userId) && !in_array($user->get('id'), $userId))) {
			JError::raiseWarning(500, JText::_('ALERTNOTAUTH'));
			$this->_app->redirect(JURI::root());
			return;
		}
		
		if ($this->params->get('show_confirmed', 0) && !$submission->confirmed) {
			JError::raiseWarning(500, JText::_('ALERTNOTAUTH'));
			$this->_app->redirect(JURI::root());
			return;
		}
		
		$confirmed = $submission->confirmed ? JText::_('RSFP_YES') : JText::_('RSFP_NO');
		list($replace, $with) = RSFormProHelper::getReplacements($cid, true);
		list($replace2, $with2) = $this->getReplacements($submission->UserId);
		$replace = array_merge($replace, $replace2, array('{global:userip}', '{global:date_added}', '{global:submissionid}', '{global:submission_id}', '{global:confirmed}'));
		$with 	 = array_merge($with, $with2, array($submission->UserIp, RSFormProHelper::getDate($submission->DateSubmitted), $cid, $cid, $confirmed));
		
		if ($format == 'pdf') {
			if (strpos($template, ':path}') !== false) {
				$template = str_replace(':path}',':localpath}',$template);
			}
		}
		
		if (strpos($template, '{if ') !== false && strpos($template, '{/if}') !== false) {
			require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/scripting.php';
			RSFormProScripting::compile($template, $replace, $with);
		}
		
		$detailsLayout = str_replace($replace, $with, $template);
		eval($directory->DetailsScript);
		
		return $detailsLayout;
	}
	
	public function getReplacements($user_id) {
		$config  = JFactory::getConfig();
		$user    = JFactory::getUser((int) $user_id);
		$replace = array('{global:sitename}', '{global:siteurl}', '{global:userid}', '{global:username}', '{global:email}');
		$with 	 = array($config->get('sitename'), JURI::root(), $user->get('id'), $user->get('username'), $user->get('email'));
			
		return array($replace, $with);
	}
	
	public function save() {
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		
		$offset 	= $this->_app->getCfg('offset');
		$cid    	= JRequest::getInt('id');
		$form   	= JRequest::getVar('form', array(), 'post', 'none', JREQUEST_ALLOWRAW);
		$formId 	= JRequest::getInt('formId');
		$files  	= JRequest::getVar('form', array(), 'files', 'none', JREQUEST_ALLOWRAW);
		$validation = RSFormProHelper::validateForm($formId, 'directory');
		
		if (!empty($validation)) {
			return false;
		}
		
		//Trigger Event - onBeforeDirectorySave
		$this->_app->triggerEvent('rsfp_f_onBeforeDirectorySave', array(array('SubmissionId'=>&$cid,'formId'=>$formId,'post'=>&$form)));
		
		// Handle file uploads first
		if (!empty($files['error']))
		foreach ($files['error'] as $field => $error)
		{
			if ($error)
				continue;
				
			// The above $validation should suffice
			$this->_db->setQuery("SELECT FieldValue FROM #__rsform_submission_values WHERE FieldName='".$this->_db->escape($field)."' AND SubmissionId='".$cid."' LIMIT 1");
			$original = $this->_db->loadResult();
			
			// Prefix
			$componentId 	= RSFormProHelper::getComponentId($field, $formId);
			$data 			= RSFormProHelper::getComponentProperties($componentId);
			$prefix 		= uniqid('').'-';
			if (isset($data['PREFIX']) && strlen(trim($data['PREFIX'])) > 0)
				$prefix = RSFormProHelper::isCode($data['PREFIX']);
			
			// Path
			$realpath = realpath($data['DESTINATION'].DIRECTORY_SEPARATOR);
			if (substr($realpath, -1) != DIRECTORY_SEPARATOR)
				$realpath .= DIRECTORY_SEPARATOR;
			
			// Filename
			$file = $realpath.$prefix.$files['name'][$field];
			
			// Upload File
			if (JFile::upload($files['tmp_name'][$field], $file) && $file != $original) {
				// Remove the original file to save up space
				if (file_exists($original) && is_file($original)) {
					JFile::delete($original);
				}
				
				// Add to db (submission value)
				$form[$field] = $file;
			}
		}
		
		// Update fields
		foreach ($form as $field => $value)
		{
			if (is_array($value))
				$value = implode("\n", $value);
				
			$this->_db->setQuery("SELECT SubmissionValueId, FieldValue FROM #__rsform_submission_values WHERE FieldName='".$this->_db->escape($field)."' AND SubmissionId='".$cid."' LIMIT 1");
			$original = $this->_db->loadObject();
			if (!$original)
			{
				$this->_db->setQuery("INSERT INTO #__rsform_submission_values SET FormId='".$formId."', SubmissionId='".$cid."', FieldName='".$this->_db->escape($field)."', FieldValue='".$this->_db->escape($value)."'");
				$this->_db->execute();
			}
			else
			{
				// Update only if we've changed something
				if ($original->FieldValue != $value)
				{						
					$this->_db->setQuery("UPDATE #__rsform_submission_values SET FieldValue='".$this->_db->escape($value)."' WHERE SubmissionValueId='".$original->SubmissionValueId."' LIMIT 1");
					$this->_db->execute();
				}
			}
		}
		
		// Checkboxes don't send a value if nothing is checked
		$this->_db->setQuery("SELECT p.PropertyValue FROM #__rsform_components c LEFT JOIN #__rsform_properties p ON (c.ComponentId=p.ComponentId) WHERE c.ComponentTypeId='4' AND p.PropertyName='NAME' AND c.FormId='".$formId."'");
		$checkboxes = $this->_db->loadColumn();
		foreach ($checkboxes as $checkbox) {
			$value = isset($form[$checkbox]) ? $form[$checkbox] : '';
			if (is_array($value))
				$value = implode("\n", $value);
				
			$this->_db->setQuery("UPDATE #__rsform_submission_values SET FieldValue='".$this->_db->escape($value)."' WHERE FieldName='".$this->_db->escape($checkbox)."' AND FormId='".$formId."' AND SubmissionId='".$cid."' LIMIT 1");
			$this->_db->execute();
		}
		
		// Send emails
		$this->sendEmails($formId, $cid);
		return true;
	}
	
	public function sendEmails($formId, $SubmissionId) {
		$directory = $this->getDirectory();
		
		$this->_db->setQuery("SELECT Lang FROM #__rsform_submissions WHERE FormId='".$formId."' AND SubmissionId='".$SubmissionId."'");
		$lang = $this->_db->loadResult();
		
		list($placeholders,$values) = RSFormProHelper::getReplacements($SubmissionId);
		
		$this->_db->setQuery("SELECT * FROM #__rsform_emails WHERE `type` = 'directory' AND `formId` = ".$formId." AND `from` != ''");
		if ($emails = $this->_db->loadObjectList()) {
			$etranslations = RSFormProHelper::getTranslations('emails', $formId, $lang);
			foreach ($emails as $email) {
				if (isset($etranslations[$email->id.'.fromname'])) {
					$email->fromname = $etranslations[$email->id.'.fromname'];
				}
				if (isset($etranslations[$email->id.'.subject'])) {
					$email->subject = $etranslations[$email->id.'.subject'];
				}
				if (isset($etranslations[$email->id.'.message'])) {
					$email->message = $etranslations[$email->id.'.message'];
				}
				
				if (empty($email->fromname) || empty($email->subject) || empty($email->message)) {
					continue;
				}
				
				// RSForm! Pro Scripting - Additional Email Text
				// performance check
				if (strpos($email->message, '{if ') !== false && strpos($email->message, '{/if}') !== false) {
					require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/scripting.php';
					RSFormProScripting::compile($email->message, $placeholders, $values);
				}
				
				$directoryEmail = array(
					'to' => str_replace($placeholders, $values, $email->to),
					'cc' => str_replace($placeholders, $values, $email->cc),
					'bcc' => str_replace($placeholders, $values, $email->bcc),
					'from' => str_replace($placeholders, $values, $email->from),
					'replyto' => str_replace($placeholders, $values, $email->replyto),
					'fromName' => str_replace($placeholders, $values, $email->fromname),
					'text' => str_replace($placeholders, $values, $email->message),
					'subject' => str_replace($placeholders, $values, $email->subject),
					'mode' => $email->mode,
					'files' => array()
				);
				
				// additional cc
				if (strpos($directoryEmail['cc'], ',') !== false)
					$directoryEmail['cc'] = explode(',', $directoryEmail['cc']);
				// additional bcc
				if (strpos($directoryEmail['bcc'], ',') !== false)
					$directoryEmail['bcc'] = explode(',', $directoryEmail['bcc']);
				
				//Trigger Event - beforeDirectoryEmail
				$this->_app->triggerEvent('rsfp_beforeDirectoryEmail', array(array('directory' => &$directory, 'placeholders' => &$placeholders, 'values' => &$values, 'submissionId' => $SubmissionId, 'directoryEmail'=>&$directoryEmail)));
				
				eval($directory->EmailsScript);
				
				// mail users
				$recipients = explode(',',$directoryEmail['to']);
				if(!empty($recipients))
					foreach($recipients as $recipient)
						if(!empty($recipient))
							RSFormProHelper::sendMail($directoryEmail['from'], $directoryEmail['fromName'], $recipient, $directoryEmail['subject'], $directoryEmail['text'], $directoryEmail['mode'], !empty($directoryEmail['cc']) ? $directoryEmail['cc'] : null, !empty($directoryEmail['bcc']) ? $directoryEmail['bcc'] : null, $directoryEmail['files'], !empty($directoryEmail['replyto']) ? $directoryEmail['replyto'] : '');
				
			}
		}
	}
	
	public function getUploadFields() {
		return $this->uploadFields;
	}
	
	public function getMultipleFields() {
		return $this->multipleFields;
	}
	
	public function getTotal() {
		$query	= $this->getListQuery();
		
		$this->_db->setQuery($query);
		$this->_db->execute();

		return @$this->_db->getNumRows();
	}
	
	public function getPagination() {
		jimport('joomla.html.pagination');
		return new JPagination($this->getTotal(), $this->getStart(), $this->getLimit());
	}
	
	public function getStart() {
		static $limitstart;
		if (is_null($limitstart)) {
			$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');
		}
		
		return $limitstart;
	}
	
	public function getLimit() {
		static $limit;
		if (is_null($limit)) {
			$limit	= JRequest::getVar('limit', $this->params->get('display_num'), '', 'int');
		}
		
		return $limit;
	}
	
	public function getSearch() {
		return $this->_app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search', '', 'string');
	}
	
	public function getListOrder() {
		return $this->_app->getUserStateFromRequest($this->context.'.filter.filter_order', 'filter_order', 'SubmissionId', '');
	}
	
	public function getListDirn() {
		return $this->_app->getUserStateFromRequest($this->context.'.filter.filter_order_Dir', 'filter_order_Dir', 'desc', 'word');
	}
	
	public function getEditFields() {
		$id	= $this->_app->input->getInt('id',0);
		return RSFormProHelper::getEditFields($id);
	}
	
	// Get current Itemid
	public function getItemid() {
		if ($menu = $this->_app->getMenu()) {
			$active = $menu->getActive();
			return $active->id;
		} else {
			return 0;
		}
	}
}