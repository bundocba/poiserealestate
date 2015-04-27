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

JHtml::_('bootstrap.tooltip');
JHtml::_('bootstrap.modal');
JHtml::_('behavior.formvalidation');

$document   = JFactory::getDocument();
$uri        = JURI::getInstance();

// add Open Graph tags-- http://ogp.me/
$document->addCustomTag( '<meta property="og:title" content="'.$this->iptitle.'" />' );
$document->addCustomTag( '<meta property="og:image" content="'.ipropertyHTML::getThumbnail($this->p->id,'','','','','','',false, false).'" />' );
$document->addCustomTag( '<meta property="og:url" content="'.htmlspecialchars( $uri->toString() ).'" />' );
$document->addCustomTag( '<meta property="og:site_name" content="'.$this->app->getCfg('sitename').'" />' );
$document->addCustomTag( '<meta property="og:type" content="website" />' );
$document->addCustomTag( '<meta property="og:description" content="'.$this->p->short_description.'" />' );

$this->agents                 = ipropertyHTML::getAvailableAgents($this->p->id);
$this->openhouses             = ipropertyHTML::getOpenHouses($this->p->id);
$this->property_full_address  = ipropertyHTML::getFullAddress($this->p);

if($this->mapOk)
{
    jimport('joomla.filesystem.file');
    
    $app        = JFactory::getApplication();
    //if max zoom is higher than 13, set the default to a medium level 13 zoom.
    //if max zoom is less than 13, set to the maximum zoom level allowable
    $default_zoom   = ($this->settings->max_zoom > 13) ? 13 : $this->settings->max_zoom;
    $max_zoom       = $this->settings->max_zoom ? $this->settings->max_zoom : 21;

    // check for template map icons
    $templatepath       = $app->getTemplate();
    $map_house_icon     = JURI::root(true).'/components/com_iproperty/assets/images/map/icon56.png';
    if(JFile::exists('templates/'.$templatepath.'/images/iproperty/map/icon56.png')) $map_house_icon = JURI::root(true).'/templates/'.$templatepath.'/images/iproperty/map/icon56.png';

    // check for places plugin
    $places = (JPluginHelper::isEnabled('iproperty', 'googleplaces')) ? 'true' : 'false';
    
    // check for KML
    $kml = $this->p->kml ? JURI::root(true).'/media/com_iproperty/kml/'.$this->p->kml : false;
    
    $mapscript = 'var ipmapoptions = {
        zoom: '.$default_zoom.',
        lat: "' . $this->p->lat_pos . '",
        lon: "' . $this->p->long_pos . '",
        mapicon: "'.$map_house_icon.'",
        maxZoom: '.$max_zoom.',
        places: '.$places.',
        kml: "'.$kml.'"    
    };';
    $document->addScriptDeclaration( $mapscript );
}
?>

<?php
// trigger onBeforeRenderToolbar plugins
$this->dispatcher->trigger('onBeforeRenderToolbar', array($this->settings));

// trigger onBeforeRenderProperty plugins
echo '<div class="ip-before-property">';
$this->dispatcher->trigger('onBeforeRenderProperty', array($this->p, $this->settings));
echo '</div>';
?>

