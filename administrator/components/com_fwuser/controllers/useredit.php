<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controllerform');
class fwuserControlleruserEdit extends JControllerForm{
	public function __construct($config = array()){
		parent::__construct($config);		
		$this->view_list = 'user';
	}
	public function save(){
		$db = JFactory::getDBO();
		$id = JRequest::getVar('id', 0);
		$return = 'index.php?option=com_fwuser&task=useredit.edit&id=' . $id;

		$post = JRequest::get( 'post' );
		$name		= $post['jform']['firstname'].' '.$post['jform']['midlename'].' '.$post['jform']['lastname'];
		$username	= $post['jform']['username'];
		$email		= $post['jform']['email'];
		$password	= $post['jform']['password'];
		$re_password= $post['jform']['re_password'];
		$published	= $post['jform']['published'];
		
		//USER
		if($id==''||$id==0){	
			$query="select id from #__users where `username`='".$username."' OR `email`='".$email."' ";
			$db->setQuery($query);
			$check_user_email=$db->loadObjectList();
			
			if(count($check_user_email)>0){
				$msg=JText::_('COM_FWUSER_USER_ALERT_EMAIL_EXIST');
				$this->setRedirect($return,$msg,'error');
				return false;
			}
			
			if($password==''){
				$msg=JText::_('COM_FWUSER_USER_ALERT_PASSWORD_EMPTY');
				$this->setRedirect($return,$msg,'error');
				return false;
			}
			
			if($password!=$re_password){
				$msg=JText::_('COM_FWUSER_USER_ALERT_PASSWORD_MACTCH');
				$this->setRedirect($return,$msg,'error');
				return false;
			}
			
			// Save user Joomla
			$instance = JUser::getInstance(0);
			$acl = JFactory::getACL();
			$data=array();
			$data['id']				=0;
			$data['sendEmail']		=1;
			$data['groups']			=array(2);
			if($published==0){
				$block=1;
			}else{
				$block=0;
			}
			$data['block']			=$block;
			if($name!=''){
				$data['name']		=$name;
			}
			if($username!=''){
				$data['username']	=$username;
			}
			if($password!=''){
				$data['password']	=$password;
				$data['password2']	=$password;
			}
			if($email!=''){
				$data['email']		=$email;
			}
			if (!$instance->bind($data)) {
			//	$this->setRedirect($return,$instance->getError(),'error');
			}
			if (!$instance->save()) {
			//	$this->setRedirect($return,$instance->getError(),'error');
			}
			$query="select id from #__users ORDER BY id DESC";
			$db->setQuery($query);
			$max_id=$db->loadObject();
			$max_id=$max_id->id;
			
			$_POST['jform']['user_id'] = $max_id;
			$_POST['jform']['date'] = date("Y-m-d H:i:s");
		}else{
			$query="select * from #__fwuser_user WHERE `id`='".$id."' ";
			$db->setQuery($query);
			$row_con=$db->loadObject();
			$user_id=$row_con->user_id;
			
			$query="select id from #__users where (`username`='".$username."' OR `email`='".$email."') AND ( `id`!='".$user_id."' ) ";
			$db->setQuery($query);
			$check_user_email=$db->loadObjectList();
			
			if(count($check_user_email)>0){
				$msg=JText::_('COM_FWUSER_USER_ALERT_EMAIL_EXIST');
				$this->setRedirect($return,$msg,'error');
				return false;
			}
			
			if($password!=$re_password){
				$msg=JText::_('COM_FWUSER_USER_ALERT_PASSWORD_MACTCH');
				$this->setRedirect($return,$msg,'error');
				return false;
			}
			
			// Save user Joomla
			$instance = JUser::getInstance($user_id);
			$acl = JFactory::getACL();
			$data=array();
			$data['id']				=$user_id;
			if($published==0){
				$block=1;
			}else{
				$block=0;
			}
			$data['block']			=$block;
			if($name!=''){
				$data['name']		=$name;
			}
			if($username!=''){
				$data['username']	=$username;
			}
			if($password!=''){
				$data['password']	=$password;
				$data['password2']	=$password;
			}
			if($email!=''){
				$data['email']		=$email;
			}
			if (!$instance->bind($data)) {
			//	$this->setRedirect($return,$instance->getError(),'error');
			}
			if (!$instance->save()) {
			//	$this->setRedirect($return,$instance->getError(),'error');
			}
		}
		
		//FILE
		if(Jfolder::exists(JPATH_ROOT.DS.'images'.DS.'avatars')==false){
			Jfolder::create(JPATH_ROOT.DS.'images'.DS.'avatars'.DS);
			Jfile::copy(JPATH_ROOT.DS.'images'.DS.'index.html',JPATH_ROOT.DS.'images'.DS.'avatars'.DS.'index.html');
			Jfile::copy(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_fwuser'.DS.'images'.DS.'default.jpg',JPATH_ROOT.DS.'images'.DS.'avatars'.DS.'default.jpg');
		}
		
		$file = JRequest::get( 'FILES' );
		$file = $file['jform'];
		$filename = 'avatar';
		$uploadFile = array();
		if (!empty($file['tmp_name'][$filename])) {
			foreach ($file as $key => $value) {
				$uploadFile[$key] = $value[$filename];	
			}			
			if (!is_array($uploadFile) || $uploadFile['error'] || $uploadFile['size'] < 1 || !is_uploaded_file($uploadFile['tmp_name'])) {
				$this->setRedirect( $return, JText::_("COM_FWUSER_USER_ALERT_WRONGIMG") );
				return false;
			}
			
			$filename_temp 		= 'temp_'.uniqid().'.'.JFILE::getExt($uploadFile['name']);
			$fileDestination 	= JPATH_SITE.DS.'images'.DS.'avatars'.DS.$filename_temp;
			$filename 			= uniqid().'.'.JFILE::getExt($uploadFile['name']);
			$fileThumbnail 		= JPATH_ROOT.DS.'images'.DS.'avatars'.DS.$filename;
			$uploaded = JFile::upload($uploadFile['tmp_name'], $fileDestination);
			if (!$uploaded) {
				$this->setRedirect( $return, JText::_("COM_FWUSER_USER_ALERT_WRONGIMG") );
				return false;
			}
			
			//resize
			if($ex_img!='bmp'){
				$width=150;
				$height=0;
				$crop=0;
				$watermarkParams['create']	= 0;
				$watermarkParams['x'] 		= 'left';
				$watermarkParams['y']		= 'top';
				$watermarkParams['file']	= JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_fwuser'.DS.'images'.DS.'icon-48-user.png';
				$errorMsg='';
				fwuserHelper::imageMagic($fileDestination, $fileThumbnail, $width , $height, $crop, null, $watermarkParams, 0, $errorMsg);
			}else{
				Jfile::copy($fileDestination,$fileThumbnail);
			}
			if(JFile::Exists($fileDestination)){
				JFile::Delete($fileDestination);
			}
			if($id){
				$model = $this->getModel();
				$item = $model->getItem($id);
				JFile::delete(JPATH_SITE.DS.'images'.DS.'avatars'.DS.$item->avatar);
			}
			$_POST['jform']['avatar'] = $filename;
		}

		return parent::save();
	}
}