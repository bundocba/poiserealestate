<?php 
defined('_JEXEC') or die('Restricted access');



?>
<link rel="stylesheet" type="text/css" href="/templates/portman/css/phoca-lagely.css"/>

<div class="row">
	
	<div class="local-projects-event">
	<h2 class="local_projects">
        <?php echo JText::_('EVENTS'); ?>
    </h2>
		<?php foreach ($this->categories as $key => $value): ?>
			<div class="product-event">
				<div class="product-img-event">
					<a href="<?php echo $value->link; ?>"><img class="img-responsive"src="<?php echo '/images/phocagallery/'.$value->filename;?>" alt=""></a> 
				<div class="product-title-event">
					<a href="<?php echo $value->link; ?>"><?php echo $value->title;?></a>
				</div>
				</div>
			</div>
		<?php endforeach ?>
	</div>
	
</div>