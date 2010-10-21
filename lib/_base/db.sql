CREATE TABLE `session` (
	`id` VARCHAR(32) NOT NULL,
	`data` LONGTEXT NOT NULL,
	`last_modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
ROW_FORMAT=DEFAULT