<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
jimport('joomla.application.component.controllerform');

class IpropertyControllerTraining extends JControllerForm
{
	protected $text_prefix = 'COM_IPROPERTY';

    protected function allowAdd($data = array())
	{
        $allow  = parent::allowAdd($data);
        
        // Check if the user should be in this editing area
        $auth   = new ipropertyHelperAuth();
        $allow  = $auth->canAddTraining();
        
        return $allow;
	}

    protected function allowEdit($data = array(), $key = 'id')
    {
        $allow  = parent::allowEdit($data, $key);

        // Check if the user should be in this editing area
        $recordId	= (int) isset($data[$key]) ? $data[$key] : 0;
        $auth   = new ipropertyHelperAuth();
        $allow  = $auth->canEditTraining($recordId);

        return $allow;
    }
    
    public function register($cid = 0,$userid = 0){
        
        //training id 
     
         $db = JFactory::getDBO();
       
        
        //check exist
        if($this->checkExist()){
            //update data
            $query = "Update from #__iproperty_training_detail set register=0 where trainingid=".$cid." and userid=".$userid;
            
        }else{
            //insert data
            $query = "insert into #__iproperty_training_detail(trainingid,userid,register)values(".$cid.",".$userid.",1)";
        }
        
        $db->setQuery($query);
        
        $count = $db->execute();
        
        return $count;
    }
    
    protected function checkExist($cid = 0,$userid = 0){
        
        $db = JFactory::getDBO();
        $query="select id from #__iproperty_training_detail where trainingid=".$cid." and userid=".$userid." ORDER BY id DESC";
        $db->setQuery($query);
        
        $result = $db->execute();
        
        return $result;
    }
    
}
?>
