<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
jimport( 'joomla.application.component.view');

class IpropertyViewCondition extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;
    protected $settings;
    protected $ipauth;

    public function display($tpl = null)
	{
        // Initialiase variables.
       
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');
        $this->settings = ipropertyAdmin::config(); 
        $this->ipauth   = new ipropertyHelperAuth();

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raise(E_ERROR, 500, implode("\n", $errors));
			return false;
		}

        // if no agent id and user is not admin - no access
        if (!$this->ipauth->getAdmin() && !$this->ipauth->getSuper()){
            $this->setLayout('noaccess');
            $this->_displayNoAccess($tpl);
            return;
        }

		$this->addToolbar();
		parent::display($tpl);
	}

    protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$isNew		= ($this->item->id == 0);

		JToolBarHelper::title($isNew ? JText::_('COM_IPROPERTY_ADD_CONDITION') : JText::_('COM_IPROPERTY_EDIT_CONDITION').': '.$this->item->name, 'iproperty.png');

        // If not checked out, can save the item.
        JToolBarHelper::apply('condition.apply', 'JTOOLBAR_APPLY');
        JToolBarHelper::save('condition.save', 'JTOOLBAR_SAVE');
        JToolBarHelper::divider();
        // Only show these options to super agents or admin
        if ($this->ipauth->getAdmin()){
            JToolBarHelper::custom('condition.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);

            // If an existing item, can save to a copy.
            if (!$isNew) {
                JToolBarHelper::custom('condition.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
            }
            JToolBarHelper::divider();
        }        

		if (empty($this->item->id))  {
			JToolBarHelper::cancel('condition.cancel','JTOOLBAR_CANCEL');
		}
		else {
			JToolBarHelper::cancel('condition.cancel', 'JTOOLBAR_CLOSE');
		}
	}

    public function _displayNoAccess($tpl = null)
    {
        JToolBarHelper::title(JText::_('COM_IPROPERTY_NO_ACCESS'), 'iproperty.png');
        JToolBarHelper::back();
        parent::display($tpl);
    }
}
?>