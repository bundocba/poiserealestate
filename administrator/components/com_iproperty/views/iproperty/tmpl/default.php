<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
$thinkery_feeds = $this->settings->feed_admin;
$active_tab     = ($thinkery_feeds) ? 'ipnews' : 'ipstats';
?>

<?php if (!empty( $this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
<?php else : ?>
    <div id="j-main-container">
<?php endif;?>
        <?php IpropertyAdmin::buildAdminToolbar(); 
        // admin panel
        echo JHtmlBootstrap::startTabSet('ipAdmin', array('active' => $active_tab));
            // news feed
            if ($thinkery_feeds){
                echo JHtmlBootstrap::addTab('ipAdmin', 'ipnews', JText::_('COM_IPROPERTY_THINKERY_NEWS' ));
                    echo $this->loadTemplate('news'); 
                echo JHtmlBootstrap::endTab();
            }
            // stats
            echo JHtmlBootstrap::addTab('ipAdmin', 'ipstats', JText::_('COM_IPROPERTY_STATISTICS' ));
                echo $this->loadTemplate('stats'); 
            echo JHtmlBootstrap::endTab();
            // changelog
            echo JHtmlBootstrap::addTab('ipAdmin', 'ipchangelog', JText::_('COM_IPROPERTY_CHANGE_LOG' ));
                echo $this->loadTemplate('changelog'); 
            echo JHtmlBootstrap::endTab();
            // FAQ feed
            if ($thinkery_feeds){
                echo JHtmlBootstrap::addTab('ipAdmin', 'ipfaq', JText::_('COM_IPROPERTY_FAQ' ));
                    echo $this->loadTemplate('faq');
                echo JHtmlBootstrap::endTab();
            }
            $this->dispatcher->trigger('onAfterRenderAdminTabs', array($this->user, $this->settings)); 
        echo JHtmlBootstrap::endTabSet(); ?>
        <div class="clearfix"></div>
        <?php echo ipropertyAdmin::footer( ); ?>
    </div>