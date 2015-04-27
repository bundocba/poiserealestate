<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
jimport('joomla.application.component.controller');
jimport('joomla.log.log');

class IpropertyControllerAjax extends JControllerLegacy
{
	protected $text_prefix = 'COM_IPROPERTY';
    
    public function resetHits()
    {
        // Check for request forgeries
        JSession::checkToken() or die( 'Invalid Token');
        $prop_id = JRequest::getInt('prop_id');
        
        $db     = JFactory::getDbo();
        $query  = 'UPDATE #__iproperty SET hits = 0 WHERE id = '.(int)$prop_id.' LIMIT 1';
        $db->setQuery($query);
        
        if($db->Query()){
            echo JText::_('COM_IPROPERTY_COUNTER_RESET');
        }else{
            return false;
        }
    }

    public function checkUserAgent()
    {
        // Check for request forgeries
        JSession::checkToken() or die( 'Invalid Token');
        $user_id = JRequest::getInt('user_id');
        $agent_id = JRequest::getInt('agent_id', 0);

        if(!$user_id) return false;

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('id');
        $query->from('#__iproperty_agents');
        $query->where('user_id = '.(int)$user_id);
        $query->where('id != '.(int)$agent_id);
        $db->setQuery($query);

        echo $db->loadResult();
    }
    
    public function displayStypes()
    {
        JSession::checkToken('get') or die( 'Invalid Token');
        JHtml::_('bootstrap.tooltip');
        $model = $this->getModel('settings');
        
        $i = 0;
        $html = '';        
        if ($stypes = $model->getStypes()) {
            $k = 0;
            for($i = 0, $n = count( $stypes ); $i < $n; $i++){
                $stype = $stypes[$i];
                $pchecked = ($stype->state) ? ' checked="checked"' : '';
                $bchecked = ($stype->show_banner) ? ' checked="checked"' : '';
                $rchecked = ($stype->show_request_form) ? ' checked="checked"' : '';

                $html .= '
                <tr class="stype_row row'.$k.'">
                    <td class="center"><input type="hidden" id="ipstype" value="'.$stype->id.'" class="s'.$i.'"/><input type="text" id="name" class="inputbox s'.$i.'" value="'.JText::_($stype->name).'" style="width: 100%;" /></td>
                    <td class="center"><input type="text" id="banner_image" class="inputbox s'.$i.'" value="'.$stype->banner_image.'" style="width: 100%;" /></td>
                    <td class="center"><input type="text" id="banner_color" class="inputbox s'.$i.'" value="'.$stype->banner_color.'" style="width: 100%;" /></td>
                    <td class="center"><input type="checkbox" id="state" class="inputbox s'.$i.'" value="1"'.$pchecked.' /></td>
                    <td class="center"><input type="checkbox" id="show_banner" class="inputbox s'.$i.'" value="1"'.$bchecked.' /></td>
                    <td class="center"><input type="checkbox" id="show_request_form" class="inputbox s'.$i.'" value="1"'.$rchecked.' /></td>
                    <td class="center">'.$stype->id.'</td>
                    <td class="center"><button class="btn btn-danger" type="button" onclick="if(confirm(\''.JText::_('COM_IPROPERTY_CONFIRM_DELETE' ).'\')){deleteStype('.$stype->id.');}">'.JText::_('COM_IPROPERTY_DELETE' ).'</button></td>
                </tr>';
                $k = 1 - $k;
            }
        }else{
            $html .= '<tr><td class="center" colspan="7">'.JText::_('COM_IPROPERTY_NO_RESULTS').'</td></tr>';
        }
        $html .= '
            <tr><td colspan="8">&nbsp;</td></tr>
            <tr><td colspan="8" style="background: #0093D4 !important; color: #fff !important;"><b>Add new:</b></td></tr>
            <tr class="stype_row">
                <td class="center"><input type="hidden" id="ipstype" value="new" class="s'.$i.'"/><input type="text" id="name" class="inputbox s'.$i.'" value="" style="width: 100%;" /></td>
                <td class="center"><input type="text" id="banner_image" class="inputbox s'.$i.'" value="" style="width: 100%;" /></td>
                <td class="center"><input type="text" id="banner_color" class="inputbox s'.$i.'" value="" style="width: 100%;" /></td>
                <td class="center"><input type="checkbox" id="state" class="inputbox s'.$i.'" value="1" checked="checked" /></td>
                <td class="center"><input type="checkbox" id="show_banner" class="inputbox s'.$i.'" value="1" /></td>
                <td class="center"><input type="checkbox" id="show_request_form" class="inputbox s'.$i.'" value="1" /></td>
                <td class="center">--</td>
                <td class="center"><button type="button" class="btn btn-success" onclick="saveStypes(); return false;" />'.JText::_('COM_IPROPERTY_ADD' ).'</button></td>
            </tr>';
        echo $html;
    }

