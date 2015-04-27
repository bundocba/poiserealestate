<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.model' );
jimport( 'joomla.filesystem.file' );

class IpropertyModelBackup extends JModelLegacy
{
    public function backupNow()
	{
        $app  = JFactory::getApplication();
        $option     = JRequest::getCmd('option');

        $CONFIG         = new JConfig();
        $database 		= JFactory::getDBO();
        $host		    = $app->getCfg( 'host');
        $user		    = $app->getCfg( 'user');
        $password	    = $app->getCfg( 'password');
        $db			    = $app->getCfg( 'db');
        $mailfrom	    = $app->getCfg( 'mailfrom');
        $fromname	    = $app->getCfg( 'fromname');
        $livesite	    = $app->getCfg( 'live_site');
        $pluginParams   = '';
        $testing		= false;

        // You can manually set the production flag here if you don't want the "testing" option to kick in
        // at any point. Effectively it means that the query will not be run until $okToContinue is true, which
        // only occurs if today's checkFile doesn't exist.
        // If you DO manually set this flag, then of course none of the testing data will be echoed to your browser

        $mediaPath		= JPATH_ROOT.'/media/com_iproperty';
        $checkfileName	= 'ip_checkfile_';
        $today 			= date("Y-m-d");
        $dateCheckFile	= $checkfileName.$today;
        $okToContinue	= true;

        if (is_writable($mediaPath) )  // a couple of simple checks to see if we need to actually do anything
        {
            if (!$testing)
            {
                if (!touch($mediaPath.'/'.$dateCheckFile)) // Oops, we can't create the date check file, no point in continuing
                {
                    $this->setError(sprintf(JText::_('COM_IPROPERTY_CHECK_FILE_NOT_CREATED' ), $mediaPath));
                    $okToContinue = false;
                    return false;
                }
            }
        }else{
            $this->setError(sprintf(JText::_('COM_IPROPERTY_BACKUP_NOT_CREATED' ), $mediaPath));
            $okToContinue = false;
            return false;
        }

        if ($okToContinue)
        {
            // No need to do the require beforehand if not ok to continue, so we'll do it here to save an eeny weeny amount of time
            require_once (JPATH_COMPONENT_ADMINISTRATOR.'/classes/mysql_db_backup.class.php');
            JFile::delete($mediaPath.'/'.$dateCheckFile);
            $deletefile		= false;
            $compress		= 1;
            $backuppath		= 0;
            $verbose		= 1;

            // Ok, let's keep going. First we want to get rid of yesterday's jombackup_checkfile, no need to have that lying around now
            // Now we need to create the backup
            $backup_obj 	= new ip_MySQL_DB_Backup();
            $dp             = $database->getPrefix();
            $backup_obj->tablesToInclude = array(
                    $dp.'iproperty',
                    $dp.'iproperty_agentmid',
                    $dp.'iproperty_agents',
                    $dp.'iproperty_amenities',
                    $dp.'iproperty_categories',
                    $dp.'iproperty_companies',
                    $dp.'iproperty_countries',
                    $dp.'iproperty_currency',
                    $dp.'iproperty_images',
                    $dp.'iproperty_openhouses',
                    $dp.'iproperty_propmid',
                    $dp.'iproperty_saved',
                    $dp.'iproperty_settings',
                    $dp.'iproperty_states',
                    $dp.'iproperty_stypes'
                    );

            $result		       = $this->ipBackup($backup_obj, $host, $user, $password, $db, $pluginParams, $mediaPath, $fromname, $compress, $backuppath);
            $backupFile		   = $backup_obj->ip_file_name;

            if($deletefile == "1" && !empty($backupFile) )
            {
                if ($testing){
                    echo "Deleting backup file $backupFile";
                    unlink($backupFile);
                }
            }else if($testing){
                echo "Not deleting backup file $backupFile";
            }
            return JText::_('COM_IPROPERTY_BACKUP_FILE_READY_TO_DOWNLOAD' ).' - '.$backup_obj->ip_file_name;
        }else{
            $this->setError($this->getError());
            return false;
        }        
    }

