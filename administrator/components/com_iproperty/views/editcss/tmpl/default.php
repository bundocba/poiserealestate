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
        <table class="table table-striped" id="cssList">
            <thead>
                <tr>
                    <th><?php echo JText::_('COM_IPROPERTY_SELECT_CSS'); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <?php echo $this->cssList; ?>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td>&nbsp;</td>
                </tr>
            </tfoot>
        </table>
        <?php echo JHTML::_( 'form.token'); ?>
        <input type="hidden" name="task" value="" />
    </div>
</form>
<div class="clearfix"></div>
<?php echo ipropertyAdmin::footer( ); ?>