<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access');

$companylink        = JRoute::_(ipropertyHelperRoute::getCompanyPropertyRoute($this->company->id.':'.$this->company->alias));
$companyagentslink  = JRoute::_(ipropertyHelperRoute::getCompanyAgentRoute($this->company->id.':'.$this->company->alias));
$companycontactlink = JRoute::_(ipropertyHelperRoute::getContactRoute('company', $this->company->id.':'.$this->company->alias));

// check URL for company website and add http if required
if ($this->company->website && substr($this->company->website, 0, 4) != 'http') $this->company->website = 'http://'.$this->company->website;
$company_img    = ($this->settings->co_show_image && $this->company->icon && $this->company->icon != 'nopic.png') ? ipropertyHTML::getIconpath($this->company->icon, 'company') : false;
$company_span   = ($company_img) ? 10 : 12;
?>

<div class="row-fluid ip-row<?php echo $this->k; ?>">
    <div class="span12" itemscope itemtype="http://www.schema.org/RealEstateAgent">
        <?php if($company_img): ?>
        <div class="span2 pull-left company_overview_img">
            <div class="company_thumb_holder">
                <div class="ip_company_photo">
                    <a href="<?php echo $companylink; ?>">
                        <img itemprop="image" src="<?php echo $company_img; ?>" alt="<?php echo $this->company->name; ?>" width="<?php echo $this->co_photo_width; ?>" class="thumbnail" />
                    </a>
                </div>
            </div>
        </div> 
        <?php endif; ?>
        <div class="span<?php echo $company_span; ?> company_overview_desc">
            <?php if($this->ipauth->canEditCompany($this->company->id)) echo '<div class="iplistaction">'.JHtml::_('icon.edit', $this->company, 'company').'</div>'; ?>
            <a href="<?php echo $companylink; ?>"><b><span itemprop="name"><?php echo ipropertyHTML::getCompanyName($this->company->id); ?></span></b></a>
            <ul class="nav nav-tabs ip-overview-tabs">
                <?php                        
                if($this->company->email && $this->settings->co_show_contact && JRequest::getVar('view') != 'contact') echo '<li class="ip-contact pull-right"><a href="'.$companycontactlink.'"><span class="icon-envelope hasTooltip" title="'.JText::_('COM_IPROPERTY_CONTACT_COMPANY' ).'"></span></a></li>';
                if(JRequest::getVar('view') != 'companyproperties') echo '<li class="ip-props pull-right"><a href="'.$companylink.'"><span class="icon-search hasTooltip" title="'.JText::_('COM_IPROPERTY_VIEW_PROPERTIES' ).'"></span></a></li>';
                if(JRequest::getVar('view') != 'companyagents') echo '<li class="ip-agents pull-right"><a href="'.$companyagentslink.'"><span class="icon-user hasTooltip" title="'.JText::_('COM_IPROPERTY_VIEW_AGENTS' ).'"></span></a></li>';
                if($this->company->website && $this->settings->co_show_website) echo '<li class="ip-website pull-right"><a href="'.$this->company->website.'" target="_blank" itemprop="url"><span class="icon-home hasTooltip" title="'.JText::_('COM_IPROPERTY_VISIT').'"></span></a></li>';
                if($this->company->featured) echo '<li class="ip_featured pull-right"><a href="'.$companylink.'"><span class="icon-star hasTooltip" title="'.JText::_('COM_IPROPERTY_FEATURED').'"></span></a></li>';
                ?>
            </ul>
            <div class="row-fluid">
                <div class="span12">                    
                    <div class="span6 ip_company_details">                
                        <ul class="company-list-details small">
                            <?php
                            if($this->company->phone && $this->settings->co_show_phone) echo '<li class="ip-phone"><b><abbr title="'.JText::_('COM_IPROPERTY_PHONE').'">P:</abbr></b> <span class="ip-phone-container" itemprop="telephone">' . $this->company->phone . '</span></li>';
                            if($this->company->fax && $this->settings->co_show_fax) echo '<li class="ip-fax"><b><abbr title="'.JText::_('COM_IPROPERTY_FAX' ).'">F:</abbr></b> <span itemprop="faxNumber">' . $this->company->fax . '</span></li>';
                            if($this->company->email && $this->settings->co_show_email) echo '<li class="ip-email"><b><abbr title="'.JText::_('COM_IPROPERTY_EMAIL' ).'">E:</abbr></b> <span itemprop="email">'.JHTML::_('email.cloak', $this->company->email).'</span></li>';                                                
                            if($this->company->clicense && $this->settings->co_show_license) echo '<li class="ip-license"><b><abbr title="'.JText::_('COM_IPROPERTY_LICENSE' ).'">L:</abbr></b> '.$this->company->clicense . '</li>';
                            ?>
                        </ul>
                    </div>
                    <?php if ($this->settings->co_show_address): ?>
                    <div class="span6 ip_company_address">
                        <span itemprop="address" itemscope itemtype="http://data-vocabulary.org/Address">
                        <address>
                            <?php
                            if($this->company->street) echo '<strong><span itemprop="streetAddress">'.$this->company->street.'</span></strong><br />';
                            if($this->company->city) echo '<span itemprop="addressLocality">'.$this->company->city.'</span>';
                            if($this->company->locstate) echo ', <span itemprop="region">'.ipropertyHTML::getStateName($this->company->locstate) . '</span> ';
                            if($this->company->province) echo ', <span itemprop="region">'.$this->company->province . '</span> ';
                            if($this->company->postcode) echo '<span itemprop="postalCode">'.$this->company->postcode.'</span>';
                            if($this->company->country) echo '<br /><span itemprop="country">'.ipropertyHTML::getCountryName($this->company->country).'</span>';
                            ?>
                        </address>
                        </span>
                    </div> 
                    <?php endif; ?>
                </div>
            </div>
            <div class="clearfix"></div>            
            <?php $this->dispatcher->trigger( 'onAfterRenderCompanyList', array( &$this->company, &$this->settings ) ); ?>
        </div>
    </div>
</div>
<div class="clearfix"></div>
