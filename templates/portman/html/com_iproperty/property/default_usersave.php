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

<div class="modal hide fade" id="saveModal" tabindex="-1" role="dialog" aria-labelledby="saveModalLabel" aria-hidden="true">   
    <?php
    ////see if user is logged in
    if( !$this->user->id ){
        ?>
        <div class="modal-body">
            <div align="center">
                <?php echo JHtml::_('image', 'components/com_iproperty/assets/images/iproperty.png', JText::_('COM_IPROPERTY_PLEASE_LOG_IN')); ?><br />
                <?php echo JText::_('COM_IPROPERTY_LOG_IN_TO_SAVE_FAVORITES'); ?><br />
                <a href="<?php echo JRoute::_('index.php?option=com_users&view=login&return='.base64_encode(JURI::getInstance()->toString())); ?>"><?php echo JText::_('COM_IPROPERTY_PLEASE_LOG_IN'); ?></a>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('JCANCEL'); ?></button>
        </div>
        <?php
    }else{
        ?>
        <form name="ipsaveProperty" action="<?php echo JRoute::_('index.php', true); ?>" method="post" class="form-horizontal ip-saveprop-form">        
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h3 id="saveModalLabel"><?php echo JText::_('COM_IPROPERTY_SAVE'); ?></h3>
            </div>
            <div class="modal-body">            
                <div class="row-fluid">
                    <p><?php echo JText::_('COM_IPROPERTY_SAVE_PROPERTY_TO_FAVORITES_TEXT'); ?></p>
                    <div class="clearfix"></div>
                    <div class="control-group">
                        <div class="control-label"><?php echo JText::_('COM_IPROPERTY_NOTES'); ?></div>
                        <div class="controls"><input type="text" class="inputbox" name="notes" value="" maxlength="125" /></div>
                    </div>
                    <?php if($this->settings->show_propupdate): ?>
                    <div class="control-group">
                        <div class="control-label"><?php echo JText::_('COM_IPROPERTY_EMAIL_UPDATES'); ?></div>
                        <div class="controls"><input type="checkbox" name="email_update" value="1" checked="checked" /></div>
                    </div>
                    <?php endif; ?> 
                </div> 
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('JCANCEL'); ?></button>
                <button class="btn btn-primary" type="submit"><?php echo JText::_('COM_IPROPERTY_SAVE_PROPERTY_TO_FAVORITES'); ?></button>
            </div>
            <input type="hidden" name="task" value="ipuser.saveProperty" />
            <input type="hidden" name="option" value="com_iproperty" />
            <input type="hidden" name="view" value="property" />
            <input type="hidden" name="userid" value="<?php echo $this->user->id; ?>" />
            <input type="hidden" name="id" value="<?php echo $this->p->id; ?>" />
            <?php echo JHTML::_( 'form.token'); ?>
        </form>
        <?php 
    }//end logged in if
    ?>    
</div>