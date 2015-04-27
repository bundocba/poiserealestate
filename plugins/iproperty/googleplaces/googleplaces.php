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

class plgIpropertyGoogleplaces extends JPlugin
{
    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
        $this->loadLanguage();
    }

    public function onBeforeRenderForms($property, $settings)
    {
		if ($this->params->get('position')) return true;
        $this->_doGPlaces($property, $settings);
    }
	
	public function onAfterRenderForms($property, $settings)
    {
        if (!$this->params->get('position')) return true;
        $this->_doGPlaces($property, $settings);
    } 
    
    private function _doGPlaces($property, $settings)
    {
        $app        = JFactory::getApplication();
        $document   = JFactory::getDocument();

        if($app->getName() != 'site' || !$property->show_map || $settings->map_provider != 1) return true;
        $gplanguage = $this->params->get('gplanguage', 'en');

        // convert distance for radius to meters
        $distance   = $this->params->get('radius', 1);
        if ($this->params->get('unit', 'mi') == 'mi'){
            $distance = $distance * 1609.3;
        } else {
            $distance = $distance * 1000;
        }

        // build types array
        $types      = $this->params->get('types', array('school'));
        if (!is_array($types)) $types = array($types);
        $t_string   = '[';
        foreach ($types as $t){
            $t_string .= '"' . $t . '",';
        }
        $t_string = rtrim($t_string, ',') . ']';

        if (!$property->lat_pos || !$property->long_pos) return true;

        $radius     = $this->params->get('unit', 'mi') == 'mi' ? 3958.75 : 6371;
        $radiustag  = $this->params->get('unit', 'mi') == 'mi' ? JText::_('PLG_IP_GP_MILE') : JText::_('PLG_IP_GP_KM');
        
        // load the javascript stub
        $document->addScript(JURI::root(true).'/plugins/iproperty/googleplaces/googleplaces.js');

        $gp_script  = "var gplacesOptions = {
                            distance: ".$distance.",
                            types: ".$t_string.",
                            language: '".$gplanguage."',
                            radius: ".$radius.",
                            radiustag: '".$radiustag."',
                            noresults: '".addslashes(JText::_('PLG_IP_GP_NORESULTS'))."'
                        }
                    jQuery(window).load(function($){
                        ipPropertyMap.doGplaces();
                    });"."\n";
        $document->addScriptDeclaration($gp_script);
        echo JHtmlBootstrap::addTab('ipDetails', 'ipgplacesplug', JText::_($this->params->get('tabtitle', 'Places')));
        ?>
        <table id="ipgplacestable" class="table table-striped">
            <thead>
                <tr>                       
                    <th width="25%"><?php echo JText::_('PLG_IP_GP_NAME'); ?></th>
                    <th width="25%" class="hidden-phone"><?php echo JText::_('PLG_IP_GP_LOCATION'); ?></th>
                    <th width="25%"><?php echo JText::_('PLG_IP_GP_TYPE'); ?></th>
                    <th width="25%" class="hidden-phone"><?php echo JText::_('PLG_IP_GP_DISTANCE'); ?></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="small" style="text-align: center;">                            
                    </td>
                </tr>  
            </tfoot>
        </table>     
        <?php 
        echo JHtmlBootstrap::endTab();
        return true;
    }
}
