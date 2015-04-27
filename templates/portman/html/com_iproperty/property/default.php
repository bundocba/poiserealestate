<?php

/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

JHtml::_('bootstrap.tooltip');
JHtml::_('bootstrap.modal');
JHtml::_('behavior.formvalidation');

$document   = JFactory::getDocument();
$uri        = JURI::getInstance();

// add Open Graph tags-- http://ogp.me/
$document->addCustomTag( '<meta property="og:title" content="'.$this->iptitle.'" />' );
$document->addCustomTag( '<meta property="og:image" content="'.ipropertyHTML::getThumbnail($this->p->id,'','','','','','',false, false).'" />' );
$document->addCustomTag( '<meta property="og:url" content="'.htmlspecialchars( $uri->toString() ).'" />' );
$document->addCustomTag( '<meta property="og:site_name" content="'.$this->app->getCfg('sitename').'" />' );
$document->addCustomTag( '<meta property="og:type" content="website" />' );
$document->addCustomTag( '<meta property="og:description" content="'.$this->p->short_description.'" />' );

$this->agents                 = ipropertyHTML::getAvailableAgents($this->p->id);
$this->openhouses             = ipropertyHTML::getOpenHouses($this->p->id);
$this->property_full_address  = ipropertyHTML::getFullAddress($this->p);
?>

