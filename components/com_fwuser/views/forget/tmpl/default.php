<?php defined('_JEXEC') or die;
JHTML::_('behavior.modal');
$Itemid=JRequest::getVar('Itemid',0);
$document = & JFactory::getDocument();
$_title_tag=JText::_('FORGET_TITLE');
$document->setTitle($_title_tag);
$user=&JFactory::getUser();
$user_id=$user->get('id');
$mainframe = JFactory::getApplication();
if($user_id!=''&&$user_id!=0){
	$mainframe->redirect(JURI::root().'index.php?option=com_fwuser&view=info&Itemid='.$Itemid);
}
echo '<link rel="stylesheet" href="'.JURI::root().'components/com_easypost/css/css.css" type="text/css" />';
echo '<script type="text/javascript" src="'.JURI::root().'components/com_easypost/js/js.js"></script>';
?>
<script>
function checkForget(){
	var re=/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;
	var v = new RegExp();
	v.compile("^[A-Za-z]+://[A-Za-z0-9-_]+\\.[A-Za-z0-9-_%&\?\/.=]+$");

	if($('jform_email').value==''){
		alert("<?php echo JText::_('ASK_EMAIL'); ?>");
		$('jform_email').focus();
	}else if(re.test($('jform_email').value)==false){
		alert("<?php echo JText::_('ASK_EMAIL_WRONG'); ?>")
		$('jform_email').focus();
	}else{
		var req = new Request({
			method: 'post',
			url: "index.php?option=com_easypost&view=forget&format=raw",
			data: { 
				'id' : '<?php echo $row->id;?>',
				'email' : $('jform_email').value
			},
			onRequest: function() {
				$('form_field').style.display="none";
				$('form_field_wait').set('text', '<?php echo JText::_('PLEASE_WAIT'); ?>');
				$('form_field_wait').style.display="inline";
			},
			onComplete: function(response) { 
				if(response=='2'){
					$('form_field_wait').set('text', '<?php echo JText::_('ASK_EMAIL_NOT_FOUND'); ?>');
					$('form_field').style.display="block";
					$('form_field_wait').style.display="block";
				}else{
					$('form_field_wait').set('text', '<?php echo JText::_('ASK_NEW_PASSWORD_COME'); ?>');
					$('form_field').style.display="none";
					$('form_field_wait').style.display="block";
				}
			}
		}).send();
	}

}
</script>
<div class="easypost_wrap">
	<h2 class="item_title"><?php echo JText::_('FORGET_TITLE'); ?></h2>
	<div class="item_body">
		<div id="form_field_wait" class="form_field_wait" style="display:none;">
			<?php echo JText::_('PLEASE_WAIT'); ?>
		</div>
		<div id="form_field" class="form_field">
			<div class="field">
				<label class="label"><?php echo JText::_('FI_EMAIL'); ?><span>*</span></label>
				<div class="inputbox"><input type="email" size="50" class="validate-email required" value="" id="jform_email" name="jform[email]"></div>
			</div>
			<div class="field">
				<label class="label">&nbsp;</label>
				<input type="button" class="button" value="<?php echo JText::_('FI_SUBMIT'); ?>" onclick="checkForget();">
			</div>
		</div>
	</div>
</div>