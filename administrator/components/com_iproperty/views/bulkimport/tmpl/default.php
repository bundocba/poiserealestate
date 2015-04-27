<?php
/**
 * @version 2.0.1 2012-08-13
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.formvalidation');
?>

<form action="<?php JRoute::_('index.php?option=com_iproperty&view=bulkimport'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
<?php if (!empty( $this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
<?php else : ?>
    <div id="j-main-container">
<?php endif;?>
        <?php IpropertyAdmin::buildAdminToolbar(); ?> 
        <div class="alert alert-block"><?php echo JText::_( 'COM_IPROPERTY_BULKIMPORT_WARNING' );?></div>
        <fieldset class="adminform">
        <legend><?php echo JText::_( 'COM_IPROPERTY_BULKIMPORT_FILE' ); ?></legend>
        <div class="control-group form-inline">
                <div class="control-group">
                    <div class="control-label">
                        <label for="csv_file"><?php echo JText::_('COM_IPROPERTY_FILE_TO_IMPORT'); ?></label>
                    </div>
                    <div class="controls">
                        <?php echo $this->dataFiles; ?>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <label for="img_path"><?php echo JText::_('COM_IPROPERTY_IMAGE_PATH'); ?></label>
                    </div>
                    <div class="controls">
                        <input type="text" name="img_path" value="media/com_iproperty" size="40" class="inputbox" />
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <label id="empty-lbl" for="empty" class="hasTip"><?php echo JText::_('COM_IPROPERTY_DUMP_DATABASE'); ?></label>
                    </div>
                    <div class="controls">
                        <fieldset class="radio btn-group">
                            <input type="radio" id="empty0" name="empty" value="0" />
                            <label for="empty0"><?php echo JText::_('JNO'); ?></label>
                            <input type="radio" id="empty1" name="empty" checked="checked" value="1" />
                            <label for="empty1"><?php echo JText::_('JYES'); ?></label>
                        </fieldset>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <label id="empty-lbl" for="create_no_match" class="hasTip"><?php echo JText::_('COM_IPROPERTY_CREATE_NO_MATCH'); ?></label>
                    </div>
                    <div class="controls">
                        <fieldset class="radio btn-group">
                            <input type="radio" id="create_no_match0" name="create_no_match" value="0" />
                            <label for="create_no_match0"><?php echo JText::_('JNO'); ?></label>
                            <input type="radio" id="create_no_match1" name="create_no_match" checked="checked" value="1" />
                            <label for="create_no_match1"><?php echo JText::_('JYES'); ?></label>
                        </fieldset>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <label id="empty-lbl" for="empty" class="hasTip"><?php echo JText::_('COM_IPROPERTY_DEBUG_LOG'); ?></label>
                    </div>
                    <div class="controls">
                        <fieldset class="radio btn-group">
                            <input type="radio" id="debug0" name="debug" checked="checked" value="0" />
                            <label for="debug0"><?php echo JText::_('JNO'); ?></label>
                            <input type="radio" id="debug1" name="debug" value="1" />
                            <label for="debug1"><?php echo JText::_('JYES'); ?></label>
                        </fieldset>
                    </div>
                </div>
            </div>
        </fieldset>
        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
<div class="clearfix"></div>
<?php echo ipropertyAdmin::footer( ); ?>