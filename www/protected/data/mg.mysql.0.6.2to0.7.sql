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
