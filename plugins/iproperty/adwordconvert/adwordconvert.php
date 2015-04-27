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

class plgIpropertyAdwordconvert extends JPlugin
{
	private $conv_id;
	private $conv_lang;	
	private $conv_format;
	private $conv_color;
	private $conv_label;
	
	public function __construct(&$subject, $config)  
    {
		parent::__construct($subject, $config);
        $this->loadLanguage();
        
		$this->conv_id 		= $this->params->get('conv_id', false);
		$this->conv_lang 	= $this->params->get('conv_lang', 'en_US');	
		$this->conv_format 	= $this->params->get('conv_format', 3);
		$this->conv_color 	= $this->params->get('conv_color', 'ffffff');
		$this->conv_label 	= $this->params->get('conv_label', 'Default');
	}

	public function onAfterRenderProperty($property, $settings)
	{
        $app        = JFactory::getApplication();
        $document   = JFactory::getDocument();

		if($app->getName() != 'site') return true;
		if(!$this->conv_id) return true;
        if(!JRequest::getVar('submit')) return true;		
		
        $image = '<img height="1" width="1" style="border-style: none;" alt="" src="http://www.googleadservices.com/pagead/conversion/'.$this->conv_id.'/?label='.$this->conv_label.'&guid=ON&script=0" />';
        
		$conv_script = '
        <script type="text/javascript">
            //<![CDATA[
			window.addEvent(\'domready\', function() {
				var google_conversion_id     		= ' . $this->conv_id . ';
                var google_conversion_language   	= "' . $this->conv_lang . '";
                var google_conversion_format   		= "' . $this->conv_format . '";
                var google_conversion_color   		= "' . $this->conv_color . '";
                var google_conversion_label   		= "' . $this->conv_label . '";
            });
            //]]>
        </script>
        <script type="text/javascript" src="http://www.googleadservices.com/pagead/conversion.js">
        </script>
        <noscript>
            <div style="display:inline;">
                <img height="1" width="1" style="border-style:none;" alt="" src="'.$image.'"/>
            </div>
        </noscript>';
        
        $app->addCustomTag( $conv_script );

        return true;
	}
}
