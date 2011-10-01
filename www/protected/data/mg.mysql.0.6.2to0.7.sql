ALTER TABLE `game_partner` CHANGE COLUMN `session_id` `session_id_1` INTEGER NOT NULL,
 ADD COLUMN `session_id_2` INTEGER AFTER `session_id_1`,
 ADD COLUMN `played_game_id` INTEGER AFTER `game_id`,
DROP FOREIGN KEY `fk_game_partner_session1`,
 ADD CONSTRAINT `fk_game_partner_session_id_1` FOREIGN KEY `fk_game_partner_session_id_1` (`session_id_1`)
    REFERENCES `session` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
 ADD CONSTRAINT `fk_game_partner_session_id_2` FOREIGN KEY `fk_game_partner_session_id_2` (`session_id_2`)
    REFERENCES `session` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION;
 ADD CONSTRAINT `fk_game_partner_played_game_id` FOREIGN KEY `fk_game_partner_played_game_id` (`played_game_id`)
    REFERENCES `played_game` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT;

ALTER TABLE `message`
 ADD COLUMN `played_game_id` INTEGER AFTER `session_id`,
 ADD CONSTRAINT `fk_message_played_game1` FOREIGN KEY `played_game_id` (`plaayed_game_id`)
    REFERENCES `played_game` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION;
    
ALTER TABLE `mg`.`message` DROP FOREIGN KEY `fk_message_played_game1` ;
ALTER TABLE `mg`.`message` CHANGE COLUMN `played_game_id` `played_game_id` INT(11) NOT NULL  , 
  ADD CONSTRAINT `fk_message_played_game1`
  FOREIGN KEY (`played_game_id` )
  REFERENCES `mg`.`played_game` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, DROP PRIMARY KEY ;

ALTER TABLE `mg`.`game_submission` ADD COLUMN `turn` INT(11) NOT NULL DEFAULT 1  AFTER `submission`;

CREATE  TABLE IF NOT EXISTS `game_to_plugin` (
  `game_id` INT(11) NOT NULL ,
  `plugin_id` INT(11) NOT NULL ,
  PRIMARY KEY (`game_id`, `plugin_id`) ,
  INDEX `fk_game_to_plugin_plugin1` (`plugin_id` ASC) ,
  INDEX `fk_game_to_plugin_game1` (`game_id` ASC) ,
  CONSTRAINT `fk_game_to_plugin_game1`
    FOREIGN KEY (`game_id` )
    REFERENCES `mg`.`game` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_game_to_plugin_plugin1`
    FOREIGN KEY (`plugin_id` )
    REFERENCES `mg`.`plugin` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE  TABLE IF NOT EXISTS `played_game_turn_info` (
  `played_game_id` INT(11) NOT NULL ,
  `turn` INT(11) NOT NULL ,
  `data` TEXT NOT NULL ,
  `created_by_session_id` INT(11) NOT NULL ,
  PRIMARY KEY (`played_game_id`, `turn`) ,
  INDEX `fk_played_game_turn_info_session1` (`created_by_session_id` ASC) ,
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
ENGINE = InnoDB;