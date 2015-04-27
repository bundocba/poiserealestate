<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');

JHtml::_('formbehavior.chosen', 'select');

$core_ip_css    = ($this->fname == 'iproperty.css' || $this->fname == 'advsearch.css' || $this->fname == 'catmap.css') ? true : false;
$edit_width     = ($core_ip_css) ? 9 : 12;

//get template list
$db             = JFactory::getDbo();
$query          = $db->getQuery(true);

$query->select('element as value, name as text')
    ->from('#__extensions')
    ->where('type='.$db->quote('template'))
    ->where('enabled = 1')
    ->where('client_id = 0')
    ->order('client_id')
    ->order('name');
    
$db->setQuery($query);
$options = $db->loadObjectList();
?>
<form action="<?php echo JRoute::_('index.php?option=com_iproperty'); ?>" method="post" name="adminForm" id="adminForm">		    
    <div class="span<?php echo $edit_width; ?>">
		<fieldset class="adminform">
            <legend><?php echo JText::_('COM_IPROPERTY_EDIT_CSS'); ?> - <?php echo $this->filename; ?></legend>
            <textarea style="width: 100%; height:500px;" cols="110" rows="25" name="filecontent" class="inputbox"><?php echo $this->content; ?></textarea>
        </fieldset>
    </div>
    <?php if($core_ip_css): ?>
        <div class="span3" style="padding-left: 10px;">
            <legend><?php echo JText::_('JTOOLBAR_DUPLICATE'); ?></legend>            
            <fieldset id="filter-bar">
                <div class="filter-select fltrt">
                    <select name="copy_template" class="inputbox">
                        <option value=""><?php echo JText::_('JOPTION_SELECT_TEMPLATE'); ?></option>
                        <?php echo JHtml::_('select.options', $options, 'value', 'text', '');?>
                    </select>                        
                </div>
            </fieldset>
            <div class="clearfix"></div> 
            <button href="#" onclick="Joomla.submitbutton('editcss.copy2template')" class="btn btn-primary">
                <i class="icon-copy "></i> <?php echo JText::_('JTOOLBAR_DUPLICATE'); ?>
            </button>
            <div class="clearfix" style="height: 20px;"></div> 
            <legend><?php echo JText::_('JSTATUS'); ?></legend>  
            <?php 
            foreach($options as $o)
            {
                if(JFile::exists(JPATH_ROOT.'/templates'.'/'.$o->value.'/css'.'/'.$this->fname)){
                    $status = JText::_('JYES');
                    $class = 'success';
                }else{
                    $status = JText::_('JNO');
                    $class = 'error';
                }
                echo '<div class="alert alert-'.$class.'">'.$o->text.' - <b>'.$status.'</b></div>';
            }
            ?>
        </div>
    <?php endif; ?>
    <?php echo JHTML::_( 'form.token'); ?>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="selectview" value="1" />
    <input type="hidden" name="edit_css_file" value="<?php echo $this->fname; ?>" />	
</form>
<div class="clearfix"></div>
<?php echo ipropertyAdmin::footer( ); ?>
	