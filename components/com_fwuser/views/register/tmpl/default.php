<?php defined('_JEXEC') or die;
JHTML::_('behavior.modal');
$Itemid=JRequest::getVar('Itemid',0);
$db = JFactory::getDBO();
$user=&JFactory::getUser();
$user_id=$user->get('id');
$document = & JFactory::getDocument();
$_title_tag=JText::_('REGISTRATION_TITLE');
$document->setTitle($_title_tag);
$mainframe = JFactory::getApplication();
if($user_id!=''&&$user_id!=0){
	$mainframe->redirect(JURI::root().'index.php?option=com_fwuser&view=info&Itemid='.$Itemid);
}
echo '<link rel="stylesheet" href="'.JURI::root().'components/com_fwuser/css/css.css" type="text/css" />';
echo '<script type="text/javascript" src="'.JURI::root().'components/com_fwuser/js/js.js"></script>';

$options = array();
$options[] = JHTML::_('select.option', '', '');
$options[] = JHTML::_('select.option', 'Mr', 'Mr');
$options[] = JHTML::_('select.option', 'Mrs', 'Mrs');
$options[] = JHTML::_('select.option', 'Ms', 'Mrs');
$options[] = JHTML::_('select.option', 'Company', 'Company');
$dropdown_title = JHTML::_('select.genericlist', $options, 'title', 'class="inputbox" autocomplete="false" ', 'value', 'text', '');

$query="select * from #__fwuser_country order by name ";
$db->setQuery($query);
$rows_country=$db->loadObjectList();
$options = array();
$options[] = JHTML::_('select.option', 0, JText::_('FI_COUNTRY'));
for($i=0;$i<count($rows_country);$i++){
	$options[] = JHTML::_('select.option', $rows_country[$i]->id, $rows_country[$i]->name);
}
$dropdown_country = JHTML::_('select.genericlist', $options, 'country', 'class="inputbox" style="width:52%;" onchange="changeCountry();" autocomplete="false" ', 'value', 'text', 0);

$query ="select * from #__fwuser_state";

$query.=" order by name ";

