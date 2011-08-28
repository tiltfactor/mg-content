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
  `counter` INT UNSIGNED NOT NULL DEFAULT 0 ,
  `active` TINYINT NOT NULL DEFAULT 1 ,
  `created` DATETIME NOT NULL ,
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
-- Table `image_set`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `image_set` ;

CREATE  TABLE IF NOT EXISTS `image_set` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(64) NOT NULL ,
  `locked` INT(1) NOT NULL DEFAULT 0 ,
  `more_information` TEXT NULL ,
  `licence_id` INT(11) NULL ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_image_sets_licences1`
    FOREIGN KEY (`licence_id` )
    REFERENCES `licence` (`id` )
    ON DELETE SET NULL
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_image_sets_licences1` ON `image_set` (`licence_id` ASC) ;


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
  `activkey` VARCHAR(128) NOT NULL DEFAULT '' ,
  `lastvisit` DATETIME NULL ,
  `role` VARCHAR(45) NOT NULL DEFAULT 'player' ,
  `status` INT(1) NOT NULL DEFAULT '0' ,
  `edited_count` INT(1) NOT NULL DEFAULT 0 ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;

CREATE UNIQUE INDEX `username` ON `user` (`username` ASC) ;

CREATE UNIQUE INDEX `email` ON `user` (`email` ASC) ;

CREATE INDEX `status` ON `user` (`status` ASC) ;


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
  PRIMARY KEY (`user_id`, `subject_matter_id`) ,
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

CREATE INDEX `fk_users2subject_matters_subject_matters1` ON `user_to_subject_matter` (`subject_matter_id` ASC) ;

CREATE INDEX `fk_users_has_subject_matters_users1` ON `user_to_subject_matter` (`user_id` ASC) ;


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
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;

CREATE UNIQUE INDEX `unique_id_UNIQUE` ON `game` (`unique_id` ASC) ;


-- -----------------------------------------------------
-- Table `user_to_game`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `user_to_game` ;

CREATE  TABLE IF NOT EXISTS `user_to_game` (
  `user_id` INT(11) NOT NULL ,
  `game_id` INT NOT NULL ,
  `score` INT(11) NOT NULL DEFAULT 0 ,
  `number_played` INT(11) NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`user_id`, `game_id`) ,
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

CREATE INDEX `fk_users2games_games1` ON `user_to_game` (`game_id` ASC) ;

CREATE INDEX `fk_user_has_games_users1` ON `user_to_game` (`user_id` ASC) ;


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
-- Table `image`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `image` ;

