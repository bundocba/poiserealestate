<?php
/**
* @version 1.4.0
* @package RSform!Pro 1.4.0
* @copyright (C) 2007-2013 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access'); 
JHTML::_('behavior.keepalive'); ?>

<form action="<?php echo JRoute::_('index.php?option=com_rsform&view=directory&layout=edit&id='.$this->app->input->getInt('id',0)); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<table class="table table-condensed table-striped table-hover table-bordered category">
		<?php foreach ($this->fields as $field) { ?>
		<tr>
			<td width="200" style="width: 200px;">
				<span class="hasTip" title="<?php echo $field[0]; ?>">
					<?php echo $field[0]; ?> <?php echo $field[2]; ?>
				</span>
			</td>
			<td>
				<?php echo $field[1]; ?>
			</td>
		</tr>
		<?php } ?>
	</table>
	
	<div class="form-actions">
		<button type="submit" class="btn btn-primary button"><?php echo JText::_('RSFP_SUBM_DIR_SAVE'); ?></button> 
		<button type="button" class="btn button" onclick="document.location='<?php echo JRoute::_('index.php?option=com_rsform&view=directory'); ?>'"><?php echo JText::_('RSFP_SUBM_DIR_BACK'); ?></button>
	</div>
	
	<input type="hidden" name="option" value="com_rsform">
	<input type="hidden" name="controller" value="directory">
	<input type="hidden" name="task" value="save">
	<input type="hidden" name="id" value="<?php echo $this->app->input->getInt('id',0); ?>">
	<input type="hidden" name="formId" value="<?php echo $this->params->get('formId'); ?>">
</form>