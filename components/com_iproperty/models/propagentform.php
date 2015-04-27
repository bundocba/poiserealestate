<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

// No direct access
defined('_JEXEC') or die;

// Base this model on the backend version.
require_once JPATH_ADMINISTRATOR.'/components/com_iproperty/models/propertyagent.php';

class IpropertyModelPropAgentForm extends IpropertyModelPropertyAgent
{
    protected function populateState()
    {
        $app    = JFactory::getApplication();

        // Load state from the request.
        $pk     = JRequest::getInt('id');
        $this->setState('propertyagent.id', $pk);

        $return = JRequest::getVar('return', null, 'default', 'base64');
        $this->setState('return_page', base64_decode($return));

        // Load the parameters.
        $params = $app->getParams();
        $this->setState('params', $params);

        $this->setState('layout', JRequest::getCmd('layout'));
    }

    public function getItem($itemId = null)
    {
        // Initialise variables.
        $itemId     = (int) (!empty($itemId)) ? $itemId : $this->getState('propertyagent.id');
        $settings   = ipropertyAdmin::config();

        // Get a row instance.
        $table      = $this->getTable();

        // Attempt to load the row.
        $return     = $table->load($itemId);

        // Check for a table object error.
        if ($return === false && $table->getError()) {
            $this->setError($table->getError());
            return false;
        }

        $properties = $table->getProperties(1);
        $item       = JArrayHelper::toObject($properties, 'JObject');

        //$value->params = new JRegistry;
        if ($itemId) {
            //getStreetAddress($settings, $title = null, $street_num = null, $street = null, $street2 = null, $apt = null, $hide = null)
            $item->street_address = ipropertyHTML::getStreetAddress($settings, $item, true);
        }

        return $item;
    }

    public function checkin($pk = null)
    {
        // Only attempt to check the row in if it exists.
        if (is_array($pk)) {
			$user = JFactory::getUser();

            // Get an instance of the row to checkin.
            $table = $this->getTable();
            foreach ($pk as $p){
				if (!$table->load($p)) {
					$this->setError($table->getError());
					return false;
				}

				// Attempt to check the row in.
				if (!$table->checkin($p)) {
					$this->setError($table->getError());
					return false;
				}
			}
		} else if($pk) {
            $user = JFactory::getUser();

            // Get an instance of the row to checkin.
            $table = $this->getTable();
            if (!$table->load($pk)) {
                $this->setError($table->getError());
                return false;
            }

            // Attempt to check the row in.
            if (!$table->checkin($pk)) {
                $this->setError($table->getError());
                return false;
            }
        }

        return true;
    }

    public function getReturnPage()
    {
        return base64_encode($this->getState('return_page'));
    }

    public function autoPublishCheck($data)
    {
        $settings           = ipropertyAdmin::config();
        $propid             = $this->getState($this->getName().'.id');
        $new                = $this->getState($this->getName().'.new');
        $msg                = '';
        $needs_approval     = false;
        $ipauth             = new ipropertyHelperAuth();
        //$canApprove         = ($ipauth->getAdmin() || ($settings->approval_level == 1 && ($ipauth->getSuper()))) ? true : false;        $canApprove         = true;

        // determines if the listing should be set to unapproved on update AND new
        $approveOnUpdate    = $settings->moderate_listings; // admin setting to moderate updated listings
        $doApprove          = ($new || $approveOnUpdate) ? true : false;

        // Check to see if the autopublish setting is enabled. If not, set this listing 'approved' to 0.
        if($propid && $doApprove && !$settings->auto_publish && !$canApprove){
            $db = JFactory::getDbo();
            $query = 'UPDATE #__ipropertyagent SET approved = 0 WHERE id = '.(int)$propid.' LIMIT 1';
            $db->setQuery($query);
            if($db->execute()){
                $needs_approval = true;
                $msg = JText::_('COM_IPROPERTY_REQUIRES_APPROVAL');
            }else{
                $this->setError('Problem with autopublish function - the listing has not been disabled!');
                return false;
            }
        }
        if($doApprove && $settings->notify_newprop && !$ipauth->getAdmin()){ // new property - send admin a notification that a listing has been added
            $this->_emailAdminNotification($propid, $needs_approval, $new);
        }

        // return false by default since this is an optional setting and not always needed to return a message
        return $msg;
    }

