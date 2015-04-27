<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

JHtml::_('bootstrap.tooltip');
JHTML::_('behavior.keepalive');

$document       = JFactory::getDocument();

if($this->searches && $this->settings->show_savesearch){
	$searchscript = '
        var setCookieRedirect;
		(function($) {
			$(document).ready(function() {
				$.cookie.json = true;
				var ipsearchcookies = new Array();
				var ipadvsearchpath = "'.IpropertyHelperRoute::getAdvsearchRoute().'";'."\n";
			
	// build hash cookie objects for the saved search
	foreach($this->searches as $s){
		$searchscript	.= 'ipsearchcookies['.$s->id.'] = $.parseJSON(\''.$s->search_string.'\');'."\n";
	}
	
	$searchscript .= "
				setCookieRedirect = function(id) {
					var cookiedata = ipsearchcookies[id];	
                    // try to remove cookie first
                    $.removeCookie('ipadvsearch'+cookiedata.Itemid);
					var ipsearchcookie = jQuery.cookie('ipadvsearch'+cookiedata.Itemid, cookiedata);
                    window.location = ipadvsearchpath;
				}            
			});
		})(jQuery);";	

    $document->addScriptDeclaration($searchscript);
}

// we got here so it's a logged in end user, not an agent
$delete_script = "
    (function($) {
        $('.ipsave_delete').live('click', function(event){
            
            var row = this.getParent('tr')
            var rowId = this.getParent('tr').get('id');
            
            if(confirm('".JText::_('COM_IPROPERTY_CONFIRM_DELETE' )."'))
            {
                var request = new Request.JSON({
                    url: 'index.php?option=com_iproperty&task=ajax.deleteSaved',
                    link: 'chain',
                    method: 'post',
                    data: { '".JSession::getFormToken()."':'1',
                        'format': 'raw',
                        'editid': rowId
                    },
                    onRequest: function(){
                        row.toggleClass('alert alert-error');
                    },
                    onSuccess: function(response) {
                        if(response){
                            $('#'+rowId).fadeOut(1000, function(){
                                $('#'+rowId).remove();
                            });
                        }
                    },
                    onFailure: function() {
                        $('.ip-favorites-maincontainer').prepend('<div id=\"ip-favorites-msg\" class=\"alert alert-error\">".addslashes(JText::_('COM_IPROPERTY_IPUSER_FAIL'))."</div>').fadeIn('slow');
                        $('#ip-favorites-msg').delay(1000).fadeOut();
                    }
                }).send();
            }
        });
        
        $('.ipsave_eupdate').live('click', function(event){
            var row = this.getParent('tr')
            var rowId = this.getParent('tr').get('id');
            
            var request = new Request.JSON({
                url: 'index.php?option=com_iproperty&task=ajax.changeUpdateStatus',
                link: 'chain',
                method: 'post',
                data: { '".JSession::getFormToken()."':'1',
                    'format': 'raw',
                    'editid': rowId
                },
                onSuccess: function(response) {
                    console.log(response);
                    if(response){
                        $('.ip-favorites-maincontainer').prepend('<div id=\"ip-favorites-msg\" class=\"alert alert-success\">".addslashes(JText::_('JLIB_APPLICATION_SAVE_SUCCESS'))."</div>').fadeIn('slow');
                        $('#ip-favorites-msg').delay(1000).fadeOut();
                    }                    
                },
                onFailure: function() {
                    $('.ip-favorites-maincontainer').prepend('<div id=\"ip-favorites-msg\" class=\"alert alert-error\">".addslashes(JText::_('COM_IPROPERTY_IPUSER_FAIL'))."</div>').fadeIn('slow');
                    $('#ip-favorites-msg').delay(1000).fadeOut();
                }
            }).send();
        });
    })(jQuery);";

$document->addScriptDeclaration($delete_script);
?>

<div class="ip-favorites-maincontainer">
    <div class="favorites-list<?php echo $this->pageclass_sfx;?>">
        <?php if ($this->params->get('show_page_heading')) : ?>
            <div class="page-header">
                <h1>
                    <?php echo $this->escape($this->params->get('page_heading')); ?>
                </h1>
            </div>
        <?php endif; ?>
        <?php if ($this->params->get('show_ip_title') && $this->iptitle) : ?>
            <div class="ip-mainheader">
                <h2>
                    <?php echo $this->escape($this->iptitle); ?>
                </h2>
            </div>
        <?php endif; ?>

        <?php    
        echo JHtmlBootstrap::startTabSet('ipUser', array('active' => 'ipproplist'));

        // load saved properties tmpl
        if($this->settings->show_saveproperty)
        {
            echo JHtmlBootstrap::addTab('ipUser', 'ipproplist', JText::_('COM_IPROPERTY_MY_SAVED_PROPERTIES'));
                echo $this->loadTemplate('proplist'); 
            echo JHtmlBootstrap::endTab();
        }

        // load saved searches tmpl
        if($this->settings->show_savesearch)
        {    
            echo JHtmlBootstrap::addTab('ipUser', 'ipsearchlist', JText::_('COM_IPROPERTY_MY_SAVED_SEARCHES'));
                echo $this->loadTemplate('searchlist'); 
            echo JHtmlBootstrap::endTab();
        }

        $this->dispatcher->trigger('onAfterRenderFavorites', array($this->user, $this->settings));
        echo JHtmlBootstrap::endTabSet();

        // display footer if enabled
        if ($this->settings->footer == 1) echo ipropertyHTML::buildThinkeryFooter(); 
        ?>
    </div>
</div>
