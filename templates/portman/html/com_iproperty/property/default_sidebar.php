<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');

$agent_photo_width      = ($this->settings->agent_photo_width) ? $this->settings->agent_photo_width : '90';
$available_class        = ($this->p->available && $this->p->available < JFactory::getDate()->toSql()) ? 'success' : 'error';

$saddress = ipropertyHTML::getStreetAddress($this->settings, $this->p, '', $pview = true);
$faddress = ipropertyHTML::getFullAddress($this->p, $saddress);

//set side column if any conditions apply
$sidecol = '<div class="well">';        
    if($this->p->available) $sidecol .= '<div class="alert alert-'.$available_class.'"><b>'.JText::_('COM_IPROPERTY_AVAILABLE').':</b><br /><span>'.JFactory::getDate($this->p->available)->format(JText::_('COM_IPROPERTY_DATE_FORMAT_AVAILABLE')).'</span></div>';
    if($this->p->vtour)     $sidecol .= '<a class="ip-sidecol btn btn-info ip-vtour" href="'.$this->p->vtour.'" target="_blank"><span class="icon-bookmark"></span> '.JText::_('COM_IPROPERTY_VTOUR' ).'</a>';
    
    $sidecol .= '<div class="ip-sidecol ip-mainaddress">'.$faddress.'</div>';
    if ($this->p->county)   $sidecol .= '<div class="ip-sidecol"><b>'.JText::_('COM_IPROPERTY_COUNTY' ).'</b>: '. $this->p->county.'</div>';
    if ($this->p->region)   $sidecol .= '<div class="ip-sidecol"><b>'.JText::_('COM_IPROPERTY_REGION' ).'</b>: '. $this->p->region.'</div>';
    if ($this->p->stype)    $sidecol .= '<div class="ip-sidecol"><b>'.JText::_('COM_IPROPERTY_SALE_TYPE' ).'</b>: '. ipropertyHTML::get_stype($this->p->stype).'</div>';
    if ($this->p->mls_id)   $sidecol .= '<div class="ip-sidecol"><b>'.JText::_('COM_IPROPERTY_REF' ).'</b>: '. $this->p->mls_id.'</div>';
    if ($this->p->last_updated && $this->p->updated) $sidecol .= '<div class="ip-sidecol"><b>'.JText::_('COM_IPROPERTY_LAST_MODIFIED' ).'</b>: '. $this->p->last_updated.'</div>';

    // Display linked categories in sidebar
    if ($this->p->available_cats)
    {
        $sidecol .= '<div class="ip-sidecol ip-categories"><b>'.JText::_('COM_IPROPERTY_CATEGORY' ).':</b> ';
        $catcount = 0;
        foreach( $this->p->available_cats as $c )
        {
            $sidecol .= ipropertyHTML::getCatIcon($c, 20, false, true);
            $catcount++;
            if($catcount < count($this->p->available_cats)) $sidecol .= ', ';
        }
        $sidecol .= '</div>';
    }
    if ($this->settings->show_hits)
    {
        $sidecol .= '<div class="ip-sidecol ip-hits"><span class="label label-info">'.$this->p->hits.' '.JText::_('COM_IPROPERTY_HITS').'</span></div>';
    }
$sidecol .= '</div>';


