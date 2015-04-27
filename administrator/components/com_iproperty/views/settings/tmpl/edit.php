<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
?>
<script type="text/javascript">
    //<![CDATA[    
    window.addEvent((window.webkit) ? 'load' : 'domready', function()
    {
        displayStypes = function() {
            var checkurl = '<?php echo JURI::base('true'); ?>/index.php?option=com_iproperty&task=ajax.displayStypes';      

            req = new Request({
                method: 'get',
                url: checkurl,
                data: { '<?php echo JSession::getFormToken(); ?>':'1',
                        'format': 'raw'},
                onRequest: function() {
                    $( 'ajax-stypes-container' ).empty().addClass( 'loading_div');
                },
                onSuccess: function(response) {
                    if(response){
                        $('ajax-stypes-container').removeClass('loading_div').set('html', response);                   
                    }else{
                        alert('<?php echo $this->escape(JText::_('COM_IPROPERTY_UPDATE_UNSUCCESSFUL')); ?>');
                    }                    
                }
            }).send();
        }
        displayStypes();
    });
    
    function saveStypes()
    {
        stype_rows = $$('tr.stype_row');
        var payload = new Array();

        for(var i = 0; i < stype_rows.length; i++) 
        {
            stypes = $$('tr.stype_row input.s'+i);
            stype = new Array();
            var n = 0;
            $$('tr.stype_row input.s'+i).each(function(e){
                if(e.type == 'checkbox')
                {
                    stype[n] = (e.checked) ? 1 : 0;
                }else{
                    stype[n] = e.value;                    
                }
                n++;
            });
            payload[i] = stype;
        }

        payload = JSON.encode(payload);
        
        $('ipmessage').set('tween', {duration: 4500});
        var checkurl = '<?php echo JURI::base('true'); ?>/index.php?option=com_iproperty&task=ajax.saveStypes';
        
        req = new Request({
            method: 'post',
            url: checkurl,
            data: { 'stypes': payload,
                    '<?php echo JSession::getFormToken(); ?>':'1',
                    'format': 'raw'},
            onRequest: function() {
                $('ipmessage').set('html', '');
            },
            onSuccess: function(response) {
                if(response){
                    $('ipmessage').set('html', response);                    
                }else{
                    alert('<?php echo $this->escape(JText::_('COM_IPROPERTY_UPDATE_UNSUCCESSFUL')); ?>');
                }
                displayStypes();
            }
        }).send();
    }
    
    deleteStype = function(stypeid)
    {
        $('ipmessage').set('tween', {duration: 4500});
        var checkurl = '<?php echo JURI::base('true'); ?>/index.php?option=com_iproperty&task=ajax.deleteStype';
        
        req = new Request({
            method: 'post',
            url: checkurl,
            data: { 'id': stypeid,
                    '<?php echo JSession::getFormToken(); ?>':'1',
                    'format': 'raw'},
            onRequest: function() {
                $('ipmessage').set('html', '');
            },
            onSuccess: function(response) {
                if(response){
                    $('ipmessage').set('html', response);                    
                }else{
                    alert('<?php echo $this->escape(JText::_('COM_IPROPERTY_UPDATE_UNSUCCESSFUL')); ?>');
                }
                displayStypes();
            }
        }).send();
    }
    //]]>
