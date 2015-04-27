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
    <?php
    if ($this->item->id != 0) {
        echo "window.addEvent('domready', function() {
            var parentList = document.getElementById('jform_parent');
            for (i = parentList.length - 1; i>=0; i--) {
                if (parentList.options[i].value == ".$this->item->id.") {
                    parentList.remove(i);
                }
            }
            $('jform_parent').getElements('options[value=".$this->item->id."]').dispose();
        });";
    }
    ?>
    Joomla.submitbutton = function(task)
	{
		if (task == 'category.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
            <?php echo $this->form->getField('desc')->save(); ?>
            Joomla.submitform(task, document.getElementById('adminForm'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_iproperty&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
    <div class="row-fluid">
        <div class="span9 form-horizontal">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#catsettings" data-toggle="tab"><?php echo JText::_('COM_IPROPERTY_DETAILS');?></a></li>
                <li><a href="#catpublishing" data-toggle="tab"><?php echo JText::_('JGLOBAL_FIELDSET_PUBLISHING');?></a></li>
                <li><a href="#catimage" data-toggle="tab"><?php echo JText::_('COM_IPROPERTY_IMAGE');?></a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="catsettings">
                    <div class="row-fluid">
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('parent'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('parent'); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('ordering'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('ordering'); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('title'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('title'); ?>
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
                        <div class="clr" style="height: 10px;"></div>
                        <?php echo $this->form->getLabel('iphead1'); ?>
                        <div class="clearfix"></div>
                        <?php echo $this->form->getInput('desc'); ?>
                    </div>
                </div>
                <div class="tab-pane" id="catpublishing">
                    <div class="row-fluid">
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('publish_up'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('publish_up'); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('publish_down'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('publish_down'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="catimage">
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
            <div class="alert alert-info">
                <h4><?php echo JText::_('JDETAILS');?></h4>
                <hr />            
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('access'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('access'); ?>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('state'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('state'); ?>
                    </div>
                </div> 
            </div>
        </div>
    </div>
    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
</form>
<div class="clearfix"></div>
<?php echo ipropertyAdmin::footer( ); ?>