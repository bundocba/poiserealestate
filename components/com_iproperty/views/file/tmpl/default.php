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

?>

<div class="ip-companylist<?php echo $this->pageclass_sfx;?>">
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
   
   
    
    // display results for file
    if ($this->items)
    {
        /*echo 
            '<h2 class="company_header">'.JText::_('COM_IPROPERTY_COMPANIES').'</h2><span class="pull-right small ip-pagination-results">'.$this->pagination->getResultsCounter().'</span>';*/
        $k = 0;    
        echo '<div class="row tr_title" style="padding-top: 10px;">';
            echo '<div class="col-md-1">No.</div>';
            echo '<div class="col-md-9">Document Name</div>';
            //echo '<div class="col-md-5">Description</div>';
            echo '<div class="col-md-2"></div>';
        echo '</div>';
            foreach($this->items as $c) :
                $k ++;
               echo '<div class="row tr_template">';
                    echo '<div class="col-md-1">'.$k.'</div>';
                    echo '<div class="col-md-9">'.$c->title.'</div>';
                    //echo '<div class="col-md-5">'.$c->description.'</div>';
                    echo '<div class="col-md-2"><div class="cssreadmore">
                    <span><a href="'.JRoute::_("index.php?option=com_iproperty&view=file&layout=download&file=".$c->fname).'">Download</a></span></div></div>';
               echo '</div>';
            endforeach;
        /*echo
            '<div class="pagination">
                '.$this->pagination->rowgetPagesLinks().'<br />'.$this->pagination->getPagesCounter().'
             </div>';*/
    } else {
        echo $this->loadTemplate('noresult');
    }
    
    
    
    ?>
</div>