    protected function ipBackup($backup_obj, $host, $user, $password, $db, $pluginParams, $mediaPath, $fromname, $compress, $backuppath)
    {
        $Body 				= 'Mysql backup from'.$fromname;
        $drop_tables 		= 0;
        $create_tables 		= 0;
        $struct_only 		= 0;
        $locks 				= 1;
        $comments 			= 1;

        // Let's set the tables to ignore array.
        if(!empty($backuppath) && is_dir($backuppath) && @is_writable($backuppath)){
            $backup_dir = $backuppath;
        }else{
            $backup_dir = $mediaPath;
        }

        //----------------------- EDIT - REQUIRED SETUP VARIABLES -----------------------
        $backup_obj->server 	= $host;
        $backup_obj->port 		= 3306;
        $backup_obj->username 	= $user;
        $backup_obj->password 	= $password;
        $backup_obj->database 	= $db;
        //Tables you wish to backup. All tables in the database will be backed up if this array is null.
        $backup_obj->tables = array();
        //------------------------ END - REQUIRED SETUP VARIABLES -----------------------

        //-------------------- OPTIONAL PREFERENCE VARIABLES ---------------------
        //Add DROP TABLE IF EXISTS queries before CREATE TABLE in backup file.
        $backup_obj->drop_tables 	= $drop_tables;
        //No table structure will be backed up if false
        $backup_obj->create_tables 	= $create_tables;
        //Only structure of the tables will be backed up if true.
        $backup_obj->struct_only 	= $struct_only;
        //Add LOCK TABLES before data backup and UNLOCK TABLES after
        $backup_obj->locks 			= $locks;
        //Include comments in backup file if true.
        $backup_obj->comments 		= $comments;
        //Directory on the server where the backup file will be placed. Used only if task parameter equals MSX_SAVE.
        $backup_obj->backup_dir 	= $backup_dir.'/';
        //Default file name format.
        $backup_obj->fname_format 	= 'm_d_Y__H_i_s';
        //Values you want to be intrerpreted as NULL
        $backup_obj->null_values 	= array( );

        $savetask = MSX_SAVE;
        //Optional name of backup file if using 'MSX_APPEND', 'MSX_SAVE' or 'MSX_DOWNLOAD'. If nothing is passed, the default file name format will be used.
        $filename = '';
        //--------------------- END - REQUIRED EXECUTE VARIABLES ----------------------
        $result_bk = $backup_obj->Execute($savetask, $filename, $compress);
        if (!$result_bk)
        {
            $output = $backup_obj->error;
        }else{
            $output = $Body.': ' . strftime('%A %d %B %Y  - %T ') . ' ';
        }
        return array('result' => $result_bk, 
                     'output' => $output);
    }
    
