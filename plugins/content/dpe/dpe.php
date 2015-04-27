<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

// No direct access allowed to this file
defined('_JEXEC' ) or die( 'Restricted access');

// Import Joomla! Plugin library file
jimport('joomla.plugin.plugin');

class plgContentDpe extends JPlugin
{
    public function __construct( &$subject, $params )
    {
        parent::__construct( $subject, $params );
        $this->loadLanguage();
    }

    private function processDpe($match)
    {
        // find dpe statement
        //preg_match( $regex, $row->text, $match );

        // create array to strip out non-critical data from dpe string
        $reparray = array('{', '}', 'dpe_energie');

        // isolate number data
        $var = explode(',', str_replace($reparray, '', $match[0]));

        $energy_var = trim($var[0]);
        $climat_var = trim($var[1]);
        
        // check to validate that both values are accounted for and not both null, return without displaying graph
        if(($energy_var == 'null' && $climat_var == 'null') || (!isset($energy_var) && !isset($climat_var))){
            $row->text = preg_replace( $regex, '', $row->text );
            return true;
        }

        // create the graphs and table html
        $graph = $this->drawGraph($energy_var, $climat_var);
        return $graph;
    }

    public function onContentPrepare( $context, &$row, &$params, $page=0 )
    {
        // if there's no DPE data then return to save processing time
        if ( (JString::strpos( $row->text, 'dpe_energie' ) === false) && (JString::strpos( $row->text, 'dpe_climat' ) === false) ) {
            return true;
        }else if( !$this->params->get( 'enabled', 1 ) ) {
            $row->text = preg_replace( $regex, '', $row->text );
            return true;
        }

        // dpe statement regex
        $regex      = '#{dpe_energie (.*?)}#s';
        $row->text  = preg_replace_callback($regex, array('self', 'processDpe'), $row->text);
    }

