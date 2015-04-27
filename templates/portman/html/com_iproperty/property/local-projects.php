
<?php
// echo "<pre>";
//             print_r($this->item);
//             echo "</pre>";die;


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
<?php JHTML::_('behavior.modal'); ?>
<property class="item-page<?php echo $this->pageclass_sfx?>">
    <div class="ip-mainheader">
        <h2 class="local_projects">

            <?php echo JText::_('PROPERTY_DETAILS');?>
        </h2>
    </div>
     <?php 
			
			$db = JFactory::getDBO();
			$query = "SELECT path,fname,type FROM #__iproperty_images WHERE state =1 AND propid = ".$this->p->id;
			$db->setQuery($query);
			$images = $db->loadObjectList();

	?>
    <?php if($images):?>

            
    <div class="row conten-item-image-detail">
    	<div class="col-md-7">
    		<div class="item-image-detail">
		    	<?php
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
            
            ?>
		    </div>
    	</div>
    	<div class="col-md-5 item-product-detail">
    		<ul class="detail-image">
    			<li>
    				<span class="itemExtraFieldsLabel"><?php echo ('PROPERTY TYPE:');?></span>
    				<span class="itemExtraFieldsValue"><?php echo $this->item->property_type;?></span>
    			</li>
    			<li>
    				<span class="itemExtraFieldsLabel"><?php echo ('street address:');?></span>
    				<span class="itemExtraFieldsValue"><?php echo $this->item->street;?></span>
    			</li>
    			<li>
    				<span class="itemExtraFieldsLabel"><?php echo ('BEDROOMS:');?></span>
    				<span class="itemExtraFieldsValue"><?php echo $this->item->beds;?></span>
    			</li>
    			<li>
    				<span class="itemExtraFieldsLabel"><?php echo ('BATHS:');?></span>
    				<span class="itemExtraFieldsValue"><?php echo $this->item->baths;?></span>
    			</li>
    			<li>
    				<span class="itemExtraFieldsLabel"><?php echo ('YEAR BUILT:');?></span>
    				<span class="itemExtraFieldsValue"><?php echo $this->item->yearbuilt;?></span>
    			</li>
    			<li>
                    <span class="itemExtraFieldsLabel"><?php echo ('PRICE:');?></span>
                    <span class="itemExtraFieldsValue"><?php echo $this->settings->currency.$this->item->price;?></span>
                </li>
                <li>
                    <span class="itemExtraFieldsLabel"><?php echo ('country:');?></span>
                    <span class="itemExtraFieldsValue"><?php echo $this->item->countryname;?></span>
                </li>
                <li>
                    <span class="itemExtraFieldsLabel"><?php echo ('city:');?></span>
                    <span class="itemExtraFieldsValue"><?php echo $this->item->city;?></span>
                </li>
                <!-- <li>
                    <span class="itemExtraFieldsLabel"><?php echo ('Listed Date:');?></span>
                    <span class="itemExtraFieldsValue"><?php echo $this->item->last_updated;?></span>
                </li> -->
                
    		</ul>
    	</div>
    </div>
    <div class="row conten-item-image-detail">
    	<div class="col-md-12 item-product-detail-des">
    		<div class="description"><span>Description</span></div>
			<p><?php echo $this->item->description;?></p>
    	</div>
    </div>

    <?php endif;?>
    <input class="goback"type="button" value="Go back to previous page" onclick="history.back(-1)" />

</property>
<script type="text/javascript">
    jQuery(document).ready(function($) {
         $('.item-image-detail .gallery-detail ').hover(function() {
            $('.item-image-detail .gallery-detail .prev').css({
                'opacity': '1',
                'cursor': 'pointer'
                
            });
            $('.item-image-detail .gallery-detail .next').css({
                'opacity': '1',
                'cursor': 'pointer'
                
            });
        }, function() {
            $('.item-image-detail .gallery-detail .prev').css({
                'opacity': '0'
                
            });
            $('.item-image-detail .gallery-detail .next').css({
                'opacity': '0'
                
            });
        });
    });
</script>