    public function saveStypes()
    {
        JSession::checkToken() or die( 'Invalid Token');
        $data  = JRequest::get( 'post');
        if(!$stypes = json_decode($data['stypes'])){
            //die( 'Invalid Data');
            echo '<div class="alert alert-error">Invalid Data: '.$data['stypes'].'</div>';
            return;
        }

        $model = $this->getModel('settings');
        if($model->saveStypes( $stypes )){
            echo '<div class="alert alert-success">Success</div>';
        }else{
            echo '<div class="alert alert-error">'.$model->getError().'</div>';
        }            
    }  

    public function deleteStype()
    {
        JSession::checkToken() or die( 'Invalid Token');
        $stypeid    = JRequest::getInt('id');
        $model      = $this->getModel('settings');
        if($model->deleteStype($stypeid)){
            echo '<div class="alert alert-success">Success</div>';
        }else{
            echo '<div class="alert alert-error">'.$model->getError().'</div>';
        }
    }
    
    /**********************
     * Gallery functions
     **********************/
    
    public function ajaxLoadGallery()
    {
		/**
		* Pulls images from IP images table
		*
		* @param integer $propid ID of property 
		* @param integer $own Whether we want only our own images or all avail
		* @param integer $limitstart Starting record id
		* @param integer $limit Max rows to return
		* @param string $token Joomla token
		* @return JSON encoded image data
		*/ 
		// Check for request forgeries
		JSession::checkToken('get') or die( 'Invalid Token');
		$model = $this->getModel('gallery');

		$propid 	= JRequest::getInt('propid');
		$own		= JRequest::getInt('own');
		$limitstart	= JRequest::getInt('limitstart');
		$limit		= JRequest::getInt('limit');
        $type		= JRequest::getBool('type', 0); // 0 for image, 1 for doc
		$filter		= JRequest::getString('filter');

		if (!$propid ){
			echo json_encode(ipropertyHTML::createReturnObject('error', JText::_('NO PROPERTY ID INCLUDED')));
			die();
		}
	
		echo json_encode($model->loadGallery($propid, $own, $limitstart, $limit, $type, $filter));
	}
    
    public function ajaxLoadFiles()
    {
		/**
		* Pulls files from IP images table
		*
		* @param integer $propid ID of property 
		* @param string $token Joomla token
		* @return JSON encoded image data
		*/ 
		// Check for request forgeries
		JSession::checkToken('get') or die( 'Invalid Token');
		$model = $this->getModel('gallery');

        $propid 	= JRequest::getInt('propid');
        $own		= JRequest::getBool('own');

		if (!$propid ){
			echo json_encode(ipropertyHTML::createReturnObject('error', JText::_('NO PROPERTY ID INCLUDED')));
			die();
		}
		
		echo json_encode($model->loadFiles($propid, $own));
	}    
	
	public function ajaxDelete()
    {
		/**
		* Deleted image from IP images table, and deleted image file if it's not in use with other listing
		*
		* @param integer $rowid ID of image 
		* @param string $token Joomla token
		* @return true or false
		*/ 
		// Check for request forgeries
		JSession::checkToken('get') or die( 'Invalid Token');
		$model	= $this->getModel('gallery');
		$rowid	= JRequest::getVar('img');
		
		if (!$rowid ){
			echo json_encode(ipropertyHTML::createReturnObject('error', JText::_('NO IMAGE ID INCLUDED')));
			die();
		}
		echo json_encode($model->delete($rowid));	
	}
	
	public function ajaxAdd()
    {
		/**
		* Add existing image to IP listing
		*
		* @param integer $propid ID of property
		* @param integer $rowid ID of image
		* @param string $token Joomla token
		* @return true or false
		*/ 
		// Check for request forgeries
		JSession::checkToken('get') or die( 'Invalid Token');
		$model = $this->getModel('gallery');
		
		$propid		= JRequest::getInt('propid');
		$rowid		= JRequest::getInt('imgid');
		
		if (!$propid ){
			echo json_encode(ipropertyHTML::createReturnObject('error', JText::_('NO LISTING ID INCLUDED')));
			die();
		}
		
		echo json_encode($model->ajaxAddImage($propid, $rowid));	
	}

