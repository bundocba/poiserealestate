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

$advanced_link  = JRoute::_(ipropertyHelperRoute::getAdvsearchRoute());

?>

<div class="ip-proplist<?php echo $this->pageclass_sfx;?>">
    
	<div class="ip-mainheader">
		<h2>
			<?php echo $this->escape($this->iptitle); ?>
		</h2>
	</div>        
    <div class="clearfix"></div>
   
    <?php 
    
    // display results for properties
    if ($this->items)
    {
            $this->k = 0;			$this->p = $this->items[0];						$db = JFactory::getDBO();			$query = "SELECT path,fname,type FROM #__iproperty_images WHERE state =1 AND propid = ".$this->items[0]->id;			$db->setQuery($query);			$images = $db->loadObjectList();						if($images){				echo '<div class="gallery">					<div class="gallery-main-image">';						if(isset($images[0])){							echo '<img src="'.$images[0]->path.$images[0]->fname.$images[0]->type.'">';						}				echo '</div>';					if(count($images)>1){						echo '<div class="gallery-thumbnails">							<ul>';								foreach($images as $key=>$value){									echo '<li>										<img class="pg-image" src="'.$value->path.$value->fname.$value->type.'" alt="project photo1">									</li>';								}							echo '</ul>						</div>						<a href="/" class="prev control"></a>						<a href="/" class="next control"></a>';					}				echo '</div>';			}			echo '<h3><a href="'.$this->items[0]->proplink.'">'.JText::_('PROJECT_TITLE').'</a></h3>';			echo '<p>'.$this->items[0]->short_description.'</p>';						$this->k = 0;			unset($this->items[0]);						if(count($this->items)){				echo '<h3>'.JText::_('MORE_PROJECTS').'</h3>';				echo '<div class="row">';
					foreach($this->items as $p) :
						$this->p = $p;						echo '<div class="col-sm-4"><a href="'.$this->p->proplink.'">'.$p->street.' '.$p->street_num.'</a></div>';
						$this->k = 1 - $this->k;
					endforeach;				echo '</div>';			}
        echo
            '<div class="pagination">
                '.$this->pagination->getPagesLinks().'<br />'.$this->pagination->getPagesCounter().'
             </div>';
    } else { // no results tmpl
        echo $this->loadTemplate('noresult');
    }
    ?>
</div>