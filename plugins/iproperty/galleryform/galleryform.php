<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

// no direct access
defined('_JEXEC' ) or die( 'Restricted access');
jimport('joomla.plugin.plugin');

class plgIpropertyGalleryForm extends JPlugin
{
	public function __construct(&$subject, $config)  
    {
		parent::__construct($subject, $config);
        $this->loadLanguage();
	}
	
    public function onBeforeRenderForms($property, $settings)
    {
		if ($this->params->get('position')) return true;
        $this->_doGallery($property, $settings);
    }
	
	public function onAfterRenderForms($property, $settings)
    {
        if (!$this->params->get('position')) return true;
        $this->_doGallery($property, $settings);
    }    
    
	private function _doGallery($property, $settings)
	{
        $app        = JFactory::getApplication();
        $document   = JFactory::getDocument();
		if($app->getName() != 'site') return true;

        $thumb_width    = $this->params->get('thumb_width', 200);
        $thumb_height   = $this->params->get('thumb_height', 100);
        $limit          = $this->params->get('thumb_limit', 9999);	
        $lbox_value     = $this->params->get('lbox_value', 'ipgalleryslides');
        
        if($this->params->get('include_lbox')){
            $document->addStylesheet(JURI::root(true).'/components/com_iproperty/assets/galleries/lightbox/css/lightbox.css');
            $document->addScript(JURI::root(true).'/components/com_iproperty/assets/galleries/lightbox/js/lightbox-2.6.min.js');
        }
		
		// load images for property
        $db = JFactory::getDbo();
        
        $query = $db->getQuery(true);
        $query->select('*')
                ->from('`#__iproperty_images`')
                ->where('propid = '.(int)$property->id)
                ->where('(type = ".jpg" OR type = ".jpeg" OR type = ".gif" OR type = ".png")')
                ->order('ordering ASC');
        
        $db->setQuery($query, 0, $limit);
        $images = $db->loadObjectList();        
        if ( count($images) < 1 ) return;
        
        // create array of thumbs to use in gallery
        $gallery_display = '';
        foreach($images as $image) 
        {
			$gpath          = ($image->remote == 1) ? $image->path : JURI::root(true).$settings->imgpath;
			$gthumbnail     = ($image->remote == 1) ? $gpath.$image->fname.$image->type : $gpath.$image->fname. '_thumb' . $image->type;
			$gfullsize      = ($image->remote == 1) ? $gpath.$image->fname.$image->type : $gpath.$image->fname.$image->type;
            $gtitle         = ($image->title) ? htmlspecialchars(trim($image->title)) : htmlspecialchars(trim($property->street_address));
            $gdesc          = ($image->description) ? ':'.htmlspecialchars($image->description) : '';

			$gallery_display .= '
            <div class="ip-galleryplug-img pull-left thumbnail">
                <div style="width: '.(int)$thumb_width.'px; height: '.(int)$thumb_height.'px; overflow: hidden;">
                    <a href="'.$gfullsize.'" title="'.$gtitle.$gdesc.'" data-lightbox="'.$lbox_value.'">
                        <img src="'.$gthumbnail.'" alt="'.$gtitle.'" width="'.(int)$thumb_width.'" />
                    </a>
                </div>
            </div>';
		}
			
		echo JHtmlBootstrap::addTab('ipDetails', 'ipgalleryplug', JText::_($this->params->get('tabtitle', 'PLG_IP_GALLERY_GALLERY')));
            echo $gallery_display; 
        echo JHtmlBootstrap::endTab();
		
		return true;		
	}	
} // end class

?>