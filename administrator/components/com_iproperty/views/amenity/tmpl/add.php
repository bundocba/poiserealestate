<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
JHtml::_('formbehavior.chosen', 'select');
?>

<form action="<?php echo JRoute::_('index.php?option=com_iproperty&layout=add&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm">
    <div class="row-fluid">
        <div class="span12 form-horizontal">
            <h4><?php echo JText::_('COM_IPROPERTY_AMENITIES'); ?></h4>
            <?php echo $this->form->getInput('amenlist'); ?>
        </div>
    </div>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
<div class="clearfix"></div>
<?php echo ipropertyAdmin::footer( ); ?>