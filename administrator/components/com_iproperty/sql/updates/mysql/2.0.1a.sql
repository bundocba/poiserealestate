/* settings table */
ALTER IGNORE TABLE `#__iproperty_settings` ADD `moderate_listings` tinyint(1) unsigned NOT NULL DEFAULT '0' AFTER `notify_newprop`;