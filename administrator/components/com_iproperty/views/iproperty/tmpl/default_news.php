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

<h1><?php echo JText::_('COM_IPROPERTY_THINKERY_NEWS'); ?></h1>
<div>
    <table class="table table-striped">
        <tbody>
        <?php
            $limit          = 5;
            $rssUrl         = 'http://extensions.thethinkery.net/index.php?format=feed&type=rss';
            $cache_time     = '86400';

            $rss        = JSimplepieFactory::getFeedParser($rssUrl, $cache_time);
            $cntItems   = $rss ? $rss->get_item_quantity() : false;

            $k = 0;
            if( !$cntItems ) {
                echo '<tr class="row'.$k.'" style="text-align: center !important;"><td>'.JText::_('COM_IPROPERTY_NO_THINK_NEWS' ).'</td></tr>';
            }else{
                $cntItems = ($cntItems > $limit) ? $limit : $cntItems;
                for( $j = 0; $j < $cntItems; $j++ ){
                    $item           = $rss->get_item($j);
                    $description    = ipropertyHTML::snippet($item->get_description(), 1000, '(...)', false);
                    echo '
                    <tr class="row'.$k.'"><td><h2><a href="'.$item->get_link().'" target="_blank">'.$item->get_title().'</a></h2></td></tr>
                    <tr class="row'.$k.'"><td>'.$description.'</td></tr>';
                    $k = 1 - $k;
                }
            }
        ?>
        </tbody>
    </table>
</div>