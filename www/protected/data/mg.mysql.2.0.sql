SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';


-- -----------------------------------------------------
-- Table `licence`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `licence` ;

CREATE  TABLE IF NOT EXISTS `licence` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(64) NOT NULL ,
  `description` TEXT NULL ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB DEFAULT CHARSET=UTF8;


-- -----------------------------------------------------
-- Table `collection`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `collection` ;

CREATE  TABLE IF NOT EXISTS `collection` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(64) NOT NULL ,
  `locked` INT(1) NOT NULL DEFAULT 0 ,
  `more_information` TEXT NULL ,
  `licence_id` INT(11) NULL ,
  `last_access_interval` INT(11) NOT NULL DEFAULT 0,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_collections_licences1` (`licence_id` ASC) ,
  CONSTRAINT `fk_collections_licences1`
    FOREIGN KEY (`licence_id` )
    REFERENCES `licence` (`id` )
    ON DELETE SET NULL
    ON UPDATE NO ACTION)
ENGINE = InnoDB DEFAULT CHARSET=UTF8 DEFAULT CHARSET=UTF8;


-- -----------------------------------------------------
-- Table `user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `user` ;

CREATE  TABLE IF NOT EXISTS `user` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `username` VARCHAR(32) NOT NULL ,
  `password` VARCHAR(128) NOT NULL ,
  `email` VARCHAR(128) NOT NULL ,
  `activekey` VARCHAR(128) NOT NULL DEFAULT '' ,
  `lastvisit` DATETIME NULL ,
  `role` VARCHAR(45) NOT NULL DEFAULT 'editor' ,
  `status` INT(1) NOT NULL DEFAULT '0' ,
  `edited_count` INT(1) NOT NULL DEFAULT 0 ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `username` (`username` ASC) ,
  UNIQUE INDEX `email` (`email` ASC) ,
  INDEX `status` (`status` ASC) )
ENGINE = InnoDB DEFAULT CHARSET=UTF8;


-- -----------------------------------------------------
-- Table `media`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `media` ;

CREATE  TABLE IF NOT EXISTS `media` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(254) NOT NULL ,
  `size` INT(11) NOT NULL ,
  `mime_type` VARCHAR(45) NOT NULL ,
  `batch_id` VARCHAR(45) NOT NULL DEFAULT 'BATCH-001' ,
  `last_access` DATETIME NULL ,
  `locked` INT(1) NOT NULL DEFAULT 0 ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB DEFAULT CHARSET=UTF8;


-- -----------------------------------------------------
-- Table `log`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `log` ;

CREATE  TABLE IF NOT EXISTS `log` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `category` VARCHAR(128) NOT NULL ,
  `message` TEXT NOT NULL ,
  `user_id` INT(11) NULL ,
  `created` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_log_users1` (`user_id` ASC) ,
  CONSTRAINT `fk_log_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `user` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB DEFAULT CHARSET=UTF8;


-- -----------------------------------------------------
-- Table `session`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `session` ;

CREATE  TABLE IF NOT EXISTS `session` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `username` VARCHAR(32) NOT NULL ,
  `ip_address` INT(11) NOT NULL ,
  `php_sid` VARCHAR(45) NOT NULL ,
  `shared_secret` VARCHAR(45) NOT NULL ,
  `user_id` INT(11) NULL ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_sessions_users1` (`user_id` ASC) ,
  CONSTRAINT `fk_sessions_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `user` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB DEFAULT CHARSET=UTF8;


-- -----------------------------------------------------
-- Table `collection_toa_media`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `collection_to_media` ;

