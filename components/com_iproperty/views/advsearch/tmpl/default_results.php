<?php
/**
 * @version 3.0 2012-12-04
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
$this->document->addScript(JURI::root(true).'/components/com_iproperty/assets/advsearch/results.js');
?>
<?php if ($this->adv_layout): // overview layout ?>
<div id="ipResults" class="ip-advsearch-results-container row-fluid">
    <div id="ipPagination" class="pagination pagination-small span6 pull-left ip-pagination"></div>
    <div id="ipResultsTicker" class="span6 pull-right"></div>
    <div id="ipOrderBy" class="pull-right form-inline"></div>
    <div class="clearfix"></div>
    <div id="ipResultsBody"></div>
	<div id="ipPagination2" class="pagination pagination-small span6 pull-left ip-pagination"></div>
</div>
<hr />
<?php else: // table layout ?>
<div id="ipResults" class="ip-advsearch-results-container row-fluid">
    <div id="ipPagination" class="pagination pagination-small span6 pull-left ip-pagination"></div>
    <div id="ipResultsTicker" class="span6 pull-right"></div>
    <div class="clearfix"></div>
    <table id="ipResultTable" class="table table-striped table-hover">
        <thead id="ipResultsHead"></thead>
        <tbody id="ipResultsBody"></tbody>
    </table>
	<div id="ipPagination2" class="pagination pagination-small span6 pull-left ip-pagination"></div>
</div>
<hr />
<?php endif; ?>
