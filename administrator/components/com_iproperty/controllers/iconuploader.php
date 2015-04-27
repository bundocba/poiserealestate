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

class IpropertyControllerIconuploader extends ipropertyController
{
	public function __construct()
	{
		parent::__construct();

		// Register Extra task
		$this->registerTask( 'companiesimgup'   , 'uploadicon');
        $this->registerTask( 'agentsimgup'      , 'uploadicon');
        $this->registerTask( 'categoriesimgup'  , 'uploadicon');
	}

	public function uploadicon()
	{        
		JSession::checkToken() or die( 'Invalid Token');
        
		$app        = JFactory::getApplication();
        $settings   = ipropertyAdmin::config();
		$file 		= JRequest::getVar('userfile', '', 'files', 'array');
		$task 		= JRequest::getVar('task');       

		//set the target directory
        switch($task){
            case 'companiesimgup':
                $imgwidth = $settings->company_photo_width;
                $base_Dir = JPATH_SITE.'/media/com_iproperty/companies/';
            break;

            case 'agentsimgup':
                $imgwidth = $settings->agent_photo_width;
                $base_Dir = JPATH_SITE.'/media/com_iproperty/agents/';
            break;

            case 'categoriesimgup':
                $imgwidth = $settings->cat_photo_width;
                $base_Dir = JPATH_SITE.'/media/com_iproperty/categories/';
            break;
        }

		//do we have an upload?
		if (empty($file['name'])) {
			echo "<script> alert('".JText::_('COM_IPROPERTY_IMAGE_EMPTY')."'); window.history.go(-1); </script>\n";
			$app->close();
		}

		//check the image
		if (ipropertyIcon::check($file, $settings) === false) {
            echo "<script> alert('".htmlspecialchars(JText::_('COM_IPROPERTY_CANNOT_CHECK_ICON'))."'); window.history.go(-1); </script>\n";
			$app->redirect($_SERVER['HTTP_REFERER']);
		}

		//sanitize the image filename
		$filename = ipropertyIcon::sanitize($base_Dir, $file['name']);
		$filepath = $base_Dir.$filename;

        if(!ipropertyIcon::resizeImg($file['tmp_name'], $filepath, $imgwidth, 9999)){
            echo "<script> alert('".htmlspecialchars(JText::_('COM_IPROPERTY_UPLOAD_FAILED'))."'); window.history.go(-1); </script>\n";
			$app->close();
        }else{
			echo "<script>window.history.go(-1); window.parent.ipSwitchIcon('$filename');</script>\n";
			$app->close();
        }
	}

	public function delete()
	{
		jimport('joomla.filesystem.file');
        
        $app  = JFactory::getApplication();
        $option     = JRequest::getCmd('option');
        
		$images	= JRequest::getVar('rm', array(), '', 'array');
		$folder = JRequest::getVar('folder');

		$successful = 0;
        if (count($images)) {
			foreach ($images as $image)
			{
				if ($image !== JFilterInput::clean($image, 'path')) {
					JError::raise(E_WARNING, 100, JText::_('COM_IPROPERTY_UNABLE_TO_DELETE').' '.htmlspecialchars($image, ENT_COMPAT, 'UTF-8'));
					continue;
				}elseif($image == 'nopic.png'){
                    JError::raise(E_WARNING, 100, JText::_('COM_IPROPERTY_CANNOT_DELETE_DEFAULT_IMG').' '.htmlspecialchars($image, ENT_COMPAT, 'UTF-8'));
					continue;
                }

				$fullPath = JPath::clean(JPATH_SITE.'/media/com_iproperty'.'/'.$folder.'/'.$image);
				if (is_file($fullPath)) {
					if(JFile::delete($fullPath)) $successful++;
				}
			}
		}

        switch($folder){
            case 'companies':
                $task = 'selectcompaniesimg';
            break;

            case 'agents':
                $task = 'selectagentsimg';
            break;

            case 'categories':
                $task = 'selectcategoriesimg';
            break;
		}
        
        if($successful > 0){
            $this->setMessage(JText::plural('COM_IPROPERTY_N_ITEMS_DELETED', $successful));            
        }
        $this->setRedirect('index.php?option=com_iproperty&view=iconuploader&task='.$task.'&tmpl=component');
	}
}
?>