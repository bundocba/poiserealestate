<?php defined('_JEXEC') or die('Restricted Access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal', 'a.modal');
$db = JFactory::getDBO();
$mainframe = JFactory::getApplication();

?>
<form action="<?php echo JRoute::_('index.php?option=com_fwuser'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<table class="table table-striped">
			<thead>
				
				<tr>
					
					<th width="20"><?php echo JHtml::_('grid.checkall'); ?></th>
					<th><?php echo JText::_('FI_NAME'); ?></th>
					<th><?php echo JText::_('FI_DESCRIPTION'); ?></th>
					
					<th><?php echo JText::_('FI_STATUS'); ?></th>
					<th width="5">ID</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="7"><?php echo $this->pagination->getListFooter(); ?></td>
				</tr>
			</tfoot>
			<tbody>
	<?php 
		$i = 0;
		foreach($this->rows as $n => $row): 
			$published 	= JHTML::_('grid.published', $row, $i );
			
	?>
				<tr class="row<?php echo $n % 2; ?>">
					
					<td>
						<?php echo JHtml::_('grid.id', $n, $row->id); ?>
					</td>
					<td>
						<?php echo $row->name; ?>
					</td>
					<td>
						<?php echo $row->description; ?>
					</td>
					
					
					<td>
						<?php echo JHtml::_('jgrid.published', $row->published, $i, 'state.'); ?>
					</td>
					<td>
						<a href="index.php?option=com_fwuser&task=stateedit.edit&id=<?php echo $row->id; ?>"><?php echo $row->id; ?></a>
					</td>
				</tr>
	<?php 
			$i++;
		endforeach; 
	?>
			</tbody>
		</table>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="state" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_('form.token'); ?>
	</div>
</form>