<property class="item-page<?php echo $this->pageclass_sfx?>">
    <div class="ip-mainheader">
        <h2 class="pull-left">

            <?php echo JText::_('PROPERTY_DETAILS');?>
        </h2>
    </div>
    <div class="clearfix"></div>

    <div class="row-fluid">
        <div class="span12 ip-prop-top">
            <?php 
			
			$db = JFactory::getDBO();
			$query = "SELECT path,fname,type FROM #__iproperty_images WHERE state =1 AND propid = ".$this->p->id;
			$db->setQuery($query);
			$images = $db->loadObjectList();
			
			
			
			if($images){
				echo '<div class="gallery-detail">
					<div class="gallery-main-image-detail">';
						if(isset($images[0])){
							
							echo '<img src="'.JURI::root().$images[0]->path.$images[0]->fname.$images[0]->type.'">';
						}
				echo '</div>';
					if(count($images)>1){
						echo '<div class="gallery-thumbnails-detail">
							<ul>';
								foreach($images as $key=>$value){
									echo '<li>
										<img class="pg-image-detail" src="'.JURI::root().$value->path.$value->fname.$value->type.'" alt="project photo1">
									</li>';
								}
							echo '</ul>
						</div>
						<a href="/" class="prev control"></a>
						<a href="/" class="next control"></a>';
					}
				echo '</div>';
			}
			?>
        </div>
    </div>
    <div class="clearfix"></div>
	
    <div class="ip-propdetails">
        <div class="row">
			<div class="col-sm-6">
				<div class="row">
					<div class="col-sm-6">
						<label><?php echo JText::_('PROPERTIES_NAME');?>:</label>
					</div>
					<div class="col-sm-6">
						<?php echo $this->p->street.' '.$this->p->street_num;?>
					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="row">
					<div class="col-sm-6">
						<label>No. of Bedrooms:</label>
					</div>
					<div class="col-sm-6">
						<?php echo $this->p->beds;?>
					</div>
				</div>
			</div>
			
        </div>
		<div class="row">
			<div class="col-sm-6">
				<div class="row">
					<div class="col-sm-6">
						<label><?php echo JText::_('P_STATUS');?>:</label>
					</div>
					<div class="col-sm-6">
						<?php ?>
					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="row">
					<div class="col-sm-6">
						<label><?php echo JText::_('PROPERTY_TYPE');?>:</label>
					</div>
					<div class="col-sm-6">
						<?php echo $this->p->stypename;?>
					</div>
				</div>
			</div>
			
		</div>
		<div class="row">
			<div class="col-sm-6">
				<div class="row">
					<div class="col-sm-6">
						<label>No. of Baths:</label>
					</div>
					<div class="col-sm-6">
						<?php echo $this->p->baths;?>
					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="row">
					<div class="col-sm-6">
						<label><?php echo JText::_('PRICE');?>:</label>
					</div>
					<div class="col-sm-6">
						<?php echo $this->p->price;?>
					</div>
				</div>
			</div>
			
		</div>
		<div class="row">
			<div class="col-sm-6">
				<div class="row">
					<div class="col-sm-6">
						<label><?php echo JText::_('PRICE_P');?>:</label>
					</div>
					<div class="col-sm-6">
						<?php echo $this->p->price2;?>
					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="row">
					<div class="col-sm-6">
						<label><?php echo JText::_('COUNTRY');?>:</label>
					</div>
					<div class="col-sm-6">
						<?php echo $this->p->country;?>
					</div>
				</div>
			</div>
			
			
		</div>
		<div class="row">
			<div class="col-sm-6">
				<div class="row">
					<div class="col-sm-6">
						<label>Floor Area /<br/>Land :</label>
					</div>
					<div class="col-sm-6">
						<?php ?>
					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="row">
					<div class="col-sm-6">
						<label><?php echo JText::_('CITY');?>:</label>
					</div>
					<div class="col-sm-6">
						<?php echo $this->p->city;?>
					</div>
				</div>
			</div>
			
		</div>
		<div class="row">
			<div class="col-sm-6">
				<div class="row">
					<div class="col-sm-6">
						<label><?php echo JText::_('DEVELOPER');?>:</label>
					</div>
					<div class="col-sm-6">
						
					</div>
				</div>
			</div>
			<?php 
			/* echo '<pre>';
			print_r($this->p); */?>
			<div class="col-sm-6">
				<div class="row">
					<div class="col-sm-6">
						<label><?php echo JText::_('DISTRICT');?>:</label>
					</div>
					<div class="col-sm-6">
						<?php echo $this->p->province;?>
					</div>
				</div>
			</div>
			
		</div>
		<div class="row">
			<div class="col-sm-6">
				<div class="row">
					<div class="col-sm-6">
						<label><?php echo JText::_('TENURE');?>:</label>
					</div>
					<div class="col-sm-6">
						<?php?>
					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="row">
					<div class="col-sm-6">
						<!-- <label><?php echo JText::_('LISTED_DATE');?>:</label> -->
					</div>
					<div class="col-sm-6">
						<?php echo $this->p->available;?>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<label><?php echo JText::_('CONDITION');?>:</label>
			</div>
			<div class="col-sm-12">
				<?php ?>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<label><?php echo JText::_('DESCRIPTION');?>:</label>
			</div>
			<div class="col-sm-12">
				<?php echo $this->p->short_description.$this->p->description;?>
			</div>
		</div>
		
		
		<!-- <div class="row">
			<div class="col-sm-12">
				<label><?php //echo JText::_('ADDRESS');?>:</label>
			</div>
			<div class="col-sm-12">
				<?php ?>
			</div>
		</div> -->
		<div class="row">
			<div class="col-sm-12">
				<label><?php echo JText::_('AGENT_CONTACT');?>:</label>
			</div>
			<div class="col-sm-12">
				<?php
				/* echo '<pre>';
				print_r($this->agents); */
				?>
				<ul class="list-group">
					<?php foreach($this->agents as $key=>$value){?>
						<li class="list-group-item">
							<p>
								<?php echo $value->fname.' '.$value->lname;?>
							</p>
							<p>
								<?php
									echo $value->email;
								?>
							</p>
							<p>
								<?php
									echo $value->street;
								?>
							</p>
						</li>
					<?php }?>
				</ul>
			</div>
		</div>
    </div>
	<div class="clearfix"></div>
	 <input class="goback"type="button" value="Go back to previous page" onclick="history.back(-1)" />
</property>
