/* properties table */
ALTER IGNORE TABLE `#__iproperty` ADD `alias` varchar(255) NOT NULL AFTER `title`;
ALTER IGNORE TABLE `#__iproperty` ADD `language` char(7) NOT NULL AFTER `checked_out_time`;
ALTER IGNORE TABLE `#__iproperty` ADD `available` date NOT NULL AFTER `featured`;
ALTER IGNORE TABLE `#__iproperty` ADD `show_map` tinyint(1) unsigned NOT NULL DEFAULT '0' AFTER `hide_address`;
ALTER IGNORE TABLE `#__iproperty` CHANGE `published` `state` tinyint(3) NOT NULL DEFAULT '1';
ALTER IGNORE TABLE `#__iproperty` MODIFY `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER IGNORE TABLE `#__iproperty` MODIFY `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER IGNORE TABLE `#__iproperty` MODIFY `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER IGNORE TABLE `#__iproperty` MODIFY `checked_out` int(10) unsigned NOT NULL DEFAULT '0';
ALTER IGNORE TABLE `#__iproperty` MODIFY `modified_by` int(10) unsigned NOT NULL DEFAULT '0';
ALTER IGNORE TABLE `#__iproperty` MODIFY `created_by` int(10) unsigned NOT NULL DEFAULT '0';
ALTER IGNORE TABLE `#__iproperty` MODIFY `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER IGNORE TABLE `#__iproperty` MODIFY `access` int(10) unsigned NOT NULL DEFAULT '0';

/* agents table */
ALTER IGNORE TABLE `#__iproperty_agents` ADD `alias` varchar(255) NOT NULL AFTER `lname`;
ALTER IGNORE TABLE `#__iproperty_agents` ADD `language` char(7) NOT NULL AFTER `checked_out_time`;
ALTER IGNORE TABLE `#__iproperty_agents` ADD `linkedin` varchar(100) NOT NULL AFTER `gtalk`;
ALTER IGNORE TABLE `#__iproperty_agents` ADD `facebook` varchar(100) NOT NULL AFTER `linkedin`;
ALTER IGNORE TABLE `#__iproperty_agents` ADD `twitter` varchar(100) NOT NULL AFTER `facebook`;
ALTER IGNORE TABLE `#__iproperty_agents` ADD `social1` varchar(100) NOT NULL AFTER `twitter`;
ALTER IGNORE TABLE `#__iproperty_agents` CHANGE `state` `locstate` tinyint(3) unsigned NOT NULL;
ALTER IGNORE TABLE `#__iproperty_agents` CHANGE `published` `state` tinyint(3) NOT NULL DEFAULT '1';
ALTER IGNORE TABLE `#__iproperty_agents` MODIFY `checked_out` int(10) unsigned NOT NULL DEFAULT '0';
ALTER IGNORE TABLE `#__iproperty_agents` MODIFY `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER IGNORE TABLE `#__iproperty_agents` MODIFY `ordering` int(10) unsigned NOT NULL DEFAULT '0';

/* amenities table */
ALTER IGNORE TABLE `#__iproperty_amenities` ADD `cat` tinyint(3) unsigned NOT NULL DEFAULT '0' AFTER `title`;
ALTER IGNORE TABLE `#__iproperty_amenities` ADD `language` char(7) NOT NULL AFTER `cat`;

/* categories tables */
ALTER IGNORE TABLE `#__iproperty_categories` ADD `alias` varchar(255) NOT NULL AFTER `title`;
ALTER IGNORE TABLE `#__iproperty_categories` ADD `language` char(7) NOT NULL AFTER `checked_out_time`;
ALTER IGNORE TABLE `#__iproperty_categories` CHANGE `published` `state` tinyint(3) NOT NULL DEFAULT '1';
ALTER IGNORE TABLE `#__iproperty_categories` MODIFY `checked_out` int(10) unsigned NOT NULL DEFAULT '0';
ALTER IGNORE TABLE `#__iproperty_categories` MODIFY `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER IGNORE TABLE `#__iproperty_categories` MODIFY `access` int(10) unsigned NOT NULL DEFAULT '0';
ALTER IGNORE TABLE `#__iproperty_categories` MODIFY `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER IGNORE TABLE `#__iproperty_categories` MODIFY `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER IGNORE TABLE `#__iproperty_categories` MODIFY `ordering` int(10) unsigned NOT NULL DEFAULT '0';

