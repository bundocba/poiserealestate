<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
class fwuserViewRegister extends JViewLegacy{
	function display($tpl = null){
		jimport('joomla.user.user');
		$db =& JFactory::getDBO();
		$mainframe = JFactory::getApplication();

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
		
		$config =& JFactory::getConfig();
		$name_active=$config->get('fromname');
		$email_active=$config->get('mailfrom');
		
		$params=&$mainframe->getParams('com_users');
		
		if (!is_object($params)) {
			$params = &JComponentHelper::getParams('com_users');
		}
		$kind_active=$params->get( 'useractivation' );
		
		if($kind_active==0){
			$block=0;
			$published=1;
		}else if($kind_active==1){
			$block=1;
			$published=0;
		}else if($kind_active==2){
			$block=1;
			$published=0;
		}
		
		$query="select * from #__users where `email`='".$email."' ";
		$db->setQuery($query);
		$check_user_email=$db->loadObjectList();
		
		if(count($check_user_email)>0){
			echo 'email';
			die;
		}
		
		$query="select * from #__users where `username`='".$username."' ";
		$db->setQuery($query);
		$check_user_email=$db->loadObjectList();
		
		if(count($check_user_email)>0){
			echo 'username';
			die;
		}
		
		if($password==''){
			echo 'password';
			die;
		}
		
		$name=$firstname.' '.$midlename.' '.$lastname;
		
		// Save user Joomla
		$instance = JUser::getInstance(0);
		$acl = JFactory::getACL();
		$data=array();
		$data['id']				=0;
		$data['sendEmail']		=1;
		$data['groups']			=array(2);
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
		$date=date("Y-m-d H:i:s");
		
		$sql="INSERT INTO #__fwuser_user 
		(`user_id`,`username`,`email`,`firstname`
		,`lastname`,`midlename`,`address`,`address1`
		,`city`,`zip`,`country`,`state`
		,`phone`,`mobilephone`,`fax`,`company`
		,`title`,`date`,`published`) 
		VALUES ('$max_id','$username','$email','$firstname'
		,'$lastname','$midlename','$address','$address1'
		,'$city','$zip','$country','$state'
		,'$phone','$mobilephone','$fax','$company'
		,'$title','$date','$published')";
		$db->setQuery($sql);
		$db->query();
		
		if($kind_active==0){
			echo 'ok_login';
			die;
		}else if($kind_active==1){
			$_link=JURI::root().'index.php?option=com_fwuser&view=activeregister&secu='.MD5($max_id).'&format=raw';
			
			$body ='Hi, new user!<br>';
			$body.='You are new user of our website, please click on the link to activate your account:<br>';			
			$body.='<a href="'.$_link.'" target="_blank">'.$_link.'</a><br>';
			$body.='If you have any question, please contact us!<br><br>';
			$body.='Best regard.';
			fwuserHelper::sendMail( $email, $name_active, $email_active, 'Activate your account', $body );
			echo 'ok_mail';
			die;
		}else if($kind_active==2){
			echo 'ok_admin';
			die;
		}
	}
}