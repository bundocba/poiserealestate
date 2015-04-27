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



$img_folder = $this->ipbaseurl.$this->settings->imgpath;



if(!$this->images){ // set thumbnail nopic image to create tab dimensions for map and streetview, etc

    echo '

        <div id="ip-image-tab">

            <img src="'.$this->ipbaseurl.'/media/com_iproperty/pictures/nopic.png" alt="'.$this->p->street_address.'" width="100%" />

        </div>';    

}else{



    switch($this->settings->gallerytype)

    {

        case 1: // LIGHTBOX

            

            $this->document->addScript($this->ipbaseurl.'/components/com_iproperty/assets/galleries/lightbox/js/lightbox-2.6.min.js');

            $this->document->addStylesheet($this->ipbaseurl.'/components/com_iproperty/assets/galleries/lightbox/css/lightbox.css');

            // html

            echo '

            <div id="ip-image-tab">';

                $i = 1;

                foreach($this->images as $image)

                {

                    $path       = ($image->remote == 1) ? $image->path : $img_folder;

                    $hidden     = ($i == 1) ? '' : ' style="display: none;"';

                    $imgtitle   = ($image->title) ? htmlspecialchars(trim($image->title)) : htmlspecialchars(trim($this->p->street_address));

                    $imgtitle   .= ($image->description) ? ':'.htmlspecialchars($image->description) : '';

                    echo '

                        <a href="'.$path.$image->fname.$image->type.'" data-lightbox="propslides" title="'.$imgtitle.'">

                            <img src="'.$path.$image->fname.$image->type.'" alt="'.$this->p->street_address.'"'.$hidden.' />

                        </a>';

                    $i++;

                }

                echo '

                <div class="clear ip-image-tabImage"></div>

            </div>';

        break;

        case 2: // REMOVING GALLERIFIC



        break;

        case 3: // SERIE3

            $this->document->addStylesheet($this->ipbaseurl.'/components/com_iproperty/assets/galleries/s3Slider/s3SliderCSS.css');

            $this->document->addScript($this->ipbaseurl.'/components/com_iproperty/assets/galleries/s3Slider/s3Slider.js');

            $gallery_js ='

                jQuery(function($) {

                    $(document).ready(function(){

                        $("#ip-image-tab").s3Slider({

                            timeOut: 4000

                        });

                    });

                });';

            $this->document->addScriptDeclaration( $gallery_js );

            // html

            echo '

            <div id="ip-image-tab">

                <ul id="ip-image-tabContent">';

                $i = 1;

                foreach($this->images as $image){

                    $path = ($image->remote == 1) ? $image->path : $img_folder;

                    echo '

                        <li class="ip-image-tabImage">

                            <img src="'.$path.$image->fname.$image->type.'" alt="'.$this->p->street_address.'" />

                            <span><h4>'.$image->title.'</h4> '.$image->description.'</span>

                        </li>';

                    $i++;

                }

                echo '

                </ul>

            </div>';

        break;

        case 4: // CAROUFREDSEL

            $this->document->addStylesheet($this->ipbaseurl.'/components/com_iproperty/assets/galleries/caroufredsel/styles.css');

            $this->document->addScript($this->ipbaseurl.'/components/com_iproperty/assets/galleries/caroufredsel/jquery.carouFredSel-6.1.0-packed.js');

            $gallery_js ='

                jQuery(function($) {

                    $(document).ready(function(){

                        var gal_w = $("#propimages").width();

                        var gal_h = $("#propimages").height();

                        $("#ip-image-tab").carouFredSel({

                            responsive: true,

                            items: {

                                width: gal_w,

                                height: gal_h,

                                visible: 1,

                                minimum: 1

                            },

                            scroll: {

                                fx: "fade",

                                duration: 1000,

                                pauseOnHover: true

                            },

                            auto: {

                                timeoutDuration: 1000,

                                delay: 1000

                            }

                        }).find(".slide").hover(

                            function() { $(this).find("div").slideDown(); },

                            function() { $(this).find("div").slideUp();	}

                        );

                    });

                });';

            $this->document->addScriptDeclaration( $gallery_js );

            // html

            echo '

            <div id="ip-image-tab">';

                $i = 1;									

                foreach($this->images as $image){

                    $path = ($image->remote == 1) ? $image->path : $img_folder;

                    echo '

                        <div class="slide">

                            <img class="ip_caroufredsel" src="'.$path.$image->fname.$image->type.'" alt="'.$this->p->street_address.'" />

                            <div><h4>'.$image->title.'</h4><p>'.$image->description.'</p></div>

                        </div>';

                    $i++;

                }

                echo '

            </div>';

        break;

        case 5: // BOOTSTRAP

        default:

            $gallery_js ='

                jQuery(function($) {

                    $(document).ready(function(){

                        // set first image to active

                        $("#iproperty_image1").addClass("active");



                        $("#ip-image-tab").carousel({

                          interval: 3000

                        })

                    });

                });';

            $this->document->addScriptDeclaration( $gallery_js );		

            // html

            echo '

            <div id="ip-image-tab" class="carousel slide">

                <div class="carousel-inner">';

                    $i = 1;

                    foreach($this->images as $image)

                    {

                        $path = ($image->remote == 1) ? $image->path : $img_folder;

                        echo '

                            <div id="iproperty_image'.$i.'" class="item">

                                <img class="ip_carousel" src="'.$path.$image->fname.$image->type.'" alt="'.$this->p->street_address.'" />';

                                if ($image->title || $image->description) echo '<div class="carousel-caption"><h4>'.$image->title.'</h4><p>'.$image->description.'</p></div>';

                        echo '

                            </div>';

                        $i++;

                    }

                echo '</div>

                <a class="ip-carousel-control left" href="#ip-image-tab" data-slide="prev">&lsaquo;</a>

                <a class="ip-carousel-control right" href="#ip-image-tab" data-slide="next">&rsaquo;</a>

            </div>';

        break;

        case 6: // NIVO GALLERY

            $this->document->addStylesheet($this->ipbaseurl.'/components/com_iproperty/assets/galleries/nivo/nivo-gallery.css');

            $this->document->addScript($this->ipbaseurl.'/components/com_iproperty/assets/galleries/nivo/jquery.nivo.gallery.min.js');

            

            $gallery_js = '

                jQuery(function($) {

                    $(document).ready(function() {

                        $("#ip-prop-gallery").nivoGallery();

                    });

                });';

            $this->document->addScriptDeclaration( $gallery_js );

            

            // html

            echo '

            <div id="ip-prop-gallery" class="nivoGallery">

                <ul>';

                    $i = 1;

                    foreach($this->images as $image){

                        $path = ($image->remote == 1) ? $image->path : $img_folder;

                        echo '

                            <li data-title="'.$image->title.'" data-caption="'.$image->description.'">

                                <img src="'.$path.$image->fname.$image->type.'" alt="'.$this->p->street_address.'" />

                            </li>';

                        $i++;

                    }

                echo '

                </ul>

            </div>';

        break;

        case 7: //flexSlider

            $this->document->addStylesheet($this->ipbaseurl.'/components/com_iproperty/assets/galleries/flexSlider/flexslider.css');

            $this->document->addScript($this->ipbaseurl.'/components/com_iproperty/assets/galleries/flexSlider/jquery.flexslider-min.js');

            

            $flex_script = 'jQuery(function($) {

                              $(document).ready(function() {

                                $(".flexslider").flexslider({

                                    animation: "fade",

                                    prevText: "",

                                    nextText: "",

                                    smoothHeight: true,

                                    controlNav: true

                                });

                              });

                            });';

            $this->document->addScriptDeclaration($flex_script);            

            

            echo '

                <div class="flexslider">

                    <ul class="slides">';

                        $i = 1;

                        foreach($this->images as $image){

                            $path = ($image->remote == 1) ? $image->path : $img_folder;

                            echo '

                                <li>

                                    <img src="'.$path.$image->fname.$image->type.'" alt="'.$this->p->street_address.'" />

                                </li>';

                            $i++;

                        }

                    echo '

                    </ul>

                </div>';

        break;

        case 8: // NIVO SLIDER

            $this->document->addStylesheet($this->ipbaseurl.'/components/com_iproperty/assets/galleries/nivoslider/nivo-slider.css');

            $this->document->addStylesheet($this->ipbaseurl.'/components/com_iproperty/assets/galleries/nivoslider/themes/default/default.css');

            $this->document->addScript($this->ipbaseurl.'/components/com_iproperty/assets/galleries/nivoslider/jquery.nivo.slider.min.js');

            

            $gallery_js = '

                jQuery(function($) {

                    $(document).ready(function() {

                        $("#ip-prop-gallery").nivoSlider({"effect": "fade", "controlNav": false});

                    });

                });';

            $this->document->addScriptDeclaration( $gallery_js );

            

            // html

            echo '

            <div class="slider-wrapper theme-default">

                <div id="ip-prop-gallery" class="nivoSlider">';

                    $i = 1;

                    foreach($this->images as $image){

                        $path = ($image->remote == 1) ? $image->path : $img_folder;

                        $imgtitle   = ($image->title) ? htmlspecialchars(trim($image->title)) : htmlspecialchars(trim($this->p->street_address));

                        $imgtitle   .= ($image->description) ? ':'.htmlspecialchars($image->description) : '';

                        echo '<img src="'.$path.$image->fname.$image->type.'" alt="'.$this->p->street_address.'" title="'.$imgtitle.'" />';

                        $i++;

                    }

                echo '

                </div>

            </div>';

        break;

    }

}

?>

