<?php

/**

 * @version 3.3.1 2014-06-06

 * @package Joomla

 * @subpackage Intellectual Property

 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.

 * @license GNU/GPL see LICENSE.php

 */



defined( '_JEXEC' ) or die( 'Restricted access' );

JHtml::_('behavior.caption');



$munits     = (!$this->settings->measurement_units) ? JText::_( 'COM_IPROPERTY_SQFT' ) : JText::_( 'COM_IPROPERTY_SQM' );

$span_style = ($this->p->featured) ? ' style="color: ' . $this->settings->featured_accent . ';"' : '';



$listing_info = ipropertyHTML::getListingInfo($this->p, $this->params);



?>



<div class="row-fluid ip-row<?php echo $this->k; ?> ip-overview-row" id="ip-listing-<?php echo $this->p->id; ?>">

    <div class="span12">

        <div class="span3 ip-overview-img">

            <div class="ip-property-thumb-holder">                

                <?php echo ipropertyHTML::getThumbnail($this->p->id, $this->p->proplink, $this->p->street_address, '', 'class="img-polaroid ip-overview-thumb"'); ?>                

                <?php echo ipropertyHTML::displayBanners($this->p->stype, $this->p->new, $this->ipbaseurl, $this->settings, $this->p->updated); ?>

            </div>

            <h4 class="ip-overview-price pull-right"><?php echo $this->p->formattedprice; ?></h4>

        </div> 

        <div class="span9 ip-overview-desc">

            <?php

            // display openhouse info if openhouse view

            if (JFactory::getApplication()->input->getCmd('view') == 'openhouses')

            {

                $ohcomments = ($this->p->comments) ? htmlentities($this->p->comments, ENT_QUOTES, 'UTF-8') : '';

                // display openhouse header above regualar list view tmpl

                echo '<div id="ip-ohdetails" class="well well-small ip-openhouse-info">';

                        if ($this->p->ohname)

                        {

                            echo '<em class="hasTooltip" title="'.$ohcomments.'"><strong>'.$this->p->ohname.'</strong></em><br />';

                        }else if ($this->p->comments){

                            echo '<p>'.$this->p->comments.'</p>';

                        }

                        if ($this->p->ohstart) echo sprintf(JText::_('JLIB_HTML_PUBLISHED_START'), JHTML::_('date', htmlspecialchars($this->p->ohstart),JText::_('DATE_FORMAT_LC2'))).'<br />';

                        if ($this->p->ohend) echo sprintf(JText::_('JLIB_HTML_PUBLISHED_FINISHED'), JHTML::_('date', htmlspecialchars($this->p->ohend),JText::_('DATE_FORMAT_LC2')));

                echo '</div>';

            }

            ?>

            <?php if($this->ipauth->canEditProp($this->p->id)) echo '<div class="iplistaction">'.JHtml::_('icon.edit', $this->p, 'property', false, array('class'=>'hasTooltip')).'</div>'; ?>

            <?php if( $this->p->featured ): ?>

                <div class="pull-right ip-overview-bannerright">

                    <span class="icon-star ip-pointer hasTooltip ip-featured-icon" title="<?php echo JText::_('COM_IPROPERTY_FEATURED'); ?>"></span>

                </div>

            <?php endif; ?>

            <div class="ip-overview-title">

                <a href="<?php echo $this->p->proplink; ?>"<?php echo $span_style; ?> class="ip-property-header-accent"><?php echo $this->p->street_address; ?></a>

                <?php 

                    if( $this->p->city ) echo ' - '.$this->p->city;

                    if( $this->p->locstate ) echo ', '.ipropertyHTML::getstatename($this->p->locstate);

                    if( $this->p->province ) echo ', '.$this->p->province;

                    if( $this->p->country ) echo ' '.ipropertyHTML::getcountryname($this->p->country);

                    echo '<br />';



                    echo '<em>';

                    if( $this->p->beds ) echo '<strong>'.JText::_( 'COM_IPROPERTY_BEDS' ).':</strong> '.$this->p->beds.' &#160;&#160;';

                    if( $this->p->baths && $this->p->baths != '0.00' ) echo '<strong>'.JText::_( 'COM_IPROPERTY_BATHS' ).':</strong> '.$this->p->baths.' &#160;&#160;';

                    if( $this->p->formattedsqft ) echo '<strong>'.$munits.':</strong> '.$this->p->formattedsqft.' &#160;&#160;';

                    if( $this->p->lotsize ) echo '<strong>'.JText::_( 'COM_IPROPERTY_LOT_SIZE' ).':</strong> '.$this->p->lotsize.' &#160;&#160;';

                    if( $this->p->lot_acres ) echo '<strong>'.JText::_( 'COM_IPROPERTY_LOT_ACRES' ).':</strong> '.$this->p->lot_acres.' &#160;&#160;';

                    if( $this->p->county ) echo '<strong>'.JText::_( 'COM_IPROPERTY_COUNTY' ).':</strong> '.$this->p->county.' &#160;&#160;';

                    if( $this->p->region ) echo '<strong>'.JText::_( 'COM_IPROPERTY_REGION' ).':</strong> '.$this->p->region.' &#160;&#160;';

                    echo '</em>';

                ?>

            </div>



            <?php

            // display overview and listing_info if available

            if($this->p->short_description) echo '<div class="ip-overview-short-desc">'.ipropertyHTML::snippet($this->p->short_description, $this->settings->overview_char).'</div>'; 

            if($listing_info) echo '<div class="small">'.$listing_info.'</div>';



            // instead of calling this function - add this as part of the query

            $cats   = ipropertyHTML::getAvailableCats($this->p->id);

            if($cats){

                echo '<div class="ip-overview-catcontainer">';

                $catcount = 0;

                foreach( $cats as $c ){

                    echo ipropertyHTML::getCatIcon($c, 20);

                    $catcount++;

                    if($catcount < count($cats)) echo '<span class="ip-cat-icon-divider">&#160;</span>';

                }

                echo '</div>';

            }

            if ($this->settings->show_hits) echo '<span class="label label-info pull-right ip-hits">'.$this->p->hits.' '.JText::_('COM_IPROPERTY_HITS').'</span>';

            ?>

        </div>

    </div>

</div>

<div class="clearfix"></div>