CREATE  TABLE IF NOT EXISTS `image` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(254) NOT NULL ,
  `size` INT(11) NOT NULL ,
  `mime_type` VARCHAR(45) NOT NULL ,
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
  `level` VARCHAR(128) NOT NULL ,
  `category` VARCHAR(128) NOT NULL ,
  `logtime` INT UNSIGNED NOT NULL ,
  `message` TEXT NOT NULL ,
  `user_id` INT(11) NULL ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_log_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `user` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_log_users1` ON `log` (`user_id` ASC) ;


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
  CONSTRAINT `fk_sessions_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `user` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_sessions_users1` ON `session` (`user_id` ASC) ;


-- -----------------------------------------------------
-- Table `image_set_to_image`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `image_set_to_image` ;

CREATE  TABLE IF NOT EXISTS `image_set_to_image` (
  `image_set_id` INT NOT NULL ,
  `image_id` INT(11) NOT NULL ,
  PRIMARY KEY (`image_set_id`, `image_id`) ,
  CONSTRAINT `fk_image_sets_has_images_image_sets1`
    FOREIGN KEY (`image_set_id` )
    REFERENCES `image_set` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_image_sets_has_images_images1`
    FOREIGN KEY (`image_id` )
    REFERENCES `image` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_image_sets_has_images_images1` ON `image_set_to_image` (`image_id` ASC) ;

CREATE INDEX `fk_image_sets_has_images_image_sets1` ON `image_set_to_image` (`image_set_id` ASC) ;


-- -----------------------------------------------------
-- Table `image_set_to_subject_matter`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `image_set_to_subject_matter` ;

CREATE  TABLE IF NOT EXISTS `image_set_to_subject_matter` (
  `image_set_id` INT NOT NULL ,
  `subject_matter_id` INT(11) NOT NULL ,
  PRIMARY KEY (`image_set_id`, `subject_matter_id`) ,
  CONSTRAINT `fk_image_sets_has_subject_matters_image_sets1`
    FOREIGN KEY (`image_set_id` )
    REFERENCES `image_set` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_image_sets_has_subject_matters_subject_matters1`
    FOREIGN KEY (`subject_matter_id` )
    REFERENCES `subject_matter` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_image_sets_has_subject_matters_subject_matters1` ON `image_set_to_subject_matter` (`subject_matter_id` ASC) ;

CREATE INDEX `fk_image_sets_has_subject_matters_image_sets1` ON `image_set_to_subject_matter` (`image_set_id` ASC) ;


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
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;

CREATE UNIQUE INDEX `unique_id_UNIQUE` ON `plugin` (`unique_id` ASC) ;


-- -----------------------------------------------------
-- Table `menu`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `menu` ;

CREATE  TABLE IF NOT EXISTS `menu` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `page`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `page` ;

CREATE  TABLE IF NOT EXISTS `page` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `title` VARCHAR(45) NOT NULL ,
  `text` TEXT NULL ,
  `url` VARCHAR(254) NULL ,
  `new_tab` INT(1) NOT NULL DEFAULT 0 ,
  `type` INT(1) NULL DEFAULT 1 ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `menu_item`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `menu_item` ;

CREATE  TABLE IF NOT EXISTS `menu_item` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `menu_id` INT(11) NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `weight` INT(5) NOT NULL DEFAULT 0 ,
  `active` INT(1) NOT NULL DEFAULT 1 ,
  `special` INT(1) NULL DEFAULT 0 ,
  `lang` VARCHAR(10) NOT NULL DEFAULT 'en' ,
  `pages_id` INT NOT NULL ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_menu_item_menu1`
    FOREIGN KEY (`menu_id` )
    REFERENCES `menu` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_menu_items_pages1`
    FOREIGN KEY (`pages_id` )
    REFERENCES `page` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_menu_item_menu1` ON `menu_item` (`menu_id` ASC) ;

CREATE INDEX `fk_menu_items_pages1` ON `menu_item` (`pages_id` ASC) ;


-- -----------------------------------------------------
-- Table `tag`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tag` ;

CREATE  TABLE IF NOT EXISTS `tag` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `tag` VARCHAR(64) NOT NULL ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) )
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

CREATE INDEX `fk_played_games_sessions1` ON `played_game` (`session_id_1` ASC) ;

CREATE INDEX `fk_played_games_games1` ON `played_game` (`game_id` ASC) ;

CREATE INDEX `fk_played_game_session1` ON `played_game` (`session_id_2` ASC) ;


-- -----------------------------------------------------
-- Table `game_submission`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `game_submission` ;

CREATE  TABLE IF NOT EXISTS `game_submission` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `submission` TEXT NOT NULL ,
  `session_id` INT(11) NOT NULL ,
  `played_game_id` INT(11) NOT NULL ,
  `created` DATETIME NULL ,
  PRIMARY KEY (`id`) ,
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

CREATE INDEX `fk_game_submissions_sessions1` ON `game_submission` (`session_id` ASC) ;

CREATE INDEX `fk_game_submissions_played_games1` ON `game_submission` (`played_game_id` ASC) ;


-- -----------------------------------------------------
-- Table `tag_use`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tag_use` ;

