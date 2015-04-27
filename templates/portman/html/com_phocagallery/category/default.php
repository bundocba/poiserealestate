<?php

defined('_JEXEC') or die('Restricted access'); 

// echo "<pre>";
// print_r($this->items);
// echo "</pre>";die;

?>
<?php JHTML::_('behavior.modal'); ?>
<link rel="stylesheet" type="text/css" href="/templates/portman/css/phoca-lagely.css"/>
<div class="row">
	<div class="local-projects-event" >
		<h2 class="local_projects">
	        <?php echo JText::_($this->category->title); ?>
	    </h2>
			<?php foreach ($this->items as $key => $value): ?>
			<?php if ($key !=0): ?>
				<div class="product-event-detail">
					<div class="product-img-event">
						<a class="modal" href="<?php echo $value->linkorig;?>"rel="{handler: 'image', size: {x: 800, y: 500}}"><img class="img-responsive" src="<?php echo $value->linkorig;?>"alt=""> </a>
					</div>
				</div>
			<?php endif ?>
			
		<?php endforeach ?>
				
			
	</div>
</div>