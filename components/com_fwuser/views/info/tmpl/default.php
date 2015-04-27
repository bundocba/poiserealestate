<?php defined('_JEXEC') or die;
JHTML::_('behavior.modal');
$Itemid=JRequest::getVar('Itemid',0);
$db = JFactory::getDBO();
$user=&JFactory::getUser();
$user_id=$user->get('id');
$user_email = $user->get('email');
$document = & JFactory::getDocument();
$_title_tag=JText::_('INFORMATION_TITLE');
$document->setTitle($_title_tag);
$mainframe = JFactory::getApplication();
if($user_id==''||$user_id==0){
	$mainframe->redirect(JURI::root().'index.php?option=com_fwuser&view=login&Itemid='.$Itemid);
}
echo '<link rel="stylesheet" href="'.JURI::root().'components/com_fwuser/css/css.css" type="text/css" />';
echo '<script type="text/javascript" src="'.JURI::root().'components/com_fwuser/js/js.js"></script>';
$query="select * from #__fwuser_user where email='".$user_email."' ";


$db->setQuery($query);
$row_user=$db->loadObject();

$options = array();
$options[] = JHTML::_('select.option', '', '');
$options[] = JHTML::_('select.option', 'Mr', 'Mr');
$options[] = JHTML::_('select.option', 'Mrs', 'Mrs');
$options[] = JHTML::_('select.option', 'Ms', 'Mrs');
$options[] = JHTML::_('select.option', 'Company', 'Company');
$dropdown_title = JHTML::_('select.genericlist', $options, 'title', 'class="inputbox" autocomplete="false" ', 'value', 'text', $row_user->title);

$query="select * from #__fwuser_country order by name ";
$db->setQuery($query);
$rows_country=$db->loadObjectList();
$options = array();
$options[] = JHTML::_('select.option', 0, JText::_('FI_COUNTRY'));
for($i=0;$i<count($rows_country);$i++){
	$options[] = JHTML::_('select.option', $rows_country[$i]->id, $rows_country[$i]->name);
}
$dropdown_country = JHTML::_('select.genericlist', $options, 'country', 'class="inputbox" onchange="changeCountry();" autocomplete="false" ', 'value', 'text', $row_user->country);

$country = $row_user->country;
if(empty($country))$country = 0;

$query ="select * from #__fwuser_state";
$query.=" where country_id=".$country." ";
$query.=" order by name ";

$db->setQuery($query);
$rows_state=$db->loadObjectList();
$options = array();
$options[] = JHTML::_('select.option', 0, JText::_('FI_STATE'));
for($i=0;$i<count($rows_state);$i++){
	$options[] = JHTML::_('select.option', $rows_state[$i]->id, $rows_state[$i]->name);
}
$dropdown_state = '<div id="wrap_state">'.JHTML::_('select.genericlist', $options, 'state', 'class="inputbox" id="state"', 'value', 'text', $row_user->state).'</div>';
?>
<script>
function checkRegister(){
	var re=/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;
	var v = new RegExp();
	v.compile("^[A-Za-z]+://[A-Za-z0-9-_]+\\.[A-Za-z0-9-_%&\?\/.=]+$");

	if($('email').value==''){
		alert("<?php echo JText::_('ASK_EMAIL'); ?>");
		$('email').focus();
	}else if(re.test($('email').value)==false){
		alert("<?php echo JText::_('ASK_EMAIL_WRONG'); ?>")
		$('email').focus();
	}else if($('username').value==''){
		alert("<?php echo JText::_('ASK_USERNAME'); ?>");
		$('username').focus();
	}else if($('password').value!=''&&$('password').value!=$('re_password').value){
		alert("<?php echo JText::_('ASK_PASSWORD_WRONG'); ?>");
		$('re_password').focus();
	}else if($('title').value==''){
		alert("<?php echo JText::_('ASK_TITLE'); ?>");
		$('title').focus();
	}else if($('title').value=='Company'&&$('company').value==''){
		alert("<?php echo JText::_('ASK_COMPANY'); ?>");
		$('title').focus();
	}else if($('firstname').value==''){
		alert("<?php echo JText::_('ASK_FIRSTNAME'); ?>");
		$('firstname').focus();
	}else if($('lastname').value==''){
		alert("<?php echo JText::_('ASK_LASTNAME'); ?>");
		$('lastname').focus();
	}else if($('address').value==''){
		alert("<?php echo JText::_('ASK_ADDRESS'); ?>");
		$('address').focus();
	}else if($('city').value==''){
		alert("<?php echo JText::_('ASK_CITY'); ?>");
		$('city').focus();
	}else if($('zip').value==''){
		alert("<?php echo JText::_('ASK_ZIP'); ?>");
		$('zip').focus();
	}else if($('country').value==0){
		alert("<?php echo JText::_('ASK_COUNTRY'); ?>");
		$('country').focus();
	}else if($('state').value==''){
		alert("<?php echo JText::_('ASK_STATE'); ?>");
		$('state').focus();
	}else if($('phone').value==''){
		alert("<?php echo JText::_('ASK_PHONE'); ?>");
		$('phone').focus();
	}else{
		$('userinfoform').submit();
	}
}
function changeCountry(){
	var req = new Request({
		method: 'post',
		url: "index.php?option=com_fwuser&view=state&format=raw",
		data: { 
			'id' : $('country').value
		},
		onRequest: function() {
			$('wrap_state').set('text', '<?php echo JText::_('PLEASE_WAIT'); ?>...');
		},
		onComplete: function(response) { 
			$('wrap_state').innerHTML=response;
		}
	}).send();
}
</script>

