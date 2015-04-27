<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined('JPATH_BASE') or die;

require_once (JPATH_ADMINISTRATOR.'/components/com_iproperty/classes/admin.class.php');
require_once (JPATH_SITE.'/components/com_iproperty/helpers/auth.php');
require_once (JPATH_SITE.'/components/com_iproperty/helpers/html.helper.php');

class JFormFieldModal_Property extends JFormField
{
    protected $type = 'Modal_Property';
    
    protected function getInput()
	{
		// Load the modal behavior script.
		JHtml::_('behavior.modal', 'a.modal');
        
        // If calling from front end, restrict propert list to agent or company where applicable
        if(JFactory::getApplication()->getName() == 'site')
        {
            $ipauth     = new ipropertyHelperAuth();
            if (!$ipauth->getAdmin() && ($ipauth->getAuthLevel() == 1 || $ipauth->getAuthLevel() == 2)) {
                switch ($ipauth->getAuthLevel()){
                    case 1: //company
                        $vlink = 'companyproperties&amp;id='.(int)$ipauth->getUagentCid();
                        break;
                    case 2:
                        $vlink = 'agentproperties&amp;id='.(int)$ipauth->getUagentId();
                        break;
                }
            }else{
                $vlink = 'allproperties';
            }
            
        }else{ // If calling from adim panel, use the properties view since it already uses ACL to restrict access
            $vlink = 'properties';
        }
        
        //$vlink  = (JFactory::getApplication()->getName() == 'site') ? 'agentproperties&amp;id=1' : 'properties';
        //$vlink  = (JFactory::getApplication()->getName() == 'site') ? 'allproperties' : 'properties';
        
        $settings = ipropertyAdmin::config();       
        
        // Build the script.
		$script = array();
		$script[] = '	function ipSelectListing_'.$this->id.'(id, title, link) {';
		$script[] = '		document.id("'.$this->id.'_id").value = id;';
		$script[] = '		document.id("'.$this->id.'_name").value = title;';
		$script[] = '		SqueezeBox.close();';
		$script[] = '	}';

		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));
        
        // Setup variables for display
		$html = array();
		$link = 'index.php?option=com_iproperty&amp;view='.$vlink.'&amp;layout=modal&amp;tmpl=component&amp;field=' . $this->id;

		// Get the title of the linked chart
		$db = JFactory::getDBO();
		$db->setQuery(
			'SELECT *' .
			' FROM #__iproperty' .
			' WHERE id = '.(int) $this->value
		);

		try
		{
			if($property = $db->loadObject()){
                $title = ipropertyHTML::getStreetAddress($settings, $property);
            }
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage);
		}

		if (empty($title)) {
			$title = JText::_('COM_IPROPERTY_SELECT_PROPERTY');
		}
        $title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

		// The current user display field.
		$html[] = '<span class="input-append">';
		$html[] = '<input type="text" class="input-medium" id="'.$this->id.'_name" value="'.$title.'" disabled="disabled" size="35" /><a class="modal btn" title="'.JText::_('COM_IPROPERTY_SELECT_PROPERTY').'"  href="'.$link.'&amp;'.JSession::getFormToken().'=1" rel="{handler: \'iframe\', size: {x: 800, y: 450}}"><i class="icon-file"></i> '.JText::_('JSEARCH_FILTER_SUBMIT').'</a>';
		$html[] = '</span>';

		// The active property id field.
		if (0 == (int) $this->value) {
			$value = '';
		} else {
			$value = (int) $this->value;
		}

		// class='required' for client side validation
		$class = '';
		if ($this->required) {
			$class = ' class="required modal-value"';
		}

		$html[] = '<input type="hidden" id="'.$this->id.'_id"'.$class.' name="'.$this->name.'" value="'.$value.'" />';

		return implode("\n", $html);
	}
}