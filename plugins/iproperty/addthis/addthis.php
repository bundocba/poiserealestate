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

class plgIpropertyAddthis extends JPlugin
{
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	public function onBeforeRenderProperty($property, $settings)
	{
        $app = JFactory::getApplication();
        $doc = JFactory::getDocument();
        if($app->getName() != 'site') return true;

        if(!$username = $this->params->get('username')) return true;

        // create javascript for Addhis request
        $doc->addScript( "//s7.addthis.com/js/250/addthis_widget.js#username=".$username );

        echo '<div class="well well-small clearfix addthis_toolbox addthis_default_style">
                <a href="http://www.addthis.com/bookmark.php?v=250&amp;username='.$username.'" class="addthis_button_compact">Share</a>
                <span class="addthis_separator">|</span>
                <a class="addthis_button_preferred_1"></a>
                <a class="addthis_button_preferred_2"></a>
                <a class="addthis_button_preferred_3"></a>
                <a class="addthis_button_preferred_4"></a>
              </div>';
        return true;
	}
}