<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined('_JEXEC' ) or die( 'Restricted access');
jimport('joomla.application.component.model');

class IpropertyModelGallery extends JModelLegacy
{
    protected $text_prefix = 'COM_IPROPERTY';
    //var $_pagination = null;
    
    public function getTable($type = 'Image', $prefix = 'IpropertyTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}   

    public function loadGallery($propid = false, $own = false, $limitstart = 0, $limit = 50, $type = false, $fname = false)
    {
        $db     	= JFactory::getDbo();
        $ipauth 	= new ipropertyHelperAuth();
		$user       = JFactory::getUser();

        if($own){ //images belonging to this listing
            $query = $db->getQuery(true);
            $query->select('SQL_CALC_FOUND_ROWS *')
                    ->from('#__iproperty_images')
                    ->where('propid = '.(int)$propid)
                    ->group('fname')
                    ->order('ordering ASC');
        }else{        
            if(!$ipauth->getAdmin()) {
                switch($ipauth->getAuthLevel()){
                    case 1: // only company images
                        $query = $db->getQuery(true);
                        $query->select('SQL_CALC_FOUND_ROWS *')
                                ->from('#__iproperty_images')
                                ->where('propid != '.(int)$propid)
                                ->where('propid IN (SELECT id FROM #__iproperty WHERE listing_office = '.(int)$ipauth->getUagentCid().')')
                                ->group('fname')
                                ->order('ordering ASC');
                        break;
                    case 2: // only the agents own images
                        $query = $db->getQuery(true);
                        $query->select('SQL_CALC_FOUND_ROWS *')
                                ->from('#__iproperty_images')
                                ->where('propid != '.(int)$propid)
                                ->where('owner = '.(int)$user->id)
                                ->group('fname')
                                ->order('ordering ASC');
                        break;
                }
            }else{
                $query = $db->getQuery(true);
                $query->select('SQL_CALC_FOUND_ROWS *')
                        ->from('#__iproperty_images')
                        ->where('propid != '.(int)$propid)
                        ->group('fname')
                        ->order('ordering ASC');
            }
			if ($fname) $query->where('fname LIKE "%'.$fname.'%"');
        }
		
		if ($type) {
			$query->where('(type != ".jpg" AND type != ".jpeg" AND type != ".gif" AND type != ".png")');
		} else {
			$query->where('(type = ".jpg" OR type = ".jpeg" OR type = ".gif" OR type = ".png")');
		}
		
		$db->setQuery($query, $limitstart, $limit);		
        $result = $db->loadObjectList();
		
        // get the total # of rows pulled
		$db->setQuery('SELECT FOUND_ROWS();');
		$noimgs = $db->loadResult();
		$totalimgs = array('totalimgs' => $noimgs);
		if($noimgs){
			$totalimgs['photos'] = $result;
		}
        return ipropertyHTML::createReturnObject('ok', 'IMAGE SEARCH OK', $totalimgs);
  	}

    // File Upload
    public function uploadIMG($file, $propid)
    {
        // create result array to return-- initially it will be all false
        $result = array(
            'status'    => false,
            'fname'     => false,
            'message'   => false
        );
        
        if (isset($file)) {
			// set an array of accepted mime types to check files against
			$accepted_mimetypes = array(
									'image/jpeg',
									'image/gif',
									'image/png',
									'application/pdf',
									'application/msword',
									'application/vnd.ms-excel',
									'application/vnd.ms-powerpoint',
									'application/vnd.oasis.opendocument.text',
									'application/vnd.oasis.opendocument.presentation',
									'application/vnd.oasis.opendocument.spreadsheet',
									'application/x-shockwave-flash',
									'video/mpeg',
									'video/x-msvideo',
									'application/zip'
								);

            $db         = JFactory::getDBO();
            $settings   = ipropertyAdmin::config();
            $path       = JPATH_SITE;
            $user       = JFactory::getUser();
			$ipauth 	= new ipropertyHelperAuth();
			$coparams	= $ipauth->getCompanyParams();
			$maximgs	= $coparams->get('maximgs', 0);

            // is this the first image of this object?
            $query = $db->getQuery(true);
            $query->select('count(id)')
                    ->from('#__iproperty_images')
                    ->where('propid = '.(int)$propid);
            
            $db->setQuery($query);
            $imgcount = $db->loadResult();
			$imglimit = $imgcount + 1;

            if(	($imglimit > $settings->maximgs && $settings->maximgs!=0) || ($imglimit > $maximgs && $maximgs!=0) ){
                return ipropertyHTML::createReturnObject('error', 'FILE UPLOAD FAILED: OVERLIMIT');
            }
            $src_file	= (isset($file['tmp_name']) ? $file['tmp_name'] : "");
            
            $result['fname'] = $src_file;

            // check individual settings
            $cfg = array();
            $cfg['imgpath']			= $settings->imgpath;
            $cfg['maximgsize'] 	    = $settings->maximgsize;

            $cfg['imgwidth']		= $settings->imgwidth;
            $cfg['imgheight']		= $settings->imgheight;
            $cfg['imageprtn']		= $settings->imgproportion;
            $cfg['imgquality']		= $settings->imgquality;

            $cfg['createTb']		= 1;
            $cfg['thumbwidth']      = $settings->thumbwidth;
            $cfg['thumbheight']		= $settings->thumbheight;
            $cfg['thumbprtn']		= $settings->thumbproportion;
            $cfg['thumbquality']    = $settings->thumbquality;

            $dest_dir 				= $path.$cfg['imgpath'];
            $ext                    = strtolower( strrchr($file['name'],'.'));

            $vfilename              = $this->sanitize($file['name']);
            $dest_file              = $dest_dir.$vfilename.$ext;

            $vthumbname             = $vfilename . "_thumb";
            $dest_thmb              = $dest_dir.$vthumbname.'.jpg';

			// we're going to make sure that the file's mime type is in the accepted group of mime types
			if (function_exists('finfo_file')) {
				$finfo = finfo_open(FILEINFO_MIME);
                if (is_resource($finfo)){
                    $mime_type = finfo_file($finfo, $src_file);
                    finfo_close($finfo);

                    /* workaround for mime type returning charset */
                    $mime_type = explode(';', $mime_type);
                    $mime_type = $mime_type[0];
                    /* end workaround */

                    if (strlen($mime_type) && !in_array($mime_type, $accepted_mimetypes)){
                        return ipropertyHTML::createReturnObject('error', JText::_('COM_IPROPERTY_WRONG_FILETYPE' ). ' Error: 1');
                    } else if (!is_string($mime_type)) {
                        return ipropertyHTML::createReturnObject('error', JText::_('COM_IPROPERTY_WRONG_FILETYPE' ). ' Error: 2');
                    }
                } else {
                    return ipropertyHTML::createReturnObject('error', JText::_('COM_IPROPERTY_FINFO_FAILURE' ). ' Error: 3');
                }
			} else if (function_exists('mime_content_type')) {
				$mime_type = mime_content_type($src_file);
				if (strlen($mime_type) && !in_array($mime_type, $accepted_mimetypes)){
					return ipropertyHTML::createReturnObject('error', JText::_('COM_IPROPERTY_WRONG_FILETYPE' ). ' Error: 4');
				}
			} // else you're pretty much out of luck since we need to access filesystem functions if you don't have these.

            if(filesize($src_file) > (intval($cfg['maximgsize']) * 1000)) {
                $result['message'] = sprintf(JText::_('COM_IPROPERTY_IMAGE_TOO_LARGE' ), (filesize($src_file)/1000).'KB', $cfg['maximgsize'].'KB', ini_get('upload_max_filesize'));
                return ipropertyHTML::createReturnObject('error', sprintf(JText::_('COM_IPROPERTY_IMAGE_TOO_LARGE' ), (filesize($src_file)/1000).'KB', $cfg['maximgsize'].'KB', ini_get('upload_max_filesize')));
            }

            if (file_exists($dest_file)) {
                return ipropertyHTML::createReturnObject('error', JText::_('COM_IPROPERTY_FILE_EXISTS'));
            }

            if ((strcasecmp($ext, ".gif")) && (strcasecmp($ext, ".jpg")) && (strcasecmp($ext, ".jpeg")) && (strcasecmp($ext, ".png")) &&(strcasecmp($ext, ".doc")) && (strcasecmp($ext, ".xls")) && (strcasecmp($ext, ".ppt")) && (strcasecmp($ext, ".odt")) && (strcasecmp($ext, ".odp")) && (strcasecmp($ext, ".ods")) && (strcasecmp($ext, ".swf")) && (strcasecmp($ext, ".pdf")) && (strcasecmp($ext, ".mpg")) && (strcasecmp($ext, ".mov")) && (strcasecmp($ext, ".avi")) && (strcasecmp($ext, ".zip"))) {
                return ipropertyHTML::createReturnObject('error', JText::_('COM_IPROPERTY_WRONG_FILETYPE' ). ' Error: 5');
            }

            if($ext == ".jpg" || $ext == ".jpeg" || $ext == ".png" || $ext == ".gif" ) {
                // adding check to make sure it's really an image
				if ( !function_exists( 'exif_imagetype' ) ) {
                    // exif function doesn't exist so we'll build one
					function exif_imagetype ( $filename ) {
						if ( ( list($width, $height, $type, $attr) = getimagesize( $filename ) ) !== false ) {
							return $type;
						}
                        return false;
					}
				} 
                
                if (!exif_imagetype($src_file)){
                    return ipropertyHTML::createReturnObject('error', JText::_('COM_IPROPERTY_WRONG_FILETYPE' ). ' Error: 6');
                }

                $dest_file          = $dest_dir.$vfilename.'.jpg';
                $result['message']  = $this->resizeIMG(0, $src_file, $dest_file, $cfg['imgwidth'], $cfg['imgheight'], $cfg['imageprtn'], $cfg['imgquality']);

                if($cfg['createTb'] == 1) {
                    $result['message'] .= $this->resizeIMG(1, $src_file, $dest_thmb, $cfg['thumbwidth'], $cfg['thumbheight'], $cfg['thumbprtn'], $cfg['thumbquality']);
                }
                $ext = ".jpg";
            } else {
                if(@copy($src_file,$dest_file)){
                    //continue
                }else{
					return ipropertyHTML::createReturnObject('error', JText::_('COM_IPROPERTY_IMAGE_NOT_COPIED'));
                }
            }

            if(!$result['message']) {
                $pic = $this->getTable();
                
                $pic->title			= isset($cfg['title']) ? trim($cfg['title']) : '';
                $pic->description	= isset($cfg['description']) ? trim($cfg['description']) : '';
                $pic->fname			= $vfilename;
                $pic->type			= $ext;
                $pic->path			= trim($cfg['imgpath']);
				$pic->propid		= (int) $propid;
                $pic->owner			= $user->id;
                $pic->ordering		= 0;
                $pic->state         = 1;
                $pic->title         = trim(preg_replace( '/\s+/', ' ', $pic->title));
                $pic->description   = trim(preg_replace( '/\s+/', ' ', $pic->description));
                $pic->fname         = trim(preg_replace( '/\s+/', ' ', $pic->fname));
                $pic->type          = trim(preg_replace( '/\s+/', ' ', $pic->type));

                if (!$pic->check()) {
					return ipropertyHTML::createReturnObject('error', 'FAILED TO STORE IMAGE: '.$pic->getError());
                }
                if (!$pic->store()) {
					return ipropertyHTML::createReturnObject('error', 'FAILED TO STORE IMAGE: '.$pic->getError());
                }
                $pic->checkin();
                $pic->reorder( "propid = ".$pic->propid." AND type = ".$db->Quote($pic->type) );
                return ipropertyHTML::createReturnObject('ok', JText::_('COM_IPROPERTY_FILE_UPLOAD_SUCCESSFUL'), $pic);
            } else {
                return ipropertyHTML::createReturnObject('error', $result['message']);
            }
            return ipropertyHTML::createReturnObject('error', $result['message']);
        } else {
            return ipropertyHTML::createReturnObject('error', JText::_('COM_IPROPERTY_NO_FILE_FOUND'));
        }
        // catch all return
        return ipropertyHTML::createReturnObject('error', 'GENERIC FAILURE OF IMG UPLOAD: '.$result['message']);
    }

	// ORDER IMAGES
    public function orderImages($pid, $inc)
    {
        $img  = $this->getTable();
        $img->load($pid);
        $img->move( $inc, "propid = $img->propid AND type = '$img->type'");
        $img->reorder( "propid = $img->propid AND type = '$img->type'" );
        return true;
    }

	// DELETE IMAGE(S)
	public function delete($pks)
    {
		// Initialise variables.
		$table	= $this->getTable();
		$pks	= (array) $pks;
        $ipauth = new ipropertyHelperAuth();

		$successful = 0;
        // Access checks.
		foreach ($pks as $i => $pk) {
			if ($table->load($pk)) {
				/*if (!$ipauth->canEditProp($pk)){
					// Prune items that you can't change.
					unset($pks[$i]);
				}else{*/
                    $successful++;
                //}
			}
		}

		// Attempt to change the state of the records.
		if (!$table->delete($pks)) {
			return ipropertyHTML::createReturnObject('error', $table->getError());
		}
		if($successful && $table->reorder("propid=".$table->propid." AND type = '".$table->type."'")){
			return ipropertyHTML::createReturnObject('ok', 'SUCCESSFULLY DELETED '.$successful.' IMAGES');
		} else {
			return ipropertyHTML::createReturnObject('error', 'NO ERRORS BUT NO IMAGES DELETED');
		}
    }
    
    // icon upload for company / agent
	public function iconUpload($file, $id, $folder = 'agent')
	{
		$settings = ipropertyAdmin::config();
		$icon = new JImage($file['tmp_name']);
		if (!$icon) {
			return ipropertyHTML::createReturnObject('error', 'COULD NOT CREATE JIMAGE OBJECT FROM '.$file);
		}
        $iconwidth = ($folder == 'company') ? $settings->co_photo_width : $settings->agent_photo_width;
		$resizedIcon = $icon->resize($iconwidth, 9999);
		$type = IMAGETYPE_JPEG; // only going to support jpg for now
		$newfile = JPATH_SITE.'/media/com_iproperty/';
		switch ($folder) {
			case 'companies':
				$newfile .= 'companies/';
				$row = $this->getTable('company');
			break;
			case 'agents':
			default:
				$newfile .= 'agents/';
				$row = $this->getTable('agent');
			break;
		}
		$newfilename = $this->sanitize($file['name']).'.jpg';
		$newfile .= $newfilename;
		// Store the resized image to a new file
		if (!$resizedIcon->toFile($newfile, $type)){
			return ipropertyHTML::createReturnObject('error', 'FAILED TO STORE NEW ICON TO '.$newfile);
		}
		// now store the new icon path in DB
		$row->load($id);
		$object['icon'] = $newfilename;
		if (!$row->bind( $object )) {
			return ipropertyHTML::createReturnObject('error', $row->getError());
		}	
		if (!$row->store()) {
			return ipropertyHTML::createReturnObject('error', $row->getError());
		} else {
			return ipropertyHTML::createReturnObject('ok', 'ICON '.$newfilename.' SAVED SUCCESSFULLY FOR '.$folder.' '.$id, $newfilename);
		}
	}
    
    // function to reset company or agent icon to nopic
    public function iconReset($id, $folder){
        switch ($folder) {
			case 'companies':
				$row = $this->getTable('company');
			break;
			case 'agents':
			default:
				$row = $this->getTable('agent');
			break;
		}
        // now store the new icon path in DB
		$row->load($id);
		$object['icon'] = 'nopic.png';
		if (!$row->bind( $object )) {
			return ipropertyHTML::createReturnObject('error', $row->getError());
		}	
		if (!$row->store()) {
			return ipropertyHTML::createReturnObject('error', $row->getError());
		} else {
			return ipropertyHTML::createReturnObject('ok', 'ICON RESET SUCCESSFULLY FOR '.$id, 'nopic.png');
		}
    }

	//************************
	// AJAX SPECIFIC FUNCTIONS
	//************************
	
	// ADD IMAGE
    public function ajaxAddImage($prop_id, $image_id)
    {
        $db         = JFactory::getDBO();
        $settings 	= ipropertyAdmin::config();
		$image_id	= (int) $image_id;
        $user 		= JFactory::getUser();
        $ipauth 	= new ipropertyHelperAuth();
		$coparams	= $ipauth->getCompanyParams();
		$maximgs	= $coparams->get('maximgs', 0);

		// is this the first image of this object?
		$query = $db->getQuery(true);
		$query->select('count(id)')
				->from('#__iproperty_images')
				->where('propid = '.(int)$prop_id);
		
		$db->setQuery($query);
		$imgcount = $db->loadResult();
		$imglimit = $imgcount + 1;
		

		if(	($imglimit > $settings->maximgs && $settings->maximgs!=0) || ($imglimit > $maximgs && $maximgs!=0) ){
			return ipropertyHTML::createReturnObject('error', 'FAILED TO LINK IMAGE: OVERLIMIT');
		}

        // link new image to object
		$currimg  = $this->getTable();
		$currimg->load($image_id);

		$linkimg = $this->getTable();
		$linkimg->propid 		    = $prop_id;
		$linkimg->title				= '';
		$linkimg->description       = '';
		$linkimg->fname             = $currimg->fname;
		$linkimg->type              = substr($currimg->type, 0, 4); // in case any junk is attached
		$linkimg->path              = $currimg->path;
		$linkimg->remote            = $currimg->remote;
		$linkimg->owner				= $user->id;
		$linkimg->state             = 1;

		if (!$linkimg->check()) {
			return ipropertyHTML::createReturnObject('error', $linkimg->getError());
		}
		if (!$linkimg->store()) {
			return ipropertyHTML::createReturnObject('error', $linkimg->getError());
		}

		$linkimg->checkin();

        if($linkimg->id && $linkimg->reorder("propid = ".$prop_id." AND type = ".$db->Quote($linkimg->type))){
			return ipropertyHTML::createReturnObject('ok', 'IMAGE '.$linkimg->id.' LINKED SUCCESSFULLY TO '.$prop_id, $linkimg);
        } else {
            return ipropertyHTML::createReturnObject('error', 'LINKIMG FAILED TO GET NEW ROW ID');
        }
    }		
	
	// sort the images
    public function ajaxSort($data)
    {
		foreach($data as $index => $img_id){
            if (!is_numeric($img_id)) return ipropertyHTML::createReturnObject('error', 'NON-NUMERIC VALUES FOUND IN SORT ARRAY');
			
			$im['ordering'] = (int) $index + 1; // since index of array starts at 0
			$im['id']		= (int) $img_id;
			// get instance of table obj
			$row = $this->getTable();
			// do the bind and store
			if (!$row->bind( $im )) {
				return ipropertyHTML::createReturnObject('error', $row->getError());
			}	
			if (!$row->store()) {
				return ipropertyHTML::createReturnObject('error', $row->getError());
			}
		}
        return ipropertyHTML::createReturnObject('ok', 'SORT SAVED SUCCESSFULLY');
	}
	
	public function uploadRemote($propid, $path)
    {				
        $db         = JFactory::getDBO();	
		$pathinfo 	= pathinfo($path);
		$user 		= JFactory::getUser();
		$ipauth 	= new ipropertyHelperAuth();
		$coparams	= $ipauth->getCompanyParams();
		$maximgs	= $coparams->get('maximgs', 0);
        $settings 	= ipropertyAdmin::config();
               
        // check if this is a valid image path
        //if (!$this->checkRemoteImage($path)) {
		//	return ipropertyHTML::createReturnObject('error', 'FAILED TO ADD REMOTE IMAGE: INVALID IMAGE OR PATH');
		//}

		// is this the first image of this object?
		$query = $db->getQuery(true);
		$query->select('count(id)')
				->from('#__iproperty_images')
				->where('propid = '.(int)$propid);
		
		$db->setQuery($query);
		$imgcount = $db->loadResult();
		$imglimit = $imgcount + 1;
		
		if(	($imglimit > $settings->maximgs && $settings->maximgs!=0) || ($imglimit > $maximgs && $maximgs!=0) ){
			return ipropertyHTML::createReturnObject('error', 'FAILED TO ADD REMOTE IMAGE: OVER IMAGE LIMIT');
		}
		
		$row = $this->getTable();
		$im['propid'] 		= (int) $propid;
		$im['owner'] 		= $user->id;
		$im['created']		= JFactory::getDate()->toSQL();
		$im['fname']		= $pathinfo['filename'];
		$im['type']			= $pathinfo['extension'] ? substr('.' . $pathinfo['extension'], 0, 4) : ''; // in case any junk is attached
		$im['remote']		= 1;
		$im['path']			= $pathinfo['dirname'] . '/'; // need to add a trailing slash
        $im['ordering']     = 0; // set to first
        $im['title']        = '';
        $im['description']  = '';
        
		// do the bind and store
		if (!$row->bind( $im )) {
			return ipropertyHTML::createReturnObject('error', $row->getError());
		}	
		if (!$row->store()) {
			return ipropertyHTML::createReturnObject('error', $row->getError());
		}
		
        // return the whole object just inserted to be parsed and added to list
        if($row->id && $row->reorder("propid = ".$im['propid']." AND type = ".$db->Quote($im->type))){
            $im['id'] = $row->id;
        }
		return ipropertyHTML::createReturnObject('ok', 'REMOTE IMAGE STORED SUCCESSFULLY AS '.$row->id, $im);
	}
    
	// sort the images
    public function ajaxSaveImageTags($id, $title = '', $descr = '')
    {
		$im = array();
		$im['title'] = $title;
		$im['description'] = $descr;
		
		$row = $this->getTable();
		$row->load($id);
		// do the bind and store
		if (!$row->bind( $im )) {
			return ipropertyHTML::createReturnObject('error', $row->getError());
		}	
		if (!$row->store()) {
			return ipropertyHTML::createReturnObject('error', $row->getError());
		} else if ($row->reorder("propid = ".$row->propid." AND type = '".$row->type."'")){
			return ipropertyHTML::createReturnObject('ok', 'IMAGE TAGS SAVED SUCCESSFULLY');
		}
	}

	/*****************************************
	// UTILITY FUNCTIONS
	*****************************************/
	
	// clean filenames
	private function sanitize($filename)
	{
		jimport('joomla.filesystem.file');
        
        $filename	= str_replace(' ', '_', $filename);
        $filename	= str_replace('(', '_', $filename);
        $filename	= str_replace(')', '_', $filename);
        $filename	= str_replace('__', '_', $filename);
		$filename 	= JFile::makeSafe($filename);
		$fname		= JFile::stripExt($filename);

		//make a unique filename 
		$uniq = uniqid($fname);

		//create new filename
		$filename = $uniq;

		return $filename;
	}
	
    // function to verify remote path is valid and is valid image type
    private function checkRemoteImage($path)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $path);
        // only grab headers
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if(curl_exec($ch)!==FALSE){
            if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != '200') return false;
            // don't accept gifs due to known security exploits
            if (curl_getinfo($ch, CURLINFO_CONTENT_TYPE) != 'image/jpeg' && curl_getinfo($ch, CURLINFO_CONTENT_TYPE) != 'image/png') return false;
            return true;
        } else {
            return false;
        }
    } 	
	
	private function resizeIMG($is_thmb, $src_file, $dest_file, $width, $height, $prop, $quality)
    {
        $settings   = ipropertyAdmin::config();
        $path       = JPATH_SITE;
        $imagetype  = array( 1 => 'GIF', 2 => 'JPG', 3 => 'PNG');
        $imginfo    = getimagesize($src_file);
        if ($imginfo == null) {
            $error = JText::_('COM_IPROPERTY_NO_FILE_FOUND');
            return $error;
        }

        $imginfo[2] = $imagetype[$imginfo[2]];

        // GD can only handle JPG & PNG images
        if ($imginfo[2] != 'JPG' && $imginfo[2] != 'GIF' &&  $imginfo[2] != 'PNG' ) {
            $error = "GDERROR1";
            return $error;
        }

        // source height/width
        $srcWidth = $imginfo[0];
        $srcHeight = $imginfo[1];

        if($prop == 1) {
            if (!$width) return JText::_('COM_IPROPERTY_IMAGE_DIMENSIONS_INVALID'); // can't create image with 0 width and constrain proportions
            // if prop, maintain proportions
            $haveratio = $srcWidth/$srcHeight;
            if ($haveratio == 1){ // it's square
                $destWidth 	= $width;
                $destHeight = $width;
            } else { // it's horizontal or vertical
                $destWidth 	= $width;
                $destHeight = round($width / $haveratio);
            } 
        } else {
            // we don't care about the ratio, we're building to their specs
            if (!$height || !$width) return JText::_('COM_IPROPERTY_IMAGE_DIMENSIONS_INVALID'); // can't create image with 0 width or height
            $destWidth = (int)($width);
            $destHeight = (int)($height);
        }

    	if (!function_exists('imagecreatefromjpeg')) {
            return JText::_('GDERROR2');
    	}
    	if ($imginfo[2] == 'JPG'){
            $src_img = imagecreatefromjpeg($src_file);
        } else if($imginfo[2] == 'GIF') {
            $src_img = imagecreatefromgif($src_file);
        } else if($imginfo[2] == 'PNG'){
            $src_img = imagecreatefrompng($src_file);
        }

    	if (!$src_img) return JText::_('GDERROR3');

    	if(function_exists("imagecreatetruecolor")){
            $dst_img = imagecreatetruecolor($destWidth, $destHeight);
		} else {
		   	$dst_img = imagecreate($destWidth, $destHeight);
        }

		if(function_exists("imagecopyresampled")){
			imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, (int)$destWidth, (int)$destHeight, $srcWidth, $srcHeight);
		} else {
			imagecopyresized($dst_img, $src_img, 0, 0, 0, 0,(int) $destWidth, (int)$destHeight, $srcWidth, $srcHeight);
        }

		if(!$is_thmb && $settings->watermark){
            /* drop shadow watermark thanks to hkingman */
            $wmstr = $settings->watermark_text;
            $wmstr = "(c)" . date("Y") . " " . $wmstr;
            $ftcolor2 = imagecolorallocate($dst_img,239,239,239);
            $ftcolor = imagecolorallocate($dst_img,15,15,15);
            imagestring ($dst_img, 2,11, $destHeight-20, $wmstr, $ftcolor);
            imagestring ($dst_img, 2,10, $destHeight-21, $wmstr, $ftcolor2);
			// alternate watermark syntax for use with true type fonts
			//$fontfile = "/path/to/your/font/fontname.ttf";
			//imagettftext ($dst_img, 28, 0, 150, 20, $ftcolor2, $fontfile, $wmstr );
    	}
		imagejpeg($dst_img, $dest_file, $quality);
    	imagedestroy($src_img);
    	imagedestroy($dst_img);

  		// Set mode of uploaded picture
        chmod($dest_file, octdec('644'));

  		// We check that the image is valid
  		$imginfo = getimagesize($dest_file);
  		if ($imginfo == null){
    		return JText::_('COM_IPROPERTY_IMAGE_INFO_NOT_RETURNED');
  		}else{
            //return true;
        }
  	}	
}
?>