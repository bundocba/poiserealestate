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
jimport('joomla.filesystem.folder');
jimport( 'joomla.utilities.utility' );

class IpropertyControllerFileAgentSubmit extends JControllerForm
{
	protected $view_item = 'fileform';
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
        
        // Check if the user should be in this editing area
        $auth   = new ipropertyHelperAuth();
        $allow  = $auth->canAddFile();
        
        return $allow;
    }
    
    protected function allowEdit($data = array(), $key = 'id')
	{        
        $allow  = parent::allowEdit($data, $key);
        
        // Check if the user should be in this editing area
        $recordId	= (int) isset($data[$key]) ? $data[$key] : 0;
        $auth   = new ipropertyHelperAuth();
        $allow  = $auth->canEditFile($recordId);

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

	public function &getModel($name = 'fileagentform', $prefix = '', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$app    = JFactory::getApplication();

        // Need to override the parent method completely.
		$tmpl		= $app->input->getCmd('tmpl');
		$layout		= $app->input->getCmd('layout', 'edit');
		$append		= '';

		// Setup redirect info.
		if ($tmpl) {
			$append .= '&tmpl='.$tmpl;
		}

		$append .= '&layout=edit';

		if ($recordId) {
			$append .= '&'.$urlVar.'='.$recordId;
		}

		$itemId	= $app->input->getInt('Itemid');
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
        
        public function getTitle($id){
            
            $db = JFactory::getDBO();
            $query="select title from #__iproperty_file where id= ".$id;
            $db->setQuery($query);
            $title = $db->loadResult();
            
            return $title;
            
        }

                public function savefie(){
           
            
            require_once JPATH_ADMINISTRATOR.'/components/com_iproperty/models/fileagent.php';
            $db = JFactory::getDBO();
            $modelgallery = new IpropertyModelFileAgent();
            $post = JRequest::get("post");
           
            $arrname = $_FILES["propertyfile"]["name"];
            $arrtype = $_FILES["propertyfile"]["type"];
            $arrtmp_name = $_FILES["propertyfile"]["tmp_name"];
            $date = date('m-d-Y H:i:s');
            
            if(Jfolder::exists(JPATH_SITE.'/media/com_iproperty/agentsubmit')==false){
			Jfolder::create(JPATH_SITE.'/media/com_iproperty/agentsubmit');
			
            }
         $k = 0;
            for($i=0; $i<count($_FILES['fileagent']['name']); $i++) {
                 //Get the temp file path
                 $tmpFilePath = $_FILES['fileagent']['tmp_name'][$i];
                 $k++;
                 //Make sure we have a filepath
                 if ($tmpFilePath != ""){
                   //Setup our new file path
                    
                    $test =  $tmpFilePath;
                     
                   $newFilePath 	= JPATH_SITE.'/media/com_iproperty/agentsubmit/'.$_FILES["fileagent"]["name"][$i];
                   $path = 'media/com_iproperty/agentsubmit/';
                   //Upload the file into the temp dir
                  /*if(move_uploaded_file($tmpFilePath, $newFilePath)) {

                     //Handle other code here

                   }*/
                   $stracttach .= $path.$_FILES["fileagent"]["name"][$i] .",";
                   
                    $uploaded = JFile::upload($tmpFilePath, $newFilePath);
                    $ext                    = strtolower( strrchr($_FILES["fileagent"]["name"][$i],'.'));
                    //store data in database
                     $pic = $modelgallery->getTable();

                     $title = str_replace(".pdf", "", $_FILES["fileagent"]["name"][$i]);
                     
                     $pic->title		= $this->getTitle($_POST["title".$k]);
                     
                     $pic->description          = $pic->title;
                     $pic->fname		= $_FILES["fileagent"]["name"][$i];
                     $pic->type			= $ext;
                     $pic->path			= $path;
                     
                     $pic->owner		= $user->id;
                     $pic->createdate		= $date;
                     $pic->ordering		= 0;
                     $pic->state         = 1;
                     $pic->title         = trim(preg_replace( '/\s+/', ' ', $pic->title));
                     $pic->description   = trim(preg_replace( '/\s+/', ' ', $pic->description));
                     $pic->fname         = trim(preg_replace( '/\s+/', ' ', $pic->fname));
                     $pic->type          = trim(preg_replace( '/\s+/', ' ', $pic->type));
                     
                     
                    // print_r($pic);die;
                     if (!$pic->check()) {
                        die("check error"); //return ipropertyHTML::createReturnObject('error', 'FAILED TO STORE IMAGE: '.$pic->getError());
                     }
                     if (!$pic->store()) {
                        die("Save error"); //return ipropertyHTML::createReturnObject('error', 'FAILED TO STORE IMAGE: '.$pic->getError());
                     }
                 }
               }
               $test1 = 'Form1.pdf';
               $arracttach = split(",", $stracttach);
               //$a =  array_pop($arracttach);
               if(count($arracttach) >= 1 )
                   unset($arracttach[count($arracttach)-1]);
               
               //send email to admin.
               require_once JPATH_COMPONENT .'/helpers/property.php';
               
               $user = JFactory::getUser();
               $config =& JFactory::getConfig();
               
               $name_active=$config->get('fromname');
                $email_active=$config->get('mailfrom');	
               
               $query = "SELECT * FROM #__iproperty_agents WHERE user_id=".$user->id;
                $db->setQuery($query);
                $agent = $db->loadObjectList();
               $subject = "Submit Form";
                $body = 'Agent Name: '.$agent[0]->fname .'<br>';
                $body .= 'CEA Number: '.$agent[0]->id .'<br>';
                $body .= '<br>';
                $body .= '<br>';
                //$body .= 'Form Submitted:  '.$agent[0]->id .'<br>';
                $body .= 'Attachment:  <br>';
                $body .= 'Date Submitted:'. $date.'  <br>';
                $body .= 'Thanks  <br>';
                
               //send mail
                IpropertyHelperProperty::sendMail($email_active, $subject, $agent[0]->email, $subject, $body,$arracttach);
                //JUtility::sendMail($from, $fromname, $recipient, $subject, $body, $mode=0, $cc=null, $bcc=null, $attachment, $attachmentname, $replyto=null, $replytoname=null);
               //redirect
                $mag = "";
                $link = 'index.php?option=com_iproperty&view=fileagent&layout=confirm';
                $this->setRedirect($link, $msg);
             
        }
}

