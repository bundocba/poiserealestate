<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
JHtml::_('bootstrap.tooltip');
?>
<form action="<?php echo JRoute::_('index.php?option=com_iproperty&view=iconuploader&tmpl=component'); ?>" method="post" name="adminForm" id="adminForm">
    <div class="row-fluid">
        <div class="span12">        
            <div class="span4 pull-right">
                <span class="label label-info"><?php echo "/media/com_iproperty/". $this->folder; ?></span>
            </div>
            <div class="span8 pull-left">
            <div id="filter-bar" class="btn-toolbar">
                <div class="filter-search btn-group pull-left">
                    <label class="element-invisible" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
                    <input type="text" name="search" id="search" value="<?php echo $this->search; ?>" class="inputbox" />
                </div>
                <div class="btn-group pull-left">
                    <button class="btn tip" onclick="document.adminForm.submit();"><?php echo JText::_('COM_IPROPERTY_GO'); ?></button>
                    <button class="btn tip" onclick="document.adminForm.search.value='';document.adminForm.submit();"><?php echo JText::_('COM_IPROPERTY_RESET'); ?></button>
                </div>                
            </div>           
        </div>
    </div>
    </div>
    <div class="clearfix"></div>
    <hr />    
    <ul class="manager thumbnails">
        <?php
        for ($i = 0, $n = count($this->images); $i < $n; $i++)
        {
            $this->setImage($i);
            echo $this->loadTemplate('icon');
        }
        ?>
    </ul>
    <input type="hidden" name="task" value="<?php echo $this->task; ?>" />
    <hr />
    <div class="center"><?php echo $this->pageNav->getListFooter(); ?></div>
</form>
<div class="clearfix"></div>
<hr />
<?php echo ipropertyAdmin::footer( ); ?>
