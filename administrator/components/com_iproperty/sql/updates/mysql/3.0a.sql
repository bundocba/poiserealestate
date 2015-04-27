/* settings table */
ALTER IGNORE TABLE `#__iproperty_settings` DROP `tab_width`;
ALTER IGNORE TABLE `#__iproperty_settings` DROP `tab_height`;
ALTER IGNORE TABLE `#__iproperty_settings` DROP `form_storeforms`;
ALTER IGNORE TABLE `#__iproperty_settings` DROP `feed_kml`;
ALTER IGNORE TABLE `#__iproperty_settings` DROP `feed_gbase`;
ALTER IGNORE TABLE `#__iproperty_settings` DROP `feed_gbaseuk`;
ALTER IGNORE TABLE `#__iproperty_settings` DROP `adv_show_preview`;
ALTER IGNORE TABLE `#__iproperty_settings` DROP `adv_show_thumb`;
ALTER IGNORE TABLE `#__iproperty_settings` CHANGE `googlemap_enable` `map_provider` tinyint(1) NOT NULL DEFAULT '1';
ALTER IGNORE TABLE `#__iproperty_settings` CHANGE `feed_zillow` `feed_show` tinyint(1);
ALTER IGNORE TABLE `#__iproperty_settings` ADD `map_credentials` varchar(100) NOT NULL AFTER `map_provider`;
ALTER IGNORE TABLE `#__iproperty_settings` ADD `adv_show_city` tinyint(1) NOT NULL DEFAULT '1' AFTER `adv_show_region`;

/* property table */
ALTER IGNORE TABLE `#__iproperty` DROP `show_address`;
ALTER IGNORE TABLE `#__iproperty` MODIFY `cool` varchar(100) NOT NULL;
ALTER IGNORE TABLE `#__iproperty` MODIFY `price2` decimal(12,2) unsigned NOT NULL;