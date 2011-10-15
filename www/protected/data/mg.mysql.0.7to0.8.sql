ALTER TABLE `stop_word` 
  DROP COLUMN `active`, 
  DROP COLUMN `counter`,
  ADD COLUMN `source` VARCHAR(12) NOT NULL DEFAULT 'import' AFTER `word` ;
