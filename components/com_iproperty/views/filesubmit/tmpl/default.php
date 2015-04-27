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

JHtml::_('bootstrap.tooltip');
?>

<script>
     jQuery(document).ready(function() {
     
       
        
        jQuery('input[type=file]#uploadBtn1').change(function () {
            
            jQuery('#uploadFile1').val(jQuery('input[type=file]#uploadBtn1').val());
        });
        jQuery('input[type=file]#uploadBtn2').change(function () {
            
            jQuery('#uploadFile2').val(jQuery('input[type=file]#uploadBtn2').val());
        });
        jQuery('input[type=file]#uploadBtn3').change(function () {
            
            jQuery('#uploadFile3').val(jQuery('input[type=file]#uploadBtn3').val());
        });
        jQuery('input[type=file]#uploadBtn4').change(function () {
            
            jQuery('#uploadFile4').val(jQuery('input[type=file]#uploadBtn4').val());
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
<h2>SUBMIT FORM</h2>
<div class="ip-companylist<?php echo $this->pageclass_sfx;?>">
    
    <div class="clearfix"></div>
    
   <div class="row tr_title" style="padding-top: 10px;margin-bottom: 10px;">;
    <div class="col-md-2">.No</div>;
     <div class="col-md-9" style="padding-right: 0px;">Form Type/ Name</div>;
   </div>
    <!--# Upload Image-->
                <div class="row" style="margin-top: 10px;">
                    <div class="col-md-8">
                        <input id="uploadFile1" placeholder="Choose File" disabled="disabled" />
                    </div>
                    <div class="col-md-4">
                        <div class="fileUpload btn btn-primary">
                            <span>Upload</span>
                            <input id="uploadBtn1" type="file" class="upload" name="propertyfile[]" />
                        </div>
                    </div>
                </div>
                
                 <div class="row" style="margin-top: 10px;">
                    <div class="col-md-8">
                        <input id="uploadFile2" placeholder="Choose File" disabled="disabled" />
                    </div>
                    <div class="col-md-4">
                        <div class="fileUpload btn btn-primary">
                            <span>Upload</span>
                            <input id="uploadBtn2" type="file" class="upload" name="propertyfile[]" />
                        </div>
                    </div>
                </div>
                
                 <div class="row" style="margin-top: 10px;">
                    <div class="col-md-8">
                        <input id="uploadFile3" placeholder="Choose File" disabled="disabled" />
                    </div>
                    <div class="col-md-4">
                        <div class="fileUpload btn btn-primary">
                            <span>Upload</span>
                            <input id="uploadBtn3" type="file" class="upload" name="propertyfile[]" />
                        </div>
                    </div>
                </div>
                
                 <div class="row" style="margin-top: 10px;">
                    <div class="col-md-8">
                        <input id="uploadFile4" placeholder="Choose File" disabled="disabled" />
                    </div>
                    <div class="col-md-4">
                        <div class="fileUpload btn btn-primary">
                            <span>Upload</span>
                            <input id="uploadBtn4" type="file" class="upload" name="propertyfile[]" />
                        </div>
                    </div>
                </div>
    
  
</div>
