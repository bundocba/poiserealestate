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

require_once (JPATH_ADMINISTRATOR.'/components/com_iproperty/classes/admin.class.php');
require_once (JPATH_SITE.'/components/com_iproperty/helpers/auth.php');

class JFormFieldAgent extends JFormFieldList
{
    protected $type = 'Agent';

    public function getOptions($useauth = false)
	{
        $useauth    = (isset($this->element) && $this->element['useauth']) ? true : $useauth;
        
        JFactory::getLanguage()->load('com_iproperty', JPATH_ADMINISTRATOR);
		$options = array();
        
        $db     = JFactory::getDBO();
        $query  = $db->getQuery(true);
        $query->select('id AS value, CONCAT_WS(",", lname, fname) AS text')
            ->from('`#__iproperty_agents`')
            ->where('state = 1');
            if($useauth){
                $ipauth = new ipropertyHelperAuth();
                if (!$ipauth->getAdmin()) {
                    switch ($ipauth->getAuthLevel()){
                        case 1: //company level
                            $query->where('company = '.(int)$ipauth->getUagentCid());
                        break;
                        case 2: //agent level
                            $query->where('company = '.(int)$ipauth->getUagentCid());
                            // if not a super agent, only show all company agents if its the multiselect list
                            if (!$ipauth->getSuper()) $query->where('id = '.(int)$ipauth->getUagentId());
                        break;
                    }
                }
            }
        $query->order('lname ASC');
        $db->setQuery($query);

        try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}
        
        // Merge any additional options in the XML definition.
		if(isset($this->element))
        {            
            $options = array_merge(parent::getOptions(), $options);
            array_unshift($options, JHtml::_('select.option', '', JText::_('COM_IPROPERTY_AGENTS')));
        }

		return $options;
    }
}