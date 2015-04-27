<?php

// echo "<pre>";
// print_r($this->items);
// echo "</pre>";die;

defined( '_JEXEC' ) or die( 'Restricted access');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

JHtml::_('bootstrap.tooltip');
$this->items = glob('images/development/*.jpg');


//$this->agents = ipropertyHTML::getAvailableAgents($this->items[0]->id);
//  echo '<pre>';
// print_r($this->items);
$count = 0; 
?>

<div class="ip-proplist<?php echo $this->pageclass_sfx;?>">
    <div class="ip-mainheader">
      	<h2 class="local_projects">
            <?php echo JText::_('Property Development'); ?>
        </h2>
        <div class="row"style="background-color:white;">
	      
		        <div class="local-projects">
			        <?php foreach ($this->items as $key => $value) { 
			        	$count ++;
						if($count == 1 || $count == 2 || $count == 3){
							$title="Cambodia";
						}else
							$title = "Vietnam";
			        	echo '<div class="local-projects-products">';
				        	echo '<div class="local-projects-img">';
				        		echo '<img src="'.$value.'">';
				        	echo '</div>';
				        	//show text below image
							echo '<div class="css_title">';
								echo $title;;
							echo '</div>';
			        	echo '</div>';
			        	
			        }?>

		            
		        </div>
          
       </div>

    </div>    
    
   
</div>