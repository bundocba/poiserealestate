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

class IpropertyControllerEditcss extends JControllerForm
{
	protected $text_prefix = 'COM_IPROPERTY';

    protected function allowAdd($data = array())
	{
        $allow  = parent::allowAdd($data);
        
        // Check if the user should be in this editing area
        $auth   = new ipropertyHelperAuth();
        $allow  = $auth->getAdmin();
        
        return $allow;
	}

    protected function allowEdit($data = array(), $key = 'id')
	{
        $allow  = parent::allowEdit($data, $key);
        
        // Check if the user should be in this editing area
        $auth   = new ipropertyHelperAuth();
        $allow  = $auth->getAdmin();

        return $allow;
	}
    
    public function edit()
    {
        $css_file = JRequest::getVar('edit_css_file');
        $this->setRedirect(JRoute::_('index.php?option=com_iproperty&view=editcss&layout=edit&edit_css_file='.$css_file, false));
    }
 	
	public function save()
	{
        $task           = JRequest::getvar('task');
        $filecontent    = JRequest::getvar('filecontent');
        $css_file       = JRequest::getvar('edit_css_file');
        $file           = JPATH_COMPONENT_SITE.'/assets/css'.'/'.$css_file;
        
        //saving
        jimport('joomla.filesystem.file');

        if (JFile::write($file, $filecontent)) {
            switch ($task)
            {
                case 'apply' :
                    $link = 'index.php?option=com_iproperty&task=editcss.edit&edit_css_file='.$css_file;
                break;

                default :
                    $link = 'index.php?option=com_iproperty&view=editcss';
                break;
            }
            $msg	= JText::_( $this->text_prefix.'_ITEM_SAVED');
            $cache  = JFactory::getCache('com_iproperty');
            $cache->clean();
        } else {
            $type   = 'error';
            $msg 	= JText::_($this->text_prefix.'_FILE_NOT_SAVED');
            $link   = 'index.php?option=com_iproperty&view=settings';
        }
        $this->setRedirect( $link, $msg, $type );
	}
	 	
    public function cancel()
    {
        // Check for request forgeries
        JSession::checkToken() or die( 'Invalid Token');

        if(JRequest::getInt('selectview')){
            $this->setRedirect( 'index.php?option=com_iproperty&view=editcss');
        }else{
            $this->setRedirect( 'index.php?option=com_iproperty&view=settings');
        }
    }	 
    
    public function copy2template()
    {
        // Check for request forgeries
        JSession::checkToken() or die( 'Invalid Token');
        
        jimport('joomla.filesystem.file');
        
        $css_file       = JRequest::getvar('edit_css_file');
        $link           = 'index.php?option=com_iproperty&task=editcss.edit&edit_css_file='.$css_file;
        $template       = JRequest::getString('copy_template');
        
        if(!$template){
            $type   = 'error';
            $msg	= JText::_( $this->text_prefix.'_NO_TEMPLATE_SELECTED');
        }else{
            if(JFile::copy(JPATH_ROOT.'/components/com_iproperty/assets/css'.'/'.$css_file, JPATH_ROOT.'/templates'.'/'.$template.'/css'.'/'.$css_file)){
                $msg	= sprintf(JText::_($this->text_prefix.'_CSS_COPIED_SUCCESSFULLY'), $template);
            }else{
                $type   = 'error';
                $msg	= JText::_($this->text_prefix.'_CSS_NOT_COPIED_SUCCESSFULLY');
            }
        }
        $this->setRedirect( $link, $msg, $type );
    }
}
?>