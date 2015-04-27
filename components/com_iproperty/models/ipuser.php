<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
jimport('joomla.application.component.model');

class IpropertyModelIPuser extends JModelLegacy
{
	var $_id            = null;
	var $_properties    = null;
    var $_searches      = null;
	
	public function __construct()
	{
		parent::__construct();
	}

	public function getIpProperties()
	{
        $user       = JFactory::getUser();
        $user_id    = $user->id;
        $settings   = ipropertyAdmin::config();
        
		// Lets load the content if it doesn't already exist
		if( empty($this->_properties)){
            // Filter by start and end dates.
            $nullDate   = $this->_db->Quote($this->_db->getNullDate());
            $date       = JFactory::getDate();
            $nowDate    = $this->_db->Quote($date->toSql());

            $query = $this->_db->getQuery(true);
            $query->select('s.*, p.id as id, p.price, p.price2, p.stype, p.stype_freq, p.call_for_price, p.title AS title, p.street_num, p.street AS street, p.street2, p.city, p.state, p.hide_address, p.sqft, p.approved, s.id AS save_id, s.timestamp AS created, p.alias as alias, p.property_name')
                ->from('#__iproperty_saved as s')
                ->leftJoin('#__iproperty as p on p.id = s.prop_id')
                ->where('s.user_id = '.(int)$user_id)
                ->where('s.active = 1 AND s.type = 0')
                ->where('(p.publish_up = '.$nullDate.' OR p.publish_up <= '.$nowDate.')')
                ->where('(p.publish_down = '.$nullDate.' OR p.publish_down >= '.$nowDate.')')
                ->order('s.timestamp DESC');
            
			$this->_db->setQuery($query);
            $properties = $this->_db->loadObjectList();
		}
        
        foreach($properties as $p)
        {
            $p->available = '';
            $p->short_description = '';
            $p->description = '';
            $p->baths = '';
            $p->latitude = '';
            $p->longitude = '';
            $p->modified = '';
        }
        $this->_properties = ipropertyHelperProperty::getPropertyItems($properties);

		return $this->_properties;
	}
    
	public function getSearches()
	{
        $user    = JFactory::getUser();
        $user_id = $user->id;
		// Lets load the content if it doesn't already exist
		if( empty($this->_searches)){
            
            $query = $this->_db->getQuery(true);
            $query->select('*, s.notes AS title, s.id AS id, s.timestamp AS created')
                ->from('#__iproperty_saved as s')
                ->where('s.user_id = '.(int)$user_id)
                ->where('s.active = 1 AND s.type != 0')
                ->order('timestamp DESC');
			$this->_db->setQuery($query);
            $this->_searches = $this->_db->loadObjectList();
		}

		return $this->_searches;
	}    

    public function saveProperty($propid, $notes = '', $email_update = 1)
    {
        $user    = JFactory::getUser();
        $user_id = $user->id;
        
        JPluginHelper::importPlugin( 'iproperty');
        $dispatcher    = JDispatcher::getInstance();

        $query = $this->_db->getQuery(true);
        $query->insert('#__iproperty_saved')
                ->columns('user_id, prop_id, notes, email_update, active')
                ->values((int)$user_id.', '.(int)$propid.', '.$this->_db->Quote($notes).', '.(int)$email_update.', 1');
        
        $this->_db->setQuery($query);
        if( $this->_db->execute() ){
            if( $settings->notify_saveprop == 1 ){
                $this->_notifySave((int)$propid);
            }
            $dispatcher->trigger('onAfterSaveFavorite', array( $propid, $user_id, $notes, $email_update));
            return true;
        }else{
            return false;
        }
    }

    public function deleteSaved($id)
    {
        $user       = JFactory::getUser();
        $user_id    = $user->id;

        $query = $this->_db->getQuery(true);
        $query->update('#__iproperty_saved')
            ->set('active = 0')
            ->where('id = '.(int)$id)
            ->where('user_id = '.(int)$user_id);

        $this->_db->setQuery($query);
        if( $this->_db->execute() ){
            return true;
        }else{
            return false;
        }
    }
    
    public function saveSearch($searchstring, $notes = '', $email_update = 1)
    {
        $user    = JFactory::getUser();
        $user_id = $user->id;
        
        if(!$notes) $notes = JText::_('COM_IPROPERTY_SEARCH').'_'.rand();
        
        $query = $this->_db->getQuery(true);
        $query->insert('#__iproperty_saved')
                ->columns('user_id, notes, search_string, email_update, active, type')
                ->values((int)$user_id.', '.$this->_db->Quote($notes).', '.$this->_db->Quote($searchstring).', '.(int)$email_update.', 1, 1');
        
        $this->_db->setQuery($query);
        if( $this->_db->execute() ){
            return true;
        }else{
            return false;
        }
    }    
    
