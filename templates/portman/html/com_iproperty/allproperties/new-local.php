
<?php

// echo "<pre>";
// print_r($this->items);
// echo "</pre>";die;

defined( '_JEXEC' ) or die( 'Restricted access');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

JHtml::_('bootstrap.tooltip');

$this->agents = ipropertyHTML::getAvailableAgents($this->items[0]->id);
//  echo '<pre>';
// print_r($this->items); 
?>

<div class="ip-proplist<?php echo $this->pageclass_sfx;?>">
    <div class="ip-mainheader">
      	<h2 class="local_projects">
            <?php echo JText::_('LOCAL PROJECTS'); ?>
        </h2>
        <div class="row"style="background-color:white;">
	      
		        <div class="local-projects">
			        <?php foreach ($this->items as $key => $value) { 
			        	if($value->id!=14)
			        	{
			        	echo '<div class="local-projects-products">';
				        	echo '<div class="local-projects-img">';
				        		echo '<a href="'.Juri::root().$value->proplink.'&layout=local-projects">'.$value->thumb.'</a>';
				        	echo '</div>';
				        	echo '<div class="local-projects-detail">';

				        		echo '<a href="'.$value->proplink.'&layout=local-projects"><span>'.$value->street.'</span></a>';
				        		
				        	echo '</div>';
			        	echo '</div>';
			        	}
			        }?>

		            
		        </div>
	        <div class="pagination" style="float:left;">
		                <?php echo $this->pagination->getPagesLinks();?>
		            </div>
       </div>

    </div>    
    
   
</div>