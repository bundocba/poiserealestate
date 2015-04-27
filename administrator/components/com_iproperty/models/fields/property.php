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

class JFormFieldProperty extends JFormFieldList
{
    protected $type = 'Property';

    public function getOptions($useauth = false)
	{
        $useauth = (isset($this->element) && $this->element['useauth']) ? true : $useauth;
        
        JFactory::getLanguage()->load('com_iproperty', JPATH_ADMINISTRATOR);
        $options = array();

        $db         = JFactory::getDbo();
        $user       = JFactory::getUser();
        $groups     = $user->getAuthorisedViewLevels();

        // Filter by start and end dates.
        $nullDate   = $db->Quote($db->getNullDate());
        $date       = JFactory::getDate();
        $nowDate    = $db->Quote($date->toSql());        

        $query = $db->getQuery(true);
        $query->select('id AS value, CONCAT(street_num, " ", street, ", ", city, " - ", property_name) AS text')
            ->from('`#__iproperty`');
            if($useauth){
                $ipauth = new ipropertyHelperAuth();
                if (!$ipauth->getAdmin()) {
                    switch ($ipauth->getAuthLevel()){
                        case 1: //company level
                            $query->where('listing_office = '.(int)$ipauth->getUagentCid());
                        break;
                        case 2: //agent level
                            $query->where('listing_office = '.(int)$ipauth->getUagentCid());
                            if (!$ipauth->getSuper()) $query->where('id IN ( SELECT prop_id FROM #__iproperty_agentmid WHERE agent_id = '.(int)$ipauth->getUagentId().' )');
                        break;
                    }
                }
            }
        (is_array($groups) && !empty($groups)) ? $query->where('access IN ('.implode(",", $groups).')') : '';
        $query->where('state = 1')
            ->where('(publish_up = '.$nullDate.' OR publish_up <= '.$nowDate.')')
            ->where('(publish_down = '.$nullDate.' OR publish_down >= '.$nowDate.')')
            ->order('street_num, street ASC');

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
            array_unshift($options, JHtml::_('select.option', '', JText::_('COM_IPROPERTY_PROPERTY')));
        }

		return $options;
    }
}