    public function updateEmailSubscribe($id)
    {	
        $user       = JFactory::getUser();
        $user_id    = $user->id;
        
        $query = $this->_db->getQuery(true);
        $query->update('#__iproperty_saved')
                ->set('email_update = !email_update')
                ->where('id = '.(int)$id)
                ->where('user_id = '.$user_id);
        
        $this->_db->setQuery($query);
        if( $this->_db->execute() ){
            return true;
        }else{
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
    }
    
    // this is for a generic unsubscribe link to be included in the update emails
    public function emailUnsubscribe($id, $token, $all=false)
    {	
        $query = $this->_db->getQuery(true);
        $query->select('*')
                ->from('#__iproperty_saved')
                ->where('id = '.(int) $id);
        
        $this->_db->setQuery($query);
        if( FALSE !== ($result = $this->_db->loadObject()) )
        {
            // check the token and set email_update to false
            $config     = JFactory::getConfig();
            $secret     = $config->get('secret');
            $hash       = md5($result->user_id . $result->id . $secret);
            if ($hash == $token){
                $query = $this->_db->getQuery(true);
                if($all){                    
                    $query->update('#__iproperty_saved')
                            ->set('email_update = 0')
                            ->where('user_id = '.(int)$result->user_id);
                } else {
                    $query->update('#__iproperty_saved')
                            ->set('email_update = 0')
                            ->where('id = '.(int)$result->id);
                }
                $this->_db->setQuery($query);
                if( $this->_db->execute() ){
                    return true;
                }else{
                    $this->setError($this->_db->getErrorMsg());
                    return false;
                }
            } else {
                $this->setError(JText::_('COM_IPROPERTY_INVALID_ID_OR_TOKEN_PASSED'));
                return false;
            }
        }else{
            $this->setError(JText::_('COM_IPROPERTY_RECORD_NOT_FOUND'));
            return false;
        }    
    }  
    
    public function approveListing($id, $token)
    {	
        $query = $this->_db->getQuery(true);
        $query->select('*')
                ->from('#__iproperty')
                ->where('id = '.(int) $id);
        
        $this->_db->setQuery($query);
        if( $result = $this->_db->loadObject() )
        {
            // check the token and set email_update to false
            $config     = JFactory::getConfig();
            $secret     = $config->get('secret');
            $hash       = md5($result->id.$secret);
            if ($hash == $token){
                $query = $this->_db->getQuery(true);
                $query->update('#__iproperty')
                            ->set('approved = 1')
                            ->where('id = '.(int)$result->id);
                $this->_db->setQuery($query);
                if( $this->_db->execute() ){
                    $this->_notifyApproval($id);
                    return true;
                }else{
                    $this->setError($this->_db->getErrorMsg());
                    return false;
                }
            } else {
                $this->setError(JText::_('COM_IPROPERTY_INVALID_ID_OR_TOKEN_PASSED'));
                return false;
            }
        }else{
            $this->setError(JText::_('COM_IPROPERTY_PROPERTY_NOT_FOUND'));
            return false;
        }    
    }
    
    protected function _notifyApproval($propid)
    {
        //send notification of approval to agents
        $app  = JFactory::getApplication();

        $settings      = ipropertyAdmin::config();
        $admin_from    = $app->getCfg('fromname');
        $admin_email   = $app->getCfg('mailfrom');
        $property_path = JURI::base().ipropertyHelperRoute::getPropertyRoute($propid);

        $agents        = ipropertyHTML::getAvailableAgents($propid);
        $property      = ipropertyHTML::getPropertyTitle($propid);

		$subject        = sprintf(JText::_('COM_IPROPERTY_APPROVAL_SUBJECT' ), $property);
		$date           = JHTML::_('date','now',JText::_('DATE_FORMAT_LC4'));
        $fulldate       = JHTML::_('date','now',JText::_('DATE_FORMAT_LC2'));

        //check who admin wants to send the requests to
        $recipients = array();
        foreach($agents as $a){
            $recipients[] = $a->email;
        }     
        
		$body = sprintf(JText::_('COM_IPROPERTY_APPROVAL_BODY'), $property, $admin_from)."\n\n";
        $body .= JText::_('COM_IPROPERTY_FOLLOW_LINK' ) . ":\n"
                . $property_path . "\n\n"
                . JText::_('COM_IPROPERTY_GENERATED_BY_INTELLECTUAL_PROPERTY' ) . " " . $fulldate;

        $sento = '';
        $mail = JFactory::getMailer();
        $mail->addRecipient( $recipients );
        $mail->addReplyTo(array($admin_email, $admin_from));
        $mail->setSender( array( $admin_email, $admin_from ));
        $mail->setSubject( $subject );
        $mail->setBody( $body );
        $sento = $mail->Send();

		if( $sento ){
            return true;
		}else{
			return false;
		}
    }
    
    protected function _notifySave($id )
    {
        $app  = JFactory::getApplication();

        $admin_from    = $app->getCfg('fromname');
        $admin_email   = $app->getCfg('mailfrom');

        $user          = JFactory::getUser();
        $userid        = $user->get('id');
        $username      = $user->get('name');
        $useremail     = $user->get('email');
        $settings      = ipropertyAdmin::config();
        //$uri           =JURI::getInstance();
        $property_path  = JURI::base().ipropertyHelperRoute::getPropertyRoute($id);
        $fulldate       = JHTML::_('date','now',JText::_('DATE_FORMAT_LC2'));

        $text = '<style>
                    body{ font-family: arial; font-size: 12px; }
                    .result{color: ' . $settings->accent_color . ';}
                    .footer{font-size: 10px; color: #999;}
                </style>';

        $notification   = $app->getCfg('fromname') .' '. JText::_( 'COM_IPROPERTY_SAVED_PROPERTY_NOTIFICATION' );
        
        
        //build property object
        $where          = array();
        $where['property']['id']    = $id;			
        $pquery                     = new ipropertyHelperQuery($this->_db);
        $pquery                     = $pquery->buildPropertyQuery($where, 'property');
        $this->_db->setQuery($pquery, 0, 1);
        $data = $this->_db->loadObject();
        $data = ipropertyHelperProperty::getPropertyItems(array($data));
        $property       = $data[0];

        $property_full_address = $property->street_address.'<br />'
                                .$property->city.', '.ipropertyHTML::getstatename($property->locstate).$property->province.' '.$property->postcode.'<br />'
                                .ipropertyHTML::getcountryname($property->country);

        $text .= '<p>' . $username . ' (' . $useremail . ') '.JText::_( 'COM_IPROPERTY_SAVED_PROPERTY_NOTIFY_TEXT' ).'</p>';
        $text .= '---------------------------------------------------------<br />';
        $text .= JText::_( 'COM_IPROPERTY_USER' ).'<br />';
        $text .= '---------------------------------------------------------<br />';
        $text .= '<p><strong>'.JText::_( 'COM_IPROPERTY_USER_ID' ).':</strong> <span class="result">' . $userid . '</span><br />';
        $text .= '<strong>'.JText::_( 'COM_IPROPERTY_USER_NAME' ).':</strong> <span class="result">' . $username . '</span><br />';
        $text .= '<strong>'.JText::_( 'COM_IPROPERTY_USER_EMAIL' ).':</strong> <span class="result">' . $useremail . '</span><br /></p>';

        $text .= '---------------------------------------------------------<br />';
        $text .= JText::_( 'COM_IPROPERTY_PROPERTY' ).'<br />';
        $text .= '---------------------------------------------------------<br />';

        $text .= '<p><strong>'.JText::_( 'COM_IPROPERTY_PROP_ID' ).':</strong> <span class="result">' . $property->property_name . '</span><br />';
        $text .= '<strong>'.JText::_( 'COM_IPROPERTY_ADDRESS' ).':</strong><br /><span class="result">' . $property_full_address . '</span><br />';
        $text .= '<strong>'.JText::_( 'COM_IPROPERTY_PRICE' ).':</strong> <span class="result">' . $property->formattedprice . '</span><br />';
        $text .= '</p>';

        $text .= '<p>' . JText::_( 'COM_IPROPERTY_FOLLOW_LINK' ) . ':<br />
                     <a href="' . $property_path . '">' . $property_path . '</a><br /><br />
                     <span class="footer">' . JText::_( 'COM_IPROPERTY_GENERATED_BY_INTELLECTUAL_PROPERTY' ) . ' ' . $fulldate . '.
                  </p>';


        if( $admin_email && $settings->notify_saveprop == 1 )
        {
            $mail = JFactory::getMailer();
            $mail->addRecipient( $admin_email );
            $mail->setSender( array( $admin_email, $admin_from ) );
            $mail->setSubject( $notification );
            $mail->setBody( $text );
            $mail->isHTML(true);
            $mail->Send();
        }
    }
}

?>