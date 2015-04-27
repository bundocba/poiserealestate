<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined('JPATH_BASE') or die;

class JFormFieldMap extends JFormField
{
    protected $type = 'map';

	protected function getInput()
	{        
        $width  = ($this->element['width']) ? $this->element['width'] : '100%';
        $height = ($this->element['width']) ? $this->element['height'] : '300px';
        $border = ($this->element['border']) ? $this->element['border'] : '#666';               
        
        echo '
            <div id="ip-map-canvas" class="ip-map-div" style="width: '.$width.'; height: '.$height.'; border: solid 1px '.$border.';">
                <div align="center" style="padding: 5px;">'.JText::_($this->description).'</div>
            </div>';
	}
}


