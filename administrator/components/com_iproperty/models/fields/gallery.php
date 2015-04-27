<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined('JPATH_BASE') or die;

class JFormFieldGallery extends JFormField
{
    protected $type = 'Gallery';

	protected function getInput()
	{
        ?>
        <div class="ipgallerycontainer">
            <div class="span12 image-uploader">
                <h4><?php echo JText::_('COM_IPROPERTY_UPLOAD'); ?></h4>
                <hr />
                <div id="ipUploader">
                    <!-- ADD STANDARD UPLOADER HERE AS FALLBACK -->
                    <?php echo JText::_('COM_IPROPERTY_FLASH_DISABLED'); ?>
                </div>
                <div class="clearfix"></div>
                <div class="remote_container control-group">
                    <div class="control-label">
                        <label class="hasTip" title="<?php echo JText::_('COM_IPROPERTY_REMOTE'); ?>::<?php echo JText::_('COM_IPROPERTY_UPLOAD_REMOTE'); ?>"><?php echo JText::_('COM_IPROPERTY_REMOTE'); ?></label>
                    </div>
                    <div class="controls">
                        <input type="text" id="uploadRemote" class="inputbox" maxlength="150" value="" />
                        <div id="uploadRemoteGo" class="btn btn-info"><?php echo JText::_('COM_IPROPERTY_SAVE'); ?></div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div id="uploadProgress" class="span12 center" style="display: none;"><img src="<?php echo JURI::root(true); ?>/components/com_iproperty/assets/images/ajax-loader.gif" /></div>
            <div class="clearfix"></div>
			<div id="ip_message"></div>
			<div class="clearfix"></div>
            <ul class="nav nav-tabs">
                <li class="active"><a href="#ip_sortableimages" data-toggle="tab"><?php echo JText::_('COM_IPROPERTY_IMAGES');?></a></li>
                <li><a href="#ip_sortabledocs" data-toggle="tab"><?php echo JText::_('COM_IPROPERTY_DOCUMENTS');?></a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="ip_sortableimages">
					<div class="well"><button id="addAvailable" class="btn btn-success" type="button" data-toggle="modal" data-target="#ip_avail_modal"><?php echo JText::_('COM_IPROPERTY_SELECT_AVAILABLE'); ?></button></div>
                    <div>
                        <ul id="ip_selected_images" class="thumbnails"></ul>
                    </div>
                </div>
                <div class="tab-pane" id="ip_sortabledocs">
					<div class="well"><button id="addAvailableDocs" class="btn btn-success" type="button" data-toggle="modal" data-target="#ip_availdocs_modal"><?php echo JText::_('COM_IPROPERTY_SELECT_AVAILABLE_DOCS'); ?></button></div>
                    <div>
                        <ul id="ip_selected_docs" class="thumbnails"></ul>
                    </div>
                </div>
            </div>
        </div>
		<!-- IMAGES HIDDEN FORMS -->
		<!-- hidden form for pop up dialog -->
		<div id="ip_image_form" title="<?php echo JText::_('COM_IPROPERTY_MODIFY_IMAGE'); ?>" class="modal hide fade">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3><?php echo JText::_('COM_IPROPERTY_EDIT'); ?></h3>
			</div>
			<div class="modal-body form-horizontal">
				<fieldset>
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo JText::_('COM_IPROPERTY_TITLE'); ?>
                        </div>
                        <div class="controls">
                            <input type="text" name="imgtitle" id="imgtitle" />
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo JText::_('COM_IPROPERTY_DESCRIPTION'); ?>
                        </div>
                        <div class="controls">
                            <textarea name="imgdescription" rows="3" id="imgdescription"></textarea>
                        </div>
                    </div>
                    <input type="hidden" id="imgid" name="imgid" />
				</fieldset>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_IPROPERTY_CLOSE'); ?></button>
				<button id="ip_image_form_save" class="btn btn-primary"><?php echo JText::_('COM_IPROPERTY_SAVE'); ?></button>
			</div>
		</div>
		<!-- hidden form for available images dialog -->
		<div id="ip_avail_modal" class="modal hide fade" style="width: 800px; margin-left: -400px;">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3><?php echo JText::_('COM_IPROPERTY_SELECT_AVAILABLE'); ?></h3>
				<div class="input-append">
					<input id="ip_avail_filter" type="text" class="input-small" placeholder="<?php echo JText::_('COM_IPROPERTY_KEYWORD'); ?>">
					<button class="btn" type="button"><?php echo JText::_('COM_IPROPERTY_GO'); ?></button>
				</div>
				<div id="ip_avail_pager" class="pagination"></div>
			</div>
			<div class="modal-body">
				<div>
					<ul id="ip_available_images" class="thumbnails row-fluid"></ul>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_IPROPERTY_CLOSE'); ?></button>
				<button id="ip_avail_form_save" class="btn btn-primary"><?php echo JText::_('COM_IPROPERTY_SAVE'); ?></button>
			</div>
		</div>
		<!-- DOCS HIDDEN FORMS -->
		<!-- hidden form for pop up dialog -->
		<div id="ip_docs_form" title="<?php echo JText::_('COM_IPROPERTY_MODIFY_DOCUMENT'); ?>" class="modal hide fade">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3><?php echo JText::_('COM_IPROPERTY_EDIT'); ?></h3>
			</div>
			<div class="modal-body form-horizontal">
				<fieldset>
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo JText::_('COM_IPROPERTY_TITLE'); ?>
                        </div>
                        <div class="controls">
                            <input type="text" name="doctitle" id="doctitle" />
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo JText::_('COM_IPROPERTY_DESCRIPTION'); ?>
                        </div>
                        <div class="controls">
                            <textarea name="docdescription" rows="3" id="docdescription"></textarea>
                        </div>
                    </div>
                    <input type="hidden" id="docid" name="docid" />
				</fieldset>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_IPROPERTY_CLOSE'); ?></button>
				<button id="ip_doc_form_save" class="btn btn-primary"><?php echo JText::_('COM_IPROPERTY_SAVE'); ?></button>
			</div>
		</div>
		<!-- hidden form for available docs dialog -->
		<div id="ip_availdocs_modal" class="modal hide fade">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3><?php echo JText::_('COM_IPROPERTY_SELECT_AVAILABLE_DOCS'); ?></h3>
				<div class="input-append">
					<input id="ip_availdocs_filter" type="text" class="input-small" placeholder="<?php echo JText::_('COM_IPROPERTY_KEYWORD'); ?>">
					<button class="btn" type="button"><?php echo JText::_('COM_IPROPERTY_GO'); ?></button>
				</div>
				<div id="ip_availdocs_pager" class="pagination"></div>
			</div>
			<div class="modal-body">
				<div>
					<ul id="ip_available_docs" class="thumbnails row-fluid"></ul>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_IPROPERTY_CLOSE'); ?></button>
				<button id="ip_availdocs_form_save" class="btn btn-primary"><?php echo JText::_('COM_IPROPERTY_SAVE'); ?></button>
			</div>
		</div>
    <?php
	}
}
