<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

JHtml::_('bootstrap.tooltip');

//if no login
$user = JFactory::getUser();
if(empty($user->id)){
	header('Location: http://poiserealestate.com/');
exit;
}

// uncomment this line if you want the featured listing to only show on the first page of results
// @Todo: possibly add this as a menu item parameter
//$this->enable_featured = ($this->state->get('list.start')) ? false : true;
?>

<div class="ip-proplist<?php echo $this->pageclass_sfx;?>">
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
                <?php //echo $this->escape($this->iptitle); ?>
				Property Listings
            </h2>
        </div>        
    <?php endif; ?>
    <div class="clearfix"></div>
    
    <?php
    
    
    
   echo '<div  style="text-align: right;width: 100%; float: left;">';
   echo '<div style="width: 50%; float: left;"><span class="small ip-pagination-results">'.$this->pagination->getResultsCounter().'</span></div>';
   echo '<div style="width: 50%; float: right;">';
        echo '<span style="background-color: #00234B;padding: 5px 10px;"><a style="color: #fff;" href="'.JRoute::_('index.php?option=com_iproperty&view=propagentform&Itemid=222&lang=en').'">Add Listing</a></span>';
   echo '</div>';
   
   echo '</div>';

    // display results for properties
    if ($this->items)
    {
       
            
            $k = 0;
            echo '<div class="tr_title" style="width: 100%; float: left;  padding-top: 10px;">';
                    echo '<div class="col-md-2">.No</div>';
                    echo '<div class="col-md-8">Property Name</div>';
                    echo '<div class="col-md-2"></div>';
                echo '</div>';
            foreach($this->items as $p) :
                $k++;
                echo '<div class="row" style="width: 100%; float: left;  margin-left: 0px;">';
                
                    echo '<div class="col-md-2">'.$k.'</div>';
                    echo '<div class="col-md-8">'.$p->property_name.'</div>';
                    echo '<div class="col-md-2"><div style="float: right;width: 61%;"><span style="background-color: #00234B;padding: 5px 15px 5px 15px;"><a style="color: #fff;" href="'.JRoute::_('index.php?option=com_iproperty&view=propagentedit&propertyId='.$p->id).'">View/Edit</a></span></div></div>';
                echo '</div>';
                
            endforeach;
        echo
            '<div class="pagination">
                '.$this->pagination->getPagesLinks().'<br />'.$this->pagination->getPagesCounter().'
             </div>';
    } else { // no results tmpl
        echo $this->loadTemplate('noresult');
    }
    
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
    // display disclaimer if set in params
    if ($this->params->get('show_ip_disclaimer') && $this->settings->disclaimer)
    {
        echo '<div class="well well-small" id="ip-disclaimer">'.$this->settings->disclaimer.'</div>';
    }
    // display footer if enabled
    
    ?>
</div>