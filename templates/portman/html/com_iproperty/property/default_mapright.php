<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

$details_array = array( "beds"         => JText::_('COM_IPROPERTY_BEDS' ),
                 "baths"        => JText::_('COM_IPROPERTY_BATHS' ),
                 "formattedsqft"         => (!$this->settings->measurement_units) ? JText::_('COM_IPROPERTY_SQFT' ) : JText::_('COM_IPROPERTY_SQM' ),
                 "lotsize"      => JText::_('COM_IPROPERTY_LOT_SIZE' ),
                 "lot_acres"    => JText::_('COM_IPROPERTY_LOT_ACRES' ),
                 "yearbuilt"    => JText::_('COM_IPROPERTY_YEAR_BUILT' ),
                 "heat"         => JText::_('COM_IPROPERTY_HEAT' ),
                 "garage_type"  => JText::_('COM_IPROPERTY_GARAGE_TYPE' ),
                 "roof"         => JText::_('COM_IPROPERTY_ROOF' ));

foreach ($details_array as $key=>$value)
{
    if ($this->p->$key AND $this->p->$key != '0.0')
    {
        echo '
        <dl class="clearfix ip-mapright-'.$key.'">
            <dt class="pull-left">'.$value.'</dt>
            <dd class="pull-right">'.$this->p->$key.'</dd>
        </dl>';
    }
}
?>