    private function drawGraph($energy, $climate)
    {
        $dstyle = 'padding: 0 3px; line-height: 20px; margin-bottom: 2px; height: 20px;';
        $cspan = (($energy && $energy != 'null') && ($climate && $climate != 'null')) ? 'span6' : 'span12';

        $e_measurement = ($this->params->get('energy_measurement', '')) ? ' ('.JText::_($this->params->get('energy_measurement')).')' : '';
        $c_measurement = ($this->params->get('climate_measurement', '')) ? ' ('.JText::_($this->params->get('climate_measurement')).')' : '';
        
        $dpe_display = '';

        $dpe_display .= '<div class="row-fluid span12">';
                            
                            if(isset($energy) && $energy != 'null'){
                                $r_energy = round($energy);
                                if($r_energy <= 50){
                                    $e_height = 0;
                                }elseif(in_array($r_energy, range(51,90))){
                                    $e_height = 22;
                                }elseif(in_array($r_energy, range(91,150))){
                                    $e_height = 44;
                                }elseif(in_array($r_energy, range(151,230))){
                                    $e_height = 66;
                                }elseif(in_array($r_energy, range(231,330))){
                                    $e_height = 88;
                                }elseif(in_array($r_energy, range(331, 450))){
                                    $e_height = 110;
                                }elseif($r_energy >= 450){
                                    $e_height = 132;
                                }
                                $dpe_display .= '<div class="'.$cspan.' ip-dpe-container">
                                                    <div class="ip-dpe-header"><b>'.JText::_($this->params->get('header_energy', '')).$e_measurement.'</b></div>
                                                    <div class="clearfix"></div>
                                                    <div class="ip-dpe-energy-container" style="position: relative;">
                                                        <div style="'.$dstyle.' background: #00833d; position: relative; width: 20%;" class="ip_dpe_item e_a">(<50) <span style="float: right;">A</span></div>
                                                        <div style="'.$dstyle.' background: #1bb053; position: relative; width: 30%;" class="ip_dpe_item e_b">(51 Ã  90) <span style="float: right;">B</span></div>
                                                        <div style="'.$dstyle.' background: #8cc540; position: relative; width: 40%;" class="ip_dpe_item e_c">(91 Ã  150) <span style="float: right;">C</span></div>
                                                        <div style="'.$dstyle.' background: #ffc909; position: relative; width: 50%;" class="ip_dpe_item e_d">(151 Ã  230) <span style="float: right;">D</span></div>
                                                        <div style="'.$dstyle.' background: #faad67; position: relative; width: 60%;" class="ip_dpe_item e_e">(231 Ã  330) <span style="float: right;">E</span></div>
                                                        <div style="'.$dstyle.' background: #f48221; position: relative; width: 70%;" class="ip_dpe_item e_f">(331 Ã  450) <span style="float: right;">F</span></div>
                                                        <div style="'.$dstyle.' background: #ed1b24; position: relative; width: 80%;" class="ip_dpe_item e_g">(>451) <span style="float: right;">G</span></div>
                                                        <div style="'.$dstyle.' position: absolute; top: ' . $e_height . 'px; right: 0px; width: 10%; background: #ccc; text-align: center;" class="ip_dpe_marker m_energy">'.$energy.'</div>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                    <div class="ip-dpe-footer small">'.JText::_($this->params->get('footer_energy', '')).'</div>
                                                </div>';
                            }

                            if(isset($climate) && $climate != 'null'){
                                $r_climate = round($climate);
                                if($r_climate <= 5){
                                    $c_height = 0;
                                }elseif(in_array($r_climate, range(6,10))){
                                    $c_height = 22;
                                }elseif(in_array($r_climate, range(11,20))){
                                    $c_height = 44;
                                }elseif(in_array($r_climate, range(21,35))){
                                    $c_height = 66;
                                }elseif(in_array($r_climate, range(36,55))){
                                    $c_height = 88;
                                }elseif(in_array($r_climate, range(56,80))){
                                    $c_height = 110;
                                }elseif($r_climate >= 80){
                                    $c_height = 132;
                                }
                                $dpe_display .= '<div class="'.$cspan.' ip-dpe-container" valign="top">
                                                    <div class="ip-dpe-header"><b>'.JText::_($this->params->get('header_climate', '')).$c_measurement.'</b></div>
                                                    <div class="clearfix"></div>
                                                    <div class="ip-dpe-climate-container" style="position: relative;">
                                                        <div style="'.$dstyle.' background: #75ccf7; position: relative; width: 20%;" class="ip_dpe_item c_a">(<5) <span style="float: right;">A</span></div>
                                                        <div style="'.$dstyle.' background: #22b5eb; position: relative; width: 30%;" class="ip_dpe_item c_b">(6 Ã  10) <span style="float: right;">B</span></div>
                                                        <div style="'.$dstyle.' background: #099ad7; position: relative; width: 40%;" class="ip_dpe_item c_c">(11 Ã  20) <span style="float: right;">C</span></div>
                                                        <div style="'.$dstyle.' background: #0079c2; position: relative; width: 50%;" class="ip_dpe_item c_d">(21 Ã  35) <span style="float: right;">D</span></div>
                                                        <div style="'.$dstyle.' background: #bbbcbe; position: relative; width: 60%;" class="ip_dpe_item c_e">(36 Ã  55) <span style="float: right;">E</span></div>
                                                        <div style="'.$dstyle.' background: #a1a0a5; position: relative; width: 70%;" class="ip_dpe_item c_f">(56 Ã  80) <span style="float: right;">F</span></div>
                                                        <div style="'.$dstyle.' background: #818086; position: relative; width: 80%;" class="ip_dpe_item c_g">(>80) <span style="float: right;">G</span></div>
                                                        <div style="'.$dstyle.' position: absolute; top: ' . $c_height . 'px; right: 0px; width: 10%; background: #ccc; text-align: center;" class="ip_dpe_marker m_climate">'.$climate.'</div>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                    <div class="ip-dpe-footer small">'.JText::_($this->params->get('footer_climate', '')).'</div>
                                                </div>';
                            }
        $dpe_display .= '
                            </div>';

        return $dpe_display;
    }
}