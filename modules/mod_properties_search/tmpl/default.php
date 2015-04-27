<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_custom
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<div id="properties-search">
	<h2 class="col-sm-offset-1"><?php echo $module->title;?></h2>
	<form action="">
		<div class="row clearfix">
			<div class="col-sm-4 col-sm-offset-1">
				<label for="type"><?php echo JText::_('PROPERTY_TYPE');?>:</label>
				<input type="text" name="type" id="type">
			</div>
			<div class="col-sm-4">
				<label for="district"><?php echo JText::_('DISTRICT');?>:</label>
				<input type="text" name="district" id="district">
			</div>
			<div class="col-sm-3">
				<label for="keyword"><?php echo JText::_('KEYWORD');?>:</label>
				<input type="text" name="keyword" id="keyword">
			</div>
		</div>

		<div class="row clearfix">
			<div class="col-sm-4 col-sm-offset-1">
				<label for="salerent"><?php echo JText::_('SALE_OR_RENT');?>:</label>
				<input type="text" name="salerent" id="salerent">
			</div>
			<div class="col-sm-4">
				<label for="price"><?php echo JText::_('PRICE_RANGE');?>:</label>
				<input type="text" name="price" id="price">
			</div>
			<div class="col-sm-3">
				<input type="submit" name="search" id="search" value="<?php echo JText::_('SEARCH_BUTTON');?>">
			</div>
		</div>
	</form>
</div>