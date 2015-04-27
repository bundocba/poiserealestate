
<?php
/**ageae
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

JHtml::_('bootstrap.tooltip');


$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select("id");
		$query->from('#__iproperty where id=14');
		
		$db->setQuery($query);
		$list = $db->loadObjectList();
 $this->items=$list;
//   echo '<pre>';
// print_r($this->items); die;
$this->agents = ipropertyHTML::getAvailableAgents($this->items[0]->id);
?>

<div class="ip-proplist<?php echo $this->pageclass_sfx;?>">
    <div class="ip-mainheader">
           <!--  <h2>
                <?php echo JText::_('NEW_PROJECT'); ?>
            </h2> -->
        </div>    
    
    <?php 
    // display results for properties
    if ($this->items)
    {
       $this->p = $this->items[0];						
	   $db = JFactory::getDBO();	
	  // $query = "SELECT path,fname,type FROM #__iproperty_images WHERE state =1";		
	   $query = "SELECT path,fname,type FROM #__iproperty_images WHERE state =1 AND propid = ".$this->items[0]->id;			
	   $db->setQuery($query);			
	   $images = $db->loadObjectList();		

	   if($images){				
			echo '<div class="gallery">					
					<div class="gallery-main-image">';						
					$dem=0;
					foreach ($images as $key => $value) {
						$dem++;
							// if($key==4)return;
							// else
							//if($dem<=4)
						
							echo '<img src="'.Juri::root().$value->path.$value->fname.$value->type.'">';
								
						}					
						// if(isset($images[0])){
							
						// 	echo '<img src="'.Juri::root().$images[0]->path.$images[0]->fname.$images[0]->type.'">';
						// }

			echo '</div>';					
			if(count($images)>1){						
			echo '<div class="gallery-thumbnails">							
			<ul>';								
			foreach($images as $key=>$value){
				$key++;	
				//if($key<=4)
				echo '<li rel="'.$key.'" class="active1">										
				<img class="pg-image" src="'.Juri::root().$value->path.$value->fname.$value->type.'" alt="project photo1">									
				</li>';								
			}							
			echo '</ul>						
			</div>		
			<div class="prev control"></div>				
			<div class="next control"></div>';					
			}				
			echo '</div>';			
			}			
			// echo '<h3><a href="'.$this->items[0]->proplink.'">'.JText::_('PROJECT_TITLE').'</a></h3>';			
			// // echo '<p>'.$this->items[0]->short_description.'</p>';
			// echo '<p>'.$this->items[0]->description.'</p>';

			// print_r($this->items[0]);die;
			//echo '<h3>'.JText::_('CONTACT').'</h3>';
			// foreach($this->agents as $key=>$value){
			// 	echo '
			// 		<p>
			// 			<b>'.$value->fname.' '.$value->lname.'</b>
			// 		</p>';
			// 		if($value->phone){
			// 			echo '<p>
			// 				'.$value->phone.'
			// 			</p>';
			// 		}
			// 		if($value->email){
			// 			echo '<p>
			// 				'.$value->email.'
			// 			</p>';
			// 		}
			// }
    } else {
        echo $this->loadTemplate('noresult');
    }
    ?>
	<script type="text/javascript">
		var currentImage;
	    var currentIndex = -1;
	    var interval;
	    function showImage(index){
	        if(index < $('.gallery-main-image img').length){
	        	var indexImage = $('.gallery-main-image img')[index]
	            if(currentImage){   
	            	if(currentImage != indexImage ){
	                    $(currentImage).css('z-index',2);
	                    clearTimeout(myTimer);
	                    $(currentImage).fadeOut(250, function() {
						    myTimer = setTimeout("showNext()", 3000);
						    $(this).css({'display':'none','z-index':1})
						});
	                }
	            }
	            $(indexImage).css({'display':'block', 'opacity':1});
	            currentImage = indexImage;
	            currentIndex = index;
	            $('.gallery-thumbnails ul li').removeClass('active1');
	            $($('.gallery-thumbnails ul li')[index]).addClass('active1');
	        }
	    }
	    
	    function showNext(){

	        var len = $('.gallery-main-image img').length;
	        var next = currentIndex < (len-1) ? currentIndex + 1 : 0;
	        showImage(next);
	    }
	    function showPrev(){
	    	var len = $('.gallery-main-image img').length;

	    	if (currentIndex<=0) {
	    		currentIndex=len-1;
	    	}else{
				currentIndex--;
	    	}
	    	

	        showImage(currentIndex);
	    }
	    
	    var myTimer;
	    
	    $(document).ready(function() {
		    myTimer = setTimeout("showNext()", 3000);
			showNext(); //loads first image
	        $('.gallery-thumbnails ul li').bind('click',function(e){

	        	var count = $(this).attr('rel');
	        	showImage(parseInt(count)-1);
	        });
	        $('.prev').bind('click', function(e) {
	        	showPrev();
	        });
	        $('.next').bind('click', function(e) {
	        	showNext();
	        });

					
		});
	    
		
		</script>	
</div>