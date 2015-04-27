<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
$sspan = ($this->settings->show_searchupdate) ? 4 : 3;
?>

<h3><?php echo JText::_('COM_IPROPERTY_MY_SAVED_SEARCHES'); ?></h3>
<table class="table table-striped" id="favoriteSaveList">
    <thead>
        <tr>
            <th class="nowrap" width="65%"><?php echo JText::_('COM_IPROPERTY_TITLE'); ?></th>
            <th class="nowrap hidden-phone" width="30%"><?php echo JText::_('COM_IPROPERTY_SAVED'); ?></th>
            <?php if($this->settings->show_searchupdate): ?>
                <th class="nowrap" width="5%">
                    <span class="hasTooltip" title="<?php echo JText::_('COM_IPROPERTY_EMAIL_UPDATES'); ?> :: <?php echo JText::_('COM_IPROPERTY_EMAIL_UPDATES_TIP'); ?>">
                    <?php echo JText::_('COM_IPROPERTY_UPDATES'); ?>
                    </span>
                </th>
            <?php endif; ?>
            <th class="nowrap center" width="5%"><span class="icon-question-sign hasTooltip" title="<?php echo JText::_('COM_IPROPERTY_HELP'); ?>::<?php echo JText::_('COM_IPROPERTY_HELP_TIP'); ?>"></span></th>
        </tr>
    </thead>
    <tbody>
        <?php
        // list saved searches
        if ($this->searches)
        {
            $k = 0;
            for($i = 0; $i < count($this->searches); $i++)
            {
                $s              = $this->searches[$i];
                $save_notes     = ($s->notes) ? ' class="hasTooltip" title="'.JText::_('COM_IPROPERTY_USER_NOTES' ).' :: '.$s->notes.'"' : '';
                $checked        = $s->email_update ? 'checked="checked"' : '';

                echo '
                    <tr class="ip-row'.$k.'" id="'.$s->id.'" >
                        <td class="nowrap"><a id="ipsavedsearchlink" href="javascript:setCookieRedirect('.$s->id.')"'.$save_notes.'>'.$s->title.'</a></td>
                        <td class="nowrap center hidden-phone">'.$s->created.'</td>';
                        if($this->settings->show_searchupdate)
                        {
                            echo '<td class="nowrap" align="center"><input class="ipsave_eupdate" type="checkbox"'.$checked.' /></td>';
                        }
                echo '
                        <td id="'.$s->id.'" class="nowrap center"><a class="btn btn-small btn-danger ipsave_delete" href="javascript:void(0);"><span class="icon-trash"></span></a></td>
                     </tr>';
                $k = 1 - $k;
            }
       } else {
            echo '<tr class="ip-row0">
                    <td colspan="'.$sspan.'" class="nowrap center">'.JText::_('COM_IPROPERTY_NO_RESULTS').'</td>
                  </tr>';
       }
       ?>
    </tbody>
</table>