</script>

           
<form action="<?php echo JRoute::_('index.php?option=com_iproperty&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
<?php if (!empty( $this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
<?php else : ?>
    <div id="j-main-container">
<?php endif;?>
        <?php IpropertyAdmin::buildAdminToolbar(); ?>
        <div class="row-fluid">
            <!-- Begin Content -->
            <div class="span12 form-horizontal">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#ipgeneralsettings" data-toggle="tab"><?php echo JText::_('COM_IPROPERTY_GENERAL_SETTINGS');?></a></li>
                    <li><a href="#ipcatsettings" data-toggle="tab"><?php echo JText::_('COM_IPROPERTY_CATEGORIES');?></a></li>
                    <li><a href="#ippropsettings" data-toggle="tab"><?php echo JText::_('COM_IPROPERTY_PROPERTIES');?></a></li>
                    <li><a href="#ipagentsettings" data-toggle="tab"><?php echo JText::_('COM_IPROPERTY_AGENTS');?></a></li>
                    <li><a href="#ipcompanysettings" data-toggle="tab"><?php echo JText::_('COM_IPROPERTY_COMPANIES');?></a></li>
                    <li><a href="#ipqssettings" data-toggle="tab"><?php echo JText::_('COM_IPROPERTY_QUICKSEARCH');?></a></li>
                    <li><a href="#ipadvsettings" data-toggle="tab"><?php echo JText::_('COM_IPROPERTY_ADVSEARCH');?></a></li>
                    <li><a href="#ipgallerysettings" data-toggle="tab"><?php echo JText::_('COM_IPROPERTY_GALLERY');?></a></li>
                    <li><a href="#ipmiscsettings" data-toggle="tab"><?php echo JText::_('COM_IPROPERTY_MISC');?></a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="ipgeneralsettings">
                        <div class="row-fluid">
                            <div class="span7">
                                <h4><?php echo JText::_('COM_IPROPERTY_OFFLINE_SETTINGS');?></h4>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('offline'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('offline'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('offmessage'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('offmessage'); ?>
                                    </div>
                                </div>
                                <h4><?php echo JText::_('COM_IPROPERTY_GLOBAL_IP_SETTINGS');?></h4>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('showtitle'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('showtitle'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('street_num_pos'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('street_num_pos'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('measurement_units'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('measurement_units'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('baths_fraction'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('baths_fraction'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('new_days'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('new_days'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('updated_days'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('updated_days'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('rss'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('rss'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('banner_display'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('banner_display'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('accent'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('accent'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('secondary_accent'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('secondary_accent'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('force_accents'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('force_accents'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('require_login'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('require_login'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('footer'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('footer'); ?>
                                    </div>                           
                                </div>
                                <h4><?php echo JText::_('ACL');?></h4>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('edit_rights'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('edit_rights'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('auto_publish'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('auto_publish'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('approval_level'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('approval_level'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('notify_newprop'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('notify_newprop'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('moderate_listings'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('moderate_listings'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('auto_agent'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('auto_agent'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="span5">
                                <h4><?php echo JText::_('COM_IPROPERTY_CURRENCY_SETTINGS');?></h4>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('default_currency'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('default_currency'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('currency'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('currency'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('currency_digits'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('currency_digits'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('nformat'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('nformat'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('currency_pos'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('currency_pos'); ?>
                                    </div>
                                </div>
                                <h4><?php echo JText::_('COM_IPROPERTY_DEFAULTS');?></h4>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('default_company'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('default_company'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('default_agent'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('default_agent'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('default_category'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('default_category'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('default_state'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('default_state'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('default_country'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('default_country'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('default_a_sort'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('default_a_sort'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('default_a_order'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('default_a_order'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('default_c_sort'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('default_c_sort'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('default_c_order'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('default_c_order'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('default_p_sort'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('default_p_sort'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('default_p_order'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('default_p_order'); ?>
                                    </div>
                                </div>
                                <h4><?php echo JText::_('COM_IPROPERTY_OTHER');?></h4>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('hard404'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('hard404'); ?>
                                    </div>
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('bootstrap_css'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('bootstrap_css'); ?>
                                    </div>
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('ip_router'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('ip_router'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="ipcatsettings">
                        <div class="row-fluid">
                            <div class="span6">
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('cat_photo_width'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('cat_photo_width'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('iplayout'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('iplayout'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('cat_entries'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('cat_entries'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="span6">
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('show_scats'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('show_scats'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('cat_recursive'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('cat_recursive'); ?>
                                    </div>
                                </div>                                
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="ippropsettings">
                        <div class="row-fluid">
                            <div class="span6">
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('perpage'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('perpage'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('show_featured'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('show_featured'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('num_featured'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('num_featured'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('featured_pos'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('featured_pos'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('featured_accent'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('featured_accent'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('overview_char'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('overview_char'); ?>
                                    </div>
                                </div>
								<div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('show_requestshowing'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('show_requestshowing'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('form_recipient'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('form_recipient'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('form_copyadmin'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('form_copyadmin'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="span6">
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('show_sendtofriend'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('show_sendtofriend'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('notify_sendfriend'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('notify_sendfriend'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('show_print'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('show_print'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('show_saveproperty'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('show_saveproperty'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('show_propupdate'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('show_propupdate'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('notify_saveprop'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('notify_saveprop'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('show_mtgcalc'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('show_mtgcalc'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('show_hits'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('show_hits'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="ipagentsettings">
                        <div class="row-fluid">
                            <div class="span6">
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('show_agent'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('show_agent'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('agent_photo_width'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('agent_photo_width'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('agent_show_image'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('agent_show_image'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('agent_show_address'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('agent_show_address'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('agent_show_contact'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('agent_show_contact'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('agent_show_email'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('agent_show_email'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('agent_show_phone'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('agent_show_phone'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('agent_show_mobile'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('agent_show_mobile'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="span6">                            
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('agent_show_fax'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('agent_show_fax'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('agent_show_website'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('agent_show_website'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('agent_show_social'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('agent_show_social'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('agent_show_license'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('agent_show_license'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('agent_show_featured'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('agent_show_featured'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('agent_feat_num'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('agent_feat_num'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('agent_feat_pos'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('agent_feat_pos'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="ipcompanysettings">
                        <div class="row-fluid">
                            <div class="span6">
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('company_photo_width'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('company_photo_width'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('co_show_image'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('co_show_image'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('co_show_address'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('co_show_address'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('co_show_contact'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('co_show_contact'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('co_show_email'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('co_show_email'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('co_show_phone'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('co_show_phone'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('co_show_fax'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('co_show_fax'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="span6">
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('co_show_website'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('co_show_website'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('co_show_license'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('co_show_license'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('co_show_featured'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('co_show_featured'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('co_feat_num'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('co_feat_num'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('co_feat_pos'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('co_feat_pos'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="ipqssettings">
                        <div class="row-fluid">
                            <div class="span6">
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('qs_show_keyword'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('qs_show_keyword'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('qs_show_cat'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('qs_show_cat'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('qs_show_stype'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('qs_show_stype'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('qs_show_minbeds'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('qs_show_minbeds'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('qs_show_minbaths'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('qs_show_minbaths'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('qs_show_price'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('qs_show_price'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('qs_show_sqft'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('qs_show_sqft'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="span6">
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('qs_show_country'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('qs_show_country'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('qs_show_state'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('qs_show_state'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('qs_show_province'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('qs_show_province'); ?>
                                    </div>
                                </div>                                
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('qs_show_county'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('qs_show_county'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('qs_show_region'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('qs_show_region'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('qs_show_city'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('qs_show_city'); ?>
                                    </div>
                                </div>                               
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="ipadvsettings">
                        <div class="row-fluid">
                            <div class="span6">
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('adv_perpage'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('adv_perpage'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('adv_nolimit'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('adv_nolimit'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('adv_price_low'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('adv_price_low'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('adv_price_high'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('adv_price_high'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('adv_beds_low'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('adv_beds_low'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('adv_beds_high'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('adv_beds_high'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('adv_baths_low'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('adv_baths_low'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('adv_baths_high'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('adv_baths_high'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('adv_sqft_low'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('adv_sqft_low'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('adv_sqft_high'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('adv_sqft_high'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('adv_default_lat'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('adv_default_lat'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('adv_default_long'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('adv_default_long'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('adv_default_zoom'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('adv_default_zoom'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('adv_maptype'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('adv_maptype'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('show_savesearch'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('show_savesearch'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('show_searchupdate'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('show_searchupdate'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="span6">
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('adv_show_shapetools'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('adv_show_shapetools'); ?>
                                    </div>
                                </div>
								<div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('adv_show_clusterer'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('adv_show_clusterer'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('adv_show_amen'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('adv_show_amen'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('adv_show_hoa'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('adv_show_hoa'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('adv_show_reo'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('adv_show_reo'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('adv_show_wf'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('adv_show_wf'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('adv_show_stype'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('adv_show_stype'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('adv_show_country'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('adv_show_country'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('adv_show_locstate'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('adv_show_locstate'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('adv_show_province'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('adv_show_province'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('adv_show_county'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('adv_show_county'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('adv_show_region'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('adv_show_region'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('adv_show_city'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('adv_show_city'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="ipgallerysettings">
                        <div class="row-fluid">
                            <div class="span6">
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('gallerytype'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('gallerytype'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('gallery_width'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('gallery_width'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('gallery_height'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('gallery_height'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('imgpath'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('imgpath'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('maximgs'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('maximgs'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('maximgsize'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('maximgsize'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('gplibrary'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('gplibrary'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="span6">
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('imgwidth'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('imgwidth'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('imgheight'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('imgheight'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('imgproportion'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('imgproportion'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('thumbwidth'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('thumbwidth'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('thumbheight'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('thumbheight'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('thumbproportion'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('thumbproportion'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('thumbquality'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('thumbquality'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('watermark'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('watermark'); ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $this->form->getLabel('watermark_text'); ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this->form->getInput('watermark_text'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="ipmiscsettings">
                    <div id="ipmessage"></div>
                        <div class="row-fluid">
                            <h4 class="hidden-phone"><?php echo JText::_('COM_IPROPERTY_SALE_TYPES'); ?></h4>
                            <div class="span12 hidden-phone">
                                <div id="filter-bar" class="btn-toolbar">
                                    <div class="filter-search btn-group pull-left">
                                        <input type="button" class="btn btn-success" onclick="saveStypes(); return false;" value="<?php echo JText::_('COM_IPROPERTY_SAVE'); ?>" />
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <table class="table table-striped" id="stypesList">
                                    <thead>
                                        <tr>
                                            <th width="15%" class="center"><?php echo JText::_('COM_IPROPERTY_TITLE'); ?></th>
                                            <th width="20%" class="center"><?php echo JText::_('COM_IPROPERTY_BANNER_IMAGE'); ?></th>
                                            <th width="15%" class="center"><?php echo JText::_('COM_IPROPERTY_BANNER_COLOR'); ?></span></th>
                                            <th width="10%" class="center"><?php echo JText::_('COM_IPROPERTY_PUBLISHED'); ?></th>
                                            <th width="15%" class="center"><?php echo JText::_('COM_IPROPERTY_SHOW_BANNER'); ?></th>
                                            <th width="15%" class="center"><?php echo JText::_('COM_IPROPERTY_SHOW_REQUEST'); ?></th>
                                            <th width="5%" class="center"><?php echo JText::_('COM_IPROPERTY_ID'); ?></th>
                                            <th width="5%" class="center"><?php echo JText::_('COM_IPROPERTY_ACTION'); ?></th>
                                        </tr>
                                    </thead>
                                    <tfoot><tr><td colspan="7">&nbsp;</td></tr></tfoot>
                                    <tbody id="ajax-stypes-container">
                                    </tbody>
                                </table>
                            </div>
                            <div class="clearfix"></div>
                            <hr />
                            <div class="row-fluid">
                                <div class="span6">
                                    <h4><?php echo JText::_('COM_IPROPERTY_DISCLAIMER'); ?></h4>
                                    <div class="control-group">
                                        <div class="control-label">
                                            <?php echo $this->form->getLabel('disclaimer'); ?>
                                        </div>
                                        <div class="controls">
                                            <?php echo $this->form->getInput('disclaimer'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="span6">
                                    <h4><?php echo JText::_('COM_IPROPERTY_MAP_SETTINGS'); ?></h4>
                                    <div class="control-group">
                                        <div class="control-label">
                                            <?php echo $this->form->getLabel('map_provider'); ?>
                                        </div>
                                        <div class="controls">
                                            <?php echo $this->form->getInput('map_provider'); ?>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <div class="control-label">
                                            <?php echo $this->form->getLabel('map_locale'); ?>
                                        </div>
                                        <div class="controls">
                                            <?php echo $this->form->getInput('map_locale'); ?>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <div class="control-label">
                                            <?php echo $this->form->getLabel('map_credentials'); ?>
                                        </div>
                                        <div class="controls">
                                            <?php echo $this->form->getInput('map_credentials'); ?>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <div class="control-label">
                                            <?php echo $this->form->getLabel('max_zoom'); ?>
                                        </div>
                                        <div class="controls">
                                            <?php echo $this->form->getInput('max_zoom'); ?>
                                        </div>
                                    </div>
                                    <h4><?php echo JText::_('COM_IPROPERTY_FEED_SETTINGS'); ?></h4>
                                    <div class="control-group">
                                        <div class="control-label">
                                            <?php echo $this->form->getLabel('feed_show'); ?>
                                        </div>
                                        <div class="controls">
                                            <?php echo $this->form->getInput('feed_show'); ?>
                                        </div>
                                        <div class="control-label">
                                            <?php echo $this->form->getLabel('feed_admin'); ?>
                                        </div>
                                        <div class="controls">
                                            <?php echo $this->form->getInput('feed_admin'); ?>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
<div class="clearfix"></div>
<?php echo ipropertyAdmin::footer( ); ?>