if ($this->agents && $this->settings->show_agent && !$this->p->listing_info)
{                                
    foreach ($this->agents as $a)
    {
        $alink              = JRoute::_(ipropertyHelperRoute::getAgentPropertyRoute($a->id.':'.$a->alias));
        $colink             = JRoute::_(ipropertyHelperRoute::getCompanyPropertyRoute($a->company.':'.$a->co_alias));
        $agentcontactlink   = JRoute::_(ipropertyHelperRoute::getContactRoute('agent',$a->id.':'.$a->alias));                    

        $sidecol .= '<div class="well">';
            if($a->icon && $this->settings->agent_show_image)
            {
                $icon = ipropertyHTML::getIconpath($a->icon, 'agent');
                $sidecol .= '<a href="'.$alink.'"><img src="'.$icon . '" width="'.$agent_photo_width.'" border="0" alt="" class="thumbnail" /></a>';
            }
            $sidecol .= '<a href="' . $alink . '"><b>' . ipropertyHTML::getAgentName($a->id) . '</b></a><br />';
            $sidecol .= '<a href="' . $colink . '">' . ipropertyHTML::getCompanyName($a->companyid) . '</a><br />';
            $sidecol .= '<div class="clearfix"></div><br />';

            // email and phone numbers
            if($a->email && $this->settings->agent_show_email) 
                $sidecol .= '<div class="ip-sidecol sidecol-email"><b><abbr title="'.JText::_('COM_IPROPERTY_EMAIL').'">E:</abbr></b> ' . JHTML::_('email.cloak', $a->email.'?subject='.JText::_('Re').': '.rawurlencode($this->p->street_address), true, $a->email) . '</div>';
            if($a->phone && $this->settings->agent_show_phone) 
                $sidecol .= '<div class="ip-sidecol sidecol-phone"><b><abbr title="'.JText::_('COM_IPROPERTY_PHONE').'">P:</abbr></b> ' . $a->phone . '</div>';
            if($a->mobile && $this->settings->agent_show_mobile) 
                $sidecol .= '<div class="ip-sidecol sidecol-cell"><b><abbr title="'.JText::_('COM_IPROPERTY_MOBILE').'">M:</abbr></b> ' . $a->mobile . '</div>';
            $sidecol .= '<div class="clearfix"></div>';
            if($a->alicense && $this->settings->agent_show_license) 
                $sidecol .= '<div class="ip-sidecol sidecol-license"><b><abbr title="'.JText::_('COM_IPROPERTY_LICENSE').'">L:</abbr></b> ' . $a->alicense .'</div>';
            
            // social media contact
            if ($this->settings->agent_show_social)
            {
                if($a->msn) 
                    $sidecol .= '<div class="ip-sidecol sidecol-msn"><b><abbr title="'.JText::_('COM_IPROPERTY_MSN' ).'">MSN:</abbr></b> '.$a->msn.'</div>';
                if($a->skype) 
                    $sidecol .= '<div class="ip-sidecol sidecol-skype"><b><abbr title="'.JText::_('COM_IPROPERTY_SKYPE' ).'">S:</abbr></b> <a href="skype:' . $a->skype . '?call"> ' . $a->skype . '</a></div>';
                if($a->gtalk) 
                    $sidecol .= '<div class="ip-sidecol sidecol-gtalk"><b><abbr title="'.JText::_('COM_IPROPERTY_GTALK' ).'">G:</abbr></b> '.$a->gtalk.'</div>';
                if($a->linkedin) 
                    $sidecol .= '<div class="ip-sidecol sidecol-linkedin"><b><abbr title="'.JText::_('COM_IPROPERTY_LINKEDIN' ).'">LI:</abbr></b> '.$a->linkedin.'</div>';
                if($a->twitter) 
                    $sidecol .= '<div class="ip-sidecol sidecol-twitter"><b><abbr title="'.JText::_('COM_IPROPERTY_TWITTER' ).'">T:</abbr></b> '.$a->twitter.'</div>';
                if($a->facebook) 
                    $sidecol .= '<div class="ip-sidecol sidecol-facebook"><b><abbr title="'.JText::_('COM_IPROPERTY_FACEBOOK' ).'">F:</abbr></b> '.$a->facebook.'</div>';
                if($a->social1) 
                    $sidecol .= '<div class="ip-sidecol sidecol-facebook"><b><abbr title="'.JText::_('COM_IPROPERTY_SOCIAL1').'">O:</abbr></b> '.$a->social1.'</div>';
            }

            // show contact form button if email is set and contact agent setting is enabled
            if($a->email && $this->settings->agent_show_contact)
                $sidecol .= '<div class="clearfix"></div><a href="'.$agentcontactlink.'" class="btn pull-right ip-agent-contact-btn">'.JText::_('COM_IPROPERTY_CONTACT_AGENT' ).'</a><div class="clearfix"></div>';               

            // @todo: this is kind of a hack to allow the plugin to return data in the loop. Maybe figure out a cleaner solution later
            $plugins_ext = $this->dispatcher->trigger('onAfterRenderPropertyAgent', array( &$a, &$this->settings ));
            if (count($plugins_ext)) $sidecol .= $plugins_ext[0];
            
        $sidecol .= '</div>';
    }                
}

if ($this->openhouses)
{
    $sidecol .= '<div class="well">';
    foreach ($this->openhouses as $o)
    {
        $oh_title = ($o->name) ? $o->name : JText::_('COM_IPROPERTY_OPENHOUSE');
        
        $tipstart   = ($o->comments) ? '<span class="hasTooltip" title="'.JText::_('COM_IPROPERTY_OPENHOUSE' ).'::'.htmlentities($o->comments, ENT_QUOTES, 'UTF-8').'">' : '';
        $tipend     = ($o->comments) ? '</span>' : '';
        $start    	= JHTML::_('date', htmlspecialchars($o->startdate),JText::_('COM_IPROPERTY_DATE_FORMAT_IPOH'));
        $end		= JHTML::_('date', htmlspecialchars($o->enddate),JText::_('COM_IPROPERTY_DATE_FORMAT_IPOH'));
        $sidecol    .= '
            <div class="ip-sidecol oh-header"><span class="label label-info">'.$tipstart.$oh_title.$tipend.'</span></div>
            <div class="ip-sidecol oh-details">
                <div class="ip-sidecol oh-date-start alert alert-success small"><b>Start</b><br />'.$start.'</div>
                <div class="ip-sidecol oh-date-enda alert alert-error small"><b>End</b><br />'.$end.'</div>
            </div>';
    }
    $sidecol .= '</div>';
}

// @TODO: add Joomla tags
//$this->item->tagLayout = new JLayoutFile('joomla.content.tags'); 
//$sidecol .= $this->item->tagLayout->render($this->item->tags->itemTags);

// add the listing agent for pro sites
if ($this->p->listing_info){
	$listing_info = ipropertyHTML::getListingInfo($this->p, $this->params);
	$sidecol .= '<div class="ip-sidecol listing-details"><span class="ipsmall">'.$listing_info.'</span></div>';
}
echo $sidecol;
?>
