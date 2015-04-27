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
		if (task == 'company.save2copy'){
			var alias = document.id('jform_alias').value;
			document.id('jform_alias').value = alias +'_'+String.uniqueID();
            document.id('jform_state').value = 0;
		}
        
        if (task == 'company.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
            Joomla.submitform(task, document.getElementById('adminForm'));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_iproperty&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
    <div class="row-fluid">
        <div class="span9 form-horizontal">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#codetails" data-toggle="tab"><?php echo JText::_('COM_IPROPERTY_DETAILS');?></a></li>
                <li><a href="#coimage" data-toggle="tab"><?php echo JText::_('COM_IPROPERTY_IMAGE');?></a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="codetails">
                    <div class="row-fluid">
                        <div class="span6 form-vertical">
                            <h4><?php echo JText::_('COM_IPROPERTY_COMPANY'); ?></h4>
                            <hr />
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $this->form->getLabel('name'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('name'); ?>
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
                                    <?php echo $this->form->getLabel('fax'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('fax'); ?>
                                </div>
                            </div>
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
                                    <?php echo $this->form->getLabel('clicense'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('clicense'); ?>
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
                    </div>
                </div>
                <div class="tab-pane" id="coimage">
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
            <?php if ($this->ipauth->getAdmin()): ?>
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
                    <h4><?php echo JText::_('COM_IPROPERTY_COMPANY_PARAMETERS');?></h4>   
                    <hr />
                    <div class="control-group">
                        <?php foreach($this->form->getFieldset('admin_params') as $field) :?>
                            <div class="control-label"><?php echo $field->label; ?></div>
                            <div class="controls"><?php echo $field->input; ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
<div class="clearfix"></div>
<?php echo ipropertyAdmin::footer( ); ?>