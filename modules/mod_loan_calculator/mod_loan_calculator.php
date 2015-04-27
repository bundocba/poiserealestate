<?php
defined('_JEXEC') or die('Restricted access');

$myabsoluteurl=JURI::base();

?>

<link type="text/css" href="<?php echo $myabsoluteurl; ?>modules/mod_loan_calculator/styles.css" rel="stylesheet">

<script type="text/javascript" src="<?php echo $myabsoluteurl; ?>modules/mod_loan_calculator/cal.js" language="Javascript"></script>

<script type="text/javascript">

	function getValueSelected(){

		var x = document.form0.selectFormName.selectedIndex;

		document.getElementById('txtAutoInterestRate').value = document.form0.selectFormName.options[x].value;

		

	};

</script>
<div id="calculator_wrap">
	<div class="calculator">
		<div class="line_input">
			<span class="title_input">Total Home Loan Amt:</span>
			<input type="text" name="total_amt" value="" placeholder="$">
		</div>
		<div class="line_input">
			<span class="title_input">Annual Interest Rate:</span>
			<input type="text" name="annual_rate" value="1.5%" placeholder="">
		</div>
		<div class="line_input">
			<span class="title_input">Term of loan:</span>
			<select name="term_of_loan">
				<?php 
					for($i=5;$i<=40;$i+=5){?>
					<option value="<?php echo $i;?>"><?php echo $i.' years';?></option>
					<?php
				}?>
			</select>
		</div>
	</div>
	<span class="btn_calculate">
		<a class="button" href="#">Calculate</a>
	</span>
</div>