<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access');
jimport('joomla.application.component.controllerform');

class IpropertyControllerPropAgentEdit extends JControllerForm
{
	protected $view_item = 'propagentedit';
    protected $view_list = 'manage';

	public function add()
	{
		if (!parent::add()) {
			// Redirect to the return page.
			$this->setRedirect($this->getReturnPage());
		}
	}
    
    protected function allowAdd($data = array())
    {
        $allow  = parent::allowAdd($data);
        $post = JRequest::get("post");
      $user       = JFactory::getUser();
        // Check if the user should be in this editing area
        $auth   = new ipropertyHelperAuth();
        $allow  = $auth->canAddProp();
        
        $maxid = $this->getMaxId($data["property_name"]);
        //insert image in database
       require_once JPATH_ADMINISTRATOR.'/components/com_iproperty/models/gallery.php';
       
       $modelgallery = new IpropertyModelGallery();
      
       
       $arrname = $_FILES["propertyfile"]["name"];
       $arrtype = $_FILES["propertyfile"]["type"];
       $arrtmp_name = $_FILES["propertyfile"]["tmp_name"];
       
       for($i=0; $i<count($_FILES['propertyfile']['name']); $i++) {
            //Get the temp file path
            $tmpFilePath = $_FILES['propertyfile']['tmp_name'][$i];

            //Make sure we have a filepath
            if ($tmpFilePath != ""){
              //Setup our new file path
             
              $newFilePath 	= JPATH_SITE.'/media/com_iproperty/agent/'.$_FILES["propertyfile"]["name"][$i];
              $path = '/media/com_iproperty/agent/';
              //Upload the file into the temp dir
             /*if(move_uploaded_file($tmpFilePath, $newFilePath)) {

                //Handle other code here

              }*/
               $uploaded = JFile::upload($tmpFilePath, $newFilePath);
               $ext                    = strtolower( strrchr($_FILES["propertyfile"]["name"][$i],'.'));
               //store data in database
                $pic = $modelgallery->getTable();
                
                $pic->title			= isset($cfg['title']) ? trim($cfg['title']) : '';
                $pic->description	= isset($cfg['description']) ? trim($cfg['description']) : '';
                $pic->fname			= $_FILES["propertyfile"]["name"][$i];
                $pic->type			= $ext;
                $pic->path			= $path;
		$pic->propid		= (int) $maxid;
                $pic->owner			= $user->id;
                $pic->ordering		= 0;
                $pic->state         = 1;
                $pic->title         = trim(preg_replace( '/\s+/', ' ', $pic->title));
                $pic->description   = trim(preg_replace( '/\s+/', ' ', $pic->description));
                $pic->fname         = trim(preg_replace( '/\s+/', ' ', $pic->fname));
                $pic->type          = trim(preg_replace( '/\s+/', ' ', $pic->type));
               // print_r($pic);die;
                if (!$pic->check()) {
                   //die("chekc"); //return ipropertyHTML::createReturnObject('error', 'FAILED TO STORE IMAGE: '.$pic->getError());
                }
                if (!$pic->store()) {
                   //die; //return ipropertyHTML::createReturnObject('error', 'FAILED TO STORE IMAGE: '.$pic->getError());
                }
            }
          }
        
       
        //print_r($_FILES);die;
        /* return $allow; */        return true;
          //$this->setRedirect("index.php?option=com_iproperty&view=agentproperty&Itemid=219");
    }
    protected function rearrange($arr){
        foreach( $arr as $key => $all ){
            foreach( $all as $i => $val ){
                $new[$i][$key] = $val;    
            }    
        }
        return $new;
    }
    
    function reArrayFiles(&$file_post) {

        $file_ary = array();
        $file_count = count($file_post['name']);
        $file_keys = array_keys($file_post);

        for ($i=0; $i<$file_count; $i++) {
            foreach ($file_keys as $key) {
                $file_ary[$i][$key] = $file_post[$key][$i];
            }
        }

        return $file_ary;
    }
    
    protected function getMaxId($property_name=""){
        
        $db = JFactory::getDbo();
        $query = "SELECT max(id) FROM `tdvmh_ipropertyagent` WHERE `property_name`='".$property_name."'";
        $db->setQuery($query);
        $max_id = $db->loadResult();
        
        return $max_id;
    }
    
    protected function allowEdit($data = array(), $key = 'id')
	{        
        $allow  = parent::allowEdit($data, $key);
        
        // Check if the user should be in this editing area
        $recordId	= (int) isset($data[$key]) ? $data[$key] : 0;
        $auth   = new ipropertyHelperAuth();
        $allow  = $auth->canEditProp($recordId);

        return $allow;
	}
    
	public function cancel($key = 'id')
	{
		parent::cancel($key);

		// Redirect to the return page.
		$this->setRedirect($this->getReturnPage());
	}

	public function edit($key = null, $urlVar = 'id')
	{
        $result = parent::edit($key, $urlVar);

		return $result;
	}

	public function &getModel($name = 'propagentform', $prefix = '', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		// Need to override the parent method completely.
		$tmpl		= JRequest::getCmd('tmpl');
		$layout		= JRequest::getCmd('layout', 'edit');
		$append		= '';

		// Setup redirect info.
		if ($tmpl) {
			$append .= '&tmpl='.$tmpl;
		}

		$append .= '&layout=edit';

		if ($recordId) {
			$append .= '&'.$urlVar.'='.$recordId;
		}

		$itemId	= JRequest::getInt('Itemid');
		$return	= $this->getReturnPage();

		if ($itemId) {
			$append .= '&Itemid='.$itemId;
		}

		if ($return) {
			$append .= '&return='.base64_encode($return);
		}

		return $append;
	}

	protected function getReturnPage()
	{
		//$return = JFactory::getApplication()->input->get('return', null, 'default', 'base64');
        $return = $this->input->get('return', null, 'base64');

		if (empty($return) || !JUri::isInternal(base64_decode($return))) {
			return JURI::base();
		}
		else {
			return base64_decode($return);
		}
	}
    
    /*protected function getReturnPage()
	{
        $return = $this->input->get('return', null, 'base64');

		if ($return) return base64_decode($return);
        
        return JURI::base();
	}*/
    
    protected function postSaveHook(JModelLegacy $model, $validData = array())
	{
        $app = JFactory::getApplication();
        $model->saveMids($validData);
        if($msg = $model->autoPublishCheck($validData)){
            $app->enqueueMessage($msg);
        }
	}

	public function save($key = null, $urlVar = 'id')
	{
		$task   = $this->getTask();
        $result = parent::save($key, $urlVar);

		// If ok, redirect to the return page.
		if ($result && $task != 'apply') {
			$this->setRedirect($this->getReturnPage());
		}

		return $result;
	}
}
