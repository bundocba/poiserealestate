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

$advanced_link = JRoute::_(ipropertyHelperRoute::getAdvsearchRoute());

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
                <?php echo $this->escape($this->iptitle); ?>
            </h2>
        </div>        
    <?php endif; ?>
    <div class="clearfix"></div>
    
    <?php 
    /* $limit = JRequest::getVar('limit',$mainframe->getCfg('list_limit'));
	$limitstart = JRequest::getVar('limitstart', 0);
	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit); */
    // display results for properties
    if ($this->items)
    {
        echo 
            '<h2 class="ip-property-header-search">'.JText::_('COM_IPROPERTY_PROPERTIES_SEARCH').'</h2><span class="small ip-pagination-results">'.$this->pagination->getResultsCounter().'</span>';
			echo '<div class="row clearfix">';
					echo '<div class="col-sm-2"></div>';
					echo '<div class="col-sm-2 text_algin_center"><label>'.JText::_('PROPERTY_TYPE').'</label></div>';
					echo '<div class="col-sm-2 text_algin_center"><label>'.JText::_('PRICE').'</label></div>';
					echo '<div class="col-sm-2 text_algin_center"><label>'.JText::_('STATUS').'</label></div>';
					echo '<div class="col-sm-2 text_algin_center"><label>'.JText::_('DISTRICT').'</label></div>';
					echo '<div class="col-sm-2 text_algin_center"><label>'.JText::_('MORE_INFO').'</label></div>';
				echo '</div>';
				echo '<hr>';
            foreach($this->items as $p) :
				echo '<div class="row">';
					echo '<div class="col-sm-2 text_algin_center">'.$p->thumb.'</div>';
					echo '<div class="col-sm-2 text_algin_center">'.$p->property_type.'</div>';
					echo '<div class="col-sm-2 text_algin_center">'.$p->price.'</div>';
					echo '<div class="col-sm-2 text_algin_center">'.$p->stypename.'</div>';
					echo '<div class="col-sm-2 text_algin_center">'.$p->city.'</div>';
					echo '<div class="col-sm-2 text_algin_center"><a href="'.$p->proplink.'">'.JText::_('MORE_INFO').'</a></div>';
				echo '</div>';
				echo '<hr>';
            endforeach;
        echo
            '<div class="pagination">
                '.$this->pagination->getPagesLinks().'
             </div>';
    } else {
        echo $this->loadTemplate('noresult');
    }
    ?>
</div>
<style>
    .text_algin_center{
        text-align: center;
    }
</style>