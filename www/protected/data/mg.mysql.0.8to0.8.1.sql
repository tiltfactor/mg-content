ALTER TABLE `mg`.`tag_original_version` CHANGE COLUMN `id` `id` INT(11) NOT NULL AUTO_INCREMENT  ;
ALTER TABLE `tag_original_version` DROP FOREIGN KEY `fk_tag_original_version_tag_uses1` ;
ALTER TABLE `tag_original_version` CHANGE COLUMN `tag_uses_id` `tag_use_id` INT(11) NOT NULL  , 
  ADD CONSTRAINT `fk_tag_original_version_tag_uses1`
  FOREIGN KEY (`tag_use_id` )
  REFERENCES `tag_use` (`id` )
  ON DELETE CASCADE
  ON UPDATE NO ACTION
, DROP PRIMARY KEY 
, ADD PRIMARY KEY (`id`, `tag_use_id`) 
, DROP INDEX `fk_tag_original_version_tag_uses1` 
, ADD INDEX `fk_tag_original_version_tag_uses1` (`tag_use_id` ASC) ;
ALTER TABLE `mg`.`tag_original_version` ADD COLUMN `created` DATETIME NULL  AFTER `user_id` ;
