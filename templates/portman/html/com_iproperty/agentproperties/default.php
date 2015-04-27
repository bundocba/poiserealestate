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

// uncomment this line if you want the featured listing to only show on the first page of results
// @Todo: possibly add this as a menu item parameter
//$this->enable_featured = ($this->state->get('list.start')) ? false : true;
?>

<div class="ip-proplist<?php echo $this->pageclass_sfx;?>">
    
    <div class="clearfix"></div>
    <?php            if ($this->items)    {        echo             '<h2 class="ip-property-header">'.JText::_('COM_IPROPERTY_PROPERTIES_HANDLED_BY' ).' '.ipropertyHTML::getAgentName($this->agent->id).' </h2><span class="pull-right small ip-pagination-results">'.$this->pagination->getResultsCounter().'</span>';			echo '<div class="row clearfix">';					echo '<div class="col-sm-2"></div>';					echo '<div class="col-sm-2"><label>'.JText::_('PROPERTY_TYPE').'</label></div>';					echo '<div class="col-sm-2"><label>'.JText::_('PRICE').'</label></div>';					echo '<div class="col-sm-2"><label>'.JText::_('STATUS').'</label></div>';					echo '<div class="col-sm-2"><label>'.JText::_('DISTRICT').'</label></div>';					echo '<div class="col-sm-2"><label>'.JText::_('MORE_INFO').'</label></div>';				echo '</div>';				echo '<hr>';            foreach($this->items as $p) :				echo '<div class="row">';					echo '<div class="col-sm-2">'.$p->thumb.'</div>';					echo '<div class="col-sm-2">'.$p->property_type.'</div>';					echo '<div class="col-sm-2">'.$p->price.'</div>';					echo '<div class="col-sm-2">'.$p->stypename.'</div>';					echo '<div class="col-sm-2">'.$p->city.'</div>';					echo '<div class="col-sm-2"><a href="'.$p->proplink.'">'.JText::_('MORE_INFO').'</a></div>';				echo '</div>';				echo '<hr>';            endforeach;        echo            '<div class="pagination">                '.$this->pagination->getPagesLinks().'             </div>';    } else {        echo $this->loadTemplate('noresult');    }
    ?>
</div>
