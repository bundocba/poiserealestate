<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

// no direct access
defined('_JEXEC' ) or die( 'Restricted access');
jimport('joomla.plugin.plugin');

class plgIpropertyCurrencyTab extends JPlugin
{
	public function __construct(&$subject, $config)  
    {
		parent::__construct($subject, $config);
        $this->loadLanguage();
    }

	public function onAfterRenderMap($property, $settings)
	{
        $app = JFactory::getApplication();
        if($app->getName() != 'site') return true;
        
        // currencies supported by Google as of 02/2013
        $currencies = array (
			"USD" => "US Dollar ($)",
			"JPY" => "Japanese Yen (¥)",
			"BGN" => "Bulgarian Lev (BGN)", 
			"CZK" => "Czech Republic Koruna (CZK)",
			"DKK" => "Danish Krone (DKK)",
			"GBP" => "British Pound Sterling (£)",
			"HUF" => "Hungarian Forint (HUF)",
			"LTL" => "Lithuanian Litas (LTL)",
			"LVL" => "Latvian Lats (LVL)",
			"PLN" => "Polish Zloty (PLN)",
			"RON" => "Romanian Leu (RON)",
			"SEK" => "Swedish Krona (SEK)",
			"CHF" => "Swiss Franc (CHF)",
			"NOK" => "Norwegian Krone (NOK)",
			"HRK" => "Croatian Kuna (HRK)",
			"RUB" => "Russian Ruble (RUB)",
			"TRY" => "Turkish Lira (TRY)",
			"AUD" => "Australian Dollar (A$)",
			"BRL" => "Brazilian Real (R$)",
			"CAD" => "Canadian Dollar (CA$)",
			"CNY" => "Chinese Yuan (CN¥)",
			"HKD" => "Hong Kong Dollar (HK$)",
			"IDR" => "Indonesian Rupiah (IDR)",
			"ILS" => "Israeli New Sheqel (ILS)",
			"INR" => "Indian Rupee (Rs.)",
			"KRW" => "South Korean Won (KRW)",
			"MXN" => "Mexican Peso (MX$)",
			"MYR" => "Malaysian Ringgit (MYR)",
			"NZD" => "New Zealand Dollar (NZ$)",
			"PHP" => "Philippine Peso (Php)",
			"SGD" => "Singapore Dollar (SGD)",
			"THB" => "Thai Baht (THB)",
			"ZAR" => "South African Rand (ZAR)",
            "EUR" => "Euro (EUR)"
        );

        asort($currencies);
        // get site's normal currency and check that it's in the currency array
        $start_curr = $settings->default_currency ?: 'USD';
        if(!array_key_exists($start_curr, $currencies)) return true; // not in array so bail out
        // if "call for price" obviously we can't do a conversion so bail out
        if($property->formattedprice == JText::_('COM_IPROPERTY_CALL_FOR_PRICE')) return true;

        // else we can do this so get the price
        $price = $property->price;

        // create javascript for ajax request
        $javascript = "
			var ratedata;
            jQuery(document).ready(function($){
				$.getJSON( '" . JURI::root(true) . "/plugins/iproperty/currencytab/currencytab_proxy.php')
				.done(function( json ) {
					ratedata = json;
					ratedata['EUR'] = 1;
				})
				.fail(function( jqxhr, textStatus, error ) {
					var err = textStatus + ', ' + error;
					console.log( 'Request Failed: ' + err );
				});
				
                $('#new_price').html('".addslashes(JText::_('PLG_IP_CONVERT_SELECT_CURRENCY'))."');		
                $('#currency_select').change(function(){
                    var data = {
						price: '".$price."',
						start: '".$start_curr."',
						end: $('#currency_select').val()
					};
					var euros = data.price / ratedata[data.start];
                    var final = Math.round( (euros * ratedata[data.end] + 0.00001) * 100) / 100; // hack to eliminate floating point weirdness
					if (final) $('#new_price').html(final + ' ' + data.end);
                });
             });"; 

        $doc = JFactory::getDocument();
        $doc->addScriptDeclaration($javascript);

        $currencies_list   = array();
        $currencies_list[] = JHTML::_('select.option', '', JText::_('PLG_IP_CONVERT_CURRENCY'), 'id', 'name');

        foreach($currencies as $key => $value){
            $currencies_list[] = JHTML::_('select.option', $key, $value, 'id', 'name');
        }

        $curr_select  = JHTML::_('select.genericlist', $currencies_list, 'currency_select', 'class="inputbox"', 'id', 'name', 'currency_select');

        echo JHtmlBootstrap::addTab('ipMap', 'ipcurrency', JText::_($this->params->get('tabtitle', 'PLG_IP_CONVERT_CONVERT')));
            echo '
				<div>
					<div class="control-group form-horizontal">
                        <label class="control-label" for="currency_select">'.JText::_('PLG_IP_CONVERT_SELECT_CURRENCY').'</label>
                        <div class="controls">
                            '.$curr_select.'
                        </div>
					</div>
				</div>
                <hr />
				<div>
					<div class="alert alert-info"><strong>'.JText::_('PLG_IP_CONVERT_CURRENT_PRICE').':</strong> '.ipropertyHTML::getFormattedPrice($price).'</div>
					<div class="alert alert-success"><strong>'.JText::_('PLG_IP_CONVERT_NEW_PRICE').':</strong> <span id="new_price"></span></div>
					<div class="small">'.JText::_('PLG_IP_CONVERT_DISCLAIMER').'</div>
				</div>';
        echo JHtmlBootstrap::endTab();
        return true;
	}
}
