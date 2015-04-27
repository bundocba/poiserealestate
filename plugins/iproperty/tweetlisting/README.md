/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */
 
 Twitter Tweet New / Modified Listing Plugin for Intellectual Property
 -----------------------------------------------------------------------
 
 Before beginning:
 
 + Install the Intellectual Property Twitter / Tweet plugin using the normal Joomla package installer.
 
 + Visit https://twitter.com/apps to register your website as an "application". You will need to login with your Twitter username / password. If you don't have a Twitter account, get one from http://twitter.com.
 
 + From https://twitter.com/apps, click on the Create an Application button.
 
 + Create a name for your application. This can be the name of your website, your business, or "IProperty website". This will tag all tweets from this plugin, so choose wisely. You  can change this later if required.
 
 + Add a description and your website address in the appropriate area. You are NOT required to supply a callback URL
 
 + Once you submit the form you will be taken to the details tab of the Twitter application page. On this page you will see some important information:
    Click the Settings tab, and under Application Type, select "Read and Write"
    Click back to the Details tab, and click on the "Create my Access Token" button at the bottom of the page.

 + In a new browser window / tab, navigate to the Joomla administrator plugin manager, and click on the Twitter / Tweet New listing plugin.

 + Copy and paste the "Consumer key", "Consumer secret", "Access token", and "Access token secret" to the Twitter / Tweet plugin parameters.
 
 + Select the other options as you desire. Options include Show Price, Show Image, Show Address, Tweet New, Tweet Updated, and Geolocate.
 
 + If you'd like to use the bit.ly URL shortener, you will need to create a bit.ly account at http://bit.ly. 
    Once you've created a bit.ly account, you can visit https://bitly.com/a/oauth_apps. You will need to create a new app and request a Generic Access Token, which you will supply to the Tweet Listing plugin.
    NOTE: the bit.ly service will not work on a testing service, since "localhost" is not recognized as a valid URL.
    
 + If you need to find your existing App's API keys in Twitter later, you can visit https://apps.twitter.com to check your existing App settings.

Thanks to Jennifer Swarts from Mazpc who contributed updated code for the new OAuth method required by Twitter!