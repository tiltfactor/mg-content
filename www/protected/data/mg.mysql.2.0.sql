SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';


-- -----------------------------------------------------
-- Table `stop_word`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `stop_word` ;

CREATE  TABLE IF NOT EXISTS `stop_word` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `word` VARCHAR(64) NOT NULL ,
  `source` VARCHAR(12) NOT NULL DEFAULT 'import' ,
  `counter` INT UNSIGNED NOT NULL DEFAULT 0 ,
  `modified` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


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
ENGINE = InnoDB;


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
  `last_access_interval` INT NOT NULL DEFAULT 0 ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_collections_licences1` (`licence_id` ASC) ,
  CONSTRAINT `fk_collections_licences1`
    FOREIGN KEY (`licence_id` )
    REFERENCES `licence` (`id` )
    ON DELETE SET NULL
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `subject_matter`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `subject_matter` ;

CREATE  TABLE IF NOT EXISTS `subject_matter` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(64) NOT NULL ,
  `locked` INT(1) NOT NULL DEFAULT 0 ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


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
  `role` VARCHAR(45) NOT NULL DEFAULT 'player' ,
  `status` INT(1) NOT NULL DEFAULT '0' ,
  `edited_count` INT(1) NOT NULL DEFAULT 0 ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `username` (`username` ASC) ,
  UNIQUE INDEX `email` (`email` ASC) ,
  INDEX `status` (`status` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `user_to_subject_matter`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `user_to_subject_matter` ;

CREATE  TABLE IF NOT EXISTS `user_to_subject_matter` (
  `user_id` INT(11) NOT NULL ,
  `subject_matter_id` INT(11) NOT NULL ,
  `trust` INT(11) NOT NULL DEFAULT 0 ,
  `expertise` INT(11) NOT NULL DEFAULT 0 ,
  `interest` INT(11) NOT NULL DEFAULT 0 ,
  INDEX `fk_users2subject_matters_subject_matters1` (`subject_matter_id` ASC) ,
  PRIMARY KEY (`user_id`, `subject_matter_id`) ,
  INDEX `fk_users_has_subject_matters_users1` (`user_id` ASC) ,
  CONSTRAINT `fk_users2subject_matters_subject_matters1`
    FOREIGN KEY (`subject_matter_id` )
    REFERENCES `subject_matter` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_users_has_subject_matters_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `user` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `game`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `game` ;

CREATE  TABLE IF NOT EXISTS `game` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `active` INT(1) NOT NULL DEFAULT 0 ,
  `number_played` INT(11) NOT NULL DEFAULT 0 ,
  `unique_id` VARCHAR(45) NOT NULL ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `unique_id_UNIQUE` (`unique_id` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `user_to_game`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `user_to_game` ;

CREATE  TABLE IF NOT EXISTS `user_to_game` (
  `user_id` INT(11) NOT NULL ,
  `game_id` INT NOT NULL ,
  `score` INT(11) NOT NULL DEFAULT 0 ,
  `number_played` INT(11) NOT NULL DEFAULT 0 ,
  INDEX `fk_users2games_games1` (`game_id` ASC) ,
  PRIMARY KEY (`user_id`, `game_id`) ,
  INDEX `fk_user_has_games_users1` (`user_id` ASC) ,
  CONSTRAINT `fk_users2games_games1`
    FOREIGN KEY (`game_id` )
    REFERENCES `game` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_has_games_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `user` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `badge`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `badge` ;

CREATE  TABLE IF NOT EXISTS `badge` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `title` VARCHAR(45) NOT NULL ,
  `points` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


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
ENGINE = InnoDB;


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
ENGINE = InnoDB;


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
ENGINE = InnoDB;


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
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `collection_to_subject_matter`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `collection_to_subject_matter` ;

CREATE  TABLE IF NOT EXISTS `collection_to_subject_matter` (
  `collection_id` INT NOT NULL ,
  `subject_matter_id` INT(11) NOT NULL ,
  PRIMARY KEY (`collection_id`, `subject_matter_id`) ,
  INDEX `fk_collections_has_subject_matters_subject_matters1` (`subject_matter_id` ASC) ,
  INDEX `fk_collections_has_subject_matters_collections1` (`collection_id` ASC) ,
  CONSTRAINT `fk_collections_has_subject_matters_collections1`
    FOREIGN KEY (`collection_id` )
    REFERENCES `collection` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_collections_has_subject_matters_subject_matters1`
    FOREIGN KEY (`subject_matter_id` )
    REFERENCES `subject_matter` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `plugin`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `plugin` ;

