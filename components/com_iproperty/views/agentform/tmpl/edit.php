<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.modal');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', '.ipform select');

// Create shortcut to parameters.
$params = $this->state->get('params');
?>

<script type="text/javascript">
	var pluploadpath	= '<?php echo JURI::root().'/components/com_iproperty/assets/js'; ?>';
	Joomla.submitbutton = function(task) {
		// hack for &*%& IE8
		if(Browser.ie8){
			if(document.getElementById('pluploadcontainer') != null){
				document.id('pluploadcontainer').destroy();
			}
		}	
	
		if (task == 'agentform.cancel'){
            <?php echo $this->form->getField('bio')->save(); ?>
			Joomla.submitform(task);
        }else if(document.formvalidator.isValid(document.id('adminForm'))) {
            <?php if($this->ipauth->getAdmin()): //only confirm company if admin user ?>
                if(document.id('jform_company').selectedIndex == ''){
                    alert('<?php echo $this->escape(JText::_('COM_IPROPERTY_SELECT_COMPANIES')); ?>');
                    return false;
                }
            <?php endif; ?>
			<?php echo $this->form->getField('bio')->save(); ?>
			Joomla.submitform(task);
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}

    checkAgentEmail = function(){
        document.id('system-message-container').set('tween', {duration: 4500});
        var checkurl = '<?php echo JURI::base('true'); ?>/index.php?option=com_iproperty&task=ajax.checkUserEmail';
        var agentEmail = document.id('jform_email').value;
        
        req = new Request({
            method: 'post',
            url: checkurl,
            data: { 'email': agentEmail,
                    'agent_id': <?php echo (int) $this->item->id; ?>,
                    '<?php echo JSession::getFormToken(); ?>':'1',
                    'format': 'raw'},
            onRequest: function() {
                document.id('system-message-container').set('html', '');
            },
            onSuccess: function(response) {
                if(response){
                    document.id('system-message-container').highlight('#ff0000');
                    document.id('jform_email').value = '';
                    document.id('jform_email').set('class', 'inputbox invalid');
                    document.id('jform_email').focus();
                    document.id('system-message-container').set('html', '<div class="ip_warning"><?php echo JText::_('COM_IPROPERTY_AGENT_EMAIL_ALREADY_EXISTS'); ?></div>');                    
                }
            }
        }).send();
    }
</script>

<div class="edit item-page<?php echo $this->pageclass_sfx; ?>">
    <?php if ($this->params->get('show_page_heading', 1)) : ?>
        <h1>
            <?php echo $this->escape($this->params->get('page_heading')); ?>
        </h1>
    <?php endif; ?>
    <div class="ip-mainheader">
        <h2><?php echo $this->iptitle; ?></h2>
    </div>
    
    <div id="system-message-container"></div>

    <form action="<?php echo JRoute::_('index.php?option=com_iproperty&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate ipform form-horizontal">
        <div class="btn-toolbar">
			<div class="btn-group">
                <button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('agentform.apply')">
                    <?php echo JText::_('COM_IPROPERTY_APPLY') ?>
                </button>
                <button type="button" class="btn" onclick="Joomla.submitbutton('agentform.save')">
                    <?php echo JText::_('JSAVE') ?>
                </button>
                <button type="button" class="btn" onclick="Joomla.submitbutton('agentform.cancel')">
                    <?php echo JText::_('JCANCEL') ?>
                </button>
            </div>
        </div>
        <ul class="nav nav-tabs">
            <li class="active"><a href="#agentdetails" data-toggle="tab"><?php echo JText::_('COM_IPROPERTY_DETAILS') ?></a></li>
            <li><a href="#agentbio" data-toggle="tab"><?php echo JText::_('COM_IPROPERTY_IMAGE').' / '.JText::_('COM_IPROPERTY_AGENT_BIO'); ?></a></li>
            <?php if ($this->ipauth->getAdmin() || $this->ipauth->getSuper()): ?>
                <li><a href="#agentother" data-toggle="tab"><?php echo JText::_('COM_IPROPERTY_OTHER') ?></a></li>
            <?php endif; ?>
        </ul>
        
        <div class="tab-content">
            <div class="tab-pane active" id="agentdetails">
                <fieldset>
                    <legend><?php echo JText::_('COM_IPROPERTY_DETAILS'); ?></legend>
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
                    <?php if (is_null($this->item->id) || $this->ipauth->getAdmin()):?>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('alias'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('alias'); ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('alias'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getValue('alias'); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if($this->ipauth->getAdmin()): //only show company if admin user ?>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('company'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('company'); ?>
                            </div>
                        </div>
                    <?php elseif($this->form->getValue('company')): //if not admin and company already set, leave it as a hidden field ?>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('company'); ?>
                            </div>
                            <div class="controls">
                                <?php echo ipropertyHTML::getCompanyName($this->form->getValue('company')); ?>
                            </div>
                        </div>
                        <input type="hidden" name="jform[company]" value="<?php echo $this->form->getValue('company'); ?>" />
                    <?php else: ?>
                        <input type="hidden" name="jform[company]" value="<?php echo $this->ipauth->getUagentCid(); ?>" />
                    <?php endif; ?>
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
                </fieldset>
                <fieldset>
                    <legend><?php echo JText::_('COM_IPROPERTY_ADDRESS'); ?></legend>
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
                </fieldset>
                <fieldset>
                    <legend><?php echo JText::_('COM_IPROPERTY_WEB'); ?></legend>
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
                </fieldset>
            </div>
            <div class="tab-pane" id="agentbio">
                <fieldset>
                    <legend><?php echo JText::_('COM_IPROPERTY_IMAGE'); ?></legend>
                    <div class="control-group">
                        <div class="controls">
                            <?php echo $this->form->getInput('icon'); ?>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend><?php echo JText::_('COM_IPROPERTY_AGENT_BIO'); ?></legend>
                    <div class="control-group form-vertical">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('iphead1'); ?>
                        </div>
                        <div class="control">
                            <?php echo $this->form->getInput('bio'); ?>
                        </div>
                    </div>            
                </fieldset>
            </div>  
            <!-- super agent or admin can edit params -->
            <?php if ($this->ipauth->getAdmin() || $this->ipauth->getSuper()): ?>
                <div class="tab-pane" id="agentother">
                    <fieldset class="adminform">
                        <legend><?php echo JText::_('JPUBLISHED'); ?></legend>
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
                    </fieldset>
                    <fieldset class="adminform superagent_params">
                        <legend><?php echo JText::_('COM_IPROPERTY_AGENT_PARAMETERS'); ?></legend>
                        <?php foreach($this->form->getFieldset('superagent_params') as $field) :?>
                                <div class="control-group">
                                    <div class="control-label">
                                        <?php echo $field->label; ?>
                                    </div>
                                    <div class="controls">
                                        <?php echo $field->input; ?>
                                    </div>
                                </div>
                        <?php endforeach; ?>
                    </fieldset>  
                </div>
            <?php endif; ?>
        </div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="return" value="<?php echo $this->return_page;?>" />
        <?php echo JHtml::_( 'form.token'); ?>
    </form>
</div>