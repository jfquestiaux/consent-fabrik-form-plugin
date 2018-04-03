CREATE TABLE IF NOT EXISTS `#__fabrik_privacy` (
	`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`date_time` DATETIME DEFAULT NULL,
	`reference` VARCHAR( 50 ) NOT NULL COMMENT 'tableid.formid.rowid reference',
	`list_id` INT( 6 ) NOT NULL,
	`contact_id` INT( 6 ) NOT NULL ,
	`contact_message` TEXT NOT NULL,
	`ip` VARCHAR( 100 ) NOT NULL
);
 