<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
$pspan = ($this->settings->show_propupdate) ? 6 : 5;
?>

<h3><?php echo JText::_('COM_IPROPERTY_MY_SAVED_PROPERTIES'); ?></h3>
<table class="table table-striped" id="favoritePropList">
    <thead>
        <tr>
            <th class="nowrap" width="35%"><?php echo JText::_('COM_IPROPERTY_TITLE'); ?></th>
            <th class="nowrap hidden-phone" width="20%"><?php echo JText::_('COM_IPROPERTY_CITY'); ?></th>
            <th class="nowrap" width="20%"><?php echo JText::_('COM_IPROPERTY_PRICE'); ?></th>
            <th class="nowrap hidden-phone" width="15%"><?php echo JText::_('COM_IPROPERTY_SAVED'); ?></th>
            <?php if($this->settings->show_propupdate): ?>
                <th class="nowrap" width="5%">
                    <span class="hasTooltip" title="<?php echo JText::_('COM_IPROPERTY_EMAIL_UPDATES'); ?> :: <?php echo JText::_('COM_IPROPERTY_EMAIL_UPDATES_TIP'); ?>">
                    <?php echo JText::_('COM_IPROPERTY_UPDATES'); ?>
                    </span>
                </th>
            <?php endif; ?>
            <th class="nowrap center" width="5%"><i class="icon-question-sign hasTooltip" title="<?php echo JText::_('COM_IPROPERTY_HELP'); ?>::<?php echo JText::_('COM_IPROPERTY_HELP_TIP'); ?>"></i></th>
        </tr>
    </thead>
    <tbody>
        <?php
        // list saved properties
        if ($this->properties)
        {
            $k = 0;
            foreach($this->properties as $p)
            {
                $thumbnail      = ($p->thumb) ? htmlentities($p->thumb.'<br />') : '';
                $property_notes = ($p->notes || $thumbnail) ? ' class="hasTooltip" title="'.JText::_('COM_IPROPERTY_USER_NOTES').' :: '.$thumbnail.$p->notes.'"' : '';                            
                $checked        = $p->email_update ? 'checked="checked"' : '';

                if( $p->state == 1 && $p->approved ): //property still exists
                    echo '<tr class="ip-row'.$k.'" id="'.$p->save_id.'" >
                            <td class="ipuser-title"><a href="'.$p->proplink.'"'.$property_notes.'>'.$p->street_address.'</a></td>
                            <td class="nowrap hidden-phone">'.$p->city.'</td>
                            <td class="nowrap">'.$p->formattedprice.'</td>
                            <td class="nowrap center hidden-phone">'.$p->created.'</td>';
                            if($this->settings->show_propupdate){
                                echo '<td class="nowrap center"><input class="ipsave_eupdate" type="checkbox"'.$checked.' /></td>';
                            }
                    echo '
                            <td class="nowrap center"><a class="btn btn-small btn-danger ipsave_delete" href="javascript:void(0);"><i class="icon-trash"></i></a></td>
                         </tr>';
                else: //property no longer available
                    echo '<tr class="ip-row'.$k.'" id="'.$p->save_id.'" >
                            <td class="nowrap"><span'.$property_notes.'>'.$p->street_address.'</span></td>
                            <td colspan="'.($pspan - 2).'" class="nowrap"><span class="ipblink">'.JText::_('COM_IPROPERTY_PROPERTY_NOT_AVAILABLE').'</span></td>
                            <td id="'.$p->save_id.'" class="nowrap center"><a class="btn btn-small btn-danger ipsave_delete" href="javascript:void(0);"><i class="icon-trash"></i></a></td>
                         </tr>';
                endif;
                $k = 1 - $k;
            }
       } else {
            echo '<tr class="ip-row0">
                    <td colspan="'.$pspan.'" class="nowrap center">'.JText::_('COM_IPROPERTY_NO_RESULTS').'</td>
                  </tr>';
       }
       ?>
    </tbody>
</table>