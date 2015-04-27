<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Iproperty
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license see LICENSE.php
 * @notes  based on Joomla mod_popular module
 */

defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');
?>
<div class="row-striped">
	<?php if (count($list)) : ?>
		<?php foreach ($list as $i => $item) :
			// Calculate popular items
			$hits = (int) $item->hits;

			if($hits >= 25)     $hits_class = 'warning';
			if($hits > 100)     $hits_class = 'important';
			if($hits < 24)      $hits_class = 'info';
			if($hits < 10)      $hits_class = '';
		?>
			<div class="row-fluid">
				<div class="span10">
					<span class="badge badge-<?php echo $hits_class;?> hasTooltip" title="<?php echo JText::_('JGLOBAL_HITS');?>"><?php echo $item->hits;?></span>
					<?php if ($item->checked_out) : ?>
							<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time); ?>
					<?php endif; ?>

					<strong class="row-title">
                        <?php if ($item->link) :?>
                            <a href="<?php echo $item->link; ?>">
                                <?php echo htmlspecialchars($item->street_address, ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        <?php else : ?>
                            <?php echo htmlspecialchars($item->street_address, ENT_QUOTES, 'UTF-8'); ?>                                    
                        <?php endif; ?>                        
					</strong>
                    <?php echo ($item->title) ? ' - <span class="small">'.$item->title.'</span>' : ''; ?>
				</div>
				<div class="span2">
					<span class="small"><i class="icon-calendar"></i> <?php echo JHtml::_('date', $item->created, 'Y-m-d'); ?></span>
				</div>
			</div>
		<?php endforeach; ?>
	<?php else : ?>
		<div class="row-fluid">
			<div class="span12">
				<div class="alert"><?php echo JText::_('MOD_IP_POPADMIN_NORESULTS');?></div>
			</div>
		</div>
	<?php endif; ?>
</div>
