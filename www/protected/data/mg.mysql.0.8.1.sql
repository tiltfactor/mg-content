SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `mg` DEFAULT CHARACTER SET utf8 ;
USE `mg` ;

-- -----------------------------------------------------
-- Table `mg`.`stop_word`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mg`.`stop_word` ;

CREATE  TABLE IF NOT EXISTS `mg`.`stop_word` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `word` VARCHAR(64) NOT NULL ,
  `source` VARCHAR(12) NOT NULL DEFAULT 'import' ,
  `counter` INT UNSIGNED NOT NULL DEFAULT 0 ,
  `modified` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mg`.`licence`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mg`.`licence` ;

CREATE  TABLE IF NOT EXISTS `mg`.`licence` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(64) NOT NULL ,
  `description` TEXT NULL ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mg`.`image_set`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mg`.`image_set` ;

CREATE  TABLE IF NOT EXISTS `mg`.`image_set` (
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
    REFERENCES `mg`.`licence` (`id` )
    ON DELETE SET NULL
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_image_sets_licences1` ON `mg`.`image_set` (`licence_id` ASC) ;


-- -----------------------------------------------------
-- Table `mg`.`subject_matter`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mg`.`subject_matter` ;

CREATE  TABLE IF NOT EXISTS `mg`.`subject_matter` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(64) NOT NULL ,
  `locked` INT(1) NOT NULL DEFAULT 0 ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mg`.`user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mg`.`user` ;

