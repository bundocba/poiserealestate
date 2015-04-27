/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */
 
 Facebook Post New / Modified Listing Plugin for Intellectual Property
 -----------------------------------------------------------------------
 
 Before beginning:

 + NOTE: you will not be able to post listings if your site is on a private server or "localhost" since Facebook will not be able to visit the page!
 
 + Install the Intellectual Property Facebook plugin using the normal Joomla package installer.
 
 + Log in to Facebook, then visit http://www.facebook.com/developers/ to register your website as an "application". You will need to login with your Facebook username / password. If you don't have a Facebook account, get one from http://facebook.com.
 
 + Click on the Apps menu item in the top bar, then Create A New App button.
 
 + Create a name for your application. This can be the name of your website, your business, "IProperty website", etc. Select Apps for Pages from the Type dropdown.
 
 + Add a description and your website address in the appropriate area. You are NOT required to supply a callback URL

 + In the Settings / Basic tab, click the Add Platform button and select Website. Supply the URL of your site (eg. http://mysite.com)

 + Go to the App Details tab. Click on the App Center Listed Platforms / Configure App Center Permissions button:
     In the App Center Permissions input, type:
       publish_actions
       You may also need to select manage_pages if you want your site to post to a business page (rather than your FB timeline).
       
       NOTE: as of 5/2014 Facebook requires you to submit each app for their approval before it grants these elevated approvals. 
       You must go to the Status & Review tab, and click Start a Submission. Select the permissions above (publish_actions and manage_pages) if required.


 + In a new browser window / tab, navigate to the Joomla administrator plugin manager, and click on the Facebook Post New listing plugin.

 + NOTE!! If you have pop-up blockers enabled, add your site to the "whitelist" or else the Login window may be blocked.

 + Copy and paste the "App ID" and "App Secret" from the Dashboard tab of your Facebook App to the plugin parameters.

 + If you want to post to a business page or different page that you are an administrator of rather than your personal timeline, add the page ID to the plugin parameters.
 
 + Select the other options as you desire. Options include Show Price, Post New and Post Updated.
 
 + Make sure you also set the plugin to Enabled!
 
 + If you'd like to use the bit.ly URL shortener, you will need to create a bit.ly account at http://bit.ly. 
    Once you've created a bit.ly account, you can visit http://bitly.com/a/your_api_key to get the Username and API Key required to use the service via the API.
    NOTE: the bit.ly service will not work on a testing service, since "localhost" is not recognized as a valid URL.
    
 + Once you've supplied the App ID and logged in, the Token field should be auto-populated with a short-term token. SAVE the plugin now.

 + Proceed immediately to a property listing and either modify / create and save a property-- this will request a long-term token from Facebook and store it in the plugin params. If you wait too long to do this your initial short-term token will expire and you'll have to log in again!

 + Make sure that the plugin is enabled, then modify or create a new listing.
