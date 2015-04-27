<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
JHTML::_('behavior.modal');
JHtml::_('bootstrap.tooltip');
$document   = JFactory::getDocument();
//if no login
$user = JFactory::getUser();
if(empty($user->id)){
	header('Location: http://poiserealestate.com/');
exit;
}
$db = JFactory::getDBO();

$query="select * from #__iproperty_file order by title ";
$db->setQuery($query);
$rows_file=$db->loadObjectList();
$options = array();

$options[] = JHTML::_('select.option', 0, JText::_('SELECT FORM'));

for($i=0;$i<count($rows_file);$i++){
	$options[] = JHTML::_('select.option', $rows_file[$i]->id, $rows_file[$i]->title);
}
$dropdown_file = JHTML::_('select.genericlist', $options, 'title', 'autocomplete="off" class="inputbox" id="filename" ', 'value', 'text', 0);



?>

<script>
     jQuery(document).ready(function() {

           jQuery('input[type=file].upload').change(function () {
               var tileNum = jQuery(this).attr("id");
                var number = tileNum.replace("uploadBtn","");
                jQuery('#uploadFile'+number).val(jQuery('input[type=file]#'+tileNum).val());
            });

           
            
      });
</script>

<style>
	.tooltip{
		display:none !important;
	}
        .fileUpload {
	position: relative;
	overflow: hidden;
	
}
.fileUpload input.upload {
	position: absolute;
	top: 0;
	right: 0;
	margin: 0;
	padding: 0;
	font-size: 20px;
	cursor: pointer;
	opacity: 0;
	filter: alpha(opacity=0);
}
</style>
<div class="ip-companylist<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading')) : ?>
        <div class="page-header">
            <h1>
                <?php echo $this->escape($this->params->get('page_heading')); ?>
            </h1>
        </div>
    <?php endif; ?>
    <?php if ($this->params->get('show_ip_title') && $this->iptitle) : ?>
        <div class="ip-mainheader">
            <h2>
                <?php echo $this->escape($this->iptitle); ?>
            </h2>
        </div>        
    <?php endif; ?>
    <div class="clearfix"></div>
    <form action="<?php echo JRoute::_('index.php?option=com_iproperty&layout=default&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">  
    <?php 
   
   
    echo '<div class="row tr_title" style="padding-top: 10px;">';
            echo '<div class="col-md-1">No.</div>';
            echo '<div class="col-md-6">Form Type/Name</div>';
            echo '<div class="col-md-3"></div>';
            echo '<div class="col-md-2"></div>';
        echo '</div>';
    // display results for file
    
        /*echo 
            '<h2 class="company_header">'.JText::_('COM_IPROPERTY_COMPANIES').'</h2><span class="pull-right small ip-pagination-results">'.$this->pagination->getResultsCounter().'</span>';*/
        
         
            for($i = 1; $i <= 10; $i++){
                
                $dropdown = '<select name="title'.$i.'" id="title'.$i.'">';
                $dropdown .= '<option value="0">Select Form</option>';
                for($j=0;$j<count($rows_file);$j++){
                        $dropdown .= '<option id="option'.$i.$j.'" value="'.$rows_file[$j]->id.'">'.$rows_file[$j]->title.'</option>';
                }

                $dropdown .='</select>';
                
                
               echo '<div class="row tr_template">';
                    echo '<div class="col-md-1">'.$i.'</div>';
                    echo '<div class="col-md-6">'.$dropdown.'</div>';
                    echo '<div class="col-md-3"><input name="jform[fname'.$i.']" id="uploadFile'.$i.'" value="" /></div>';
                    echo '<div class="col-md-2"><div class="fileUpload btn btn-primary">
                    <span>Select File...</span><input id="uploadBtn'.$i.'" type="file" class="upload" name="fileagent[]" /></div></div>';
               echo '</div>';
            }
        /*echo
            '<div class="pagination">
                '.$this->pagination->rowgetPagesLinks().'<br />'.$this->pagination->getPagesCounter().'
             </div>';*/
    
    
    
    
    ?>
        <div style="float: left; width: 95%;margin-bottom: 4%;  margin-top: 4%;  text-align: right;">
            <input type="submit" name="btnSubmit" value="Save" id="btnSubmitForm"> 
            
        </div>
        
        <input type="hidden" name="task" value="fileagentsubmit.savefie" />
        <input type="hidden" name="option" value="com_iproperty" />
	<?php echo JHtml::_('form.token'); ?>
</form>
</div>