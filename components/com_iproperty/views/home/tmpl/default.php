<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

JHtml::_('bootstrap.tooltip');

$total      = count( $this->items[0]->children );
$cols       = (12/$this->settings->iplayout);
?>

<?php if ($this->params->get('show_page_heading')) : ?>
    <div class="page-header">
        <h1>
            <?php echo $this->escape($this->params->get('page_heading')); ?>
        </h1>
    </div>
<?php endif; ?>
<?php if ($this->params->get('show_ip_title') && $this->iptitle) : ?>
    <div class="ip-mainheader">
        <h2>
            <?php echo $this->escape($this->iptitle); ?>
        </h2>
    </div>
<?php endif; ?>

<?php
// featured properties top position
if( $this->featured && $this->enable_featured && $this->settings->featured_pos == 0 ){
    echo '
    <h2 class="ip-property-header">'.JText::_( 'COM_IPROPERTY_FEATURED_PROPERTIES' ).'</h2>';
    $this->k = 0;
    foreach( $this->featured as $f ){
        $this->p = $f;
        echo $this->loadTemplate('property');
        $this->k = 1 - $this->k;
    }
}
?>

<div class="row-fluid">
    <div class="span12">
        <?php
        $x = 0;
        foreach( $this->items[0]->children as $c) { // show maincategories
            $cat = $this->items[strval($c)];
            if( $cat->entries + $cat->entriesR > 0 ){
                $cat_link = JRoute::_(IpropertyHelperRoute::getCatRoute($cat->id.':'.$cat->alias));
                $cat_img  = ($cat->icon && $cat->icon != 'nopic.png') ? ipropertyHTML::getIconpath($cat->icon, 'category') : '';
                $catcount = ($this->settings->cat_entries==1) ? "<br /><strong>" . JText::_( 'COM_IPROPERTY_ENTRIES' ) . ":</strong> (" . $cat->entries . ")" : '';

                echo '
                <div class="span'.$cols.' pull-left well ip_cat_entry">
                    <div>';
                        if($cat_img){
                            echo '<a href="'.$cat_link.'">
                                    <img src="'.$cat_img.'" alt="'.$cat->title.'" title="'.$cat->title.'" width="'.$this->cat_photo_width.'" class="thumbnail ip-cat-home-img" />
                                  </a>';
                        }
                        echo '
                        <a href="'. $cat_link . '">' . $cat->title . '</a> ' . $catcount . '<br />
                        ' . strip_tags($cat->desc,'<br /><strong><b><br>');
                        //show subcategories if any and set in admin
                        if( count( $this->items[0]->children ) > 0 &&  $this->settings->show_scats == 1 && count(@$cat->children) > 0){
                            if(count(@$cat->children) > 0){ //check for subcategories
                                $valid_subcats = 0;
                                foreach($cat->children as $sc) { //check for entries in subcategories
                                    $scat= $this->items[$sc];
                                    if(($scat->entries+$scat->entriesR) > 0){
                                        $valid_subcats++;
                                    }
                                }
                                if($valid_subcats > 0){ //if subcategories with entries show subcat title
                                    echo '<h5 class="ip_subcattitle">'.$cat->title.' '.JText::_( 'COM_IPROPERTY_SUBCATEGORIES' ).':</h5>';
                                }
                            }
                            $stotal = 0;
                            foreach($cat->children as $sc) {
                                $scat= $this->items[$sc];
                                if($cat->id){
                                    if(($scat->entries+$scat->entriesR) > 0){
                                        $scat_name = (strlen($scat->title) > 30 ) ? (substr( $scat->title, 0, 27) . '...') : ($scat->title);
                                        if( $this->settings->cat_entries == 1 ) $scount = ' - <span class="ip_subcatlink_count"> (' . $scat->entries . ')</span>';
                                        $slink = JRoute::_(IpropertyHelperRoute::getCatRoute($scat->id.':'.$scat->alias));
                                        echo '<a href="' . $slink . '" class="ip_subcatlink">' . $scat_name . '</a>' . $scount;
                                        if($stotal < ($valid_subcats - 1)) echo ', ';
                                    }
                                }
                                $stotal++;
                           }
                       }
                echo '
                    </div>
                </div>';

                $x++;

                if($x == $this->settings->iplayout){
                    $x = 0;
                    echo '</div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="row-fluid">
                                <div class="span12">';
                }
           }
        }
        ?>
    </div>
</div>

<?php
// featured properties bottom position
if( $this->featured && $this->enable_featured && $this->settings->featured_pos == 1 ){
    echo '
    <h2 class="ip-property-header">'.JText::_( 'COM_IPROPERTY_FEATURED_PROPERTIES' ).'</h2>';
    $this->k = 0;
    foreach( $this->featured as $f ){
        $this->p = $f;
        echo $this->loadTemplate('property');
        $this->k = 1 - $this->k;
    }
}
// display footer if enabled
if ($this->settings->footer == 1) echo ipropertyHTML::buildThinkeryFooter(); 
?>