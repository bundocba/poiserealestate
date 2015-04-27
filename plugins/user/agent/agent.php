<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  User.joomla
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Joomla User plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  User.joomla
 * @since       1.5
 */
class PlgUserAgent extends JPlugin
{
	protected $db;
	
	public function onUserAfterSave($user, $isnew, $success, $msg)
	{
		if (!$success)
		{
			return false;
		}
			jimport( 'joomla.access.access' );
			$groups = JAccess::getGroupsByUser($user['id']);			if(in_array(10,$groups)){
				if($isnew){
					$query = "INSERT INTO #__iproperty_agents (`uid`,`lname`,`company`)
									VALUES (".$this->db->quote($user['id']).",
									".$this->db->quote($user['name']).",									1
									)";
					$this->db->setQuery($query);
					return $this->db->query();
				}else{					$query = "SELECT count(*) FROM #__iproperty_agents WHERE uid=".$user['id'];										$this->db->setQuery($query);					$is_insert = $this->db->loadResult();					if($is_insert){						$query = "UPDATE #__iproperty_agents SET
									`lname`= ".$this->db->quote($user['name'])."
									WHERE uid = '".(int)$user['id']."'
									";					}else{						$query = "INSERT INTO #__iproperty_agents (`uid`,`lname`,`company`)									VALUES (".$this->db->quote($user['id']).",									".$this->db->quote($user['name']).",1									)";					}

					$this->db->setQuery($query);
					return $this->db->query();
				}			}
	}
	public function onUserBeforeDelete($user)
	{
		$query = $this->db->getQuery(true)
			->delete($this->db->quoteName('#__iproperty_agents'))
			->where($this->db->quoteName('uid') . ' = ' . (int) $user['id']);

		$this->db->setQuery($query)->execute();

		return true;
	}
}
