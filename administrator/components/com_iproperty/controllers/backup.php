<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */
 
defined( '_JEXEC' ) or die( 'Restricted access');
jimport('joomla.application.component.controllerform');

class IpropertyControllerBackup extends JControllerForm
{
	protected $text_prefix = 'COM_IPROPERTY';

    public function __construct($config = array())
	{
		parent::__construct($config);
	}

    public function getModel($name = 'Backup', $prefix = 'IpropertyModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

    public function backupDB()
	{
        // Check for request forgeries
		JSession::checkToken() or die( 'Invalid Token');
        $link   = 'index.php?option=com_iproperty&view=backup';

		$model = $this->getModel();
        if($msg = $model->backupNow()){            
            $type   = 'message';
        }else{
            $msg    = $model->getError();
            $type   = 'notice';
        }

		$cache = JFactory::getCache('com_iproperty');
		$cache->clean();
		$this->setRedirect( $link, $msg, $type );
	}
    
    public function restoreDB( )
    {
        // Check for request forgeries
		JSession::checkToken() or die( 'Invalid Token');
        $post           = JRequest::get('post');
        
        $bak_file       = $post['bak_file'];
        $prefix         = $post['bak_prefix'];
        
        if(!$bak_file){
            $link   = 'index.php?option=com_iproperty&view=restore';
            $msg    = JText::_('COM_IPROPERTY_NO_FILE_SELECTED');
            $type   = 'notice';
        }else{
            $bak_file      = JPATH_SITE.'/media/com_iproperty'.'/'.$bak_file;

            $model = $this->getModel();
            if($msg = $model->restoreNow($bak_file, $prefix)){
                $link   = 'index.php?option=com_iproperty';
                $type   = 'message';
            }else{
                $link   = 'index.php?option=com_iproperty&view=restore';
                $msg    = $model->getError();
                $type   = 'notice';
            }
        }

		$cache = JFactory::getCache('com_iproperty');
		$cache->clean();
		$this->setRedirect( $link, $msg, $type );
    }
}
?>