<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined('JPATH_BASE') or die;

class JFormFieldIconBasic extends JFormField
{
	protected $type = 'Iconbasic';

	protected function getInput()
    {
        $user       = JFactory::getUser();
        $document   = JFactory::getDocument();
        $folder     = $this->element['folder']; // agents or companies
        $id         = JRequest::getInt('id');
        $settings   = ipropertyAdmin::config();
        $database   = JFactory::getDBO();
               
        switch ($folder){
            case 'agents':
                $width = $settings->agent_photo_width;
                $table = '#__iproperty_agents';
            break;
            case 'companies':
                $width = $settings->company_photo_width;
                $table = '#__iproperty_companies';
            break;
            default:
                $width = $settings->agent_photo_width;
                $table = '#__iproperty_agents';
            break;
        }
        
        $sql = "SELECT icon FROM ".$table." WHERE id = ".(int)$id;
        $database->setQuery($sql);
        $icon = $database->loadResult();
        
        if (!$icon) $icon = 'nopic.png';
        $document->addScript( JURI::root()."components/com_iproperty/assets/js/plupload/js/plupload.full.min.js" );
        $script = 
        "jQuery.noConflict();
        jQuery(document).ready(function($) {
			var ipimgbase = '".JURI::root()."/media/com_iproperty/".$folder."/';
            var uploader = new plupload.Uploader({
                runtimes : 'html5,flash,silverlight,html4',
                browse_button : 'pickfiles',
                container : 'pluploadcontainer',
                unique_names : true,
                url : '".JURI::root()."index.php?option=com_iproperty&task=ajax.ajaxIconUpload&format=raw&".JSession::getFormToken()."=1&target=".$folder."&id=".$id."',
               // Flash settings
				flash_swf_url : pluploadpath+'/plupload/js/Moxie.swf',
				// Silverlight settings
				silverlight_xap_url : pluploadpath+'/plupload/js/Moxie.xap',
				filters: {
					max_file_size : '2mb',
					mime_types: [
						{title : 'Image files', extensions : 'jpg,gif,png'}
					]
				},
                resize : {width : ".$width.", height : ".$width."} // we use the same value for width/height so it scales proportionally
            });

            $('#uploadfiles').click(function(e) {
                uploader.start();
                e.preventDefault();
            });

            uploader.init();

            uploader.bind('FilesAdded', function(up, files) {
                $.each(files, function(i, file) {
                    $('#filelist').attr('placeholder',file.name);
                });
                uploader.start(); // auto start when file added
                up.refresh(); // Reposition Flash/Silverlight
            });

            uploader.bind('Error', function(up, err) {
            console.log(err);
                $('#filelist').append(\"<div>Error: \" + err.code +
                    \", Message: \" + err.message +
                    (err.file ? \", File: \" + err.file.name : \"\") +
                    \"</div>\"
                );
                up.refresh(); // Reposition Flash/Silverlight
            });

            uploader.bind('FileUploaded', function(up, file, res) {
                var response = $.parseJSON(res.response);				
				if(response[0].status == 'ok'){ 		
					$('#ip_photo_holder').attr('src', ipimgbase+response[0].data);
                    $('#filelist').attr('placeholder',response[0].data);
				} else {
					if(response[0].status == 'error'){
						var errortext = '<div class=\"alert alert-error fade in\">';
						errortext +=	'<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>';
						errortext += 	'<strong>WARNING:</strong> '+response[0].message;
						errortext += 	'</div>';
						$('#ipUploader_container').append(errortext);
					} else {
						console.log(response[0].message);
					}
				}
            });
            
            // build reset function for icon
            $('#resetIcon').click(function(e){
                e.preventDefault();
                $.ajax({
                    url : '".JURI::root()."index.php?option=com_iproperty&task=ajax.ajaxIconReset&format=raw&".JSession::getFormToken()."=1&target=".$folder."&id=".$id."',
                    type: 'POST',
                    cache: false,
                    error: function(request, status, error_message){
                        console.log(status+' - '+error_message);
                    },
                    success: function(data) {
                        var response = $.parseJSON(data);
                        if (response.status == 'ok'){
                            $('#ip_photo_holder').attr('src', ipimgbase+response.data);
                            $('#filelist').attr('placeholder',response.data);
                        }
                    }
                });
            });
        });"."\n";   

        // if no ID, can't upload image
		if ($id){ 
			$document->addScriptDeclaration($script);
			$imgdiv = '
            <div class="row-fluid">
                <div id="pluploadcontainer" class="span6">                
                    <!-- physical input to show user which file has been selected -->
                    <input type="text" class="inputbox" id="filelist" placeholder="'.$icon.'" disabled="disabled" />
                    <div class="clearfix"></div>
                    <hr />
                    <!-- Buttons to upload, reset image -->
                    <a class="btn btn-success" id="pickfiles" href="#">'.JText::_("COM_IPROPERTY_UPLOAD").'</a>
                    <a class="btn btn-danger" id="resetIcon" href="#">'.JText::_('COM_IPROPERTY_RESET').'</a>                
                </div>
                <div class="span6">
                    <img class="img-polaroid" id="ip_photo_holder" src="'.JURI::root().'/media/com_iproperty/'.$folder.'/'.$icon.'" width="100" alt="" />
                </div>
            </div>
            <div class="clearfix"></div>';
		} else {
			$imgdiv = '<div class="alert alert-info">'.JText::_('COM_IPROPERTY_SAVE_FIRST').'</div>';
		}
        
		echo $imgdiv;
	}
}