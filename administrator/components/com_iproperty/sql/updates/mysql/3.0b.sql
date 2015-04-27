/* property table */
ALTER IGNORE TABLE `#__iproperty` MODIFY `frontage` tinyint(1) unsigned NOT NULL DEFAULT '0';
ALTER IGNORE TABLE `#__iproperty` MODIFY `hoa` tinyint(1) unsigned NOT NULL DEFAULT '0';
ALTER IGNORE TABLE `#__iproperty` MODIFY `reo` tinyint(1) unsigned NOT NULL DEFAULT '0';
ALTER IGNORE TABLE `#__iproperty` MODIFY `featured` tinyint(1) unsigned NOT NULL DEFAULT '0';

ALTER IGNORE TABLE `#__iproperty_agents` MODIFY `hometeam` tinyint(1) unsigned NOT NULL DEFAULT '0';
ALTER IGNORE TABLE `#__iproperty_agents` MODIFY `featured` tinyint(1) unsigned NOT NULL DEFAULT '0';

ALTER IGNORE TABLE `#__iproperty_saved` MODIFY `active` tinyint(1) unsigned NOT NULL DEFAULT '0';