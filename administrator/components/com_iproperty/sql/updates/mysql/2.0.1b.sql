/* settings table */
ALTER IGNORE TABLE `#__iproperty_settings` DROP `adv_slider_length`;
ALTER IGNORE TABLE `#__iproperty_settings` DROP `adv_map_width`;
ALTER IGNORE TABLE `#__iproperty_settings` DROP `adv_map_height`;
ALTER IGNORE TABLE `#__iproperty_settings` DROP `googlemap_key`;
ALTER IGNORE TABLE `#__iproperty_settings` DROP `css_file`;

/* new css setting to force accent colors */
ALTER IGNORE TABLE `#__iproperty_settings` ADD `force_accents` tinyint(1) unsigned NOT NULL DEFAULT '0' AFTER `secondary_accent`;