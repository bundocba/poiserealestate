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

<div align="center">
    <form action="<?php echo JRoute::_('index.php?option=com_iproperty&view=categories'); ?>" method="post" name="adminForm" id="adminForm">   
        <table  border="0" cellspacing="4" cellpadding="1">
            <tr>
                <th colspan="2">
                    <div align="center" style="width: 500px;">
                        <span style="color: #ff0000;font-weight: bold;">
                            <?php echo JText::_('COM_IPROPERTY_CAT_REMOVE_TEXT'); ?>
                        </span><br /><br />
                        <?php echo JText::_('COM_IPROPERTY_CAT_REMOVE_SUBTEXT'); ?>
                    </div>
                </th>
            </tr>
            <tr>
                <td colspan="2" class="center">
                    <select name="subcats" id="select">
                        <option value="" selected="selected"><?php echo JText::_('COM_IPROPERTY_ONE_LEVEL_UP'); ?></option>
                        <option value="1"><?php echo JText::_('COM_IPROPERTY_DELETE_RECURSIVELY'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="center">
                    <input type="submit" name="button" id="button" value="<?php echo JText::_('COM_IPROPERTY_CONTINUE'); ?>" />
                </td>
            </tr>
        </table>
        <input type="hidden" name="task" value="categories.performRemove" />
        <input type="hidden" name="cids" value="<?php echo JRequest::getVar('cids'); ?>" />
        <?php echo JHtml::_('form.token'); ?>
    </form>
    <div class="clearfix"></div>
</div>