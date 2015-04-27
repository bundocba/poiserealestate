<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined('JPATH_BASE') or die;

class JFormFieldIcon extends JFormField
{
	protected $type = 'Icon';

	protected function getInput()
    {
        $user       = JFactory::getUser();
        $document   = JFactory::getDocument();
        $folder     = $this->element['folder'];

        // Build image select js and load the view
        $img_path   = JURI::root(true).'/media/com_iproperty/'.$folder.'/';
        $img_upload = $folder.'img';
        $img_select = 'select'.$folder.'img';

        // Add js function to switch icon image
		$js = "
            function ipSwitchIcon(image) {
                $('live_image').value = image;
                $('image_name').value = image;
                $('imagepreview').src = '".$img_path."' + image;
                window.parent.SqueezeBox.close();
            }";
        $document->addScriptDeclaration($js);

		$upload_link = 'index.php?option=com_iproperty&amp;view=iconuploader&amp;layout=uploadicon&amp;task='.$img_upload.'&amp;tmpl=component';
		$select_link = 'index.php?option=com_iproperty&amp;view=iconuploader&amp;task='.$img_select.'&amp;tmpl=component';		
        ?>
        <div class="span6">
            <!-- physical input to show user which file has been selected -->
            <input type="text" class="inputbox" id="image_name" value="<?php echo $this->value; ?>" disabled="disabled" />
            <div class="clearfix"></div>
            <hr />

            <!-- Buttons to upload, select, or reset image -->
            <div class="btn-group pull-left">
                <!-- upload image button -->
                <a class="btn btn-success modal" href="<?php echo $upload_link; ?>" rel="{handler: 'iframe', size: {x: 400, y: 270}}" style="color: #fff;">
                    <?php echo JText::_('COM_IPROPERTY_UPLOAD'); ?>
                </a>
                <!-- select image button -->
                <?php if($user->authorise('core.admin')): ?>
                <a class="btn btn-info modal" href="<?php echo $select_link; ?>" rel="{handler: 'iframe', size: {x: 800, y: 500}}" style="color: #fff;">
                    <?php echo JText::_('COM_IPROPERTY_SELECTIMAGE'); ?>
                </a>
                <?php endif; ?>
                <!-- reset image button -->
                <a class="btn btn-danger" href="javascript:void(0);" onclick="ipSwitchIcon('nopic.png');">
                    <?php echo JText::_('COM_IPROPERTY_RESET'); ?>
                </a>
            </div>

            <!-- hidden field to store the actual value of the image name -->
            <input type="hidden" id="live_image" name="<?php echo $this->name; ?>" value="<?php echo $this->value; ?>" />
        </div>
        <!-- image preview display and script to swap image with live image -->
        <div class="span6">
            <img src="<?php echo JURI::root(true); ?>/media/com_iproperty/nopic.png" id="imagepreview" style="padding: 2px; border: solid 1px #ccc;" width="100" alt="Preview" />
            <script language="javascript" type="text/javascript">
                //<!CDATA[
                if ($('image_name').value != ''){
                    var imname = $('image_name').value;
                }else{
                    var imname = 'nopic.png';
                    $('live_image').value = imname;
                    $('image_name').value = imname;
                }
                jsimg = '<?php echo JURI::root(true); ?>/media/com_iproperty/<?php echo $folder; ?>/' + imname;
                $('imagepreview').src = jsimg;
                //]]>
            </script>
        </div>
        <?php
	}
}