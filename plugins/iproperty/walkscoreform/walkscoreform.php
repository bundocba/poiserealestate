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

class plgIpropertyWalkscoreForm extends JPlugin
{
	public function __construct(&$subject, $config)  
    {
		parent::__construct($subject, $config);
        $this->loadLanguage();
	}

    public function onBeforeRenderForms($property, $settings)
    {
		if ($this->params->get('position')) return true;
        $this->_doWalkscoreForm($property, $settings);
    }
	
	public function onAfterRenderForms($property, $settings)
    {
        if (!$this->params->get('position')) return true;
        $this->_doWalkscoreForm($property, $settings);
    } 
    
	private function _doWalkscoreForm($property, $settings)
	{
        $app        = JFactory::getApplication();
        $document   = JFactory::getDocument();
        
		if($app->getName() != 'site') return true;
        if(!$this->params->get('walkscore-id')) return true;

		$address	= $property->street_address . " " . $property->zip;		
		$latitude	= $property->lat_pos;
		$longitude	= $property->long_pos;
		
		$ws_script = "
                var ws_wsid    = '" . $this->params->get('walkscore-id') . "';
                var ws_address = '" . urlencode($address) . "';
                var ws_lat     = '" . $latitude . "';
                var ws_lon     = '" . $longitude . "';
                var ws_width   = '" . (int)$this->params->get('width') . "';
                var ws_height  = '" . (int)$this->params->get('height') . "';
                var ws_layout  = '" . $this->params->get('wslayout') . "';
                var ws_distance_units = '" . $this->params->get('unit') . "';                
                var ws_map_modules = 'all';
                var toggler    = 0;

                jQuery(document).ready(function($){						
                    $('#ipwalkscoreplug').click(function(e){
                        if (!toggler){
                            var headID = document.getElementsByTagName(\"head\")[0];
                            var newScript = document.createElement('script');
                            newScript.type = 'text/javascript';
                            newScript.src = '//www.walkscore.com/tile/show-walkscore-tile.php';
                            headID.appendChild(newScript);
                            toggler = 1;
                        }
                    });
                });";
        $document->addScriptDeclaration($ws_script);            

		echo JHtmlBootstrap::addTab('ipDetails', 'ipwalkscoreplug', JText::_($this->params->get('tabtitle', 'PLG_IP_WS_WALKSCORE')));
            ?>
            <div class="ip-walkscore-wrapper">
                <div id="ws-walkscore-tile">
                    <div id="ws-footer">
                        <form id="ws-form">
                            <a id="ws-a" href="http://www.walkscore.com/" target="_blank">What's your Walk Score:</a>
                            <input type="text" id="ws-street" class="input-medium" />
                            <input type="image" id="ws-go" src="//cdn2.walk.sc/images/tile/go-button.gif" height="15" width="22" border="0" alt="Walk Score" />
                        </form>
                    </div>
                </div>
                <script type="text/javascript" src="//www.walkscore.com/tile/show-walkscore-tile.php"></script>
            </div>
            <?php
        echo JHtmlBootstrap::endTab();
		
		return true;
	}	
}
?>