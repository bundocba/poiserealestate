<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');
class fwuserController extends JControllerLegacy
{
	function saveinfo(){
		jimport('joomla.user.user');
		$db =& JFactory::getDBO();
		$mainframe = JFactory::getApplication();
		$user=&JFactory::getUser();
		$user_id=$user->get('id');
		$user_email=$user->get('user_email');

		$Itemid		= JRequest::getVar( 'Itemid', 0 );
		$email		= JRequest::getVar( 'email', '' );
		$username	= JRequest::getVar( 'username', '' );
		$password	= JRequest::getVar( 'password', '' );
		$company	= JRequest::getVar( 'company', '' );
		$title		= JRequest::getVar( 'title', '' );
		$firstname	= JRequest::getVar( 'firstname', '' );
		$lastname	= JRequest::getVar( 'lastname', '' );
		$midlename	= JRequest::getVar( 'midlename', '' );
		$address	= JRequest::getVar( 'address', '' );
		$address1	= JRequest::getVar( 'address1', '' );
		$city		= JRequest::getVar( 'city', '' );
		$zip		= JRequest::getVar( 'zip', '' );
		$country	= JRequest::getVar( 'country', 0 );
		$state		= JRequest::getVar( 'state', 0 );
		$phone		= JRequest::getVar( 'phone', '' );
		$mobilephone= JRequest::getVar( 'mobilephone', '' );
		$fax		= JRequest::getVar( 'fax', '' );
		
		if($user_id==0||$user_id==''){
			$mainframe->redirect("index.php?option=com_fwuser&view=login&Itemid=".$Itemid,JText::_('PLEASE_LOGIN'),'error');
		}
		
		$query="select * from #__eap_user WHERE `user_id`='".$user_id."' ";
		$db->setQuery($query);
		$row_con=$db->loadObject();
		
		$query="select * from #__users where ( `email`='".$email."' AND `id`!='".$user_id."' ) ";
		$db->setQuery($query);
		$check_user_email=$db->loadObjectList();
		
		if((count($check_user_email)>0)){
			$mainframe->redirect("index.php?option=com_fwuser&view=info&Itemid=".$Itemid,JText::_('ASK_EMAIL_EXIST'),'error');
		}
		
		$query="select * from #__users where (`username`='".$username."' AND `id`!='".$user_id."' ) ";
		$db->setQuery($query);
		$check_user_email=$db->loadObjectList();
		
		if((count($check_user_email)>0)){
			$mainframe->redirect("index.php?option=com_fwuser&view=info&Itemid=".$Itemid,JText::_('ASK_USERNAME_EXIST'),'error');
		}
		
		// $full_img=JRequest::getVar('avatar',null,'files','array');
		// if($full_img[name]!=''){
			// if (!is_array($full_img) || $full_img['error'] || $full_img['size'] < 1 || !is_uploaded_file($full_img['tmp_name'])) {
				// $mainframe->redirect("index.php?option=com_fwuser&view=info&Itemid=".$Itemid,JText::_('ASK_IMAGE_WRONG'),'error');
			// }
			
			// $filename_temp 		= 'temp_'.uniqid().'.'.JFILE::getExt($full_img['name']);
			// $fileDestination 	= JPATH_SITE.DS.'images'.DS.'avatars'.DS.$filename_temp;
			// $filename 			= uniqid().'.'.JFILE::getExt($full_img['name']);
			// $fileThumbnail 		= JPATH_ROOT.DS.'images'.DS.'avatars'.DS.$filename;
			// $uploaded = JFile::upload($full_img['tmp_name'], $fileDestination);
			// if (!$uploaded) {
				// $mainframe->redirect("index.php?option=com_fwuser&view=info&Itemid=".$Itemid,'Please choose correct avatar','error');
			// }
			
			// resize
			// if($ex_img!='bmp'){
				// $width=150;
				// $height=0;
				// $crop=0;
				// $watermarkParams['create']	= 0;
				// $watermarkParams['x'] 		= 'left';
				// $watermarkParams['y']		= 'top';
				// $watermarkParams['file']	= JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_fwuser'.DS.'images'.DS.'icon-48-user.png';
				// $errorMsg='';
				// fwuserHelper::imageMagic($fileDestination, $fileThumbnail, $width , $height, $crop, null, $watermarkParams, 0, $errorMsg);
			// }else{
				// Jfile::copy($fileDestination,$fileThumbnail);
			// }
			// if(JFile::Exists($fileDestination)){
				// JFile::Delete($fileDestination);
			// }
			// if($row_con->avatar!=''){
				// JFile::delete(JPATH_SITE.DS.'images'.DS.'avatars'.DS.$row_con->avatar);
			// }
		// }
		
		// Save user Joomla
		$instance = JUser::getInstance($user_id);
		$acl = JFactory::getACL();
		$data=array();
		$data['id']				=$user_id;
		if($firstname!=''){
			$data['name']		=$firstname.' '.$midlename.' '.$lastname;
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

		$query = "UPDATE #__fwuser_user SET 
		`email`='".$email."', `username`='".$username."', `title`='".$title."', 
		`company`='".$company."', `firstname`='".$firstname."', 
		`lastname`='".$lastname."', `midlename`='".$midlename."', 
		`address`='".$address."', `address1`='".$address1."', 
		`city`='".$city."', `zip`='".$zip."', 
		`country`='".$country."', `state`='".$state."', 
		`phone`='".$phone."', `mobilephone`='".$mobilephone."', 
		`fax`='".$fax."' 
		WHERE email=".$user_email;
		$db->setQuery( $query );
		$db->query();
		$mainframe->redirect("index.php?option=com_fwuser&view=info&Itemid=".$Itemid,JText::_('ASK_UPDATE_SUCCESS'));
	}
}