CREATE  TABLE IF NOT EXISTS `mg`.`user` (
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
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;

CREATE UNIQUE INDEX `username` ON `mg`.`user` (`username` ASC) ;

CREATE UNIQUE INDEX `email` ON `mg`.`user` (`email` ASC) ;

CREATE INDEX `status` ON `mg`.`user` (`status` ASC) ;


-- -----------------------------------------------------
-- Table `mg`.`user_to_subject_matter`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mg`.`user_to_subject_matter` ;

CREATE  TABLE IF NOT EXISTS `mg`.`user_to_subject_matter` (
  `user_id` INT(11) NOT NULL ,
  `subject_matter_id` INT(11) NOT NULL ,
  `trust` INT(11) NOT NULL DEFAULT 0 ,
  `expertise` INT(11) NOT NULL DEFAULT 0 ,
  `interest` INT(11) NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`user_id`, `subject_matter_id`) ,
  CONSTRAINT `fk_users2subject_matters_subject_matters1`
    FOREIGN KEY (`subject_matter_id` )
    REFERENCES `mg`.`subject_matter` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_users_has_subject_matters_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `mg`.`user` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_users2subject_matters_subject_matters1` ON `mg`.`user_to_subject_matter` (`subject_matter_id` ASC) ;

CREATE INDEX `fk_users_has_subject_matters_users1` ON `mg`.`user_to_subject_matter` (`user_id` ASC) ;


-- -----------------------------------------------------
-- Table `mg`.`game`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mg`.`game` ;

CREATE  TABLE IF NOT EXISTS `mg`.`game` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `active` INT(1) NOT NULL DEFAULT 0 ,
  `number_played` INT(11) NOT NULL DEFAULT 0 ,
  `unique_id` VARCHAR(45) NOT NULL ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;

CREATE UNIQUE INDEX `unique_id_UNIQUE` ON `mg`.`game` (`unique_id` ASC) ;


-- -----------------------------------------------------
-- Table `mg`.`user_to_game`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mg`.`user_to_game` ;

CREATE  TABLE IF NOT EXISTS `mg`.`user_to_game` (
  `user_id` INT(11) NOT NULL ,
  `game_id` INT NOT NULL ,
  `score` INT(11) NOT NULL DEFAULT 0 ,
  `number_played` INT(11) NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`user_id`, `game_id`) ,
  CONSTRAINT `fk_users2games_games1`
    FOREIGN KEY (`game_id` )
    REFERENCES `mg`.`game` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_has_games_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `mg`.`user` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_users2games_games1` ON `mg`.`user_to_game` (`game_id` ASC) ;

CREATE INDEX `fk_user_has_games_users1` ON `mg`.`user_to_game` (`user_id` ASC) ;


-- -----------------------------------------------------
-- Table `mg`.`badge`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mg`.`badge` ;

CREATE  TABLE IF NOT EXISTS `mg`.`badge` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `title` VARCHAR(45) NOT NULL ,
  `points` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mg`.`image`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mg`.`image` ;

CREATE  TABLE IF NOT EXISTS `mg`.`image` (
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
-- Table `mg`.`log`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mg`.`log` ;

CREATE  TABLE IF NOT EXISTS `mg`.`log` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `category` VARCHAR(128) NOT NULL ,
  `message` TEXT NOT NULL ,
  `user_id` INT(11) NULL ,
  `created` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_log_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `mg`.`user` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_log_users1` ON `mg`.`log` (`user_id` ASC) ;


-- -----------------------------------------------------
-- Table `mg`.`session`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mg`.`session` ;

CREATE  TABLE IF NOT EXISTS `mg`.`session` (
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
    REFERENCES `mg`.`user` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_sessions_users1` ON `mg`.`session` (`user_id` ASC) ;


-- -----------------------------------------------------
-- Table `mg`.`image_set_to_image`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mg`.`image_set_to_image` ;

CREATE  TABLE IF NOT EXISTS `mg`.`image_set_to_image` (
  `image_set_id` INT NOT NULL ,
  `image_id` INT(11) NOT NULL ,
  PRIMARY KEY (`image_set_id`, `image_id`) ,
  CONSTRAINT `fk_image_sets_has_images_image_sets1`
    FOREIGN KEY (`image_set_id` )
    REFERENCES `mg`.`image_set` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_image_sets_has_images_images1`
    FOREIGN KEY (`image_id` )
    REFERENCES `mg`.`image` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_image_sets_has_images_images1` ON `mg`.`image_set_to_image` (`image_id` ASC) ;

CREATE INDEX `fk_image_sets_has_images_image_sets1` ON `mg`.`image_set_to_image` (`image_set_id` ASC) ;


-- -----------------------------------------------------
-- Table `mg`.`image_set_to_subject_matter`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mg`.`image_set_to_subject_matter` ;

CREATE  TABLE IF NOT EXISTS `mg`.`image_set_to_subject_matter` (
  `image_set_id` INT NOT NULL ,
  `subject_matter_id` INT(11) NOT NULL ,
  PRIMARY KEY (`image_set_id`, `subject_matter_id`) ,
  CONSTRAINT `fk_image_sets_has_subject_matters_image_sets1`
    FOREIGN KEY (`image_set_id` )
    REFERENCES `mg`.`image_set` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_image_sets_has_subject_matters_subject_matters1`
    FOREIGN KEY (`subject_matter_id` )
    REFERENCES `mg`.`subject_matter` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_image_sets_has_subject_matters_subject_matters1` ON `mg`.`image_set_to_subject_matter` (`subject_matter_id` ASC) ;

CREATE INDEX `fk_image_sets_has_subject_matters_image_sets1` ON `mg`.`image_set_to_subject_matter` (`image_set_id` ASC) ;


-- -----------------------------------------------------
-- Table `mg`.`plugin`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mg`.`plugin` ;

CREATE  TABLE IF NOT EXISTS `mg`.`plugin` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `type` VARCHAR(20) NOT NULL ,
  `active` INT(1) NOT NULL ,
  `unique_id` VARCHAR(254) NOT NULL ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;

CREATE UNIQUE INDEX `unique_id_UNIQUE` ON `mg`.`plugin` (`unique_id` ASC) ;


-- -----------------------------------------------------
-- Table `mg`.`menu`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mg`.`menu` ;

CREATE  TABLE IF NOT EXISTS `mg`.`menu` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mg`.`page`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mg`.`page` ;

CREATE  TABLE IF NOT EXISTS `mg`.`page` (
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
-- Table `mg`.`menu_item`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mg`.`menu_item` ;

CREATE  TABLE IF NOT EXISTS `mg`.`menu_item` (
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
    REFERENCES `mg`.`menu` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_menu_items_pages1`
    FOREIGN KEY (`pages_id` )
    REFERENCES `mg`.`page` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_menu_item_menu1` ON `mg`.`menu_item` (`menu_id` ASC) ;

CREATE INDEX `fk_menu_items_pages1` ON `mg`.`menu_item` (`pages_id` ASC) ;


-- -----------------------------------------------------
-- Table `mg`.`tag`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mg`.`tag` ;

CREATE  TABLE IF NOT EXISTS `mg`.`tag` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `tag` VARCHAR(64) NOT NULL ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;

CREATE UNIQUE INDEX `tag_UNIQUE` ON `mg`.`tag` (`tag` ASC) ;


-- -----------------------------------------------------
-- Table `mg`.`played_game`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mg`.`played_game` ;

CREATE  TABLE IF NOT EXISTS `mg`.`played_game` (
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
    REFERENCES `mg`.`session` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_played_games_games1`
    FOREIGN KEY (`game_id` )
    REFERENCES `mg`.`game` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_played_game_session1`
    FOREIGN KEY (`session_id_2` )
    REFERENCES `mg`.`session` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_played_games_sessions1` ON `mg`.`played_game` (`session_id_1` ASC) ;

CREATE INDEX `fk_played_games_games1` ON `mg`.`played_game` (`game_id` ASC) ;

CREATE INDEX `fk_played_game_session1` ON `mg`.`played_game` (`session_id_2` ASC) ;


-- -----------------------------------------------------
-- Table `mg`.`game_submission`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mg`.`game_submission` ;

CREATE  TABLE IF NOT EXISTS `mg`.`game_submission` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `submission` TEXT NOT NULL ,
  `turn` INT(11) NOT NULL DEFAULT 0 ,
  `session_id` INT(11) NOT NULL ,
  `played_game_id` INT(11) NOT NULL ,
  `turn` DATETIME NULL ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_game_submissions_sessions1`
    FOREIGN KEY (`session_id` )
    REFERENCES `mg`.`session` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_game_submissions_played_games1`
    FOREIGN KEY (`played_game_id` )
    REFERENCES `mg`.`played_game` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_game_submissions_sessions1` ON `mg`.`game_submission` (`session_id` ASC) ;

CREATE INDEX `fk_game_submissions_played_games1` ON `mg`.`game_submission` (`played_game_id` ASC) ;


-- -----------------------------------------------------
-- Table `mg`.`tag_use`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mg`.`tag_use` ;

CREATE  TABLE IF NOT EXISTS `mg`.`tag_use` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `image_id` INT(11) NOT NULL ,
  `tag_id` INT(11) NOT NULL ,
  `weight` INT(3) NOT NULL DEFAULT 0 ,
  `type` TEXT NOT NULL DEFAULT '' ,
  `game_submission_id` INT UNSIGNED NOT NULL ,
  `created` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_tag_uses_images1`
    FOREIGN KEY (`image_id` )
    REFERENCES `mg`.`image` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tag_uses_tags1`
    FOREIGN KEY (`tag_id` )
    REFERENCES `mg`.`tag` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tag_uses_game_submissions1`
    FOREIGN KEY (`game_submission_id` )
    REFERENCES `mg`.`game_submission` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_tag_uses_images1` ON `mg`.`tag_use` (`image_id` ASC) ;

CREATE INDEX `fk_tag_uses_tags1` ON `mg`.`tag_use` (`tag_id` ASC) ;

CREATE INDEX `fk_tag_uses_game_submissions1` ON `mg`.`tag_use` (`game_submission_id` ASC) ;


-- -----------------------------------------------------
-- Table `mg`.`message`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mg`.`message` ;

CREATE  TABLE IF NOT EXISTS `mg`.`message` (
  `session_id` INT(11) NOT NULL ,
  `played_game_id` INT(11) NOT NULL ,
  `message` VARCHAR(1000) NULL ,
  CONSTRAINT `fk_messages_sessions1`
    FOREIGN KEY (`session_id` )
    REFERENCES `mg`.`session` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_message_played_game1`
    FOREIGN KEY (`played_game_id` )
    REFERENCES `mg`.`played_game` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_messages_sessions1` ON `mg`.`message` (`session_id` ASC) ;

CREATE INDEX `fk_message_played_game1` ON `mg`.`message` (`played_game_id` ASC) ;


-- -----------------------------------------------------
-- Table `mg`.`blocked_ip`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mg`.`blocked_ip` ;

CREATE  TABLE IF NOT EXISTS `mg`.`blocked_ip` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `ip` VARCHAR(45) NOT NULL ,
  `type` ENUM('deny', 'allow') NOT NULL DEFAULT 'deny' ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mg`.`game_to_image_set`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mg`.`game_to_image_set` ;

CREATE  TABLE IF NOT EXISTS `mg`.`game_to_image_set` (
  `game_id` INT(11) NOT NULL ,
  `image_set_id` INT(11) NOT NULL ,
  PRIMARY KEY (`game_id`, `image_set_id`) ,
  CONSTRAINT `fk_games_has_image_sets_games1`
    FOREIGN KEY (`game_id` )
    REFERENCES `mg`.`game` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_games_has_image_sets_image_sets1`
    FOREIGN KEY (`image_set_id` )
    REFERENCES `mg`.`image_set` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_games_has_image_sets_image_sets1` ON `mg`.`game_to_image_set` (`image_set_id` ASC) ;

CREATE INDEX `fk_games_has_image_sets_games1` ON `mg`.`game_to_image_set` (`game_id` ASC) ;


-- -----------------------------------------------------
-- Table `mg`.`tag_original_version`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mg`.`tag_original_version` ;

CREATE  TABLE IF NOT EXISTS `mg`.`tag_original_version` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `original_tag` VARCHAR(64) NULL ,
  `tag_use_id` INT(11) NOT NULL ,
  `comments` TEXT NULL ,
  `user_id` INT(11) NULL ,
  `created` DATETIME NULL ,
  PRIMARY KEY (`id`, `tag_use_id`) ,
  CONSTRAINT `fk_tag_original_version_tag_uses1`
    FOREIGN KEY (`tag_use_id` )
    REFERENCES `mg`.`tag_use` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tag_original_version_user1`
    FOREIGN KEY (`user_id` )
    REFERENCES `mg`.`user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_tag_original_version_tag_uses1` ON `mg`.`tag_original_version` (`tag_use_id` ASC) ;

CREATE INDEX `fk_tag_original_version_user1` ON `mg`.`tag_original_version` (`user_id` ASC) ;


-- -----------------------------------------------------
-- Table `mg`.`profile_field`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mg`.`profile_field` ;

CREATE  TABLE IF NOT EXISTS `mg`.`profile_field` (
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

CREATE INDEX `varname` ON `mg`.`profile_field` (`varname` ASC, `widget` ASC, `visible` ASC) ;


-- -----------------------------------------------------
-- Table `mg`.`profile`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mg`.`profile` ;

CREATE  TABLE IF NOT EXISTS `mg`.`profile` (
  `user_id` INT(11) NOT NULL ,
  PRIMARY KEY (`user_id`) ,
  CONSTRAINT `fk_profile_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `mg`.`user` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_profile_users1` ON `mg`.`profile` (`user_id` ASC) ;


-- -----------------------------------------------------
-- Table `mg`.`game_partner`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mg`.`game_partner` ;

CREATE  TABLE IF NOT EXISTS `mg`.`game_partner` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `session_id_1` INT(11) NOT NULL ,
  `session_id_2` INT(11) NOT NULL ,
  `game_id` INT(11) NOT NULL ,
  `played_game_id` INT(11) NOT NULL ,
  `created` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_game_partner_session1`
    FOREIGN KEY (`session_id_1` )
    REFERENCES `mg`.`session` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_game_partner_game1`
    FOREIGN KEY (`game_id` )
    REFERENCES `mg`.`game` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_game_partner_session2`
    FOREIGN KEY (`session_id_2` )
    REFERENCES `mg`.`session` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_game_partner_played_game1`
    FOREIGN KEY (`played_game_id` )
    REFERENCES `mg`.`played_game` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_game_partner_session1` ON `mg`.`game_partner` (`session_id_1` ASC) ;

CREATE INDEX `fk_game_partner_game1` ON `mg`.`game_partner` (`game_id` ASC) ;

CREATE INDEX `fk_game_partner_session2` ON `mg`.`game_partner` (`session_id_2` ASC) ;

CREATE INDEX `fk_game_partner_played_game1` ON `mg`.`game_partner` (`played_game_id` ASC) ;


-- -----------------------------------------------------
-- Table `mg`.`AuthItem`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mg`.`AuthItem` ;

CREATE  TABLE IF NOT EXISTS `mg`.`AuthItem` (
  `name` VARCHAR(64) NOT NULL ,
  `type` INT NOT NULL ,
  `description` TEXT NULL DEFAULT NULL ,
  `bizrule` TEXT NULL DEFAULT NULL ,
  `data` TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`name`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mg`.`AuthItemChild`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mg`.`AuthItemChild` ;

CREATE  TABLE IF NOT EXISTS `mg`.`AuthItemChild` (
  `parent` VARCHAR(64) NOT NULL ,
  `child` VARCHAR(64) NOT NULL ,
  PRIMARY KEY (`parent`, `child`) ,
  CONSTRAINT `fk_{D3C449C2-B9CC-46EA-80A5-1FBCB86CD3A2}`
    FOREIGN KEY (`parent` )
    REFERENCES `mg`.`AuthItem` (`name` )
    ON DELETE cascade
    ON UPDATE cascade,
  CONSTRAINT `fk_{818C790C-3BBE-4C8D-A383-37DED516A298}`
    FOREIGN KEY (`child` )
    REFERENCES `mg`.`AuthItem` (`name` )
    ON DELETE cascade
    ON UPDATE cascade)
ENGINE = InnoDB;

CREATE INDEX `fk_{818C790C-3BBE-4C8D-A383-37DED516A298}` ON `mg`.`AuthItemChild` (`child` ASC) ;


-- -----------------------------------------------------
-- Table `mg`.`AuthAssignment`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mg`.`AuthAssignment` ;

CREATE  TABLE IF NOT EXISTS `mg`.`AuthAssignment` (
  `itemname` VARCHAR(64) NOT NULL ,
  `userid` VARCHAR(64) NOT NULL ,
  `bizrule` TEXT NULL DEFAULT NULL ,
  `data` TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`itemname`, `userid`) ,
  CONSTRAINT `fk_{4D666442-7B7E-4C6F-84DC-566EB7E44203}`
    FOREIGN KEY (`itemname` )
    REFERENCES `mg`.`AuthItem` (`name` )
    ON DELETE cascade
    ON UPDATE cascade)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mg`.`game_to_plugin`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mg`.`game_to_plugin` ;

CREATE  TABLE IF NOT EXISTS `mg`.`game_to_plugin` (
  `game_id` INT(11) NOT NULL ,
  `plugin_id` INT(11) NOT NULL ,
  PRIMARY KEY (`game_id`, `plugin_id`) ,
  CONSTRAINT `fk_game_has_plugin_game1`
    FOREIGN KEY (`game_id` )
    REFERENCES `mg`.`game` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_game_has_plugin_plugin1`
    FOREIGN KEY (`plugin_id` )
    REFERENCES `mg`.`plugin` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_game_has_plugin_plugin1` ON `mg`.`game_to_plugin` (`plugin_id` ASC) ;

CREATE INDEX `fk_game_has_plugin_game1` ON `mg`.`game_to_plugin` (`game_id` ASC) ;


-- -----------------------------------------------------
-- Table `mg`.`played_game_turn_info`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mg`.`played_game_turn_info` ;

CREATE  TABLE IF NOT EXISTS `mg`.`played_game_turn_info` (
  `played_game_id` INT(11) NOT NULL ,
  `turn` INT(11) NOT NULL ,
  `data` TEXT NOT NULL ,
  `created_by_session_id` INT(11) NOT NULL ,
  PRIMARY KEY (`played_game_id`, `turn`) ,
  CONSTRAINT `fk_table1_played_game1`
    FOREIGN KEY (`played_game_id` )
    REFERENCES `mg`.`played_game` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_played_game_turn_info_session1`
    FOREIGN KEY (`created_by_session_id` )
    REFERENCES `mg`.`session` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'used in multiplayer games' ;

CREATE INDEX `fk_played_game_turn_info_session1` ON `mg`.`played_game_turn_info` (`created_by_session_id` ASC) ;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `mg`.`licence`
-- -----------------------------------------------------
START TRANSACTION;
USE `mg`;
INSERT INTO `mg`.`licence` (`id`, `name`, `description`, `created`, `modified`) VALUES (1, 'Default Licence', 'This is the default licence. The images used are not licenced', '2011-01-01 12:00', '2011-01-01 12:00');

COMMIT;

-- -----------------------------------------------------
-- Data for table `mg`.`image_set`
-- -----------------------------------------------------
START TRANSACTION;
USE `mg`;
INSERT INTO `mg`.`image_set` (`id`, `name`, `locked`, `more_information`, `licence_id`, `created`, `modified`) VALUES (1, 'All', 1, 'This is the default image set. All images will be automatically assigned to it. It cannot be deleted.', 1, '2011-01-01 12:00', '2011-01-01 12:00');

COMMIT;

-- -----------------------------------------------------
-- Data for table `mg`.`subject_matter`
-- -----------------------------------------------------
START TRANSACTION;
USE `mg`;
INSERT INTO `mg`.`subject_matter` (`id`, `name`, `locked`, `created`, `modified`) VALUES (1, 'All', 1, '2011-01-01 12:00', '2011-01-01 12:00');

COMMIT;

-- -----------------------------------------------------
-- Data for table `mg`.`user`
-- -----------------------------------------------------
START TRANSACTION;
USE `mg`;
INSERT INTO `mg`.`user` (`id`, `username`, `password`, `email`, `activekey`, `lastvisit`, `role`, `status`, `edited_count`, `created`, `modified`) VALUES (1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin@admin.com', '9a24eff8c15a6a141ece27eb6947da0f', NULL, 'admin', 1, 0, '2011-01-01 12:00', '2011-01-01 12:00');
INSERT INTO `mg`.`user` (`id`, `username`, `password`, `email`, `activekey`, `lastvisit`, `role`, `status`, `edited_count`, `created`, `modified`) VALUES (2, 'dbmanager', 'a945e003507368542f40f13b3076ca6f', 'dbmanager@dbmanager.com', 'b58e465dcb47f8459439cf54535bc8ae', NULL, 'dbmanager', 1, 0, '2011-01-01 12:00', '2011-01-01 12:00');
INSERT INTO `mg`.`user` (`id`, `username`, `password`, `email`, `activekey`, `lastvisit`, `role`, `status`, `edited_count`, `created`, `modified`) VALUES (3, 'editor', '5aee9dbd2a188839105073571bee1b1f', 'editor@editor.com', '52c200c7ca710d13f2ba1bea3788723c', NULL, 'editor', 1, 0, '2011-01-01 12:00', '2011-01-01 12:00');
INSERT INTO `mg`.`user` (`id`, `username`, `password`, `email`, `activekey`, `lastvisit`, `role`, `status`, `edited_count`, `created`, `modified`) VALUES (4, 'player', '912af0dff974604f1321254ca8ff38b6', 'player@player.com', 'd3f33d7d0e80bbe3004adc82fc9e779c', NULL, 'player', 1, 0, '2011-01-01 12:00', '2011-01-01 12:00');

COMMIT;

-- -----------------------------------------------------
-- Data for table `mg`.`badge`
-- -----------------------------------------------------
START TRANSACTION;
USE `mg`;
INSERT INTO `mg`.`badge` (`id`, `title`, `points`) VALUES (1, 'Alpha', 100);
INSERT INTO `mg`.`badge` (`id`, `title`, `points`) VALUES (2, 'Beta', 500);
INSERT INTO `mg`.`badge` (`id`, `title`, `points`) VALUES (3, 'Gamma', 1000);
INSERT INTO `mg`.`badge` (`id`, `title`, `points`) VALUES (4, 'Delta', 2000);

COMMIT;

-- -----------------------------------------------------
-- Data for table `mg`.`profile`
-- -----------------------------------------------------
START TRANSACTION;
USE `mg`;
INSERT INTO `mg`.`profile` (`user_id`) VALUES (1);
INSERT INTO `mg`.`profile` (`user_id`) VALUES (2);
INSERT INTO `mg`.`profile` (`user_id`) VALUES (3);
INSERT INTO `mg`.`profile` (`user_id`) VALUES (4);

COMMIT;

-- -----------------------------------------------------
-- Data for table `mg`.`AuthItem`
-- -----------------------------------------------------
START TRANSACTION;
USE `mg`;
INSERT INTO `mg`.`AuthItem` (`name`, `type`, `description`, `bizrule`, `data`) VALUES ('player', 2, 'A player can only record his or her games', NULL, NULL);
INSERT INTO `mg`.`AuthItem` (`name`, `type`, `description`, `bizrule`, `data`) VALUES ('editor', 2, 'An editor has access to several tools in the system', NULL, NULL);
INSERT INTO `mg`.`AuthItem` (`name`, `type`, `description`, `bizrule`, `data`) VALUES ('dbmanager', 2, 'A db manager has access to nearly all tools', NULL, NULL);
INSERT INTO `mg`.`AuthItem` (`name`, `type`, `description`, `bizrule`, `data`) VALUES ('admin', 2, 'The admin can access everything', NULL, NULL);

COMMIT;

-- -----------------------------------------------------
-- Data for table `mg`.`AuthItemChild`
-- -----------------------------------------------------
START TRANSACTION;
USE `mg`;
INSERT INTO `mg`.`AuthItemChild` (`parent`, `child`) VALUES ('editor', 'player');
INSERT INTO `mg`.`AuthItemChild` (`parent`, `child`) VALUES ('dbmanager', 'player');
INSERT INTO `mg`.`AuthItemChild` (`parent`, `child`) VALUES ('dbmanager', 'editor');
INSERT INTO `mg`.`AuthItemChild` (`parent`, `child`) VALUES ('admin', 'player');
INSERT INTO `mg`.`AuthItemChild` (`parent`, `child`) VALUES ('admin', 'editor');
INSERT INTO `mg`.`AuthItemChild` (`parent`, `child`) VALUES ('admin', 'dbmanager');

COMMIT;
