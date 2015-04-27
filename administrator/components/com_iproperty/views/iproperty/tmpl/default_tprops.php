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
<div class="tab-pane active" id="iptopprops">
    <table class="table table-striped">
        <thead>
            <tr>
                <th width="1%" class="nowrap center hidden-phone"><?php echo JText::_('COM_IPROPERTY_ID'); ?></th>
                <th width="40%" class="nowrap center hidden-phone"><?php echo JText::_('COM_IPROPERTY_TITLE'); ?></th>
                <th width="30%" class="nowrap center hidden-phone"><?php echo JText::_('COM_IPROPERTY_COMPANY'); ?></th>
                <th width="10%" class="nowrap center hidden-phone"><?php echo JText::_('COM_IPROPERTY_HITS'); ?></th>
                <th width="10%" class="nowrap center hidden-phone"><?php echo JText::_('COM_IPROPERTY_SAVED'); ?></th>
                <th width="10%" class="nowrap center hidden-phone"><?php echo JText::_('COM_IPROPERTY_EDIT'); ?></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="6">
                    &nbsp;
                </td>
            </tr>
        </tfoot>
        <tbody>
        <?php
        $k = 0;
        if($this->tproperties){
            foreach($this->tproperties as $t){
                $tlink = JRoute::_('index.php?option=com_iproperty&task=property.edit&id='.$t->id);
                $tlisting_office = ipropertyHTML::getCompanyName($t->listing_office);
                if(!$this->settings->street_num_pos){ //street number before street
                    $ttitle = '<a href="' . $tlink . '">'.$t->street_num.' '.$t->street.' '.$t->street2.'</a>';
                    if($t->title) $ttitle .= '<br />'.$t->title;
                }else{ //street number after street
                    $ttitle = '<a href="' . $tlink . '">'.$t->street.' '.$t->street2.' '.$t->street_num.'</a>';
                    if($t->title) $ttitle .= '<br />'.$t->title;
                }
                echo '
                <tr class="row'.$k.'">
                    <td class="center">'.$t->id.'</td>
                    <td>'.$ttitle.'</td>
                    <td>'.(($tlisting_office) ? $tlisting_office : '--').'</td>
                    <td class="center">'.(($t->hits) ? $t->hits : '--').'</td>
                    <td class="center">'.(($t->saved) ? $t->saved : '--').'</td>
                    <td class="center"><a href="' . $tlink . '"><i class="icon-edit"></i></a></td>
                </tr>';
                $k = 1 - $k;
            }
        }else{
            echo '
            <tr class="row'.$k.'">
                <td colspan="6" class="center">'.JText::_('COM_IPROPERTY_NO_RESULTS' ).'</td>
            </tr>';
        }
        ?>
        </tbody>
    </table>
</div>