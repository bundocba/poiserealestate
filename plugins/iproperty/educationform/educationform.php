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

class plgIpropertyEducationForm extends JPlugin
{
	public function __construct(&$subject, $config)  
    {
		parent::__construct($subject, $config);
        $this->loadLanguage();
    }

    public function onBeforeRenderForms($property, $settings)
    {
		if ($this->params->get('position')) return true;
        $this->_doEducationForm($property, $settings);
    }
	
	public function onAfterRenderForms($property, $settings)
    {
        if (!$this->params->get('position')) return true;
        $this->_doEducationForm($property, $settings);
    }    
    
	private function _doEducationForm($property, $settings)
	{
        $app        = JFactory::getApplication();
        
        if($app->getName() != 'site') return true;
        if(!$property->postcode && ( !$property->latitude || !$property->longitude )) return true;

        $values = array();

        $values['zip']			= $property->postcode;
        $values['latitude']		= $property->latitude;
        $values['longitude']    = $property->longitude;
        $values['key']			= 'dad04b84073a265e5244ba6db8892348';
        $values['radius']		= $this->params->get('radius', 1.5);
        $values['min']			= $this->params->get('minimum', 3);
        $values['city']         = $property->city;
        $values['state']        = ipropertyHTML::getStateCode($property->locstate);
        $max				    = $this->params->get('maximum', 5);
        $debug				    = $this->params->get('debug', 0);

        $i = 1;
        $result = $this->_getSchoolData($values);
        
        if(isset($result[0]['methodResponse']['faultString']) && $debug == 0) return true;
		
		echo JHtmlBootstrap::addTab('ipDetails', 'ipeducationplug', JText::_($this->params->get('tabtitle', 'PLG_IP_ED_SCHOOLS')));
            ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th width="25%">School Name</th>
                        <th width="25%" class="hidden-phone">Grade Level</th>
                        <th width="25%">Distance from Listing</th>
                        <th width="25%" class="hidden-phone">Enrollment</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if(isset($result[0]['methodResponse']['faultString'])) {
                        echo '<tr><td colspan="4" align="center"><b>Education.com Error:</b> '.$result[0]['methodResponse']['faultString'].'</td></tr>';
                        $no_results = true;
                    } elseif( count($result[0]['methodResponse']) < 1 ){
                        echo '<tr><td colspan="4" align="center"><b>No results were found.</b></td></tr>';
                        $no_results = true;
                    } else {
                        $k = 0;
                        foreach ($result[0]['methodResponse'] as $school) 
                        {
                            echo '
                            <tr>
                                <td><a href="'.$school['school']['url'].'" target="_blank">'.$school['school']['schoolname'].'</a></td>
                                <td class="hidden-phone">'.$school['school']['gradelevel'].'</td>
                                <td>'.round($school['school']['distance'], 2).' miles</td>
                                <td class="hidden-phone">'.$school['school']['enrollment'].'</td>
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
                            Schools provided by: <a href="http://www.education.com/schoolfinder/" target="_blank"><img src ="<?php echo $result[1]['methodResponse']['logosrc']; ?>" alt="" /></a><br />
                            <?php if(!$no_results) echo '<a href="'.$result[1]['methodResponse']['lsc'].'" target="_blank">See more information on '.$property->city.' schools from Education.com</a><br />'; ?>
                            <?php echo $result[1]['methodResponse']['disclaimer']; ?>
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
		$key	 = $values['key'];
		$radius	 = $values['radius'];
		$min	 = $values['min'];
		$lat	 = $values['latitude'];
		$lon	 = $values['longitude'];
		$zip	 = $values['zip'];
        $city    = urlencode($values['city']);
        $state   = $values['state'];

		$query_string = "";
		$query_string .= "key=" . $key;
		$query_string .= "&v=3";
		$query_string .= "&f=system.multiCall";
		$query_string .= "&resf=php";

		// do the school search
		$query_string .= "&methods[0][f]=schoolSearch";
		$query_string .= "&methods[0][sn]=sf";
		$query_string .= "&methods[0][key]=" . $key;
		if($lat != 0 && $lon != 0) {
			$query_string .= "&methods[0][latitude]=" . $lat;
			$query_string .= "&methods[0][longitude]=" . $lon;
			$query_string .= "&methods[0][distance]=" . $radius;
		} elseif (($lat = 0 && $lon = 0) && $zip != 0) {
			$query_string .= "&methods[0][zip]=" . $zip;
		}
		$query_string .= "&methods[0][minResult]=" . $min;
		$query_string .= "&methods[0][fid]=F1";

		// do the branding search
		$query_string .= "&methods[1][f]=gbd";
                $query_string .= "&methods[1][city]=" . $city;
                if($state) $query_string .= "&methods[1][state]=" . $state;
        $query_string .= "&methods[1][sn]=sf";
		$query_string .= "&methods[1][key]=" . $key;
		$query_string .= "&methods[1][fid]=F2";

		$result = $this->_curlContents($query_string);

		$schoolinfo = unserialize($result);

		return $schoolinfo;
	}
	
    private function _curlContents($u)
    {
    	$url = "http://api.education.com/service/service.php?" . $u;

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

