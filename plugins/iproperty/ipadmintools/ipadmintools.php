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

class plgIpropertyIpAdminTools extends JPlugin
{
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();       
	}

	public function onAfterRenderAdminTabs($user, $settings)
	{
        $app        = JFactory::getApplication();
        $dispatcher = JDispatcher::getInstance();
        
        // don't display plugin if not in the admin panel or user is not authorized
		if($app->getName() != 'administrator') return true;
        if(!$user->authorise('core.admin')) return true;
        
        echo JHtmlBootstrap::addTab('ipAdmin', 'iptools', JText::_($this->params->get('tabtitle', 'PLG_IP_TOOLS_TOOLS')));
        ?>
        <h1><?php echo JText::_('PLG_IP_TOOLS_TOOLS'); ?></h1>
        <div>
            <?php 
                echo JHtmlBootstrap::startTabSet('ipAdminTools'); 
                $dispatcher->trigger( 'onAfterRenderTools', array( $user, $settings ) ); 
                echo JHTMLBootstrap::endPane(); 
            ?>
        </div>                    
        <?php 
        echo JHtmlBootstrap::endTab();
	}
}
