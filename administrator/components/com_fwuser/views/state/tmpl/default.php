<?php defined('_JEXEC') or die('Restricted Access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal', 'a.modal');
$db = JFactory::getDBO();
$mainframe = JFactory::getApplication();
$query="select * from #__fwuser_country order by name ";
$db->setQuery($query);
$rows_cate=$db->loadObjectList();

$options = array();
$options[] = JHTML::_('select.option', 0, JText::_('FI_COUNTRY'));
for($i=0;$i<count($rows_cate);$i++){
	$options[] = JHTML::_('select.option', $rows_cate[$i]->id, $rows_cate[$i]->name);
}
$dropdown_catid = JText::_('FI_COUNTRY').' '.JHTML::_('select.genericlist', $options, 'country_id', 'class="inputbox" onchange="$(\'adminForm\').submit();"', 'value', 'text', $mainframe->getUserStateFromRequest('com_fwuser.state.country_id','country_id',0,'cmd'));
?>
<form action="<?php echo JRoute::_('index.php?option=com_fwuser'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<table class="table table-striped">
			<thead>
				<tr>
					<td colspan="7"><?php echo $dropdown_catid; ?></td>
				</tr>
				<tr>
					
					<th width="20"><?php echo JHtml::_('grid.checkall'); ?></th>
					<th><?php echo JText::_('FI_NAME'); ?></th>
					<th><?php echo JText::_('FI_CODE1'); ?></th>
					<th><?php echo JText::_('FI_CODE2'); ?></th>
					<th><?php echo JText::_('FI_COUNTRY'); ?></th>
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
			$query="select * from #__fwuser_country where id=".$row->country_id."";
			$db->setQuery($query);
			$row_country=$db->loadObject();
	?>
				<tr class="row<?php echo $n % 2; ?>">
					
					<td>
						<?php echo JHtml::_('grid.id', $n, $row->id); ?>
					</td>
					<td>
						<?php echo $row->name; ?>
					</td>
					<td>
						<?php echo $row->code1; ?>
					</td>
					<td>
						<?php echo $row->code2; ?>
					</td>
					<td>
						<?php echo $row_country->name; ?>
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