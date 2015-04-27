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

require_once (JPATH_SITE.'/components/com_iproperty/helpers/query.php');

class JFormFieldIpCategory extends JFormFieldList
{
    protected $type = 'IpCategory';

    public function getOptions()
	{        
        JFactory::getLanguage()->load('com_iproperty', JPATH_ADMINISTRATOR);

        $options = self::_multicatSelectList();
        
        // Merge any additional options in the XML definition.
		if(isset($this->element))
        {            
            $options = array_merge(parent::getOptions(), $options);
            array_unshift($options, JHtml::_('select.option', '', JText::_('COM_IPROPERTY_CATEGORY')));
        }
        
        return $options;
    }
    
    protected function _multicatSelectList()
    {
        $cats       = array();
        $options    = array_merge($cats, self::_multisubcatSelect(0, ""));
        
        return $options;
    }

    protected function _multisubcatSelect($parent, $prefix)
    {
        $options    = array();

        $db         = JFactory::getDbo();
        $query      = ipropertyHelperQuery::getCategories($parent);

        $db->setQuery($query);
        try
		{
			$cats = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}
        $total      = count($cats);

        for ($i = 0; $i < ($total-1); $i++)
        {
            $options[]  = JHTML::_('select.option', $cats[$i]->id,$prefix."- ".$cats[$i]->title, "value", "text");
            $options    = array_merge($options, self::_multisubcatSelect($cats[$i]->id, $prefix."- "));
        }

        if ($total > 0)
        {
            $options[]  = JHTML::_('select.option', $cats[$total-1]->id, $prefix."- ".$cats[$total-1]->title, "value", "text");
            $options    = array_merge($options, self::_multisubcatSelect($cats[$total-1]->id, $prefix."- "));
        }

        return $options;
    }
}


