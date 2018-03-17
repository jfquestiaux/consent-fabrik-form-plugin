CREATE TABLE IF NOT EXISTS `#__fabrik_gdpr` (
	`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`date_time` DATETIME DEFAULT NULL,
	`reference` VARCHAR( 50 ) NOT NULL COMMENT 'tableid.formid.rowid reference',
	`list_id` INT( 6 ) NOT NULL,
	`contact_id` INT( 6 ) NOT NULL ,
	`acymailing_user_id` INT( 6 ) DEFAULT NULL ,
	`acymailing_list_ids` VARCHAR( 50 ) DEFAULT NULL,
	`contact_message` TEXT NOT NULL,
	`acymailing_message` TEXT DEFAULT NULL,
	`ip` VARCHAR( 100 ) NOT NULL
);
 