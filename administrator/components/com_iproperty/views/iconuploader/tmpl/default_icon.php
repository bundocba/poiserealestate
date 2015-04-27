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
<li class="imgOutline thumbnail height-80 width-80 center">
    <a class="img-preview" onclick="window.parent.ipSwitchIcon('<?php echo $this->_tmp_icon->name; ?>');">
        <div class="height-60">
            <img src="<?php echo JURI::root(true); ?>/media/com_iproperty/<?php echo $this->folder; ?>/<?php echo $this->_tmp_icon->name; ?>" width="<?php echo $this->_tmp_icon->width_60; ?>" height="<?php echo $this->_tmp_icon->height_60; ?>" alt="<?php echo $this->_tmp_icon->name; ?> - <?php echo $this->_tmp_icon->size; ?>" class="hasTooltip" title="<?php echo $this->_tmp_icon->name; ?>" />
        </div>
    </a>
    <a class="delete-item" href="<?php echo JRoute::_('index.php?option=com_iproperty&task=iconuploader.delete&tmpl=component&folder='.$this->folder.'&rm[]='.$this->_tmp_icon->name); ?>" onclick="if(!confirm('<?php echo $this->escape(JText::_('COM_IPROPERTY_CONFIRM_DELETE')); ?>')) return false">
        <?php echo JHtml::_('image', 'media/remove.png', JText::_('JACTION_DELETE'), array('width' => 16, 'height' => 16), true); ?>
    </a>
</li>