<div class="fwuser_wrap item-page">
	
	<div class="item_body">
		<p id="form_field_wait" class="form_field_wait" style="display:none;">
			<?php echo JText::_('PLEASE_WAIT'); ?>
		</p>
		<div id="form_field" class="form_field">
<form id="member-registration" action="<?php echo JRoute::_('index.php?option=com_fwuser&task=registration.register'); ?>" method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
<div class="fwuser_wrap item-page">
	<h2 class="item_title"><?php echo JText::_('MEMBER DETAILS'); ?></h2>
	<div style="margin-bottom: 2%;"><p>To update your details, simply edit the form below.</p></div>
	<p id="form_field_wait" class="form_field_wait" style="display:none;">
			<?php echo JText::_('PLEASE_WAIT'); ?>
		</p>
	<div class="row clearfix" id="fwzregister">
		<div class="col-sm-6">	
			<div class="row field">
				<div class="col-sm-5"><label class="label"><?php echo JText::_('Name in Full'); ?>(*)</label></div>
				<div class="col-sm-7"><input type="text" size="14" class="cssinput" value="<?php echo $row_user->name; ?>" id="name" name="name">	</div>
			</div>
			<div class="row field">
				<div class="col-sm-5"><label class="label"><?php echo JText::_('Company name'); ?></label></div>
				<div class="col-sm-7"><input type="text" size="14" class="cssinput" value="<?php echo $row_user->company; ?>" id="companyname" name="companyname">	</div>
			</div>
			<div class="row field">
				<div class="col-sm-5"><label class="label"><?php echo JText::_('Mobile Number'); ?></label></div>
				<div class="col-sm-7"><input type="text" size="14" class="cssinput" value="<?php echo $row_user->mobilephone; ?>" id="mobile" name="mobile">	</div>
			</div>
			<div class="row field">
				<div class="col-sm-5"><label class="label"><?php echo JText::_('Office Number'); ?></label>	</div>
				<div class="col-sm-7"><input type="text" size="14" class="cssinput" value="<?php echo $row_user->mobilephone; ?>" id="officenumber" name="officenumber">	</div>
			</div>
			<div class="row field">
				<div class="col-sm-5"><label class="label"><?php echo JText::_('Password'); ?>(*)</label>	</div>
				<div class="col-sm-7"><input type="password" size="14" class="cssinput" value="" id="password" name="password">	</div>
			</div>
			<div class="row field">
				<div class="col-sm-5"><label class="label"><?php echo JText::_('Confirm password'); ?>(*)</label>	</div>
				<div class="col-sm-7"><input type="password" size="14" class="cssinput" value="" id="confirmpassword" name="confirmpassword">	</div>
			</div>
			
		</div>
		<div class="col-sm-6">
			<div class="row field">
				<div class="col-sm-4"><label class="label"><?php echo JText::_('Email'); ?>(*)</label>	</div>
				<div class="col-sm-8"><input type="text" size="14" class="cssinput" value="<?php echo $row_user->email; ?>" id="email" name="email" readonly="true">	</div>
			</div>		
			<div class="row field">
				<div class="col-sm-4"><label class="label"><?php echo JText::_('Address'); ?></label> 	</div>
				<div class="col-sm-8"><input type="text" size="14" class="cssinput" value="<?php echo $row_user->address; ?>" id="address" name="address">	</div>
			</div>
			<div class="row field">
				<div class="col-sm-4"><label class="label"><?php echo JText::_('Country'); ?></label>	</div>
				<div class="col-sm-8"><?php echo $dropdown_country; ?></div>
			</div>
			<div class="row field">
				<div class="col-sm-4"><label class="label"><?php echo JText::_('City'); ?></label>	</div>
				<div class="col-sm-8"><input type="text" size="14" class="cssinput" value="<?php echo $row_user->city; ?>" id="city" name="city">	</div>
			</div>
			<div class="row field">
				<div class="col-sm-4"><label class="label"><?php echo JText::_('Postal'); ?></label>	</div>
				<div class="col-sm-8"><input type="text" size="14" class="cssinput" value="<?php echo $row_user->postal; ?>" id="postal" name="postal">	</div>
			</div>
		</div>
		
	</div>
	<div class="row" id="fwzregister">
		<div><p>I would like to receive news and announcements via*:</p></div>
		<div style="width: 80%;float: left;margin-bottom: 4%;margin-top: 1%;">
			<div style="width: 30%;float: left;">
				<label class="label"><?php echo JText::_('Newletter'); ?><span>*</span></label>
				<input type="checkbox" name="chk_receivetype[]" size="14" class="" value="1" <?php echo (preg_match("/1/",$row_user->receivetype)  ? 'checked' : '');?> id="newletter">
			</div>
			<div style="width: 30%;float: left;">
				<label class="label"><?php echo JText::_('SMS'); ?><span>*</span></label>
				<input type="checkbox" name="chk_receivetype[]" size="14" class="" <?php echo (preg_match("/2/",$row_user->receivetype)  ? 'checked' : '');?> value="2" id="sms">
			</div>
			<div style="width: 30%;float: left;">
				<label class="label"><?php echo JText::_('Telephone Call'); ?><span>*</span></label>
				<input type="checkbox" name="chk_receivetype[]" size="14" class="" <?php echo (preg_match("/3/",$row_user->receivetype)  ? 'checked' : '');?> value="3" id="telephone">
			</div>
		</div>
	</div>
	<div class="row" id="fwzregister">
		<div><p>I would like to receive the following news and updates*:</p></div>
		<div class="left80" style="margin-bottom: 3%;">
			<div class="width100">
				<label class="label"><?php echo JText::_('Property Updates'); ?><span>*</span></label>
				<input type="checkbox" name="chk_property" size="14" class="" value="" id="property">
			</div>
			<div class="row">
				<div class="left30 mf10">
					<div class="left85">
						<label class="label"><?php echo JText::_('Resale HDB'); ?><span>*</span></label>
					</div>
					<div class="left10">
						<input type="checkbox" name="chk_propertyupdate[]" size="14" class="" <?php echo (preg_match("/1/",$row_user->propertyupdate)  ? 'checked' : '');?> value="1" id="resale">
					</div>
				</div>
			</div>
			<div class="row">
				<div class="left30 mf10">
					<div class="left85">
						<label class="label"><?php echo JText::_('Executive Condo'); ?><span>*</span></label>
					</div>
					<div class="left10">
						<input type="checkbox" name="chk_propertyupdate[]" size="14" class="" <?php echo (preg_match("/2/",$row_user->propertyupdate)  ? 'checked' : '');?> value="2" id="executive">
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row" id="fwzregister">
		
		<div class="left80" style="margin-bottom: 3%;">
			<div class="width100">
				<label class="label"><?php echo JText::_('News Updates'); ?><span>*</span></label>
				<input type="checkbox" name="chk_news" size="14" class="" value="" id="news">
			</div>
			<div class="row">
				<div class="left40 mf10">
					<div class="left85">
						<label class="label"><?php echo JText::_('Latest Property News'); ?><span>*</span></label>
					</div>
					<div class="left10">
						<input type="checkbox" name="chk_newsupdate[]" size="14" class="" <?php echo (preg_match("/1/",$row_user->newsupdate)  ? 'checked' : '');?> value="1" id="lastetproperty">
					</div>
				</div>
			</div>
			<div class="row">
				<div class="left40 mf10">
					<div class="left85">
						<label class="label"><?php echo JText::_('Investment News'); ?><span>*</span></label>
					</div>
					<div class="left10">
						<input type="checkbox" name="chk_newsupdate[]" size="14" class="" <?php echo (preg_match("/2/",$row_user->newsupdate)  ? 'checked' : '');?> value="2" id="invesment">
					</div>
				</div>
			</div>
			<div class="row">
				<div class="left40 mf10">
					<div class="left85">
						<label class="label"><?php echo JText::_('New Project Launches'); ?><span>*</span></label>
					</div>
					<div class="left10">
						<input type="checkbox" name="chk_newsupdate[]" size="14" class="" <?php echo (preg_match("/3/",$row_user->newsupdate)  ? 'checked' : '');?> value="3" id="newproject">
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="field" style="margin-bottom: 8%;">
		<label class="label">&nbsp;</label>
		<input type="submit" class="button" value="<?php echo JText::_('Save'); ?>">
		<input type="button" class="button" value="<?php echo JText::_('Cancel'); ?>" onclick="checkRegister();">
	</div>
</div>

<input type="hidden" name="id" value="<?php echo $row_user->email; ?>" />
<input type="hidden" name="option" value="com_fwuser" />
<input type="hidden" name="task" value="registration.update" />
</form>
		</div>
	</div>
</div>