$db->setQuery($query);
$rows_state=$db->loadObjectList();
$options = array();
$options[] = JHTML::_('select.option', 0, JText::_('FI_STATE'));
for($i=0;$i<count($rows_state);$i++){
	$options[] = JHTML::_('select.option', $rows_state[$i]->id, $rows_state[$i]->name);
}
$dropdown_state = '<div id="wrap_state">'.JHTML::_('select.genericlist', $options, 'state', 'class="inputbox" id="state"', 'value', 'text', 0).'</div>';
?>
<script>



 jQuery(document).on(' change', 'input[name="chk_property"]', function() { 
	jQuery('input[name="chk_propertyupdate[]"]').prop("checked", this.checked);
});


 jQuery(document).on(' change', 'input[name="chk_news"]', function() { 
	jQuery('input[name="chk_newsupdate[]"]').prop("checked", this.checked);
});

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
	}else if($('password').value==''){
		alert("<?php echo JText::_('ASK_PASSWORD'); ?>");
		$('password').focus();
	}else if($('password').value!=$('re_password').value){
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
	}else if($('phone').value==''){
		alert("<?php echo JText::_('ASK_PHONE'); ?>");
		$('phone').focus();
	}else{
		$('form_field').style.display="none";
		$('form_field_wait').set('text', '<?php echo JText::_('PLEASE_WAIT'); ?>');
		$('form_field_wait').style.display="inline";
		var req = new Request({
			method: 'post',
			url: "index.php?option=com_fwuser&view=register&format=raw",
			data: { 
				'id' : '<?php echo $row->id;?>',
				'email' : $('email').value,
				'username' : $('username').value,
				'password' : $('password').value,
				'company' : $('company').value,
				'title' : $('title').value,
				'firstname' : $('firstname').value,
				'lastname' : $('lastname').value,
				'midlename' : $('midlename').value,
				'address' : $('address').value,
				'address1' : $('address1').value,
				'city' : $('city').value,
				'zip' : $('zip').value,
				'country' : $('country').value,
				'state' : $('state').value,
				'phone' : $('phone').value,
				'mobilephone' : $('mobilephone').value,
				'fax' : $('fax').value
			},
			onRequest: function() {
				$('form_field').style.display="none";
				$('form_field_wait').set('text', '<?php echo JText::_('PLEASE_WAIT'); ?>');
				$('form_field_wait').style.display="inline";
			},
			onComplete: function(response) { 
				if(response=='email'){
					$('form_field_wait').set('text', '<?php echo JText::_('ASK_EMAIL_EXIST'); ?>');
					$('form_field').style.display="block";
					$('form_field_wait').style.display="block";
				}else if(response=='username'){
					$('form_field_wait').set('text', '<?php echo JText::_('ASK_USERNAME_EXIST'); ?>');
					$('form_field').style.display="block";
					$('form_field_wait').style.display="block";
				}else if(response=='password'){
					$('form_field_wait').set('text', '<?php echo JText::_('ASK_PASSWORD_WRONG'); ?>');
					$('form_field').style.display="block";
					$('form_field_wait').style.display="block";	
				}else if(response=='ok_login'){
					$('form_field_wait').set('text', '<?php echo JText::_('ASK_REGISTER_LOGIN_NOW'); ?>');
					$('form_field').style.display="none";
					$('form_field_wait').style.display="block";
				}else if(response=='ok_admin'){
					$('form_field_wait').set('text', '<?php echo JText::_('ASK_REGISTER_LOGIN_ADMIN'); ?>');
					$('form_field').style.display="none";
					$('form_field_wait').style.display="block";
				}else{
					$('form_field_wait').set('text', '<?php echo JText::_('ASK_REGISTER_LOGIN_EMAIL'); ?>');
					$('form_field').style.display="none";
					$('form_field_wait').style.display="block";
				}
			}
		}).send();
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
<form id="member-registration" action="<?php echo JRoute::_('index.php?option=com_fwuser&task=registration.register'); ?>" method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
<div class="fwuser_wrap item-page">
	<h2 class="item_title"><?php echo JText::_('JOIN AS A MEMBER'); ?></h2>
	<div style="margin-bottom: 2%;"><p>Join as a member and get updated with the latest property news and annoucements. To join, simply compete the form below. Membership is free.</p></div>
	<p id="form_field_wait" class="form_field_wait" style="display:none;">
			<?php echo JText::_('PLEASE_WAIT'); ?>
		</p>
	<div class="row clearfix" id="fwzregister">
		<div class="col-sm-6">	
			<div class="row field">
				<div class="col-sm-5"><label class="label"><?php echo JText::_('Name in Full'); ?>(*)</label></div>
				<div class="col-sm-7"><input type="text" size="14" class="" value="" id="name" name="name">	</div>
			</div>
			<div class="row field">
				<div class="col-sm-5"><label class="label"><?php echo JText::_('Company name'); ?></label></div>
				<div class="col-sm-7"><input type="text" size="14" class="" value="" id="companyname" name="companyname">	</div>
			</div>
			<div class="row field">
				<div class="col-sm-5"><label class="label"><?php echo JText::_('Mobile Number'); ?></label></div>
				<div class="col-sm-7"><input type="text" size="14" class="" value="" id="mobile" name="mobile">	</div>
			</div>
			<div class="row field">
				<div class="col-sm-5"><label class="label"><?php echo JText::_('Office Number'); ?></label>	</div>
				<div class="col-sm-7"><input type="text" size="14" class="" value="" id="officenumber" name="officenumber">	</div>
			</div>
			<div class="row field">
				<div class="col-sm-5"><label class="label"><?php echo JText::_('Password'); ?>(*)</label>	</div>
				<div class="col-sm-7"><input type="password" size="14" class="" value="" id="password" name="password">	</div>
			</div>
			<div class="row field">
				<div class="col-sm-5"><label class="label"><?php echo JText::_('Confirm password'); ?>(*)</label>	</div>
				<div class="col-sm-7"><input type="password" size="14" class="" value="" id="confirmpassword" name="confirmpassword">	</div>
			</div>
			
		</div>
		<div class="col-sm-6">
			<div class="row field">
				<div class="col-sm-4"><label class="label"><?php echo JText::_('Email'); ?>(*)</label>	</div>
				<div class="col-sm-8"><input type="text" size="14" class="" value="" id="email" name="email">	</div>
			</div>		
			<div class="row field">
				<div class="col-sm-4"><label class="label"><?php echo JText::_('Address'); ?></label> 	</div>
				<div class="col-sm-8"><input type="text" size="14" class="" value="" id="address" name="address">	</div>
			</div>
			<div class="row field">
				<div class="col-sm-4"><label class="label"><?php echo JText::_('Country'); ?></label>	</div>
				<div class="col-sm-8"><?php echo $dropdown_country; ?></div>
			</div>
			<div class="row field">
				<div class="col-sm-4"><label class="label"><?php echo JText::_('City'); ?></label>	</div>
				<div class="col-sm-8"><input type="text" size="14" class="" value="" id="city" name="city">	</div>
			</div>
			<div class="row field">
				<div class="col-sm-4"><label class="label"><?php echo JText::_('Postal'); ?></label>	</div>
				<div class="col-sm-8"><input type="text" size="14" class="" value="" id="postal" name="postal">	</div>
			</div>
		</div>
		
	</div>
	<div class="row" id="fwzregister">
		<div><p>I would like to receive news and announcements via*:</p></div>
		<div style="width: 80%;float: left;margin-bottom: 4%;margin-top: 1%;">
			<div style="width: 30%;float: left;">
				<label class="label"><?php echo JText::_('Newletter'); ?><span>*</span></label>
				<input type="checkbox" checked name="chk_receivetype[]" size="14" class="" value="1" id="newletter">
			</div>
			<div style="width: 30%;float: left;">
				<label class="label"><?php echo JText::_('SMS'); ?><span>*</span></label>
				<input type="checkbox" checked name="chk_receivetype[]" size="14" class="" value="2" id="sms">
			</div>
			<div style="width: 30%;float: left;">
				<label class="label"><?php echo JText::_('Telephone Call'); ?><span>*</span></label>
				<input type="checkbox" checked name="chk_receivetype[]" size="14" class="" value="3" id="telephone">
			</div>
		</div>
	</div>
	<div class="row" id="fwzregister">
		<div><p>I would like to receive the following news and updates*:</p></div>
		<div class="left80" style="margin-bottom: 3%;">
			<div class="width100">
				<label class="label"><?php echo JText::_('Property Updates'); ?><span>*</span></label>
				<input type="checkbox" checked name="chk_property" size="14" class="" value="" id="property">
			</div>
			<div class="row">
				<div class="left30 mf10">
					<div class="left66">
						<label class="label"><?php echo JText::_('Resale HDB'); ?><span>*</span></label>
					</div>
					<div class="left10">
						<input type="checkbox" checked name="chk_propertyupdate[]" size="14" class="" value="1" id="resale">
					</div>
				</div>
			</div>
			<div class="row">
				<div class="left30 mf10">
					<div class="left66">
						<label class="label"><?php echo JText::_('Executive Condo'); ?><span>*</span></label>
					</div>
					<div class="left10">
						<input type="checkbox" checked name="chk_propertyupdate[]" size="14" class="" value="2" id="executive">
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row" id="fwzregister">
		
		<div class="left80" style="margin-bottom: 3%;">
			<div class="width100">
				<label class="label"><?php echo JText::_('News Updates'); ?><span>*</span></label>
				<input type="checkbox" checked name="chk_news" size="14" class="" value="" id="news">
			</div>
			<div class="row">
				<div class="left40 mf10">
					<div class="left50">
						<label class="label"><?php echo JText::_('Latest Property News'); ?><span>*</span></label>
					</div>
					<div class="left10">
						<input type="checkbox" checked name="chk_newsupdate[]" size="14" class="" value="1" id="lastetproperty">
					</div>
				</div>
			</div>
			<div class="row">
				<div class="left40 mf10">
					<div class="left50">
						<label class="label"><?php echo JText::_('Investment News'); ?><span>*</span></label>
					</div>
					<div class="left10">
						<input type="checkbox" checked name="chk_newsupdate[]" size="14" class="" value="2" id="invesment">
					</div>
				</div>
			</div>
			<div class="row">
				<div class="left40 mf10">
					<div class="left50">
						<label class="label"><?php echo JText::_('New Project Launches'); ?><span>*</span></label>
					</div>
					<div class="left10">
						<input type="checkbox" checked name="chk_newsupdate[]" size="14" class="" value="3" id="newproject">
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="field" style="margin-bottom: 8%;">
		<label class="label">&nbsp;</label>
		<input type="submit" class="button" value="<?php echo JText::_('FI_SAVE'); ?>">
		<input type="button" class="button" value="<?php echo JText::_('CANCEL'); ?>" onclick="checkRegister();">
	</div>
</div>

<input type="hidden" name="option" value="com_fwuser" />
<input type="hidden" name="task" value="registration.register" />
</form>