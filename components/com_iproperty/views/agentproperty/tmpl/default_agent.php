<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');

$agentlink          = JRoute::_(ipropertyHelperRoute::getAgentPropertyRoute($this->agent->id.':'.$this->agent->alias));
$companylink        = JRoute::_(ipropertyHelperRoute::getCompanyPropertyRoute($this->agent->companyid.':'.$this->agent->co_alias));
$agentcontactlink   = JRoute::_(ipropertyHelperRoute::getContactRoute('agent', $this->agent->id.':'.$this->agent->alias));

// check URL for agent website and add http if required
if ($this->agent->website && substr($this->agent->website, 0, 4) != 'http') $this->agent->website = 'http://'.$this->agent->website;
$agent_img  = ($this->settings->agent_show_image && $this->agent->icon && $this->agent->icon != 'nopic.png') ? ipropertyHTML::getIconpath($this->agent->icon, 'agent') : false;
$agent_span = ($agent_img) ? 10 : 12;
$divider = '&#160;<span class="divider">-</span>&#160;';
?>

<div class="row-fluid ip-row<?php echo $this->k; ?>">
    <div class="span12" itemscope itemtype="http://www.schema.org/RealEstateAgent">
        <?php if($agent_img): ?>
        <div class="span2 pull-left ip-agent-overview-img">
            <div class="ip-agent-thumb-holder">
                <div class="ip-agent-photo">
                    <a href="<?php echo $agentlink; ?>">
                        <img itemprop="image" src="<?php echo $agent_img; ?>" alt="<?php echo $this->agent->name; ?>" width="<?php echo $this->agent_photo_width; ?>" class="thumbnail" />
                    </a>
                </div>
            </div>
        </div> 
        <?php endif; ?>
        <div class="span<?php echo $agent_span; ?> ip-agent-overview-desc">
            <?php if($this->ipauth->canEditAgent($this->agent->id)) echo '<div class="iplistaction">'.JHtml::_('icon.edit', $this->agent, 'agent').'</div>'; ?>
            <a href="<?php echo $agentlink; ?>"><b><span itemprop="name"><?php echo ipropertyHTML::getAgentName($this->agent->id); ?></span></b></a>
            <?php if($this->agent->companyid): ?>
                <?php echo $divider; ?><a href="<?php echo $companylink; ?>"><em><?php echo ipropertyHTML::getCompanyName($this->agent->companyid); ?></em></a>
            <?php endif; ?>
            <ul class="nav nav-tabs ip-overview-tabs">
                <?php                        
                if($this->agent->email && $this->settings->agent_show_contact && JRequest::getVar('view') != 'contact') echo '<li class="ip-contact pull-right"><a href="' . $agentcontactlink . '"><span class="icon-envelope ip-pointer hasTooltip" title="'.JText::_('COM_IPROPERTY_CONTACT_AGENT' ).'"></span></a></li>';
                if(JRequest::getVar('view') != 'agentproperties') echo '<li class="ip-props pull-right"><a href="'.$agentlink.'"><span class="icon-search ip-pointer hasTooltip" title="'.JText::_('COM_IPROPERTY_VIEW_PROPERTIES' ).'"></span></a></li>';
                if($this->agent->website && $this->settings->agent_show_website) echo '<li class="ip-website pull-right"><a href="'.$this->agent->website.'" target="_blank" itemprop="url"><span class="icon-home ip-pointer hasTooltip" title="'.JText::_('COM_IPROPERTY_VISIT').'"></span></a></li>';
                if($this->agent->featured) echo '<li class="ip_featured pull-right"><a href="'.$agentlink.'"><span class="icon-star ip-pointer hasTooltip" title="'.JText::_('COM_IPROPERTY_FEATURED').'"></span></a></li>';
                ?>
            </ul>
            <div class="row-fluid">
                <div class="span12">                    
                    <div class="span4 ip-agent-details">                
                        <ul class="agent-list-details small">
                            <?php
                            if($this->agent->phone && $this->settings->agent_show_phone) echo '<li class="ip-phone"><b><abbr title="'.JText::_('COM_IPROPERTY_PHONE').'">P:</abbr></b> <span class="ip-phone-container" itemprop="telephone">' . $this->agent->phone . '</span></li>';
                            if($this->agent->mobile && $this->settings->agent_show_mobile) echo '<li class="ip-cell"><b><abbr title="'.JText::_('COM_IPROPERTY_MOBILE' ).'">M:</abbr></b> <span class="ip-phone-container" itemprop="telephone">' . $this->agent->mobile . '</span></li>';
                            if($this->agent->fax && $this->settings->agent_show_fax) echo '<li class="ip-fax"><b><abbr title="'.JText::_('COM_IPROPERTY_FAX' ).'">F:</abbr></b> <span itemprop="faxNumber">' . $this->agent->fax . '</span></li>';
                            if($this->agent->email && $this->settings->agent_show_email) echo '<li class="ip-email"><b><abbr title="'.JText::_('COM_IPROPERTY_EMAIL' ).'">E:</abbr></b> <span itemprop="email">'.JHTML::_('email.cloak', $this->agent->email).'</span></li>';                                                
                            if($this->agent->alicense && $this->settings->agent_show_license) echo '<li class="ip-license"><b><abbr title="'.JText::_('COM_IPROPERTY_LICENSE' ).'">L:</abbr></b> '.$this->agent->alicense . '</li>';
                            ?>
                        </ul>
                    </div>
                    <?php if ($this->settings->agent_show_social): ?>
                    <div class="span4 ip-social">
                        <ul class="agent-list-details small">
                            <?php
                            if($this->agent->msn) echo '<li class="ip_msn"><b><abbr title="'.JText::_('COM_IPROPERTY_MSN' ).'">MSN:</abbr></b> ' . $this->agent->msn . '</li>';
                            if($this->agent->skype) echo '<li class="ip-skype"><b><abbr title="'.JText::_('COM_IPROPERTY_SKYPE' ).'">S:</abbr></b><a href="skype:' . $this->agent->skype . '?call"> ' . $this->agent->skype . '</a></li>';
                            if($this->agent->gtalk) echo '<li class="ip-gtalk"><b><abbr title="'.JText::_('COM_IPROPERTY_GTALK' ).'">G:</abbr></b> ' . $this->agent->gtalk . '</li>';
                            if($this->agent->linkedin) echo '<li class="ip-linkedin"><b><abbr title="'.JText::_('COM_IPROPERTY_LINKEDIN' ).'">LI:</abbr></b> ' . $this->agent->linkedin . '</li>';
                            if($this->agent->twitter) echo '<li class="ip-twitter"><b><abbr title="'.JText::_('COM_IPROPERTY_TWITTER' ).'">T:</abbr></b> ' . $this->agent->twitter . '</li>';
                            if($this->agent->facebook) echo '<li class="ip-facebook"><b><abbr title="'.JText::_('COM_IPROPERTY_FACEBOOK' ).'">F:</abbr></b> ' . $this->agent->facebook . '</li>';
                            if($this->agent->social1) echo '<li class="ip-social1"><b><abbr title="'.JText::_('COM_IPROPERTY_SOCIAL1').'">O:</abbr></b> ' . $this->agent->social1 . '</li>';
                            ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    <?php if ($this->settings->agent_show_address): ?>
                    <div class="span4 ip-agent-address">
                        <address>
                            <?php
                            if($this->agent->street || $this->agent->street2) echo '<strong><span itemprop="streetAddress">'.$this->agent->street.' '.$this->agent->street2.'</span></strong><br />';
                            if($this->agent->city) echo '<span itemprop="addressLocality">'.$this->agent->city.'</span>';
                            if($this->agent->locstate) echo ', <span itemprop="region">'.ipropertyHTML::getStateName($this->agent->locstate) . '</span> ';
                            if($this->agent->province) echo ', <span itemprop="region">'.$this->agent->province . '</span> ';
                            if($this->agent->postcode) echo '<span itemprop="postalCode">'.$this->agent->postcode.'</span>';
                            if($this->agent->country) echo '<br /><span itemprop="country">'.ipropertyHTML::getCountryName($this->agent->country).'</span>';
                            ?>
                        </address>
                    </div> 
                    <?php endif; ?>
                </div>
            </div>
            <div class="clearfix"></div>
            
            <?php
            if($this->agent->bio && JRequest::getVar('view') != 'agents' && JRequest::getVar('view') != 'companyagents'):
                echo '<div class="clearfix"></div>';
                echo '<div class="ip-agentbio">'.JHTML::_('content.prepare', $this->agent->bio).'</div>';
            endif;
            $this->dispatcher->trigger( 'onAfterRenderAgentList', array( &$this->agent, &$this->settings ) );
            ?>
        </div>
    </div>
</div>
<div class="clearfix"></div>