CREATE  TABLE IF NOT EXISTS `collection_to_media` (
  `collection_id` INT NOT NULL ,
  `media_id` INT(11) NOT NULL ,
  PRIMARY KEY (`collection_id`, `media_id`) ,
  INDEX `fk_collections_has_medias_medias1` (`media_id` ASC) ,
  INDEX `fk_collections_has_medias_collections1` (`collection_id` ASC) ,
  CONSTRAINT `fk_collections_has_medias_collections1`
    FOREIGN KEY (`collection_id` )
    REFERENCES `collection` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_collections_has_medias_medias1`
    FOREIGN KEY (`media_id` )
    REFERENCES `media` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB DEFAULT CHARSET=UTF8;


-- -----------------------------------------------------
-- Table `AuthItem`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `AuthItem` ;

CREATE  TABLE IF NOT EXISTS `AuthItem` (
  `name` VARCHAR(64) NOT NULL ,
  `type` INT(11) NOT NULL,
  `description` TEXT  ,
  `bizrule` TEXT ,
  `data` TEXT ,
  PRIMARY KEY (`name`) )
ENGINE = InnoDB DEFAULT CHARSET=UTF8;


-- -----------------------------------------------------
-- Table `AuthItemChild`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `AuthItemChild` ;

CREATE  TABLE IF NOT EXISTS `AuthItemChild` (
  `parent` VARCHAR(64) NOT NULL ,
  `child` VARCHAR(64) NOT NULL ,
  PRIMARY KEY (`parent`, `child`) ,
  INDEX `fk_{818C790C-3BBE-4C8D-A383-37DED516A298}` (`child` ASC) ,
  CONSTRAINT `fk_{D3C449C2-B9CC-46EA-80A5-1FBCB86CD3A2}`
    FOREIGN KEY (`parent` )
    REFERENCES `AuthItem` (`name` )
    ON DELETE cascade
    ON UPDATE cascade,
  CONSTRAINT `fk_{818C790C-3BBE-4C8D-A383-37DED516A298}`
    FOREIGN KEY (`child` )
    REFERENCES `AuthItem` (`name` )
    ON DELETE cascade
    ON UPDATE cascade)
ENGINE = InnoDB DEFAULT CHARSET=UTF8;


-- -----------------------------------------------------
-- Table `AuthAssignment`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `AuthAssignment` ;

CREATE  TABLE IF NOT EXISTS `AuthAssignment` (
  `itemname` VARCHAR(64) NOT NULL ,
  `userid` VARCHAR(64) NOT NULL ,
  `bizrule` TEXT ,
  `data` TEXT ,
  PRIMARY KEY (`itemname`, `userid`) ,
  CONSTRAINT `fk_{4D666442-7B7E-4C6F-84DC-566EB7E44203}`
    FOREIGN KEY (`itemname` )
    REFERENCES `AuthItem` (`name` )
    ON DELETE cascade
    ON UPDATE cascade)
ENGINE = InnoDB DEFAULT CHARSET=UTF8;


-- -----------------------------------------------------
-- Table `pcounter_users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pcounter_users` ;

CREATE  TABLE IF NOT EXISTS `pcounter_users` (
  `user_ip` VARCHAR(39) NOT NULL ,
  `user_time` INT(10) UNSIGNED NOT NULL ,
  UNIQUE INDEX `user_ip` (`user_ip` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pcounter_save`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pcounter_save` ;

CREATE  TABLE IF NOT EXISTS `pcounter_save` (
  `save_name` VARCHAR(10) NOT NULL ,
  `save_value` INT(10) UNSIGNED NOT NULL )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;

-- -----------------------------------------------------
-- Table `cron_jobs`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cron_jobs` ;

CREATE TABLE IF NOT EXISTS `cron_jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `execute_after` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `executed_started` timestamp NULL DEFAULT NULL,
  `executed_finished` timestamp NULL DEFAULT NULL,
  `succeeded` tinyint(1) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `parameters` text,
  `execution_result` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB CHARACTER SET = utf8;

--
-- Table structure for table `tbl_migration`
--

CREATE TABLE IF NOT EXISTS `tbl_migration` (
  `version` varchar(255) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `licence`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `licence` (`id`, `name`, `description`, `created`, `modified`) VALUES (1, 'Default Licence', 'This is the default licence. The medias used are not licenced', '2011-01-01 12:00', '2011-01-01 12:00');

COMMIT;

-- -----------------------------------------------------
-- Data for table `collection`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `collection` (`id`, `name`, `locked`, `more_information`, `licence_id`, `last_access_interval`, `created`, `modified`) VALUES (1, 'All', 1, 'This is the default media set. All medias will be automatically assigned to it. It cannot be deleted.', 1, 0, '2011-01-01 12:00', '2011-01-01 12:00');

COMMIT;

-- -----------------------------------------------------
-- Data for table `AuthItem`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `AuthItem` (`name`, `type`, `description`, `bizrule`, `data`) VALUES ('editor', 2, 'An editor has access to several tools in the system', NULL, NULL);
INSERT INTO `AuthItem` (`name`, `type`, `description`, `bizrule`, `data`) VALUES ('admin', 2, 'The admin can access everything', NULL, NULL);

COMMIT;

-- -----------------------------------------------------
-- Data for table `AuthItemChild`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `AuthItemChild` (`parent`, `child`) VALUES ('admin', 'editor');

COMMIT;

-- -----------------------------------------------------
-- Data for table `pcounter_users`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `pcounter_users` (`user_ip`, `user_time`) VALUES ('127.0.0.1', 1290821670);

COMMIT;

-- -----------------------------------------------------
-- Data for table `pcounter_save`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `pcounter_save` (`save_name`, `save_value`) VALUES ('day_time', 2455527);
INSERT INTO `pcounter_save` (`save_name`, `save_value`) VALUES ('max_count', 0);
INSERT INTO `pcounter_save` (`save_name`, `save_value`) VALUES ('counter', 0);
INSERT INTO `pcounter_save` (`save_name`, `save_value`) VALUES ('yesterday', 0);

COMMIT;

