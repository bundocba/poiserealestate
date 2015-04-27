<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
?>

<h1><?php echo JText::_('COM_IPROPERTY_THINKERY_FAQ'); ?></h1>
<div>
    <table class="table table-striped">
        <tbody>
        <?php
            $limit          = 10;
            $rssUrl         = 'http://extensions.thethinkery.net/index.php?option=com_quickfaq&view=category&cid=1&format=feed&type=rss';
            $cache_time     = '86400';

            $rss        = JSimplepieFactory::getFeedParser($rssUrl, $cache_time);
            $cntItems   = $rss ? $rss->get_item_quantity() : false;

            $k = 0;
            if( !$cntItems ) {
                echo '<tr class="row'.$k.'" style="text-align: center !important;"><td>'.JText::_('COM_IPROPERTY_NO_THINK_FAQ' ).'</td></tr>';
            }else{
                $cntItems = ($cntItems > $limit) ? $limit : $cntItems;
                for( $j = 0; $j < $cntItems; $j++ ){
                    $item           = $rss->get_item($j);
                    //$description    = ipropertyHTML::snippet($item->get_description(), 1000);
                    echo '
                    <tr class="row'.$k.'"><td><h2><a href="'.$item->get_link().'" target="_blank">'.$item->get_title().'</a></h2></td></tr>
                    <tr class="row'.$k.'"><td>'.$item->get_description().'</td></tr>';
                    $k = 1 - $k;
                }
            }
        ?>
        </tbody>
    </table>
</div>