<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
$db = JFactory::getDBO();
$mainframe = JFactory::getApplication();
$query="select * from #__fwuser_country order by name ";
$db->setQuery($query);
$rows_country=$db->loadObjectList();
$options = array();
$options[] = JHTML::_('select.option', 0, JText::_('FI_COUNTRY'));
for($i=0;$i<count($rows_country);$i++){
	$options[] = JHTML::_('select.option', $rows_country[$i]->id, $rows_country[$i]->name);
}
$dropdown_country = JHTML::_('select.genericlist', $options, 'jform[country]', 'class="inputbox" onchange="changeCountry();" autocomplete="false" ', 'value', 'text', $this->form->getField('country')->value);

$query ="select * from #__fwuser_state";
if($this->form->getField('country')->value!=0&&$this->form->getField('country')->value!=''){
	$query.=" where country_id='".$this->form->getField('country')->value."' ";
}else{
	$query.=" where country_id=0 ";
}
$query.=" order by name ";
$db->setQuery($query);
$rows_state=$db->loadObjectList();

$options = array();
$options[] = JHTML::_('select.option', 0, JText::_('FI_STATE'));
for($i=0;$i<count($rows_state);$i++){
	$options[] = JHTML::_('select.option', $rows_state[$i]->id, $rows_state[$i]->name);
}
$dropdown_state = '<div id="wrap_state">'.JHTML::_('select.genericlist', $options, 'jform[state]', 'class="inputbox" id="state"', 'value', 'text', $this->form->getField('state')->value).'</div>';
?>
<form enctype="multipart/form-data" action="<?php echo JRoute::_('index.php?option=com_fwuser&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	<fieldset class="adminform">
		<legend>Details</legend>
		<ul class="adminformlist">
<?php $i=0; foreach($this->form->getFieldset() as $field): ?>
			<li>
<?php
	$i++;
	if($i==14){
		echo $field->label;
		if (strtolower($field->type) == 'editor') {
			echo "<div class='clr'></div>";
		}
		echo $dropdown_country;
	}else if($i==15){
		echo $field->label;
		if (strtolower($field->type) == 'editor') {
			echo "<div class='clr'></div>";
		}
		echo $dropdown_state;
	}else{
		echo $field->label;
		if (strtolower($field->type) == 'editor') {
			echo "<div class='clr'></div>";
		}
		echo $field->input;
	}
?>
            </li>
<?php endforeach; ?>
			<li>
            	<label> </label>
                <div>
<?php 
if($this->form->getField('avatar')->value!=''){
	$image = $this->form->getField('avatar')->value;
	if ($image){
		$image = "".JURI::root()."/images/avatars/".$image;
?>
<img src="<?php echo $image; ?>" width="150" /><br />
<?php }else{ 
		$image = "".JURI::root()."/images/avatars/default.jpg";
?>
<img src="<?php echo $image; ?>" width="150" /><br />
<?php } ?>
<?php } ?>
                </div>
            </li>
		</ul>
	</fieldset>
	<div>
		<input type="hidden" name="task" value="fwuser.edit" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<style>
fieldset.adminform label, fieldset.adminform span.faux-label{
	min-width:250px;
}
</style>
<script>
function changeCountry(){
	var req = new Request({
		method: 'post',
		url: "index.php?option=com_fwuser&view=state&format=raw",
		data: { 
			'id' : $('jformcountry').value
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