    public function restoreNow($bak_file, $prefix = '')
    {
        jimport('joomla.filesystem.archive');
        $database      = JFactory::getDBO();
        
        //if can't extract file, return with error
        if(!JArchive::extract($bak_file, JPATH_SITE.'/media/com_iproperty')){
            $this->setError(sprintf(JText::_('COM_IPROPERTY_COULD_NOT_EXTRACT_FILE'), $bak_file));
            return false;
        }
        
        // confirm that we're able to read back up file
        $text_bak_file = substr($bak_file, 0, strlen($bak_file)-3);
        if(!$bquery = JFile::read($text_bak_file)){
            $this->setError(sprintf(JText::_('COM_IPROPERTY_COULD_NOT_READ_BACKUP'), $text_bak_file));
            return false;
        }
        
        // if a prefix was entered, make sure that the prefix exists in the backup file content before executing any changes
        if($prefix && !strpos($bquery, $prefix.'iproperty')){
            $this->setError(sprintf(JText::_('COM_IPROPERTY_DB_PREFIX_NOT_FOUND'), $prefix));
            return false;
        }else if(!$prefix && !strpos($bquery, $database->getPrefix().'iproperty')){ // if no prefix was entered, make sure that current db prefix exists in the backup file content before executing any changes
            $this->setError(sprintf(JText::_('COM_IPROPERTY_DB_PREFIX_NOT_FOUND'), $database->getPrefix()));
            return false;
        }
            
        JFile::delete($text_bak_file);

        $backup_version = substr(strrchr($text_bak_file, '_v'), 2, 3);
        
        if($backup_version < '2.0'){            
            $bquery = str_replace('`state`', '`locstate`',$bquery);
            $bquery = str_replace('`published`', '`state`', $bquery);            
        }
        $bquery = str_replace('`agent_show_social1`', '`agent_show_social`', $bquery); 
        if($prefix) $bquery = str_replace($prefix, $database->getPrefix(), $bquery);
        
        // check if pro_last_run exists
        $last_run   = (property_exists(JTable::getInstance('Property', 'IPropertyTable'), 'pro_last_run')) ? true : false;
        // check if pro_api_key exists
        $api_key    = (property_exists(JTable::getInstance('Property', 'IPropertyTable'), 'pro_api_key')) ? true : false;
        // check if ip_source index exists in property table
        $database->setQuery('SHOW INDEX FROM #__iproperty WHERE Key_name = "ip_source"');
        $ip_source_prop = $database->loadObjectList();
        // check if ip_source index exists in agents table
        $database->setQuery('SHOW INDEX FROM #__iproperty_agents WHERE Key_name = "ip_source"');
        $ip_source_agents = $database->loadObjectList();
        // check if ip_source index exists in companies table
        $database->setQuery('SHOW INDEX FROM #__iproperty_companies WHERE Key_name = "ip_source"');
        $ip_source_company = $database->loadObjectList();
        // end pre-restore checks

        $emptying_query = '';
        
        // Temp solution for deprecated settings
        // Any time a setting table field is changed or removed, add it to 
        // this array so the restore doesn't break from older versions
        $deprecated_settings = array('agent_show_msn', 'agent_show_skype', 
                                    'agent_show_gtalk', 'agent_show_linkedin', 
                                    'agent_show_facebook', 'agent_show_twitter', 
                                    'adv_slider_length', 'adv_map_width', 
                                    'adv_map_height', 'googlemap_key', 'css_file', 
                                    'tab_width', 'tab_height', 'form_storeforms', 
                                    'googlemap_enable', 'feed_kml', 'feed_gbase', 
                                    'feed_gbaseuk', 'feed_zillow', 'adv_show_preview', 
                                    'adv_show_thumb', 'adv_show_radius', 'cat_featured', 'cat_featured_pos');
        foreach($deprecated_settings as $d){
            if(strpos($bquery, $d)){
                $emptying_query .= 'ALTER TABLE #__iproperty_settings ADD `'.$d.'` VARCHAR(255);';
            }
        }
        $emptying_query .= (!property_exists(JTable::getInstance('Property', 'IPropertyTable'), 'show_address')) ? 'ALTER TABLE #__iproperty ADD `show_address` TINYINT(1);' : '';
        $emptying_query .= (!property_exists(JTable::getInstance('Property', 'IPropertyTable'), 'developer')) ? 'ALTER TABLE #__iproperty ADD `developer` varchar(100) NOT NULL AFTER `property_name`;' : '';
        // end temp solution
        
        if(!$last_run) $emptying_query .= 'ALTER TABLE #__iproperty ADD `pro_last_run` TIMESTAMP;'; // 1.5.4
        if(!$api_key) $emptying_query .= 'ALTER TABLE #__iproperty ADD `pro_api_key` VARCHAR(255);'; // 1.5.4
        if(!empty($ip_source_prop)) $emptying_query .= 'ALTER IGNORE TABLE #__iproperty DROP INDEX ip_source;'; // 1.5.4
        if(!empty($ip_source_agents)) $emptying_query .= 'ALTER IGNORE TABLE #__iproperty_agents DROP INDEX ip_source;'; // 1.5.4
        if(!empty($ip_source_company)) $emptying_query .= 'ALTER IGNORE TABLE #__iproperty_companies DROP INDEX ip_source;'; // 1.5.4

        $emptying_query .= 'TRUNCATE TABLE #__iproperty;';
        $emptying_query .= 'TRUNCATE TABLE #__iproperty_agentmid;';
        $emptying_query .= 'TRUNCATE TABLE #__iproperty_agents;';
        $emptying_query .= 'TRUNCATE TABLE #__iproperty_amenities;';
        $emptying_query .= 'TRUNCATE TABLE #__iproperty_categories;';
        $emptying_query .= 'TRUNCATE TABLE #__iproperty_companies;';
        $emptying_query .= 'TRUNCATE TABLE #__iproperty_countries;';
        $emptying_query .= 'TRUNCATE TABLE #__iproperty_currency;'; //1.5.5
        $emptying_query .= 'TRUNCATE TABLE #__iproperty_images;';
        $emptying_query .= 'TRUNCATE TABLE #__iproperty_openhouses;'; // 1.5.4
        $emptying_query .= 'TRUNCATE TABLE #__iproperty_propmid;';
        $emptying_query .= 'TRUNCATE TABLE #__iproperty_saved;';
        $emptying_query .= 'TRUNCATE TABLE #__iproperty_settings;';
        $emptying_query .= 'TRUNCATE TABLE #__iproperty_states;';
        $emptying_query .= 'TRUNCATE TABLE #__iproperty_stypes;'; //1.5.5

        // Execute pre-backup query to truncate tables and make necessary backward compatible changes     
        try{
            $this->populateDatabase($database, $emptying_query);
        }
        catch (Exception $e)
        {
            $this->setError(JText::_('COM_IPROPERTY_QUERIES_EXECUTION_FAILED' ).' IP Error1 - '.$e->getMessage());
            return false;
        }

        // set char set to utf8 just in case
        $database->setUTF();

        // Execute backup data from selected backup file
        try{
            $this->populateDatabase($database, $bquery);
        }
        catch (Exception $e)
        {
            $this->setError(JText::_('COM_IPROPERTY_QUERIES_EXECUTION_FAILED' ).' IP Error2 - '.$e->getMessage());
            return false;
        }

        // now that data is restored from backup, do some cleanup
        // drop old pro columns
        $update_query = 'ALTER TABLE #__iproperty DROP `pro_last_run`;';
        $update_query .= 'ALTER TABLE #__iproperty DROP `pro_api_key`;';
        
        // Temp solution for deprecated settings
        foreach($deprecated_settings as $d)
        {
            if(strpos($bquery, $d)){
                $update_query .= 'ALTER TABLE #__iproperty_settings DROP `'.$d.'`;';
            }
        }
        $update_query .= (property_exists(JTable::getInstance('Property', 'IPropertyTable'), 'show_address')) ? 'ALTER TABLE #__iproperty DROP `show_address`;' : '';        
        
        // update any 0 values where key needs to be unique
        $update_query .= 'UPDATE #__iproperty SET ip_source = NULL WHERE ip_source = 0;';
        $update_query .= 'UPDATE #__iproperty_agents SET ip_source = NULL WHERE ip_source = 0;';
        $update_query .= 'UPDATE #__iproperty_companies SET ip_source = NULL WHERE ip_source = 0;';
        // add the unique keys to table that require unique
        $update_query .= 'ALTER TABLE #__iproperty ADD UNIQUE ( ip_source );';
        $update_query .= 'ALTER TABLE #__iproperty_agents ADD UNIQUE ( ip_source );';
        $update_query .= 'ALTER TABLE #__iproperty_companies ADD UNIQUE ( ip_source );';
        // might as well set the rest of ip_source null
        $update_query .= 'UPDATE #__iproperty_agentmid SET ip_source = NULL WHERE ip_source = 0;';
        $update_query .= 'UPDATE #__iproperty_images SET ip_source = NULL WHERE ip_source = 0;';
        $update_query .= 'UPDATE #__iproperty_propmid SET ip_source = NULL WHERE ip_source = 0;';
        $update_query .= 'UPDATE #__iproperty SET access = 1 WHERE access = 0;';
        $update_query .= 'UPDATE #__iproperty_categories SET access = 1 WHERE access = 0;';

        // Clean up backward compatible changes and set tables back to current structure
        try{
            $this->populateDatabase($database, $update_query);
        }
        catch (Exception $e)
        {
            $this->setError(JText::_('COM_IPROPERTY_QUERIES_EXECUTION_FAILED' ).' IP Error3 - '.$e->getMessage());
            return false;
        }
        
        // Seems the restore has executed succesfully at this point. return message.
        return JText::_('COM_IPROPERTY_QUERIES_EXECUTED_SUCCESSFULLY');
    }
    