<property class="item-page<?php echo $this->pageclass_sfx?>">
    <div class="ip-mainheader">
        <h1 class="pull-left">
            <?php echo $this->escape($this->iptitle); ?> <small class="ip-detail-price"><?php echo $this->p->formattedprice; ?></small>
        </h1>
    </div>
    <div class="clearfix"></div>

    <div class="row-fluid">
        <div class="span12 ip-prop-top">
            <div class="span7 pull-left ip-mapleft">
				<?php echo JHtmlBootstrap::startTabSet('ipMap', array('active' => 'propimages'));
                    // images
                    echo JHtmlBootstrap::addTab('ipMap', 'propimages', JText::_('COM_IPROPERTY_IMAGES').' ('.count($this->images).')');
                        echo $this->p->banner; 
                        echo $this->loadTemplate('gallery'); 
                    echo JHtmlBootstrap::endTab(); 
                    // docs
                    if ($this->docs): 
						echo JHtmlBootstrap::addTab('ipMap', 'propdocs', JText::_('COM_IPROPERTY_DOCS' ));
                        echo '<ul class="nav nav-tabs nav-stacked">';
						foreach ($this->docs as $d){
							$doc_title = ($d->title) ? $d->title : $d->fname;
							if($doc_title && !$d->remote) {
								echo '<li class="ip_sidecol_item"><a href="'.$this->ipbaseurl.$this->settings->imgpath.$d->fname.$d->type.'" target="_blank">' . $doc_title . ' - <b>[</b>'.substr($d->type,1).'<b>]</b></a></li>';
							} else if($doc_title && $d->remote){
								echo '<li class="ip_sidecol_item"><a href="'.$d->path.$d->fname.$d->type.'" target="_blank">' . $doc_title . ' - <b>[</b>'.substr($d->type,1).'<b>]</b></a></li>';
							} 
						}
                        echo '</ul>';
						echo JHtmlBootstrap::endTab();
                    endif; 
                    // map
                    if ($this->mapOk):
						echo JHtmlBootstrap::addTab('ipMap', 'propmap', JText::_('COM_IPROPERTY_MAP' ));
                        switch($this->settings->map_provider)
                        { 
                            case '1': //google
                            default:
                                echo $this->loadTemplate('gmaptab');
                                break;
                            case '2': //bing
                                echo $this->loadTemplate('bingtab');
                                break;
                        }
                        echo JHtmlBootstrap::endTab();   
                    endif;
                    $this->dispatcher->trigger('onAfterRenderMap', array($this->p, $this->settings));    
				echo JHtmlBootstrap::endTabSet(); ?>
            </div>
            <div class="span5">                
                <?php echo $this->loadTemplate('iptoolbar'); ?>
                <div class="well ip-mapright">
                    <?php echo $this->loadTemplate('mapright'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="ip-propdetails-divider"></div>
    <div class="row-fluid">
        <div class="span12 ip-prop-bottom">
            <?php 
            // details pane
            echo JHtmlBootstrap::startTabSet('ipDetails', array('active' => 'propdescription'));
                $this->dispatcher->trigger('onBeforeRenderForms', array($this->p, $this->settings));
                // description
                echo JHtmlBootstrap::addTab('ipDetails', 'propdescription', JText::_('COM_IPROPERTY_DESCRIPTION' ));
                    echo $this->loadTemplate('description'); 
                echo JHtmlBootstrap::endTab();
                // details
                echo JHtmlBootstrap::addTab('ipDetails', 'propdetails', JText::_('COM_IPROPERTY_DETAILS' ));
                    echo $this->loadTemplate('details'); 
                echo JHtmlBootstrap::endTab();
                // video
                if ($this->p->video):
                    echo JHtmlBootstrap::addTab('ipDetails', 'propvideo', JText::_('COM_IPROPERTY_VIDEO' ));
                        echo JHTML::_('content.prepare', $this->p->video);
                    echo JHtmlBootstrap::endTab();
                endif;
                // prop request
                if(ipropertyHtml::stypeRequestForm($this->p->stype) && $this->settings->show_requestshowing):
                    echo JHtmlBootstrap::addTab('ipDetails', 'proprequest', JText::_('COM_IPROPERTY_REQUEST_SHOWING' ));
                        echo $this->loadTemplate('requestshow');
                    echo JHtmlBootstrap::endTab();
                endif;
                // send to friend
                if ($this->settings->show_sendtofriend):
                    echo JHtmlBootstrap::addTab('ipDetails', 'propstf', JText::_('COM_IPROPERTY_SEND_TO_FRIEND' ));
                        echo $this->loadTemplate('sendtofriend');
                    echo JHtmlBootstrap::endTab();
                endif;
                $this->dispatcher->trigger('onAfterRenderForms', array($this->p, $this->settings)); 
            echo JHtmlBootstrap::endTabSet(); ?> 
        </div>
    </div>

    <div class="clearfix"></div>

    <?php     
    // display disclaimer if set in params
    if ($this->settings->disclaimer){
        echo '<div class="well well-small" id="ip-disclaimer">'.$this->settings->disclaimer.'</div>';
    }
    // display footer if enabled
    if ($this->settings->footer == 1) echo ipropertyHTML::buildThinkeryFooter(); 
    ?>
</property>

<?php
// trigger onAfterRenderProperty plugins
echo '<div class="ip-after-property">';
$this->dispatcher->trigger('onAfterRenderProperty', array($this->p, $this->settings));
echo '</div>';
?>
