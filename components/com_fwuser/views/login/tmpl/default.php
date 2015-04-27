<?php defined('_JEXEC') or die;
JHTML::_('behavior.modal');
$Itemid=JRequest::getVar('Itemid',0);
$user=&JFactory::getUser();
$user_id=$user->get('id');
$document = & JFactory::getDocument();
$_title_tag=JText::_('LOGIN_TITLE');
$document->setTitle($_title_tag);
$session = JFactory::getSession();
if($session->get('link_url')==''){
	$link_url=JURI::root();
}else{
	$link_url=$session->get('link_url');
}
echo '<link rel="stylesheet" href="'.JURI::root().'components/com_fwuser/css/css.css" type="text/css" />';
echo '<script type="text/javascript" src="'.JURI::root().'components/com_fwuser/js/js.js"></script>';
?>
<script>
function checkLogout(){
	$('form_field').style.display="none";
	$('form_field_wait').set('text', '<?php echo JText::_('PLEASE_WAIT'); ?>');
	$('form_field_wait').style.display="inline";
	var req = new Request({
		method: 'post',
		url: "index.php?option=com_fwuser&view=login&format=raw",
		data: { 'id' : '<?php echo $row->id;?>' },
		onRequest: function() {},
		onComplete: function(response) { 
			$('form_field_wait').set('text', '<?php echo JText::_('LOGOUT_SUCCESS'); ?>');
			$('form_field').style.display="none";
			$('form_field_wait').style.display="block";
			window.location="<?php echo JURI::root(); ?>";
		}
	}).send();
}
function checkRegister(){
	var re=/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;
	var v = new RegExp();
	v.compile("^[A-Za-z]+://[A-Za-z0-9-_]+\\.[A-Za-z0-9-_%&\?\/.=]+$");

	if($('jform_username').value==''){
		alert("<?php echo JText::_('ASK_USERNAME'); ?>");
		$('jform_username').focus();
	}else if($('jform_password').value==''){
		alert("<?php echo JText::_('ASK_PASSWORD'); ?>");
		$('jform_password').focus();
	}else{
		$('form_field').style.display="none";
		$('form_field_wait').set('text', '<?php echo JText::_('PLEASE_WAIT'); ?>');
		$('form_field_wait').style.display="inline";
		var req = new Request({
			method: 'post',
			url: "index.php?option=com_fwuser&view=login&format=raw",
			data: { 
				'id' : '<?php echo $row->id;?>',
				'username' : $('jform_username').value, 
				'password' : $('jform_password').value 
			},
			onRequest: function() {},
			onComplete: function(response) { 
				if(response=='1'){
					$('form_field_wait').set('text', '<?php echo JText::_('LOGIN_SUCCESS'); ?>');
					$('form_field').style.display="none";
					$('form_field_wait').style.display="block";
					window.location="<?php echo $link_url; ?>";
				}else{
					$('form_field_wait').set('text', '<?php echo JText::_('WRONG_EMAIL_PASSWORD'); ?>');
					$('form_field').style.display="block";
					$('form_field_wait').style.display="block";
				}
			}
		}).send();
	}
}
</script>
<div class="fwuser_wrap item-page">
	<h2 class="item_title"><?php echo JText::_('LOGIN_TITLE'); ?></h2>
	<div class="item_body">
		<p id="form_field_wait" class="form_field_wait" style="display:none;">
			<?php echo JText::_('PLEASE_WAIT'); ?>
		</p>
		<?php if($user_id!=0&&$user_id!=''){ ?>
		<div id="form_field" class="form_field">
			<div class="field">
				<label class="label">&nbsp;</label>
				<input type="button" class="button" value="<?php echo JText::_('FI_LOGOUT'); ?>" onclick="checkLogout();">
			</div>
		</div>
		<?php }else{ ?>
		<div id="form_field" class="form_field">
			<div class="field">
				<label class="label"><?php echo JText::_('FI_USERNAME'); ?><span>*</span></label>
				<div class="inputbox"><input type="username" size="50" class="validate-username required" value="" id="jform_username" name="jform[username]"></div>
			</div>
			<div class="field">
				<label class="label"><?php echo JText::_('FI_PASSWORD'); ?><span>*</span></label>
				<div class="inputbox"><input type="password" size="50" class="" value="" id="jform_password" name="jform[password]"></div>
			</div>
			<div class="field">
				<label class="label">&nbsp;</label>
				<input type="button" class="button" value="<?php echo JText::_('FI_LOGIN'); ?>" onclick="checkRegister();">
			</div>
			<div class="field">
				<label class="label">&nbsp;</label>
				<a class="link" href="<?php echo JURI::root(); ?>index.php?option=com_fwuser&view=forget&Itemid=<?php echo $Itemid; ?>"><?php echo JText::_('FORGET_PASSWORD'); ?></a>
			</div>
			<div class="field">
				<label class="label">&nbsp;</label>
				<a class="link" href="<?php echo JURI::root(); ?>index.php?option=com_fwuser&view=register&Itemid=<?php echo $Itemid; ?>"><?php echo JText::_('REGISTER'); ?></a>
			</div>
		</div>
		<?php } ?>
	</div>
</div>