    protected function populateDatabase($db, $buffer)
	{
        $return = true;

		// Get an array of queries from the schema and process them.
		$queries = $this->_splitQueries($buffer);
		foreach ($queries as $query)
		{
			// Trim any whitespace.
			$query = trim($query);

			// If the query isn't empty and is not a MySQL or PostgreSQL comment, execute it.
			if (!empty($query) && ($query{0} != '#') && ($query{0} != '-'))
			{
				// Execute the query.
				$db->setQuery($query);

				try
				{
					$db->execute();
				}
				catch (RuntimeException $e)
				{
					throw new Exception($e->getMessage());
					$return = false;
				}
			}
		}

		return $return;
	}
    
    protected function _splitQueries($sql)
	{
		$buffer    = array();
		$queries   = array();
		$in_string = false;

		// Trim any whitespace.
		$sql = trim($sql);

		// Remove comment lines.
		$sql = preg_replace("/\n\#[^\n]*/", '', "\n" . $sql);

		// Remove PostgreSQL comment lines.
		$sql = preg_replace("/\n\--[^\n]*/", '', "\n" . $sql);

		// find function
		$funct = explode('CREATE OR REPLACE FUNCTION', $sql);
		// save sql before function and parse it
		$sql = $funct[0];

		// Parse the schema file to break up queries.
		for ($i = 0; $i < strlen($sql) - 1; $i++)
		{
			if ($sql[$i] == ";" && !$in_string)
			{
				$queries[] = substr($sql, 0, $i);
				$sql = substr($sql, $i + 1);
				$i = 0;
			}

			if ($in_string && ($sql[$i] == $in_string) && $buffer[1] != "\\")
			{
				$in_string = false;
			}
			elseif (!$in_string && ($sql[$i] == '"' || $sql[$i] == "'") && (!isset ($buffer[0]) || $buffer[0] != "\\"))
			{
				$in_string = $sql[$i];
			}
			if (isset ($buffer[1]))
			{
				$buffer[0] = $buffer[1];
			}
			$buffer[1] = $sql[$i];
		}

		// If the is anything left over, add it to the queries.
		if (!empty($sql))
		{
			$queries[] = $sql;
		}

		// add function part as is
		for ($f = 1; $f < count($funct); $f++)
		{
			$queries[] = 'CREATE OR REPLACE FUNCTION ' . $funct[$f];
		}

		return $queries;
	}
}
?>