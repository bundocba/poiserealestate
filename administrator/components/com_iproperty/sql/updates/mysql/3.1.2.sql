ALTER IGNORE TABLE `#__iproperty` ADD `mls_org` varchar(100) NOT NULL AFTER `mls_id`;
ALTER IGNORE TABLE `#__iproperty_openhouses` ADD `ip_source` varchar(255) NOT NULL AFTER `state`;
ALTER IGNORE TABLE `#__iproperty_stypes` ADD `show_request_form` tinyint(1) unsigned NOT NULL DEFAULT '1' AFTER `show_banner`;
ALTER IGNORE TABLE `#__iproperty_settings` ADD `map_locale` varchar(5) NOT NULL AFTER `map_credentials`;
ALTER IGNORE TABLE `#__iproperty_settings` ADD `feed_admin` tinyint(1) unsigned NOT NULL DEFAULT '1' AFTER `feed_show`;
ALTER IGNORE TABLE `#__iproperty_settings` ADD `hard404` tinyint(1) unsigned NOT NULL DEFAULT '0' AFTER `feed_admin`;
ALTER IGNORE TABLE `#__iproperty_settings` ADD `bootstrap_css` tinyint(1) unsigned NOT NULL DEFAULT '0' AFTER `hard404`;
ALTER IGNORE TABLE `#__iproperty_settings` ADD `show_hits` tinyint(1) unsigned NOT NULL DEFAULT '0' AFTER `show_mtgcalc`;