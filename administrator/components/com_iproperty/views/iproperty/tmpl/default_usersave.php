<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
$document = JFactory::getDocument();

$usersave_script = "
(function($) {
    getAjaxSaved = function(type, userid){
        var el      = $('#'+type+'_'+userid);
        var type    = (type == 'active') ? 1 : 0;
        $.ajax({
            type: 'GET',
            url: '".JURI::base(true)."/index.php?option=com_iproperty&task=ajax.getUserSaved&format=raw&".JSession::getFormToken()."=1&active='+type+'&userid='+userid,
            cache: false,
            error: function(request, status, error_message){
                alert(status+' '+error_message);
            },
            success: function(data) {
                el.attr('data-content', data);
                el.popover('show');
            },
            async: false
        });
     }
 })(jQuery);";
$document->addScriptDeclaration($usersave_script);
?>

<div class="tab-pane" id="ipactiveusers">
    <table class="table table-striped">
        <thead>
            <tr>
                <th width="1%" class="nowrap center hidden-phone"><?php echo JText::_('COM_IPROPERTY_ID'); ?></th>                        
                <th width="20%" class="nowrap hidden-phone"><?php echo JText::_('COM_IPROPERTY_NAME'); ?></th>
                <th width="5%" class="nowrap center hidden-phone"><?php echo JText::_('COM_IPROPERTY_ACTIVE'); ?></th>
                <th width="5%" class="nowrap center hidden-phone"><?php echo JText::_('COM_IPROPERTY_INACTIVE'); ?></th>
                <th width="20%" class="nowrap center hidden-phone"><?php echo JText::_('COM_IPROPERTY_USERNAME'); ?></th>
                <th width="20%" class="nowrap center hidden-phone"><?php echo JText::_('COM_IPROPERTY_EMAIL'); ?></th>
                <th width="20%" class="nowrap center hidden-phone"><?php echo JText::_('COM_IPROPERTY_REGISTERED'); ?></th>                        
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="7">
                    &nbsp;
                </td>
            </tr>
        </tfoot>
        <tbody>
        <?php
        $k = 0;
        if($this->ausers){
            foreach($this->ausers as $a){
                $a->saved_props = "saved props here...";
                $alink = JRoute::_('index.php?option=com_users&task=user.edit&id='.$a->id);
                echo '
                <tr class="row'.$k.'">
                    <td class="center">'.$a->id.'</td>                            
                    <td><a href="' . $alink . '">'.$a->name.'</a></td>
                    <td class="center"><a class="btn btn-success" id="active_'.$a->id.'" data-placement="bottom" title="'.$a->name.'" data-content="" onclick="getAjaxSaved(\'active\', '.$a->id.')">'.(($a->active_saves) ? $a->active_saves : '--').'</a></td>
                    <td class="center"><a class="btn btn-danger" id="inactive_'.$a->id.'" data-placement="bottom" title="'.$a->name.'" data-content="" onclick="getAjaxSaved(\'inactive\', '.$a->id.')">'.(($a->inactive_saves) ? $a->inactive_saves : '--').'</a></td>
                    <td class="center">'.$a->username.'</td>
                    <td class="center">'.$a->email.'</td>
                    <td class="center">'.$a->registerDate.'</td>                            
                </tr>';
                $k = 1 - $k;
            }
        }else{
            echo '
            <tr class="row'.$k.'">
                <td colspan="7" class="center">'.JText::_('COM_IPROPERTY_NO_RESULTS' ).'</td>
            </tr>';
        }
        ?>
        </tbody>
    </table>
</div>