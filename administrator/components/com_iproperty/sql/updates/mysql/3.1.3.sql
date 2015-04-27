ALTER IGNORE TABLE `#__iproperty` ADD `metadata` TEXT NOT NULL AFTER  `metakey`;
ALTER IGNORE TABLE `#__iproperty_agents` ADD `metadata` TEXT NOT NULL AFTER  `params`;
ALTER IGNORE TABLE `#__iproperty_companies` ADD `metadata` TEXT NOT NULL AFTER  `params`;
ALTER IGNORE TABLE `#__iproperty_agents` ADD `mls_org` varchar(100) NOT NULL AFTER `ip_source`;
ALTER IGNORE TABLE `#__iproperty_companies` ADD `mls_org` varchar(100) NOT NULL AFTER `ip_source`;
ALTER IGNORE TABLE `#__iproperty` ADD `kml` varchar(225) NOT NULL AFTER `longitude`;
ALTER IGNORE TABLE `#__iproperty` CHANGE `latitude` `latitude` DOUBLE NOT NULL;
ALTER IGNORE TABLE `#__iproperty` CHANGE `longitude` `longitude` DOUBLE NOT NULL;
