<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
?>

<form method="post" action="<?php echo JRoute::_('index.php?option=com_iproperty'); ?>" enctype="multipart/form-data" name="adminForm" id="adminForm">
    <div class="row-fluid">
        <div class="span12 form-vertical">
            <h4><?php echo JText::_('COM_IPROPERTY_SELECT_IMAGE_UPLOAD'); ?></h4>
            <div class="control-group">
                <div class="control-label">
                    <input class="inputbox" name="userfile" id="userfile" type="file" />
                </div>
                <div class="controls">
                    <button class="btn tip" type="submit"><?php echo JText::_('COM_IPROPERTY_UPLOAD' ) ?></button>
                </div>
            </div>
            <hr />
            <div class="control-group">
                <div class="control-label">
                    <b><?php echo JText::_('COM_IPROPERTY_TARGET_DIRECTORY' ).':'; ?></b>
                </div>
                <div class="controls">
                    <?php
                    switch($this->task){
                        case 'companiesimg':
                            echo '<span class="label label-info">/media/com_iproperty/companies/</span>';
                            $this->task = 'companiesimgup';
                        break;

                        case 'agentsimg':
                            echo '<span class="label label-info">/media/com_iproperty/agents/</span>';
                            $this->task = 'agentsimgup';
                        break;

                        case 'categoriesimg':
                            echo '<span class="label label-info">/media/com_iproperty/categories/</span>';
                            $this->task = 'categoriesimgup';
                        break;
                    }
                    ?>
                </div>
            </div>
            <div class="control-group">
                <div class="control-label">
                    <b><?php echo JText::_('COM_IPROPERTY_IMAGE_FILESIZE' ).':'; ?></b>
                </div>
                <div class="controls">
                    <span class="label label-info"><?php echo $this->settings->maximgsize; ?> kb</span>
                </div>
            </div>
        </div>
    </div>
    <?php echo JHTML::_( 'form.token'); ?>
    <input type="hidden" name="task" value="iconuploader.<?php echo $this->task; ?>" />
</form> 