    protected function _emailAdminNotification($propid, $needs_approval = '', $new = false)
    {
        //email function here to send admin notification of new front end listing
        $app  = JFactory::getApplication();

        $admin_from    = $app->getCfg('fromname');
        $admin_email   = $app->getCfg('mailfrom');

        $property_path  = JURI::base().ipropertyHelperRoute::getPropertyRoute($propid);
        $fulldate       = JHTML::_('date','now',JText::_('DATE_FORMAT_LC2'));

        $subject      = $new ? $app->getCfg('fromname').'-'.JText::_('COM_IPROPERTY_NEW_LISTING' ) : $app->getCfg('fromname').'-'.JText::_('COM_IPROPERTY_UPDATED_LISTING');

        if($needs_approval){
            $needs_approval = $new ? JText::_('COM_IPROPERTY_NEW_LISTING_APPROVAL_REQUIRED') : JText::_('COM_IPROPERTY_UPDATED_LISTING_APPROVAL_REQUIRED');
        }

        $body = $new ? sprintf(JText::_('COM_IPROPERTY_NEW_LISTING_EMAIL_TEXT'), $admin_from, $needs_approval)."\n\n"
                    .JText::_('COM_IPROPERTY_FOLLOW_LINK' ) . ":\n\n" 
                : sprintf(JText::_('COM_IPROPERTY_UPDATED_LISTING_EMAIL_TEXT'), $admin_from, $needs_approval)."\n\n"
                    .JText::_('COM_IPROPERTY_FOLLOW_LINK' ) . ":\n\n";

        if($needs_approval){
            $config         = JFactory::getConfig();
            $secret         = $config->get('secret');
            $hash           = md5($propid.$secret);
            $approve_link   = JURI::base()."index.php?option=com_iproperty&task=ipuser.approveListing&id=".$propid."&token=".$hash;

            $body .= JText::_('COM_IPROPERTY_APPROVAL_LINK' ).": ".$approve_link."\n";
            //$body .= "\r\ntodo: This will be a link to the site with a hash for automatic approval";
        }
        $body .= JText::_('COM_IPROPERTY_PROPERTY_LINK' ).": ".$property_path;
        $body .= "\n\n".JText::_('COM_IPROPERTY_GENERATED_BY_INTELLECTUAL_PROPERTY' )." " . $fulldate;

        $sento = '';
        $mail = JFactory::getMailer();
        $mail->addRecipient( $admin_email );
        $mail->addReplyTo(array($admin_email, $admin_from));
        $mail->setSender( array( $admin_email, $admin_from ));
        $mail->setSubject( $subject );
        $mail->setBody( $body );
        $sento = $mail->Send();

        if( $sento ){
            $copySubject    = JText::_('COM_IPROPERTY_COPY_OF' ).": ".$subject;
            // @todo: send a copy to agent who submitted the listing?
            return true;
        }else{
            return false;
        }
    }

    function deleteProp($pks)
    {
        // Initialise variables.
        $table  = $this->getTable();
        $pks    = (array) $pks;
        $ipauth = new ipropertyHelperAuth();

        // Access checks.
        foreach ($pks as $i => $pk) {
            if ($table->load($pk)) {
                if (!$ipauth->canDeleteProp($pk)){
                    // Prune items that you can't change.
                    unset($pks[$i]);
                    $this->setError(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
                    return false;
                }
            }
        }

        // Attempt to change the state of the records.
        if (!$table->delete($pks)) {
            $this->setError($table->getError());
            return false;
        }

        return true;
    }
}