	public function ajaxSort()
    {
		/**
		* Save image order
		*
		* @param string $order JSON encoded sort data for property
		* @param string $token Joomla token
		* @return true or false
		*/ 
		// Check for request forgeries
		JSession::checkToken('get') or die( 'Invalid Token');
		$model = $this->getModel('gallery');
		
		$order		= JRequest::getVar('order');
		$order		= json_decode($order);
		
		if (!$order ){
			echo json_encode(ipropertyHTML::createReturnObject('error', JText::_('NO SORT DATA INCLUDED')));
			die();
		}
		echo json_encode($model->ajaxSort($order));
	}
	
	public function ajaxSaveImageTags()
    {
		/**
		* Save image title/desc for listing
		*
		* @param string $title JSON encoded title for property
		* @param string $descr JSON encoded description for property
		* @param string $token Joomla token
		* @return true or false
		*/ 
		// Check for request forgeries
		JSession::checkToken('get') or die( 'Invalid Token');
		$model = $this->getModel('gallery');
		
		$id			= JRequest::getInt('imgid');
		$title		= JRequest::getString('title');
		$descr		= JRequest::getString('descr');
			
		echo json_encode($model->ajaxSaveImageTags($id, $title, $descr));
	}
	
	public function ajaxUploadRemote()
    {
		/**
		* Save remote image for listing
		*
		* @param int $propid ID of property
		* @param string $path Remote file location
		* @param string $token Joomla token
		* @return true or false
		*/ 
		// Check for request forgeries
		JSession::checkToken('get') or die( 'Invalid Token');
		$model = $this->getModel('gallery');
			
		$propid		= JRequest::getInt('propid');
		$path		= JRequest::getString('path');
		
		if (!$propid ){
			echo json_encode(ipropertyHTML::createReturnObject('error', JText::_('NO PROPID INCLUDED')));
			die();
		}
		echo json_encode($model->uploadRemote($propid, $path));
	}
	
    
    public function ajaxUpload()
    {
		/**
		* Upload image for listing, resize, rename, move
		*
		* @param int $propid ID of property
		* @param string $file Local file info array
		* @param string $token Joomla token
		* @return success or failure message
		*/ 
		// Check for request forgeries
		JSession::checkToken('get') or die( 'Invalid Token');
 
		$propid		= JRequest::getInt('propid');
		$files		= JRequest::get('FILES'); // this should be an array
        $model      = $this->getModel('gallery');
		$status     = array();

        if (!isset($_FILES['file']['tmp_name']) && !is_uploaded_file($_FILES['file']['tmp_name'])) {
            $status[] = ipropertyHTML::createReturnObject('error', JText::_('INVALID UPLOAD'));
            echo json_encode($status);
			die();
        }  

		if (!$propid || (count($files) < 1)){
			$status[] = ipropertyHTML::createReturnObject('error', JText::_('NO ID INCLUDED OR NO FILE ARRAY FOUND'));
            echo json_encode($status);
			die();
		}
        
        foreach($files as $file){
            $status[]     = $model->uploadIMG($file, $propid);
        }
        echo json_encode($status);
	} 
    
	public function ajaxAutocomplete()
    {
		/**
		* Get json encoded list of DB values
		* @param string $field BD field to filter
		* @param string $token Joomla token
		* @return json_encoded list of values
		*/ 
		// Check for request forgeries
		JSession::checkToken('get') or die( 'Invalid Token');
		
		$field		= JRequest::getString('field');
		
		$db         = JFactory::getDbo();
		
		$query 		= $db->getQuery(true);
		$query->select('DISTINCT '.$db->quoteName($field))
			->from('#__iproperty')
			->groupby($db->quoteName($field));

		$db->setQuery($query);
		$data = $db->loadColumn();               
		
		echo json_encode($data);
	}	 
    
    public function getUserSaved()
    {
        JSession::checkToken('get') or die( 'Invalid Token');
        
        $user_id    = JRequest::getInt('userid');
        $active     = JRequest::getInt('active');
        $settings   = ipropertyAdmin::config();
        
        $model      = $this->getModel('iproperty');
        $properties = $model->getSavedProperties($user_id, $active);
        
        echo '<ul class="nav nav-tabs nav-stacked">';
        if(count($properties))
        {            
            foreach($properties as $p)
            {
                echo '<li><a href="'.JRoute::_('index.php?option=com_iproperty&view=properties&filter_search=id:'.$p->id).'">'.$p->id.' : '.IpropertyHTML::getStreetAddress($settings, $p).'</li>';
            }            
        }else{
            echo '<li>'.JText::_('COM_IPROPERTY_NO_RESULTS').'</li>';
        }
        echo '</ul>';            
    }
}
?>
