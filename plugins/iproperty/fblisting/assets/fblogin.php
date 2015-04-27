<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined('JPATH_BASE') or die;

class JFormFieldFblogin extends JFormField
{
    protected $type = 'Fblogin';

    public function getInput()
	{
		$lang 	= JFactory::getLanguage();
		$doc 	= JFactory::getDocument();
		$uri 	= JFactory::getURI();		
		$page 	= '/administrator/index.php'.$uri->toString(array('query'));
		// add the jQuery version of the FB js API
		$fbscript = "
					var langOptions = {
						login: '".JText::_('PLG_IP_FBLISTING_LOGIN')."',
						logout:'".JText::_('PLG_IP_FBLISTING_LOGOUT')."',
						appid_required:'".JText::_('PLG_IP_FBLISTING_APPID_REQUIRED')."'
					};
					
					jQuery(document).ready(function($) {
						var appid = $('#jform_params_appid').val();
						if (appid){
							setupFB(appid);		
						} else {
							$('#ip_fbsubmit').attr('disabled','disabled');
						};
						
						// attach change event to appid input
						$('#jform_params_appid').change(function(){
							$('#ip_fbsubmit').removeAttr('disabled');
							setupFB($('#jform_params_appid').val());
						});
					});
					
					function setupFB(appid){	
						jQuery.ajaxSetup({ cache: true });
						jQuery.getScript('//connect.facebook.net/en_UK/all.js', function(){
							FB.init({
								appId: appid,
								cookie: true
							});
							
							// In default case, the person is not logged into Facebook or has not authorized app, so we call the login() 
							jQuery('#ip_fbsubmit').attr('value', langOptions.login);
							jQuery('#ip_fbsubmit').click(function(e){
								e.preventDefault();
								FB.login(function(response){
								// check if valid response, set tokens
								if (response.status === 'connected') jQuery('#jform_params_apptoken').val(response.authResponse.accessToken);
								}, { scope: 'manage_pages, offline_access, publish_actions' } )
							});
							
							if (typeof(FB) != 'undefined') { 
								FB.Event.subscribe('auth.authResponseChange', function(response) {
									// Here we specify what we do with the response anytime this event occurs. 
									if (response.status === 'connected') {					
										// user is logged into FB *AND* logged into the app
										jQuery('#jform_params_apptoken').val(response.authResponse.accessToken);
										jQuery('#ip_fbsubmit').val(langOptions.logout);
										jQuery('#ip_fbsubmit').click(function(e){
											e.preventDefault();
											FB.logout(function(){
												jQuery('#jform_params_apptoken').val('');	
											});
										});
									} else {
										// In this case, the person has logged out of Facebook or has not authorized app, so we call the login() 
										jQuery('#ip_fbsubmit').attr('value', langOptions.login);
										jQuery('#ip_fbsubmit').click(function(e){
											e.preventDefault();
											FB.login(function(response){
											// check if valid response, set tokens
											if (response.status === 'connected') jQuery('#jform_params_apptoken').val(response.authResponse.accessToken);
											}, { scope: 'manage_pages, offline_access, publish_stream' } )
										});
									}
								});
							}
						});						
					};";
		$doc->addScriptDeclaration($fbscript);		

		return '<input id="ip_fbsubmit" type="submit" value="'.JText::_('PLG_IP_FBLISTING_LOGIN').'"/>';
    }
}
