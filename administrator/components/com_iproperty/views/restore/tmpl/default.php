<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
?>
         
<form action="<?php echo JRoute::_('index.php?option=com_iproperty'); ?>" method="post" name="adminForm" id="adminForm">                
<?php if (!empty( $this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
<?php else : ?>
    <div id="j-main-container">
<?php endif;?>
        <?php IpropertyAdmin::buildAdminToolbar(); ?>
        <div class="alert alert-error"><?php echo JText::_('COM_IPROPERTY_THIS_OPERATION_IS_UNDOABLE');?></div>
        <fieldset class="adminform">
            <legend><?php echo JText::_('COM_IPROPERTY_RESTORE_FROM_BACKUP_COPY'); ?></legend>
            <div class="control-group form-inline">
                <div class="control-group">                    
                    <div class="control-label">
                        <label for="bak_file"><?php echo JText::_('COM_IPROPERTY_BACKUP'); ?></label>
                    </div>
                    <div class="controls">
                        <?php echo $this->backupFiles; ?>
                    </div>
                </div>
                <div class="control-group">                    
                    <div class="control-label">
                        <label for="bak_prefix"><?php echo JText::_('COM_IPROPERTY_DB_PREFIX'); ?></label>
                    </div>
                    <div class="controls">
                        <input type="text" name="bak_prefix" value="" class="inputbox" />
                    </div>
                </div>
            </div>
        </fieldset>
        <?php echo JHTML::_('form.token'); ?>
        <input type="hidden" name="task" value="" />
    </div>
</form>
<?php echo ipropertyAdmin::footer( ); ?>