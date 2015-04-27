<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

// no direct access
defined('_JEXEC' ) or die( 'Restricted access');
jimport('joomla.plugin.plugin');

class plgIpropertyJcomments extends JPlugin
{
	public function __construct(&$subject, $config)  
    {
		parent::__construct($subject, $config);
        $this->loadLanguage();
	}
    
    public function onBeforeRenderForms($property, $settings)
    {
		if ($this->params->get('position')) return true;
        $this->_doJcommentsForm($property, $settings);
    }
	
	public function onAfterRenderForms($property, $settings)
    {
        if (!$this->params->get('position')) return true;
        $this->_doJcommentsForm($property, $settings);
    }    

	private function _doJcommentsForm($property, $settings)
	{
        $app = JFactory::getApplication();
		if($app->getName() != 'site') return true;
        
        if(file_exists(JPATH_SITE.'/components/com_jcomments/jcomments.php')){
            require_once(JPATH_SITE.'/components/com_jcomments/jcomments.php');
        }else{
            return true;
        }
        
        echo JHtmlBootstrap::addTab('ipDetails', 'ipjcommentsplug', JText::_($this->params->get('tabtitle', 'PLG_IP_JCOMMENTS_COMMENTS')));
            echo JComments::showComments($property->id, 'com_iproperty', $property->street_address);
        echo JHtmlBootstrap::endTab();
		
		return true;
	}	
}