/* companies table */
ALTER IGNORE TABLE `#__iproperty_companies` ADD `alias` varchar(255) NOT NULL AFTER `name`;
ALTER IGNORE TABLE `#__iproperty_companies` ADD `language` char(7) NOT NULL AFTER `checked_out_time`;
ALTER IGNORE TABLE `#__iproperty_companies` CHANGE `state` `locstate` tinyint(3) unsigned NOT NULL;
ALTER IGNORE TABLE `#__iproperty_companies` CHANGE `published` `state` tinyint(3) NOT NULL DEFAULT '1';
ALTER IGNORE TABLE `#__iproperty_companies` MODIFY `checked_out` int(10) unsigned NOT NULL DEFAULT '0';
ALTER IGNORE TABLE `#__iproperty_companies` MODIFY `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER IGNORE TABLE `#__iproperty_companies` MODIFY `ordering` int(10) unsigned NOT NULL DEFAULT '0';

/* images table */
ALTER IGNORE TABLE `#__iproperty_images` ADD `language` char(7) NOT NULL AFTER `ordering`;
ALTER IGNORE TABLE `#__iproperty_images` CHANGE `published` `state` tinyint(3) NOT NULL DEFAULT '1';
ALTER IGNORE TABLE `#__iproperty_images` MODIFY `ordering` int(10) unsigned NOT NULL DEFAULT '0';

/* openhouse table */
ALTER IGNORE TABLE `#__iproperty_openhouses` ADD `language` char(7) NOT NULL AFTER `checked_out_time`;
ALTER IGNORE TABLE `#__iproperty_openhouses` CHANGE `published` `state` tinyint(3) NOT NULL DEFAULT '1';
ALTER IGNORE TABLE `#__iproperty_openhouses` MODIFY `checked_out` int(10) unsigned NOT NULL DEFAULT '0';
ALTER IGNORE TABLE `#__iproperty_openhouses` MODIFY `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';

/* saved table */
ALTER IGNORE TABLE `#__iproperty_saved` ADD `search_string` varchar(255) NOT NULL AFTER `notes`;
ALTER IGNORE TABLE `#__iproperty_saved` ADD `email_update` tinyint(1) unsigned NOT NULL DEFAULT '0' AFTER `search_string`;
ALTER IGNORE TABLE `#__iproperty_saved` ADD `type` tinyint(1) unsigned NOT NULL DEFAULT '0' AFTER `email_update`;
ALTER IGNORE TABLE `#__iproperty_saved` ADD `last_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `type`;
ALTER IGNORE TABLE `#__iproperty_saved` ADD INDEX (`user_id`);

/* settings table */
ALTER IGNORE TABLE `#__iproperty_settings` ADD `notify_newprop` tinyint(1) unsigned NOT NULL DEFAULT '1' AFTER `notify_saveprop`;
ALTER IGNORE TABLE `#__iproperty_settings` ADD `auto_publish` tinyint(1) unsigned NOT NULL DEFAULT '0' AFTER `edit_rights`;
ALTER IGNORE TABLE `#__iproperty_settings` ADD `auto_agent` tinyint(1) unsigned NOT NULL DEFAULT '0' AFTER `auto_publish`;
ALTER IGNORE TABLE `#__iproperty_settings` ADD `approval_level` tinyint(1) unsigned NOT NULL DEFAULT '0' AFTER `auto_agent`;
ALTER IGNORE TABLE `#__iproperty_settings` ADD `adv_show_radius` tinyint(1) NOT NULL DEFAULT '0' AFTER `adv_show_amen`;
ALTER IGNORE TABLE `#__iproperty_settings` ADD `show_savesearch` tinyint(1) unsigned NOT NULL DEFAULT '1' AFTER `show_saveproperty`;
ALTER IGNORE TABLE `#__iproperty_settings` ADD `show_propupdate` tinyint(1) unsigned NOT NULL DEFAULT '0' AFTER `show_savesearch`;
ALTER IGNORE TABLE `#__iproperty_settings` ADD `show_searchupdate` tinyint(1) unsigned NOT NULL DEFAULT '0' AFTER `show_propupdate`;
ALTER IGNORE TABLE `#__iproperty_settings` CHANGE  `agent_show_social1`  `agent_show_social` TINYINT( 1 ) NOT NULL DEFAULT  '1';
ALTER IGNORE TABLE `#__iproperty_settings` DROP  `agent_show_msn` ,
    DROP  `agent_show_skype` ,
    DROP  `agent_show_gtalk` ,
    DROP  `agent_show_linkedin` ,
    DROP  `agent_show_facebook` ,
    DROP  `agent_show_twitter` ;

/* stypes table */
ALTER IGNORE TABLE `#__iproperty_stypes` CHANGE `published` `state` tinyint(3) NOT NULL DEFAULT '1';