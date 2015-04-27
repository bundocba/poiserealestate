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

class plgIpropertyFindmyschooluk extends JPlugin
{
    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
        $this->loadLanguage();
    }

    public function onBeforeRenderForms($property, $settings)
    {
		if ($this->params->get('position')) return true; 
		$this->_doFindmySchool($property, $settings);
    }
	
	public function onAfterRenderForms($property, $settings)
    {
        if (!$this->params->get('position')) return true;
        $this->_doFindmySchool($property, $settings);
    } 
    
    private function _doFindmySchool($property, $settings)
    {
        $app = JFactory::getApplication();

        if($app->getName() != 'site') return true;
        if(!$property->postcode) return true;

        $values = array();

        $values['zip']          = $property->postcode;
        $values['key']          = $this->params->get('apikey', false);
        $values['radius']       = $this->params->get('radius', 1.5);
        $values['type']         = $this->params->get('type', 0);
        $min                    = $this->params->get('minimum', 2);
        $max                    = $this->params->get('maximum', 5);
        $debug                  = $this->params->get('debug', 0);

        $i = 1;
        $result = $this->_getSchoolData($values);
        
        if(($result->result != 'OK') && $debug == 0) return true;

        // set the min / max values
        $schools = $result->establishments->establishment;
        $count = count($result->establishments->establishment);

        if ($count < $min && $count > 0 && $debug == 0) return true; // return if we have < min results
        
        echo JHtmlBootstrap::addTab('ipDetails', 'ipfindmyschoolukplug', JText::_($this->params->get('tabtitle', 'PLG_IP_FMS_SCHOOLS')));
		?>
		<table class="table table-striped">
                <thead>
                    <tr>
                        <th width="25%">School Name</th>
                        <th width="25%" class="hidden-phone">Grade Level</th>
                        <th width="25%">Distance from Listing</th>
                        <th width="25%" class="hidden-phone">Last Inspected</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->result != 'OK') {
                        echo '<tr><td colspan="4" align="center"><b>Findmyschool.co.uk Error:</b> '.$result->result.'</td></tr>';
                        $no_results = true;
                    } elseif( $count < 1 ){
                        echo '<tr><td colspan="4" align="center"><b>No results were found.</b></td></tr>';
                        $no_results = true;
                    } else {
                        $k = 0;
                        foreach ($schools as $school) 
                        {
                            echo '
                            <tr>
                                <td><a href="'.$school->establishment_url.'" target="_blank">'.$school->establishment_name.'</a></td>
                                <td class="hidden-phone">'.$school->establishment_type.'</td>
                                <td>'.round($school->establishment_distance_from_target, 2).' km</td>
                                <td class="hidden-phone">'.$school->establishment_last_inspected.'</td>
                            </tr>';

                            if ($i >= $max) break;
                            $i++;
                            $k = 1 - $k;
                            $no_results = false;
                        }

                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="small" style="text-align: center;">
                            Schools information provided by: <a href="http://www.findmyschool.co.uk/" target="_blank">www.findmyschool.co.uk</a><br />
                        </td>
                    </tr>  
                </tfoot>
            </table> 
			<?php
        echo JHtmlBootstrap::endTab();
		
		return true;	
	}	

    private function _getSchoolData($values)
    {
        $key     = $values['key'];
        $radius  = $values['radius'];
        $type    = $values['type'];
        $zip     = $values['zip'];

        $query_string = "";
        $query_string .= "key=" . urlencode($key);
        $query_string .= "&range=" . $radius;
        $query_string .= "&postcode=" . urlencode($zip);
        $query_string .= $type ? "&type=" . $type : '';

        try {
            $result = new SimpleXMLElement($this->_curlContents($query_string));
        } catch(Exception $e) {
            return false;
        }

        return $result;
    }

    private function _curlContents($u)
    {
        $url = "http://www.findmyschool.co.uk/api/search.aspx?" . $u;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
        curl_setopt($ch, CURLOPT_URL, $url);

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }
} // end class

?>

