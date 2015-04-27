<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controlleradmin');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.archive');
jimport('joomla.log.log');
ini_set('auto_detect_line_endings',TRUE);

class IpropertyControllerBulkimport extends JControllerAdmin
{
    private $app;
    private $database;
    private $log;
    private $debug;
    private $dumptables;
    private $create_no_match;
    private $settings;
	private $datafile;

    public function import()
    {
        $this->app          = JFactory::getApplication();
        $this->database     = JFactory::getDbo();
        $this->settings     = ipropertyAdmin::config();
        $this->stype_array  = ipropertyHTML::get_stypes();       

        if(JRequest::getInt('debug')){
            $this->debug = true;
            JLog::addLogger( array('text_file' => 'iproperty_import.log.php'));
            if($this->debug) JLog::add('Constructing Bulk Import');
        }
        
        if(JRequest::getInt('create_no_match')){
            $this->create_no_match = true;
            if($this->debug) JLog::add('create_no_match is true');
        }
		
		if(!JRequest::getVar('datafile')){
            $msg = JText::_( 'COM_IPROPERTY_NO_FILE_SELECTED' );
            $type = 'notice';
            if($this->debug) JLog::add('No file selected');
            $this->app->redirect('index.php?option=com_iproperty&view=bulkimport', $msg, $type);
            return false;
        } else { 
            if($this->debug) JLog::add('Beginning bulk import function');
        }

        $this->datafile      = JPATH_SITE.'/media/com_iproperty/'.JRequest::getVar('datafile');

        // set dumptables var
        $this->dumptables = (JRequest::getVar('empty')) ? true : false;
        
        // prepare connection for UTF8
        $this->database->setQuery("SET CHARACTER SET 'utf8'");
        if(!$this->database->execute()){
            $msg = JText::_( 'COM_IPROPERTY_QUERIES_EXECUTION_FAILED' ).' IP Error1 - row ' . $inc . ': ' . $this->database->getErrorMsg();
            $type = 'notice';
            if($this->debug) JLog::add($this->database->getErrorMsg());
            $this->app->redirect('index.php?option=com_iproperty&view=bulkimport', $msg, $type);
        }

        $this->database->setQuery("SET NAMES 'utf8'");
        if(!$this->database->execute()){
            $msg = JText::_( 'COM_IPROPERTY_QUERIES_EXECUTION_FAILED' ).' IP Error2 - row ' . $inc . ': ' . $this->database->getErrorMsg();
            $type = 'notice';
            if($this->debug) JLog::add($this->database->getErrorMsg());
            $this->app->redirect('index.php?option=com_iproperty&view=bulkimport', $msg, $type);
        }

        if ($this->datafile && (strpos($this->datafile, 'xml') !== false)){
            // it's apparently XML
            require_once JPATH_COMPONENT_ADMINISTRATOR.'/classes/importXML.php';
            if($this->debug) JLog::add('Creating importXML object');
            $import = new importXML($this->datafile, $this->debug, $this->dumptables, $this->create_no_match);
        } else if ($this->datafile && (strpos($this->datafile, 'csv') !== false)) {
            // looks like csv
            require_once JPATH_COMPONENT_ADMINISTRATOR.'/classes/importCSV.php';
            if($this->debug) JLog::add('Creating importCSV object');
            $import = new importCSV($this->datafile, $this->debug, $this->dumptables, $this->create_no_match);
        } else {
            $msg = JText::_( 'COM_IPROPERTY_IMPORTFILE_UNKNOWN' );
            $type = 'notice';
            if($this->debug) JLog::add('Import file type unrecognized.');
            $this->app->redirect('index.php?option=com_iproperty&view=bulkimport', $msg, $type);
        }
    }
    
    public function cancel()
    {
        // Check for request forgeries
        JSession::checkToken() or die( 'Invalid Token' );
        $this->setRedirect( 'index.php?option=com_iproperty' );
    }
}
?>