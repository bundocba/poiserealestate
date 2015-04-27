ALTER IGNORE TABLE `#__iproperty` ADD `total_units` tinyint(3) NOT NULL AFTER `reception`;
ALTER IGNORE TABLE `#__iproperty_openhouses` ADD `mls_org` varchar(100) NOT NULL AFTER `ip_source`;