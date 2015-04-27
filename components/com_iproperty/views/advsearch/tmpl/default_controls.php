<?php
/**
 * @version 3.0 2012-12-04
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
$this->document->addScript(JURI::root(true).'/components/com_iproperty/assets/advsearch/cascade.js');
$this->document->addScript(JURI::root(true).'/components/com_iproperty/assets/advsearch/mapcontrols.js');
?>

<div id="ipMapSaveControls" class="btn-group pull-right"></div>
<?php if($this->settings->show_saveproperty): ?>
    <div id="ipMapSavePanel" style="display: none;">
        <?php echo $this->loadTemplate('searchsave'); ?>
    </div>
<?php endif; ?>
<div class="clearfix"></div>
<ul id="ipMapnav" class="nav nav-tabs"></ul>
<div id="ipMapnavData" class="tab-content"></div>
<div class="clearfix"></div>
<hr />