<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

$children   = ($this->catinfo) ? ipropertyHTML::getCatChildren($this->catinfo->id) : '';/* 
?>

<div class="row-fluid">
    <div class="span12">
        <div class="span12 pull-right cat_overview_desc">
            <?php 
                echo ($this->catinfo->icon && $this->catinfo->icon != 'nopic.png') ? '<img src="'.$this->ipbaseurl.'/media/com_iproperty/categories/'. $this->catinfo->icon . '" alt="' . $this->catinfo->title . '" border="0" class="pull-right ip-cat-img" />' : '';
                echo $this->catinfo->desc;

                if ($this->settings->cat_recursive == 1 && $children)
                {
                    foreach ($children as $scat)
                    {
                        $valid_scats = (ipropertyHTML::countCatObjects($scat->id) > 0) ? 1 : '';
                    }

                    if ($valid_scats == 1) // if subcategories have entries, display
                    { 
                        echo '<h5 class="ip_subcattitle">' . JText::_( 'COM_IPROPERTY_SUBCATEGORIES' ) . '</h5>';
                        $stotal=0;
                        foreach($children as $scat) //foreach subcategory, show title and entries
                        {
                            $scat_name = (strlen($scat->title) > 30 ) ? (substr( $scat->title, 0, 27) . '...') : ($scat->title);
                            $scat_entries = ipropertyHTML::countCatObjects($scat->id);
                            if( $this->settings->cat_entries == 1 ) $scount = ' - <span class="ip_subcatlink_count"> (' . $scat_entries . ')</span>';
                            $slink = JRoute::_(ipropertyHelperRoute::getCatRoute($scat->id.':'.$scat->alias));

                            echo '<a href="' . $slink . '" class="ip_subcatlink">' . $scat_name . '</a>' . $scount;
                            if($stotal < (count($children)-1)) echo ', ';

                            $stotal++;
                        }
                    }
                }
            ?>
        </div>
    </div>
</div><?php  */?>
<div class="clearfix"></div>