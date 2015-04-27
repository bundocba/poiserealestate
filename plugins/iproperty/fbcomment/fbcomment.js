/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

var ipPropertyPlug = (function(app) {
    app.buildPlug = function(){
        jQuery('a[href="#ipfbcommentplug"]').on("shown", function(e) {
            FB.XFBML.parse(jQuery('#ipfbcommentplug')[0]);
        });
    }
    return app;
})(ipPropertyPlug || {});