CREATE  TABLE IF NOT EXISTS `tag_use` (
  `id` INT(11) NOT NULL ,
  `image_id` INT(11) NOT NULL ,
  `tag_id` INT(11) NOT NULL ,
  `weight` INT(3) NOT NULL DEFAULT 0 ,
  `created` DATETIME NOT NULL ,
  `game_submissions_id` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_tag_uses_images1`
    FOREIGN KEY (`image_id` )
    REFERENCES `image` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tag_uses_tags1`
    FOREIGN KEY (`tag_id` )
    REFERENCES `tag` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tag_uses_game_submissions1`
    FOREIGN KEY (`game_submissions_id` )
    REFERENCES `game_submission` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_tag_uses_images1` ON `tag_use` (`image_id` ASC) ;

CREATE INDEX `fk_tag_uses_tags1` ON `tag_use` (`tag_id` ASC) ;

CREATE INDEX `fk_tag_uses_game_submissions1` ON `tag_use` (`game_submissions_id` ASC) ;


-- -----------------------------------------------------
-- Table `message`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `message` ;

CREATE  TABLE IF NOT EXISTS `message` (
  `session_id` INT(11) NOT NULL ,
  `message` VARCHAR(1000) NULL ,
  PRIMARY KEY (`session_id`) ,
  CONSTRAINT `fk_messages_sessions1`
    FOREIGN KEY (`session_id` )
    REFERENCES `session` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_messages_sessions1` ON `message` (`session_id` ASC) ;


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
-- Table `game_to_image_set`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `game_to_image_set` ;

CREATE  TABLE IF NOT EXISTS `game_to_image_set` (
  `game_id` INT(11) NOT NULL ,
  `image_set_id` INT(11) NOT NULL ,
  PRIMARY KEY (`game_id`, `image_set_id`) ,
  CONSTRAINT `fk_games_has_image_sets_games1`
    FOREIGN KEY (`game_id` )
    REFERENCES `game` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_games_has_image_sets_image_sets1`
    FOREIGN KEY (`image_set_id` )
    REFERENCES `image_set` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_games_has_image_sets_image_sets1` ON `game_to_image_set` (`image_set_id` ASC) ;

CREATE INDEX `fk_games_has_image_sets_games1` ON `game_to_image_set` (`game_id` ASC) ;


-- -----------------------------------------------------
-- Table `tag_original_version`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tag_original_version` ;

CREATE  TABLE IF NOT EXISTS `tag_original_version` (
  `id` INT NOT NULL ,
  `original_tag` VARCHAR(64) NULL ,
  `tag_uses_id` INT(11) NOT NULL ,
  `by_editor` INT(11) NULL ,
  `comments` TEXT NULL ,
  PRIMARY KEY (`id`, `tag_uses_id`) ,
  CONSTRAINT `fk_tag_original_version_tag_uses1`
    FOREIGN KEY (`tag_uses_id` )
    REFERENCES `tag_use` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_tag_original_version_tag_uses1` ON `tag_original_version` (`tag_uses_id` ASC) ;


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
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `varname` ON `profile_field` (`varname` ASC, `widget` ASC, `visible` ASC) ;


-- -----------------------------------------------------
-- Table `profile`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `profile` ;

CREATE  TABLE IF NOT EXISTS `profile` (
  `user_id` INT(11) NOT NULL ,
  PRIMARY KEY (`user_id`) ,
  CONSTRAINT `fk_profile_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `user` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_profile_users1` ON `profile` (`user_id` ASC) ;


-- -----------------------------------------------------
-- Table `game_partner`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `game_partner` ;

CREATE  TABLE IF NOT EXISTS `game_partner` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `session_id` INT(11) NOT NULL ,
  `game_id` INT(11) NOT NULL ,
  `created` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_game_partner_session1`
    FOREIGN KEY (`session_id` )
    REFERENCES `session` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_game_partner_game1`
    FOREIGN KEY (`game_id` )
    REFERENCES `game` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_game_partner_session1` ON `game_partner` (`session_id` ASC) ;

CREATE INDEX `fk_game_partner_game1` ON `game_partner` (`game_id` ASC) ;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `licence`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `licence` (`id`, `name`, `description`, `created`, `modified`) VALUES (1, 'Default Licence', 'This is the default licence. The images used are not licenced', '2011-01-01 12:00', '2011-01-01 12:00');

COMMIT;

-- -----------------------------------------------------
-- Data for table `image_set`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `image_set` (`id`, `name`, `locked`, `more_information`, `licence_id`, `created`, `modified`) VALUES (1, 'All', 1, 'This is the default image set. All images will be automatically assigned to it. It cannot be deleted.', 1, '2011-01-01 12:00', '2011-01-01 12:00');

COMMIT;

-- -----------------------------------------------------
-- Data for table `subject_matter`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `subject_matter` (`id`, `name`, `locked`, `created`, `modified`) VALUES (1, 'All', 1, '2011-01-01 12:00', '2011-01-01 12:00');

COMMIT;

-- -----------------------------------------------------
-- Data for table `user`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `user` (`id`, `username`, `password`, `email`, `activkey`, `lastvisit`, `role`, `status`, `edited_count`, `created`, `modified`) VALUES (1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin@admin.com', '9a24eff8c15a6a141ece27eb6947da0f', NULL, 'admin', 1, 0, '2011-01-01 12:00', '2011-01-01 12:00');
INSERT INTO `user` (`id`, `username`, `password`, `email`, `activkey`, `lastvisit`, `role`, `status`, `edited_count`, `created`, `modified`) VALUES (2, 'dbmanager', 'a945e003507368542f40f13b3076ca6f', 'dbmanager@dbmanager.com', 'b58e465dcb47f8459439cf54535bc8ae', NULL, 'dbmanager', 1, 0, '2011-01-01 12:00', '2011-01-01 12:00');
INSERT INTO `user` (`id`, `username`, `password`, `email`, `activkey`, `lastvisit`, `role`, `status`, `edited_count`, `created`, `modified`) VALUES (3, 'editor', '5aee9dbd2a188839105073571bee1b1f', 'editor@editor.com', '52c200c7ca710d13f2ba1bea3788723c', NULL, 'editor', 1, 0, '2011-01-01 12:00', '2011-01-01 12:00');
INSERT INTO `user` (`id`, `username`, `password`, `email`, `activkey`, `lastvisit`, `role`, `status`, `edited_count`, `created`, `modified`) VALUES (4, 'player', '912af0dff974604f1321254ca8ff38b6', 'player@player.com', 'd3f33d7d0e80bbe3004adc82fc9e779c', NULL, 'player', 1, 0, '2011-01-01 12:00', '2011-01-01 12:00');

COMMIT;

-- -----------------------------------------------------
-- Data for table `profile`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `profile` (`user_id`) VALUES (1);
INSERT INTO `profile` (`user_id`) VALUES (2);
INSERT INTO `profile` (`user_id`) VALUES (3);
INSERT INTO `profile` (`user_id`) VALUES (4);

COMMIT;
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
  `counter` INT UNSIGNED NOT NULL DEFAULT 0 ,
  `active` TINYINT NOT NULL DEFAULT 1 ,
  `created` DATETIME NOT NULL ,
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
-- Table `image_set`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `image_set` ;

CREATE  TABLE IF NOT EXISTS `image_set` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(64) NOT NULL ,
  `locked` INT(1) NOT NULL DEFAULT 0 ,
  `more_information` TEXT NULL ,
  `licence_id` INT(11) NULL ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_image_sets_licences1`
    FOREIGN KEY (`licence_id` )
    REFERENCES `licence` (`id` )
    ON DELETE SET NULL
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_image_sets_licences1` ON `image_set` (`licence_id` ASC) ;


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
  `activkey` VARCHAR(128) NOT NULL DEFAULT '' ,
  `lastvisit` DATETIME NULL ,
  `role` VARCHAR(45) NOT NULL DEFAULT 'player' ,
  `status` INT(1) NOT NULL DEFAULT '0' ,
  `edited_count` INT(1) NOT NULL DEFAULT 0 ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;

CREATE UNIQUE INDEX `username` ON `user` (`username` ASC) ;

CREATE UNIQUE INDEX `email` ON `user` (`email` ASC) ;

CREATE INDEX `status` ON `user` (`status` ASC) ;


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
  PRIMARY KEY (`user_id`, `subject_matter_id`) ,
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

CREATE INDEX `fk_users2subject_matters_subject_matters1` ON `user_to_subject_matter` (`subject_matter_id` ASC) ;

CREATE INDEX `fk_users_has_subject_matters_users1` ON `user_to_subject_matter` (`user_id` ASC) ;


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
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;

CREATE UNIQUE INDEX `unique_id_UNIQUE` ON `game` (`unique_id` ASC) ;


-- -----------------------------------------------------
-- Table `user_to_game`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `user_to_game` ;

CREATE  TABLE IF NOT EXISTS `user_to_game` (
  `user_id` INT(11) NOT NULL ,
  `game_id` INT NOT NULL ,
  `score` INT(11) NOT NULL DEFAULT 0 ,
  `number_played` INT(11) NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`user_id`, `game_id`) ,
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

CREATE INDEX `fk_users2games_games1` ON `user_to_game` (`game_id` ASC) ;

CREATE INDEX `fk_user_has_games_users1` ON `user_to_game` (`user_id` ASC) ;


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
-- Table `image`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `image` ;

CREATE  TABLE IF NOT EXISTS `image` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(254) NOT NULL ,
  `size` INT(11) NOT NULL ,
  `mime_type` VARCHAR(45) NOT NULL ,
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
  `level` VARCHAR(128) NOT NULL ,
  `category` VARCHAR(128) NOT NULL ,
  `logtime` INT UNSIGNED NOT NULL ,
  `message` TEXT NOT NULL ,
  `user_id` INT(11) NULL ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_log_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `user` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_log_users1` ON `log` (`user_id` ASC) ;


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
  CONSTRAINT `fk_sessions_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `user` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_sessions_users1` ON `session` (`user_id` ASC) ;


-- -----------------------------------------------------
-- Table `image_set_to_image`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `image_set_to_image` ;

CREATE  TABLE IF NOT EXISTS `image_set_to_image` (
  `image_set_id` INT NOT NULL ,
  `image_id` INT(11) NOT NULL ,
  PRIMARY KEY (`image_set_id`, `image_id`) ,
  CONSTRAINT `fk_image_sets_has_images_image_sets1`
    FOREIGN KEY (`image_set_id` )
    REFERENCES `image_set` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_image_sets_has_images_images1`
    FOREIGN KEY (`image_id` )
    REFERENCES `image` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_image_sets_has_images_images1` ON `image_set_to_image` (`image_id` ASC) ;

CREATE INDEX `fk_image_sets_has_images_image_sets1` ON `image_set_to_image` (`image_set_id` ASC) ;


-- -----------------------------------------------------
-- Table `image_set_to_subject_matter`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `image_set_to_subject_matter` ;

CREATE  TABLE IF NOT EXISTS `image_set_to_subject_matter` (
  `image_set_id` INT NOT NULL ,
  `subject_matter_id` INT(11) NOT NULL ,
  PRIMARY KEY (`image_set_id`, `subject_matter_id`) ,
  CONSTRAINT `fk_image_sets_has_subject_matters_image_sets1`
    FOREIGN KEY (`image_set_id` )
    REFERENCES `image_set` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_image_sets_has_subject_matters_subject_matters1`
    FOREIGN KEY (`subject_matter_id` )
    REFERENCES `subject_matter` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_image_sets_has_subject_matters_subject_matters1` ON `image_set_to_subject_matter` (`subject_matter_id` ASC) ;

CREATE INDEX `fk_image_sets_has_subject_matters_image_sets1` ON `image_set_to_subject_matter` (`image_set_id` ASC) ;


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
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;

CREATE UNIQUE INDEX `unique_id_UNIQUE` ON `plugin` (`unique_id` ASC) ;


-- -----------------------------------------------------
-- Table `menu`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `menu` ;

CREATE  TABLE IF NOT EXISTS `menu` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `page`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `page` ;

CREATE  TABLE IF NOT EXISTS `page` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `title` VARCHAR(45) NOT NULL ,
  `text` TEXT NULL ,
  `url` VARCHAR(254) NULL ,
  `new_tab` INT(1) NOT NULL DEFAULT 0 ,
  `type` INT(1) NULL DEFAULT 1 ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `menu_item`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `menu_item` ;

CREATE  TABLE IF NOT EXISTS `menu_item` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `menu_id` INT(11) NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `weight` INT(5) NOT NULL DEFAULT 0 ,
  `active` INT(1) NOT NULL DEFAULT 1 ,
  `special` INT(1) NULL DEFAULT 0 ,
  `lang` VARCHAR(10) NOT NULL DEFAULT 'en' ,
  `pages_id` INT NOT NULL ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_menu_item_menu1`
    FOREIGN KEY (`menu_id` )
    REFERENCES `menu` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_menu_items_pages1`
    FOREIGN KEY (`pages_id` )
    REFERENCES `page` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_menu_item_menu1` ON `menu_item` (`menu_id` ASC) ;

CREATE INDEX `fk_menu_items_pages1` ON `menu_item` (`pages_id` ASC) ;


-- -----------------------------------------------------
-- Table `tag`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tag` ;

CREATE  TABLE IF NOT EXISTS `tag` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `tag` VARCHAR(64) NOT NULL ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) )
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

CREATE INDEX `fk_played_games_sessions1` ON `played_game` (`session_id_1` ASC) ;

CREATE INDEX `fk_played_games_games1` ON `played_game` (`game_id` ASC) ;

CREATE INDEX `fk_played_game_session1` ON `played_game` (`session_id_2` ASC) ;


-- -----------------------------------------------------
-- Table `game_submission`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `game_submission` ;

CREATE  TABLE IF NOT EXISTS `game_submission` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `submission` TEXT NOT NULL ,
  `session_id` INT(11) NOT NULL ,
  `played_game_id` INT(11) NOT NULL ,
  `created` DATETIME NULL ,
  PRIMARY KEY (`id`) ,
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

CREATE INDEX `fk_game_submissions_sessions1` ON `game_submission` (`session_id` ASC) ;

CREATE INDEX `fk_game_submissions_played_games1` ON `game_submission` (`played_game_id` ASC) ;


-- -----------------------------------------------------
-- Table `tag_use`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tag_use` ;

CREATE  TABLE IF NOT EXISTS `tag_use` (
  `id` INT(11) NOT NULL ,
  `image_id` INT(11) NOT NULL ,
  `tag_id` INT(11) NOT NULL ,
  `weight` INT(3) NOT NULL DEFAULT 0 ,
  `created` DATETIME NOT NULL ,
  `game_submissions_id` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_tag_uses_images1`
    FOREIGN KEY (`image_id` )
    REFERENCES `image` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tag_uses_tags1`
    FOREIGN KEY (`tag_id` )
    REFERENCES `tag` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tag_uses_game_submissions1`
    FOREIGN KEY (`game_submissions_id` )
    REFERENCES `game_submission` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_tag_uses_images1` ON `tag_use` (`image_id` ASC) ;

CREATE INDEX `fk_tag_uses_tags1` ON `tag_use` (`tag_id` ASC) ;

CREATE INDEX `fk_tag_uses_game_submissions1` ON `tag_use` (`game_submissions_id` ASC) ;


-- -----------------------------------------------------
-- Table `message`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `message` ;

CREATE  TABLE IF NOT EXISTS `message` (
  `session_id` INT(11) NOT NULL ,
  `message` VARCHAR(1000) NULL ,
  PRIMARY KEY (`session_id`) ,
  CONSTRAINT `fk_messages_sessions1`
    FOREIGN KEY (`session_id` )
    REFERENCES `session` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_messages_sessions1` ON `message` (`session_id` ASC) ;


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
-- Table `game_to_image_set`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `game_to_image_set` ;

CREATE  TABLE IF NOT EXISTS `game_to_image_set` (
  `game_id` INT(11) NOT NULL ,
  `image_set_id` INT(11) NOT NULL ,
  PRIMARY KEY (`game_id`, `image_set_id`) ,
  CONSTRAINT `fk_games_has_image_sets_games1`
    FOREIGN KEY (`game_id` )
    REFERENCES `game` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_games_has_image_sets_image_sets1`
    FOREIGN KEY (`image_set_id` )
    REFERENCES `image_set` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_games_has_image_sets_image_sets1` ON `game_to_image_set` (`image_set_id` ASC) ;

CREATE INDEX `fk_games_has_image_sets_games1` ON `game_to_image_set` (`game_id` ASC) ;


-- -----------------------------------------------------
-- Table `tag_original_version`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tag_original_version` ;

CREATE  TABLE IF NOT EXISTS `tag_original_version` (
  `id` INT NOT NULL ,
  `original_tag` VARCHAR(64) NULL ,
  `tag_uses_id` INT(11) NOT NULL ,
  `by_editor` INT(11) NULL ,
  `comments` TEXT NULL ,
  PRIMARY KEY (`id`, `tag_uses_id`) ,
  CONSTRAINT `fk_tag_original_version_tag_uses1`
    FOREIGN KEY (`tag_uses_id` )
    REFERENCES `tag_use` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_tag_original_version_tag_uses1` ON `tag_original_version` (`tag_uses_id` ASC) ;


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
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8;

CREATE INDEX `varname` ON `profile_field` (`varname` ASC, `widget` ASC, `visible` ASC) ;


-- -----------------------------------------------------
-- Table `profile`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `profile` ;

CREATE  TABLE IF NOT EXISTS `profile` (
  `user_id` INT(11) NOT NULL ,
  PRIMARY KEY (`user_id`) ,
  CONSTRAINT `fk_profile_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `user` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_profile_users1` ON `profile` (`user_id` ASC) ;


-- -----------------------------------------------------
-- Table `game_partner`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `game_partner` ;

CREATE  TABLE IF NOT EXISTS `game_partner` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `session_id` INT(11) NOT NULL ,
  `game_id` INT(11) NOT NULL ,
  `created` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_game_partner_session1`
    FOREIGN KEY (`session_id` )
    REFERENCES `session` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_game_partner_game1`
    FOREIGN KEY (`game_id` )
    REFERENCES `game` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_game_partner_session1` ON `game_partner` (`session_id` ASC) ;

CREATE INDEX `fk_game_partner_game1` ON `game_partner` (`game_id` ASC) ;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `licence`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `licence` (`id`, `name`, `description`, `created`, `modified`) VALUES (1, 'Default Licence', 'This is the default licence. The images used are not licenced', '2011-01-01 12:00', '2011-01-01 12:00');

COMMIT;

-- -----------------------------------------------------
-- Data for table `image_set`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `image_set` (`id`, `name`, `locked`, `more_information`, `licence_id`, `created`, `modified`) VALUES (1, 'All', 1, 'This is the default image set. All images will be automatically assigned to it. It cannot be deleted.', 1, '2011-01-01 12:00', '2011-01-01 12:00');

COMMIT;

-- -----------------------------------------------------
-- Data for table `subject_matter`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `subject_matter` (`id`, `name`, `locked`, `created`, `modified`) VALUES (1, 'All', 1, '2011-01-01 12:00', '2011-01-01 12:00');

COMMIT;

-- -----------------------------------------------------
-- Data for table `user`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `user` (`id`, `username`, `password`, `email`, `activkey`, `lastvisit`, `role`, `status`, `edited_count`, `created`, `modified`) VALUES (1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin@admin.com', '9a24eff8c15a6a141ece27eb6947da0f', NULL, 'admin', 1, 0, '2011-01-01 12:00', '2011-01-01 12:00');
INSERT INTO `user` (`id`, `username`, `password`, `email`, `activkey`, `lastvisit`, `role`, `status`, `edited_count`, `created`, `modified`) VALUES (2, 'dbmanager', 'a945e003507368542f40f13b3076ca6f', 'dbmanager@dbmanager.com', 'b58e465dcb47f8459439cf54535bc8ae', NULL, 'dbmanager', 1, 0, '2011-01-01 12:00', '2011-01-01 12:00');
INSERT INTO `user` (`id`, `username`, `password`, `email`, `activkey`, `lastvisit`, `role`, `status`, `edited_count`, `created`, `modified`) VALUES (3, 'editor', '5aee9dbd2a188839105073571bee1b1f', 'editor@editor.com', '52c200c7ca710d13f2ba1bea3788723c', NULL, 'editor', 1, 0, '2011-01-01 12:00', '2011-01-01 12:00');
INSERT INTO `user` (`id`, `username`, `password`, `email`, `activkey`, `lastvisit`, `role`, `status`, `edited_count`, `created`, `modified`) VALUES (4, 'player', '912af0dff974604f1321254ca8ff38b6', 'player@player.com', 'd3f33d7d0e80bbe3004adc82fc9e779c', NULL, 'player', 1, 0, '2011-01-01 12:00', '2011-01-01 12:00');

COMMIT;

-- -----------------------------------------------------
-- Data for table `profile`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `profile` (`user_id`) VALUES (1);
INSERT INTO `profile` (`user_id`) VALUES (2);
INSERT INTO `profile` (`user_id`) VALUES (3);
INSERT INTO `profile` (`user_id`) VALUES (4);

COMMIT;
