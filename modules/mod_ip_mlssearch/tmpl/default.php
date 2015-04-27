<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Iproperty
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license see LICENSE.php
 */

//no direct access
defined('_JEXEC') or die('Restricted access');
$uri 		= JFactory::getURI();
$action     = str_replace('&', '&amp;', $uri->toString());

$tooltip_text       = $params->get('dsearch',   JText::_('MOD_IP_MLSSEARCH_ENTER_REF'));
$search_text        = $params->get('dkeyword',  JText::_('MOD_IP_MLSSEARCH_DREF'));
$moduleclass_sfx    = ($params->get('moduleclass_sfx')) ? ' '.htmlspecialchars($params->get('moduleclass_sfx')) : '';

if($params->get('autocomplete')){
    $num_results = (int)$params->get('autocomplete_num', 5);
    $document   = JFactory::getDocument();
    $script = "
        jQuery(document).ready(function($){
            // create auto complete
            var url = '".JURI::base('true')."/index.php?option=com_iproperty&task=ajax.ajaxAutocomplete&format=raw&field=mls_id&validate=1&".JSession::getFormToken()."=1';
            $.getJSON(url).done(function( data ){
                $('#ip_mls_search').typeahead({source: data, items:".$num_results."});
            });
        });";

    $document->addScriptDeclaration($script);
}
?>

<div class="ip-mlssearch-holder<?php echo $moduleclass_sfx; ?>">
    <form action="<?php echo $action; ?>" method="post" id="ip-mlssearch-form" class="form-inline">
        <?php if ($params->get('pretext')): ?>
            <div class="pretext">
                <p><?php echo $params->get('pretext'); ?></p>
            </div>
        <?php endif; ?>
        <div class="mlsdata">
            <div id="form-ip-mlssearch" class="control-group">
                <div class="controls">
                    <div class="input-prepend input-append">
                        <span class="add-on hidden-tablet hidden-phone">
                            <i class="icon-home"></i>
                        </span>
                        <input id="ip_mls_search" type="text" name="ip_mls_search" class="input-small" placeholder="<?php echo JText::_($search_text) ?>" />
                        <button class="btn hasTooltip" title="<?php echo JText::_($tooltip_text); ?>" onclick="submit()">
                            <i class="icon-question-sign"></i>
                        </button>
                    </div>            
                </div>
            </div>
        </div>
        <?php if ($params->get('posttext')): ?>
            <div class="pretext">
                <p><?php echo $params->get('posttext'); ?></p>
            </div>
        <?php endif; ?>
        <input type="hidden" name="task" value="ip_mls_search" />
    </form>
</div>