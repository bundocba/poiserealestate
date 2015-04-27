<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access');
jimport('joomla.application.component.controller');

class IpropertyControllerContact extends JControllerForm
{
	protected $text_prefix = 'COM_IPROPERTY';
    
    public function getModel($name = '', $prefix = '', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, array('ignore_request' => false));
	}
    
    public function contactForm()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app    = JFactory::getApplication();
		$model  = $this->getModel('contact');
		$stub   = $this->input->getString('id');
		$id     = (int) $stub;
        
        #TODO: replace with base64encode
        $link = @$_SERVER['HTTP_REFERER'];
        if (empty($link) || !JURI::isInternal($link)) {
            $link = JURI::base();
        }

		// Get the data from POST
		$data       = $this->input->post->get('jform', array(), 'array');
		$contact    = $model->getItem($id);

		// Validate the posted data.
		$form = $model->getForm();
		if (!$form)
		{
			JError::raiseError(500, $model->getError());
			return false;
		}

		$validate = $model->validate($form, $data);

		if ($validate === false)
		{
			// Get the validation messages.
			$errors	= $model->getErrors();
			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				} else {
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_iproperty.contact.data', $data);

			// Redirect back to the contact form.
			$this->setRedirect($link);
			return false;
		}

		// Set the success message if it was a success
		if ($model->sendContact($data, $id))
		{
			$msg    = JText::_('COM_IPROPERTY_CONTACT_CONFIRM');
            $type   = 'message';
		}
		else
		{
			$msg    = JText::_('COM_IPROPERTY_CONTACT_FAIL');
            $type   = 'notice';
		}

		// Flush the data from the session
		$app->setUserState('com_iproperty.contact.data', null);

		// Redirect back to where we came from
		$this->setRedirect($link, $msg, $type);

		return true;
	}
}
