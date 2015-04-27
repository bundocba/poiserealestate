<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2013 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class TableRSForm_Directory extends JTable
{		
	public $ViewLayoutAutogenerate = 1;
	public $ViewLayoutName = 'dir-inline';
	
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	 
	public function __construct(& $db) {
		parent::__construct('#__rsform_directory', 'formId', $db);
	}
}