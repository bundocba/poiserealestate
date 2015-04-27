<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
?>

<script language="javascript" type="text/javascript">
    Joomla.submitbutton = function(task)
	{
		// if save as copy, make alias unique
		if (task == 'agent.save2copy'){
			var alias = document.id('jform_alias').value;
			document.id('jform_alias').value = alias +'_'+String.uniqueID();
            document.id('jform_state').value = 0;
		}
        
        if (task == 'agent.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
			<?php echo $this->form->getField('bio')->save(); ?>
            Joomla.submitform(task, document.getElementById('adminForm'));
		}
	}

    checkAgentUser = function()
    {
        $('ipmessage').set('tween', {duration: 4500});
        var checkurl = '<?php echo JURI::base('true'); ?>/index.php?option=com_iproperty&task=ajax.checkUserAgent';
        var attachedUser = $('jform_user_id_id').value;
        
        req = new Request({
            method: 'post',
            url: checkurl,
            data: { 'user_id': attachedUser,
                    'agent_id': <?php echo (int) $this->item->id; ?>,
                    '<?php echo JSession::getFormToken(); ?>':'1',
                    'format': 'raw'},
            onRequest: function() {
                $('ipmessage').set('html', '');
            },
            onSuccess: function(response) {
                if(response){
                    $('ipmessage').highlight('#ff0000');
                    $('jform_user_id_id').value = '';
                    $('jform_user_id_name').value = '';
                    $('ipmessage').set('html', '<div class="alert alert-error"><?php echo JText::_('COM_IPROPERTY_AGENT_USER_ALREADY_EXISTS'); ?></div>');                    
                }
            }
        }).send();
    }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_iproperty&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
    <div class="row-fluid">
        <div class="span9 form-horizontal">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#agentdetails" data-toggle="tab"><?php echo JText::_('COM_IPROPERTY_DETAILS');?></a></li>
                <li><a href="#agentweb" data-toggle="tab"><?php echo JText::_('COM_IPROPERTY_WEB');?></a></li>
                <li><a href="#agentimage" data-toggle="tab"><?php echo JText::_('COM_IPROPERTY_IMAGE');?></a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="agentdetails">
                    <div class="row-fluid">
                        <div class="span6 form-vertical">
                            <h4><?php echo JText::_('COM_IPROPERTY_AGENT'); ?></h4>
                            <hr />
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $this->form->getLabel('fname'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('fname'); ?>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $this->form->getLabel('lname'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('lname'); ?>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $this->form->getLabel('alias'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('alias'); ?>
                                </div>
                            </div>
                            <!-- <div class="control-group">
                                <div class="control-label">
                                    <?php //echo $this->form->getLabel('company'); ?>
                                </div>
                                <div class="controls">
                                    <?php //echo $this->form->getInput('company'); ?>
                                </div>
                            </div> -->
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $this->form->getLabel('email'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('email'); ?>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $this->form->getLabel('phone'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('phone'); ?>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $this->form->getLabel('mobile'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('mobile'); ?>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $this->form->getLabel('fax'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('fax'); ?>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $this->form->getLabel('alicense'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('alicense'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="span6 form-vertical">                            
                            <h4><?php echo JText::_('COM_IPROPERTY_LOCATION'); ?></h4>
                            <hr />
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $this->form->getLabel('street'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('street'); ?>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $this->form->getLabel('street2'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('street2'); ?>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $this->form->getLabel('city'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('city'); ?>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $this->form->getLabel('locstate'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('locstate'); ?>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $this->form->getLabel('province'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('province'); ?>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $this->form->getLabel('postcode'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('postcode'); ?>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $this->form->getLabel('country'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('country'); ?>
                                </div>
                            </div>                            
                        </div>
                        <div class="span12">
                            <div class="clr" style="height: 10px;"></div>
                            <?php echo $this->form->getLabel('iphead1'); ?>
                            <div class="clearfix"></div>
                            <?php echo $this->form->getInput('bio'); ?>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="agentweb">
                    <div class="row-fluid">
                        <div class="span6 form-vertical">
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $this->form->getLabel('website'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('website'); ?>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $this->form->getLabel('msn'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('msn'); ?>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $this->form->getLabel('skype'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('skype'); ?>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $this->form->getLabel('gtalk'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('gtalk'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="span6 form-vertical">
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $this->form->getLabel('linkedin'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('linkedin'); ?>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $this->form->getLabel('facebook'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('facebook'); ?>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $this->form->getLabel('twitter'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('twitter'); ?>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $this->form->getLabel('social1'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('social1'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="agentimage">
                    <div class="row-fluid">
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo JText::_('COM_IPROPERTY_IMAGE'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('icon'); ?>
                            </div>
                        </div>
                    </div>
                </div>                
            </div>
        </div>
        <div class="span3 form-vertical">
            <!-- super agent or admin can edit params -->
            <?php if ($this->ipauth->getAdmin() || $this->ipauth->getSuper()): ?>
                <div class="alert alert-info">
                    <h4><?php echo JText::_('COM_IPROPERTY_PUBLISHING');?></h4>
                    <hr />                
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('state'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('state'); ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('featured'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('featured'); ?>
                        </div>
                    </div>  
                </div>
                <div class="alert alert-success">
                    <h4><?php echo JText::_('COM_IPROPERTY_AGENT_PARAMETERS');?></h4>  
                    <hr />
                    <div class="control-group">
                        <?php foreach($this->form->getFieldset('superagent_params') as $field) :?>
                            <div class="control-label"><?php echo $field->label; ?></div>
                            <div class="controls"><?php echo $field->input; ?></div>
                        <?php endforeach; ?>
                    </div> 
                </div>
            <?php endif; ?>
            <!-- only admin can set agent to super agent level -->
            <?php if ($this->ipauth->getAdmin()): ?>
                <div class="alert alert-error">
                    <h4><?php echo JText::_('JADMINISTRATION');?></h4> 
                    <hr />
                    <div class="control-group">
                        <div id="ipmessage"></div>
                        <div class="control-label">
                            <?php echo $this->form->getLabel('user_id'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('user_id'); ?>
                        </div>
                        <div class="control-label">
                            <?php echo $this->form->getLabel('agent_type'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('agent_type'); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
<div class="clearfix"></div>
<?php echo ipropertyAdmin::footer(); ?>