CREATE  TABLE IF NOT EXISTS `plugin` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `type` VARCHAR(20) NOT NULL ,
  `active` INT(1) NOT NULL ,
  `unique_id` VARCHAR(254) NOT NULL ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `unique_id_UNIQUE` (`unique_id` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tag`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tag` ;

CREATE  TABLE IF NOT EXISTS `tag` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `tag` VARCHAR(64) NOT NULL ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `tag_UNIQUE` (`tag` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `played_game`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `played_game` ;

CREATE  TABLE IF NOT EXISTS `played_game` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `session_id_1` INT(11) NOT NULL ,
  `session_id_2` INT(11) NULL ,
  `game_id` INT(11) NOT NULL ,
  `score_1` INT(11) NOT NULL DEFAULT 0 ,
  `score_2` INT(11) NOT NULL DEFAULT 0 ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  `finished` DATETIME NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_played_games_sessions1` (`session_id_1` ASC) ,
  INDEX `fk_played_games_games1` (`game_id` ASC) ,
  INDEX `fk_played_game_session1` (`session_id_2` ASC) ,
  CONSTRAINT `fk_played_games_sessions1`
    FOREIGN KEY (`session_id_1` )
    REFERENCES `session` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_played_games_games1`
    FOREIGN KEY (`game_id` )
    REFERENCES `game` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_played_game_session1`
    FOREIGN KEY (`session_id_2` )
    REFERENCES `session` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `game_submission`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `game_submission` ;

CREATE  TABLE IF NOT EXISTS `game_submission` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `submission` TEXT NOT NULL ,
  `turn` INT(11) NOT NULL DEFAULT 0 ,
  `session_id` INT(11) NOT NULL ,
  `played_game_id` INT(11) NOT NULL ,
  `created` DATETIME NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_game_submissions_sessions1` (`session_id` ASC) ,
  INDEX `fk_game_submissions_played_games1` (`played_game_id` ASC) ,
  CONSTRAINT `fk_game_submissions_sessions1`
    FOREIGN KEY (`session_id` )
    REFERENCES `session` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_game_submissions_played_games1`
    FOREIGN KEY (`played_game_id` )
    REFERENCES `played_game` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tag_use`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tag_use` ;

CREATE  TABLE IF NOT EXISTS `tag_use` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `media_id` INT(11) NOT NULL ,
  `tag_id` INT(11) NOT NULL ,
  `weight` INT(3) NOT NULL DEFAULT 0 ,
  `type` TEXT NOT NULL ,
  `game_submission_id` INT UNSIGNED NOT NULL ,
  `created` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_tag_uses_medias1` (`media_id` ASC) ,
  INDEX `fk_tag_uses_tags1` (`tag_id` ASC) ,
  INDEX `fk_tag_uses_game_submissions1` (`game_submission_id` ASC) ,
  CONSTRAINT `fk_tag_uses_medias1`
    FOREIGN KEY (`media_id` )
    REFERENCES `media` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tag_uses_tags1`
    FOREIGN KEY (`tag_id` )
    REFERENCES `tag` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tag_uses_game_submissions1`
    FOREIGN KEY (`game_submission_id` )
    REFERENCES `game_submission` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `message`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `message` ;

CREATE  TABLE IF NOT EXISTS `message` (
  `session_id` INT(11) NOT NULL ,
  `played_game_id` INT(11) NOT NULL ,
  `message` VARCHAR(1000) NULL ,
  INDEX `fk_messages_sessions1` (`session_id` ASC) ,
  INDEX `fk_message_played_game1` (`played_game_id` ASC) ,
  CONSTRAINT `fk_messages_sessions1`
    FOREIGN KEY (`session_id` )
    REFERENCES `session` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_message_played_game1`
    FOREIGN KEY (`played_game_id` )
    REFERENCES `played_game` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `blocked_ip`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `blocked_ip` ;

CREATE  TABLE IF NOT EXISTS `blocked_ip` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `ip` VARCHAR(45) NOT NULL ,
  `type` ENUM('deny', 'allow') NOT NULL DEFAULT 'deny' ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `game_to_collection`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `game_to_collection` ;

CREATE  TABLE IF NOT EXISTS `game_to_collection` (
  `game_id` INT(11) NOT NULL ,
  `collection_id` INT(11) NOT NULL ,
  PRIMARY KEY (`game_id`, `collection_id`) ,
  INDEX `fk_games_has_collections_collections1` (`collection_id` ASC) ,
  INDEX `fk_games_has_collections_games1` (`game_id` ASC) ,
  CONSTRAINT `fk_games_has_collections_games1`
    FOREIGN KEY (`game_id` )
    REFERENCES `game` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_games_has_collections_collections1`
    FOREIGN KEY (`collection_id` )
    REFERENCES `collection` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tag_original_version`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tag_original_version` ;

CREATE  TABLE IF NOT EXISTS `tag_original_version` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `original_tag` VARCHAR(64) NULL ,
  `tag_use_id` INT(11) NOT NULL ,
  `comments` TEXT NULL ,
  `user_id` INT(11) NULL ,
  `created` DATETIME NULL ,
  PRIMARY KEY (`id`, `tag_use_id`) ,
  INDEX `fk_tag_original_version_tag_uses1` (`tag_use_id` ASC) ,
  INDEX `fk_tag_original_version_user1` (`user_id` ASC) ,
  CONSTRAINT `fk_tag_original_version_tag_uses1`
    FOREIGN KEY (`tag_use_id` )
    REFERENCES `tag_use` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tag_original_version_user1`
    FOREIGN KEY (`user_id` )
    REFERENCES `user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `profile_field`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `profile_field` ;

CREATE  TABLE IF NOT EXISTS `profile_field` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `varname` VARCHAR(50) NOT NULL ,
  `title` VARCHAR(255) NOT NULL ,
  `field_type` VARCHAR(50) NOT NULL ,
  `field_size` INT(3) NOT NULL DEFAULT '0' ,
  `field_size_min` INT(3) NOT NULL DEFAULT '0' ,
  `required` INT(1) NOT NULL DEFAULT '0' ,
  `match` VARCHAR(255) NOT NULL DEFAULT '' ,
  `range` TEXT NOT NULL ,
  `error_message` VARCHAR(255) NOT NULL DEFAULT '' ,
  `other_validator` VARCHAR(5000) NOT NULL DEFAULT '' ,
  `default` VARCHAR(255) NOT NULL DEFAULT '' ,
  `widget` VARCHAR(255) NOT NULL DEFAULT '' ,
  `widgetparams` VARCHAR(5000) NOT NULL DEFAULT '' ,
  `position` INT(3) NOT NULL DEFAULT '0' ,
  `visible` INT(1) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) ,
  INDEX `varname` (`varname` ASC, `widget` ASC, `visible` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `profile`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `profile` ;

CREATE  TABLE IF NOT EXISTS `profile` (
  `user_id` INT(11) NOT NULL ,
  INDEX `fk_profile_users1` (`user_id` ASC) ,
  PRIMARY KEY (`user_id`) ,
  CONSTRAINT `fk_profile_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `user` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `game_partner`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `game_partner` ;

CREATE  TABLE IF NOT EXISTS `game_partner` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `session_id_1` INT(11) NOT NULL ,
  `session_id_2` INT(11) NULL ,
  `game_id` INT(11) NOT NULL ,
  `played_game_id` INT(11) NULL ,
  `created` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_game_partner_session1` (`session_id_1` ASC) ,
  INDEX `fk_game_partner_game1` (`game_id` ASC) ,
  INDEX `fk_game_partner_session2` (`session_id_2` ASC) ,
  INDEX `fk_game_partner_played_game1` (`played_game_id` ASC) ,
  CONSTRAINT `fk_game_partner_session1`
    FOREIGN KEY (`session_id_1` )
    REFERENCES `session` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_game_partner_game1`
    FOREIGN KEY (`game_id` )
    REFERENCES `game` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_game_partner_session2`
    FOREIGN KEY (`session_id_2` )
    REFERENCES `session` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_game_partner_played_game1`
    FOREIGN KEY (`played_game_id` )
    REFERENCES `played_game` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `AuthItem`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `AuthItem` ;

CREATE  TABLE IF NOT EXISTS `AuthItem` (
  `name` VARCHAR(64) NOT NULL ,
  `type` INT NOT NULL ,
  `description` TEXT NULL DEFAULT NULL ,
  `bizrule` TEXT NULL DEFAULT NULL ,
  `data` TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`name`) )
ENGINE = InnoDB;


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
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `AuthAssignment`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `AuthAssignment` ;

CREATE  TABLE IF NOT EXISTS `AuthAssignment` (
  `itemname` VARCHAR(64) NOT NULL ,
  `userid` VARCHAR(64) NOT NULL ,
  `bizrule` TEXT NULL DEFAULT NULL ,
  `data` TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`itemname`, `userid`) ,
  CONSTRAINT `fk_{4D666442-7B7E-4C6F-84DC-566EB7E44203}`
    FOREIGN KEY (`itemname` )
    REFERENCES `AuthItem` (`name` )
    ON DELETE cascade
    ON UPDATE cascade)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `game_to_plugin`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `game_to_plugin` ;

CREATE  TABLE IF NOT EXISTS `game_to_plugin` (
  `game_id` INT(11) NOT NULL ,
  `plugin_id` INT(11) NOT NULL ,
  PRIMARY KEY (`game_id`, `plugin_id`) ,
  INDEX `fk_game_has_plugin_plugin1` (`plugin_id` ASC) ,
  INDEX `fk_game_has_plugin_game1` (`game_id` ASC) ,
  CONSTRAINT `fk_game_has_plugin_game1`
    FOREIGN KEY (`game_id` )
    REFERENCES `game` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_game_has_plugin_plugin1`
    FOREIGN KEY (`plugin_id` )
    REFERENCES `plugin` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `played_game_turn_info`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `played_game_turn_info` ;

CREATE  TABLE IF NOT EXISTS `played_game_turn_info` (
  `played_game_id` INT(11) NOT NULL ,
  `turn` INT(11) NOT NULL ,
  `data` TEXT NOT NULL ,
  `created_by_session_id` INT(11) NOT NULL ,
  PRIMARY KEY (`played_game_id`, `turn`) ,
  INDEX `fk_played_game_turn_info_session1` (`created_by_session_id` ASC) ,
  CONSTRAINT `fk_table1_played_game1`
    FOREIGN KEY (`played_game_id` )
    REFERENCES `played_game` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_played_game_turn_info_session1`
    FOREIGN KEY (`created_by_session_id` )
    REFERENCES `session` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'used in multiplayer games';


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
-- Data for table `subject_matter`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `subject_matter` (`id`, `name`, `locked`, `created`, `modified`) VALUES (1, 'All', 1, '2011-01-01 12:00', '2011-01-01 12:00');

COMMIT;

-- -----------------------------------------------------
-- Data for table `badge`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `badge` (`id`, `title`, `points`) VALUES (1, 'Alpha', 100);
INSERT INTO `badge` (`id`, `title`, `points`) VALUES (2, 'Beta', 500);
INSERT INTO `badge` (`id`, `title`, `points`) VALUES (3, 'Gamma', 1000);
INSERT INTO `badge` (`id`, `title`, `points`) VALUES (4, 'Delta', 2000);

COMMIT;

-- -----------------------------------------------------
-- Data for table `AuthItem`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `AuthItem` (`name`, `type`, `description`, `bizrule`, `data`) VALUES ('player', 2, 'A player can only record his or her games', NULL, NULL);
INSERT INTO `AuthItem` (`name`, `type`, `description`, `bizrule`, `data`) VALUES ('editor', 2, 'An editor has access to several tools in the system', NULL, NULL);
INSERT INTO `AuthItem` (`name`, `type`, `description`, `bizrule`, `data`) VALUES ('dbmanager', 2, 'A db manager has access to nearly all tools', NULL, NULL);
INSERT INTO `AuthItem` (`name`, `type`, `description`, `bizrule`, `data`) VALUES ('admin', 2, 'The admin can access everything', NULL, NULL);

COMMIT;

-- -----------------------------------------------------
-- Data for table `AuthItemChild`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `AuthItemChild` (`parent`, `child`) VALUES ('editor', 'player');
INSERT INTO `AuthItemChild` (`parent`, `child`) VALUES ('dbmanager', 'player');
INSERT INTO `AuthItemChild` (`parent`, `child`) VALUES ('dbmanager', 'editor');
INSERT INTO `AuthItemChild` (`parent`, `child`) VALUES ('admin', 'player');
INSERT INTO `AuthItemChild` (`parent`, `child`) VALUES ('admin', 'editor');
INSERT INTO `AuthItemChild` (`parent`, `child`) VALUES ('admin', 'dbmanager');

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
