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
<div class="tab-pane" id="ipfeaturedprops">
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
        if($this->fproperties){
            foreach($this->fproperties as $f){
                $flink = JRoute::_('index.php?option=com_iproperty&task=property.edit&id='.$f->id);
                $flisting_office = ipropertyHTML::getCompanyName($f->listing_office);
                if(!$this->settings->street_num_pos){ //street number before street
                    $ftitle = '<a href="' . $flink . '">'.$f->street_num.' '.$f->street.' '.$f->street2.'</a>';
                    if($f->title) $ftitle .= '<br />'.$f->title;
                }else{ //street number after street
                    $ftitle = '<a href="' . $flink . '">'.$f->street.' '.$f->street2.' '.$f->street_num.'</a>';
                    if($f->title) $ftitle .= '<br />'.$f->title;
                }
                echo '
                <tr class="row'.$k.'">
                    <td class="center">'.$f->id.'</td>
                    <td>'.$ftitle.'</td>
                    <td>'.(($flisting_office) ? $flisting_office : '--').'</td>
                    <td class="center">'.(($f->hits) ? $f->hits : '--').'</td>
                    <td class="center">'.(($f->saved) ? $f->saved : '--').'</td>
                    <td class="center"><a href="' . $flink . '"><i class="icon-edit"></i></a></td>
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