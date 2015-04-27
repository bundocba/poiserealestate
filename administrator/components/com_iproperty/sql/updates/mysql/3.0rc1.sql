ALTER TABLE  `#__iproperty` MODIFY `latitude` FLOAT( 10, 6 ) NOT NULL;
ALTER TABLE  `#__iproperty` MODIFY `longitude` FLOAT( 10, 6 ) NOT NULL;
ALTER IGNORE TABLE `#__iproperty` ADD INDEX (`latitude`);
ALTER IGNORE TABLE `#__iproperty` ADD INDEX (`longitude`);