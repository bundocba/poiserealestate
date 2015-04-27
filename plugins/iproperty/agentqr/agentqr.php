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

class plgIpropertyAgentqr extends JPlugin
{	
	public function __construct(&$subject, $config)  
    {
		parent::__construct($subject, $config);
        $this->loadLanguage();
	}

	public function onAfterRenderAgentList($agent, $settings)
	{
        if(!$this->params->get('show_list_agent', 1) || !$agent) return false;
        
        $size   = $this->params->get('size_list', 150);
        echo '<div class="ip-agentqr-list" align="right">'.$this->_createQrCode($agent, $settings, $size).'</div>';
    }
    
    public function onAfterRenderPropertyAgent($agent, $settings)
	{
        if(!$this->params->get('show_property_agent', 1) || !$agent) return false;

        $size   = $this->params->get('size_property', 150);
        return '<div class="ip-agentqr-property" align="center">'.$this->_createQrCode($agent, $settings, $size).'</div>';
    }
    
    private function _createQrCode($agent, $settings, $size = 150)
    {        
        $app        = JFactory::getApplication();
        $document   = JFactory::getDocument();
        
        $format     = $this->params->get('format', 1); 
        $fullname   = $agent->fname.' '.$agent->lname;
        $phone_prefix = ($this->params->get('qr_phone_prefix')) ? '+1-' : '';
        
        // create the agent address object
        $address = $agent->street ? $agent->street : '';
        $address .= $agent->street2 ? ' '.$agent->street2 : '';
        $address .= $agent->locstate ? ', '.ipropertyHTML::getStateName($agent->locstate) : '';
        $address .= $agent->province ? ', '.$agent->province : '';
        $address .= $agent->postcode ? ', '.$agent->postcode : '';
        $address .= $agent->country ? ' '.ipropertyHTML::getCountryName($agent->country) : '';     

        // get image if exists
        $picture = $agent->icon != 'nopic.png' ? JURI::root().'media/com_iproperty/agents/'.$agent->icon : '';
        
        if($format){ // create mecard
            $data = 'MECARD:N:'.$agent->lname.','.$agent->fname.';TEL:'.$agent->phone.';EMAIL:'.$agent->email.';NOTE:'.$agent->companyname.';ADR:'.$address.';URL:'.$agent->website; 
        } else { // create vcard
            $data = "BEGIN:VCARD\n".
                    "VERSION:4.0\n".
                    "N:".$agent->lname.";".$agent->fname.";;;\n".
                    "FN:".$fullname."\n".
                    "ORG:".$agent->companyname."\n".
                    "PHOTO:".$picture."\n".
                    "TEL;TYPE=work,voice;VALUE=uri:tel:".$phone_prefix.$agent->phone."\n".
                    "TEL;TYPE=mobile,voice;VALUE=uri:tel:".$phone_prefix.$agent->mobile."\n".
                    "ADR;TYPE=work;LABEL=\"".$address."\"\n".
                    "EMAIL:".$agent->email."\n".
                    "END:VCARD";
        }
        
        $data   = urlencode($data);
        $image  = '<img src="https://chart.googleapis.com/chart?chld=L|1&chs='.$size.'x'.$size.'&cht=qr&chl='.$data.'" alt="" class="thumbnail ip-agentqr-img" />';
        if($this->params->get('qr_note')) $image .= '<div class="well ip-agentqr-note">'.JText::_($this->params->get('qr_note')).'</div>';
        
        return $image;
	}
}