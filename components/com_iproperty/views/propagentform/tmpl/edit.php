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
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', '.ipform select');

//if no login
$user = JFactory::getUser();
if(empty($user->id)){
	header('Location: http://poiserealestate.com/');
exit;
}

// Create shortcut to parameters.
$params = $this->state->get('params');

// Change header element background to match template per IP settings
$headers_array = array('description', 'geocode', 'general_amen', 'exterior_amen', 'interior_amen');
foreach($headers_array as $h){
    $this->form->setFieldAttribute($h.'_header', 'color', $this->settings->accent);
    $this->form->setFieldAttribute($h.'_header', 'tcolor', $this->settings->secondary_accent);
}

// change measurement units label depending on settings
$this->form->setFieldAttribute('sqft', 'label', (!$this->settings->measurement_units) ? JText::_('COM_IPROPERTY_SQFT') : JText::_('COM_IPROPERTY_SQM'));

$app        = JFactory::getApplication();
$document   = JFactory::getDocument();
$curr_lang  = JFactory::getLanguage();
$languages  = JLanguageHelper::getLanguages('lang_code'); 
$languageCode = $languages[ $curr_lang->getTag() ]->sef;
      
// check if maps are enabled, and use google for geocoding
if($this->settings->map_provider)
{
    // set defaults from item properties or default settings
    $lat        = ($this->item->latitude) ? $this->item->latitude : $this->settings->adv_default_lat;
    $lon        = ($this->item->longitude) ? $this->item->longitude : $this->settings->adv_default_long;
    $start_zoom = ($this->item->latitude) ? '13' : $this->settings->adv_default_zoom;
    $kml        = ($this->item->kml) ? JURI::root(true).'/media/com_iproperty/kml/'.$this->item->kml : false;

    $map_script = "    
      var map_options = {
            startLat: '".$lat."',
            startLon: '".$lon."',
            startZoom: ".(int)$start_zoom.",
            mapDiv: 'ip-map-canvas',
            clickResize: '#proplocation',
            credentials: '".$this->settings->map_credentials."',
            kml: '".$kml."'
       };
       
       jQuery(document).ready(function($){
			$('#clear_geopoint').click(function(){
				$('#jform_latitude').val('');
				$('#jform_longitude').val('');
			});
		});";
    $document->addScriptDeclaration($map_script);
    
    // add map scripts
    switch ($this->settings->map_provider){
        case 1: // GOOGLE
            $document->addScript( "//maps.google.com/maps/api/js?sensor=false" ); 
            $document->addScript( JURI::root(true).'/components/com_iproperty/assets/js/fields/gmapField.js' );
        break;
        case 2: // BING
            $document->addScript( "//ecn.dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=7.0&mkt=".$curr_lang->get('tag') ); 
            $document->addScript( JURI::root(true).'/components/com_iproperty/assets/js/fields/bingmapField.js' );
        break;
    }
}
  
  //get agent name
  $user = JFactory::getUser();
  $db = JFactory::getDBO();
  
  if($user->id){
	$query = "SELECT * FROM #__iproperty_agents WHERE user_id=".$user->id;
	$db->setQuery($query);
	$agent = $db->loadObjectList();
  }
  
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
<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'propformagent.cancel'){
            <?php echo $this->form->getField('description')->save(); ?>
			Joomla.submitform(task);
        }else if(document.formvalidator.isValid(document.id('adminForm'))) {
            <?php if($this->ipauth->getAdmin()): //only confirm company if admin user ?>
                if(document.id('jform_listing_office').selectedIndex == ''){
                    alert('<?php echo $this->escape(JText::_('COM_IPROPERTY_SELECT_COMPANIES')); ?>');
                    return false;
                }
            <?php endif; ?>
            <?php if($this->ipauth->getAdmin() || $this->ipauth->getSuper()): //only confirm agnets if admin or super agent ?>
                if(document.id('jform_agents').selectedIndex == ''){
                    alert('<?php echo $this->escape(JText::_('COM_IPROPERTY_SELECT_AGENT')); ?>');
                    return false;
                }
            <?php endif; ?>
            if(document.id('jform_condition').selectedIndex == ''){
                alert('<?php echo $this->escape(JText::_('COM_IPROPERTY_SELECT_CONDITION')); ?>');
                return false;
            }else if(document.id('jform_tenure').selectedIndex == ''){
                alert('<?php echo $this->escape(JText::_('COM_IPROPERTY_SELECT_TENURE')); ?>');
                return false;
            }
			<?php echo $this->form->getField('description')->save(); ?>
			Joomla.submitform(task);
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
    
	// coordinate validation routine
	window.addEvent('domready', function(){
		// coordinate validation
		document.formvalidator.setHandler('coord', function(value) {
			if (isNaN(value) == false) return true;
			return false;
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

<div class="edit item-page<?php echo $this->pageclass_sfx; ?>">
	<h2 class="ip-property-header"><?php echo JText::_('ADD_PROPERTY')?></h2>
    <form action="<?php echo JRoute::_('index.php?option=com_iproperty&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate ipform form-horizontal" enctype="multipart/form-data">
        
        <?php 
        echo JHtmlBootstrap::startTabSet('ip-propview', array('active' => 'propgeneral'));
       // echo JHtmlBootstrap::addTab('ip-propview', 'propgeneral', JText::_('COM_IPROPERTY_DESCRIPTION')); ?>
            
        <div id="propertyedit" class="row">
            <div class="col-md-6 col-sm-6 col-xs-6">
                <!--property name-->
                 <div class="row">
                    <div class="col-md-4">
                        <span style="font-weight: bold;">Property Name</span>
                    </div>
                    <div class="col-md-8">
                        <?php echo $this->form->getInput('property_name'); ?>
                    </div>
                </div>
                <!--property type-->
                <div class="row">
                    <div class="col-md-4">
                        <?php echo $this->form->getLabel('property_type'); ?>
                    </div>
                    <div class="col-md-8">
                        <?php echo $this->form->getInput('property_type'); ?>
                    </div>
                </div>
                <!--price-->
                 <div class="row">
                    <div class="col-md-4">
                        <?php echo $this->form->getLabel('price'); ?>
                    </div>
                    <div class="col-md-8">
                        <?php echo $this->form->getInput('price'); ?>
                    </div>
                </div>
                <!--price(psf)-->
                 <div class="row">
                    <div class="col-md-4">
                        <?php echo $this->form->getLabel('price2'); ?>
                    </div>
                    <div class="col-md-8">
                        <?php echo $this->form->getInput('price2'); ?>
                    </div>
                </div>
                <!--Floor Area/ Land Area-->
                <div class="row">
                    <div class="col-md-4">
                        <?php echo $this->form->getLabel('street2'); ?>
                    </div>
                    <div class="col-md-8">
                        <?php echo $this->form->getInput('street2'); ?>
                    </div>
                </div>
                <!--Condition-->
                <div class="row">
                    <div class="col-md-4">
                        <span style="font-weight: bold;">Condition</span>
                    </div>
                    <div class="col-md-8">
                        <?php echo $this->form->getInput('condition'); ?>
                    </div>
                </div>
                <!--Developers-->
                 <div class="row">
                    <div class="col-md-4">
                        <?php echo $this->form->getLabel('developer'); ?>
                    </div>
                    <div class="col-md-8">
                        <?php echo $this->form->getInput('developer'); ?>
                    </div>
                </div>
                <!--Tenure-->
                 <div class="row">
                    <div class="col-md-4">
                        <span style="font-weight: bold;">Tenure</span>
                    </div>
                    <div class="col-md-8">
                        <?php echo $this->form->getInput('tenure'); ?>
                    </div>
                </div>
                <!--# of Bedroom-->
                <div class="row">
                    <div class="col-md-4">
                        <?php echo $this->form->getLabel('beds'); ?>
                    </div>
                    <div class="col-md-8">
                        <?php echo $this->form->getInput('beds'); ?>
                    </div>
                </div>
                <!--# of Baths-->
                <div class="row">
                    <div class="col-md-4">
                        <?php echo $this->form->getLabel('baths'); ?>
                    </div>
                    <div class="col-md-8">
                        <?php echo $this->form->getInput('baths'); ?>
                    </div>
                </div>
                <!--# Upload Image-->
                <div class="row">
                    <div class="col-md-8">
                        <input id="uploadFile1" placeholder="Choose File" disabled="disabled" />
                    </div>
                    <div class="col-md-4">
                        <div class="fileUpload btn btn-primary">
                            <span>Browse...</span>
                            <input id="uploadBtn1" type="file" class="upload" name="propertyfile[]" />
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <input id="uploadFile2" placeholder="Choose File" disabled="disabled" />
                    </div>
                    <div class="col-md-4">
                        <div class="fileUpload btn btn-primary">
                            <span>Browse...</span>
                            <input id="uploadBtn2" type="file" class="upload" name="propertyfile[]" />
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <input id="uploadFile3" placeholder="Choose File" disabled="disabled" />
                    </div>
                    <div class="col-md-4">
                        <div class="fileUpload btn btn-primary">
                            <span>Browse...</span>
                            <input id="uploadBtn3" type="file" class="upload" name="propertyfile[]" />
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <input id="uploadFile4" placeholder="Choose File" disabled="disabled" />
                    </div>
                    <div class="col-md-4">
                        <div class="fileUpload btn btn-primary">
                            <span>Browse...</span>
                            <input id="uploadBtn4" type="file" class="upload" name="propertyfile[]" />
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="col-md-6 col-sm-6 col-xs-6">
                <!--Address-->
                <div class="row">
                    <div class="col-md-4">
                        <?php echo $this->form->getLabel('street'); ?>
                    </div>
                   <div class="col-md-8">
                        <?php echo $this->form->getInput('street'); ?>
                    </div>
                </div>
                <!--Country-->
                 <div class="row">
                    <div class="col-md-4">
                        <?php echo $this->form->getLabel('country'); ?>
                    </div>
                    <div class="col-md-8">
                        <?php echo $this->form->getInput('country'); ?>
                    </div>
                </div>
                <!--City-->
                 <div class="row">
                    <div class="col-md-4">
                        <?php echo $this->form->getLabel('city'); ?>
                    </div>
                    <div class="col-md-8">
                        <?php echo $this->form->getInput('city'); ?>
                    </div>
                </div>
                <!--Postal-->
                <div class="row">
                    <div class="col-md-4">
                        <?php echo $this->form->getLabel('postcode'); ?>
                    </div>
                    <div class="col-md-8">
                        <?php echo $this->form->getInput('postcode'); ?>
                    </div>
                </div>
                <!--Disctrict-->
                <div class="row">
                    <div class="col-md-4">
                        <?php echo $this->form->getLabel('region'); ?>
                    </div>
                    <div class="col-md-8">
                        <?php echo $this->form->getInput('region'); ?>
                    </div>
                </div>
                <!--Listed Date-->
                <div class="row">
                   <div class="col-md-4">
                        <?php echo $this->form->getLabel('available'); ?>
                    </div>
                    <div class="col-md-8">
                        <?php echo $this->form->getInput('available'); ?>
                    </div>
                </div>
                <!--Status-->
                <div class="row">
                    <div class="col-md-4">
                        <?php echo $this->form->getLabel('status'); ?>
                    </div>
                    <div class="col-md-8">
                        <?php echo $this->form->getInput('status'); ?>
                    </div>
                </div>
                <!--Description-->
               <div class="row">
                    <div class="col-md-4">
                        <?php echo $this->form->getLabel('short_description'); ?>
                    </div>
                    <div class="col-md-8">
                        <?php echo $this->form->getInput('short_description'); ?>
                    </div>
                </div>
                <!--Agent Contact-->
                <div class="row">
                   <div class="col-md-4">
                        <?php echo $this->form->getLabel('agent_contact'); ?>
                    </div>
                    <div class="col-md-8">
                        <?php echo $this->form->getInput('agent_contact',null,$agent[0]->fname); ?>
                    </div>
                </div>
                <div class="btn-toolbar">
                    <div class="btn-group" style="float: right;margin-right: 22%;  margin-top: 4%;">
                       
                        <button style="background: #032D5F;color: #fff;margin-right: 5px;margin-top: -2px;" type="button" class="" onclick="Joomla.submitbutton('propagentform.save')">
                            <i class="icon-ok"></i> <?php echo JText::_('JSAVE') ?>
                        </button>
                        <?php if ($this->item->id && $this->ipauth->canAddProp()): ?>
                            <button type="button" class="" onclick="Joomla.submitbutton('propagentform.save2copy')">
                                <i class="icon-copy"></i> <?php echo JText::_('COM_IPROPERTY_CLONE') ?>
                            </button>
                        <?php endif; ?>
                       
                        <span style="background-color: #00234B;padding: 3px 10px;"><a style="color: #fff;" href="<?php echo JRoute::_("index.php?option=com_iproperty&view=agentproperty&Itemid=222"); ?>"><?php echo JText::_('JCANCEL')?></a></span>
                    </div>
                </div>
            </div>
        </div>
        <?php
        //echo JHtmlBootstrap::endTab();
       
        ?>
           
        <?php
        
        //echo JHtmlBootstrap::addTab('ip-propview', 'propimages', JText::_('COM_IPROPERTY_IMAGES').' / '.JText::_('COM_IPROPERTY_VIDEO'));
        ?>
           
        <?php
       // echo JHtmlBootstrap::endTab();
       
        ?>
            
            
            
        <?php
       
        //$this->dispatcher->trigger('onAfterRenderPropertyEdit', array($this->item, $this->settings ));
        echo JHtmlBootstrap::endTabSet();
        ?>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="return" value="<?php echo $this->return_page; ?>" />
        <?php echo JHtml::_( 'form.token'); ?>
    </form>
</div>

<?php if($this->item->id): 
    
    jimport('joomla.filesystem.file');
    
    // plupload scripts
	$document->addStyleSheet( "//ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" );
    $document->addStyleSheet( JURI::root(true)."/components/com_iproperty/assets/js/plupload/js/jquery.plupload.queue/css/jquery.plupload.queue.css" );
	$document->addScript( "//ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js" );
    $document->addScript( JURI::root(true)."/components/com_iproperty/assets/js/plupload/js/plupload.full.min.js" );
    $document->addScript( JURI::root(true)."/components/com_iproperty/assets/js/plupload/js/jquery.plupload.queue/jquery.plupload.queue.js" );
    // include language file for uploader if it exists
    if(JFile::exists(JPATH_SITE.'/components/com_iproperty/assets/js/plupload/js/i18n/'.$languageCode.'.js')){
        $document->addScript( JURI::root(true)."/components/com_iproperty/assets/js/plupload/js/i18n/".$languageCode.".js" );
    }

    // sortable tables
    $document->addScript( JURI::root(true).'/components/com_iproperty/assets/js/ipsortables.js');
    $document->addScript( JURI::root(true).'/components/com_iproperty/assets/js/ipsortables_docs.js');
	
	// ****************************
	// not sure if this should stay -- hosted version of the FULL bootstrap so icons etc. work
	//$document->addScript( JURI::root(true).'/components/com_iproperty/assets/js/image.resize.js');
	$document->addScript( JURI::root(true).'/components/com_iproperty/assets/js/bootbox.min.js');
    
    ?>
    <script type="text/javascript">                                   
        var ipbaseurl		= '<?php echo rtrim(JURI::root(), '/'); ?>';
        var pluploadpath	= '<?php echo JURI::root().'/components/com_iproperty/assets/js'; ?>';
        var ipmaximagesize  = '<?php echo $this->settings->maximgsize; ?>';
        var ipfilemaxupload = 0;
		var ipGalleryOptions = false;

        (function($) {  
			ipGalleryOptions = {
                propid: <?php echo (int)$this->item->id; ?>,
                iptoken: '<?php echo JSession::getFormToken(); ?>',
                ipbaseurl: '<?php echo JURI::root(); ?>',
				ipthumbwidth: '<?php echo $this->settings->thumbwidth; ?>',
                //ipthumbwidth: 150,
                iplimitstart: 0,
                iplimit: 50,
                debug: false,
                language: {
                    save: '<?php echo addslashes(JText::_('COM_IPROPERTY_SAVE')); ?>',
                    del: '<?php echo addslashes(JText::_('COM_IPROPERTY_DELETE')); ?>',
					edit: '<?php echo addslashes(JText::_('COM_IPROPERTY_EDIT')); ?>',
					add: '<?php echo addslashes(JText::_('COM_IPROPERTY_ADD')); ?>',
					confirm: '<?php echo addslashes(JText::_('COM_IPROPERTY_CONFIRM')); ?>',
					ok: '<?php echo addslashes(JText::_('JYES')); ?>',
					cancel: '<?php echo addslashes(JText::_('JCANCEL')); ?>',
                    iptitletext: '<?php echo addslashes(JText::_('COM_IPROPERTY_TITLE')); ?>',
                    ipdesctext: '<?php echo addslashes(JText::_('COM_IPROPERTY_DESCRIPTION')); ?>',
                    noresults: '<?php echo addslashes(JText::_('COM_IPROPERTY_NO_RESULTS')); ?>',
                    updated: '<?php echo addslashes(JText::_('COM_IPROPERTY_UPDATED')); ?>',  
                    notupdated: '<?php echo addslashes(JText::_('COM_IPROPERTY_NOT_UPDATED')); ?>',
                    previous: '<?php echo addslashes(JText::_('COM_IPROPERTY_PREVIOUS')); ?>',
                    next: '<?php echo addslashes(JText::_('COM_IPROPERTY_NEXT')); ?>',
                    of: '<?php echo addslashes(JText::_('COM_IPROPERTY_OF')); ?>',
                    fname: '<?php echo addslashes(JText::_('COM_IPROPERTY_FNAME')); ?>',
                    overlimit: '<?php echo addslashes(JText::_('COM_IPROPERTY_OVERIMGLIMIT')); ?>',
					warning: '<?php echo addslashes(JText::_('COM_IPROPERTY_WARNING')); ?>',
					uploadcomplete: '<?php echo addslashes(JText::_('COM_IPROPERTY_UPLOAD_COMPLETE')); ?>'
                },
                client: '<?php echo $app->getName(); ?>',
                allowedFileTypes: [{title : "Files", extensions : "jpg,gif,png,pdf,doc,txt,jpeg"}]
            };
            
            // create auto complete             
            $.each(['city','province','region','county'], function(index, value){
                var url = '<?php echo JURI::base('true'); ?>/index.php?option=com_iproperty&task=ajax.ajaxAutocomplete&format=raw&field='+value+'&<?php echo JSession::getFormToken(); ?>=1';
                $.getJSON(url).done(function( data ){
                    $('#jform_'+value).typeahead({source: data, items:5});
                });  
            });
        })(jQuery);
    </script>
<?php endif; ?>