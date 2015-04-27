<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined('JPATH_BASE') or die;
JFormHelper::loadFieldClass('list');

class JFormFieldAgentSortBy extends JFormFieldList
{
    protected $type = 'AgentSortby';

    public function getOptions()
	{
        JFactory::getLanguage()->load('com_iproperty', JPATH_ADMINISTRATOR);        
        $jinput = JFactory::getApplication()->input;

        $options = array('a.lname' => JText::_('COM_IPROPERTY_LAST_NAME'),
                         'a.fname' => JText::_('COM_IPROPERTY_FIRST_NAME'));
        if($jinput->get('view') == 'agents') $options['c.id'] = JText::_('COM_IPROPERTY_COMPANY');
        
        // Merge any additional options in the XML definition.
		if(isset($this->element))
        {            
            $options = array_merge(parent::getOptions(), $options);
            array_unshift($options, JHtml::_('select.option', '', JText::_('COM_IPROPERTY_SORT')));
        }

        return $options;
    }
}


