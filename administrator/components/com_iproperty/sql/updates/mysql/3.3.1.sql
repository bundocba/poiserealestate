ALTER IGNORE TABLE `#__iproperty_settings` DROP `cat_featured`;
ALTER IGNORE TABLE `#__iproperty_settings` DROP `cat_featured_pos`;
ALTER IGNORE TABLE `#__iproperty_settings` ADD `qs_show_sqft` tinyint(1) unsigned NOT NULL DEFAULT '0' AFTER `qs_show_price`;