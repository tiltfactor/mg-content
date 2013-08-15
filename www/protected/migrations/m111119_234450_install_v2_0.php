<?php
/**
 * This is the initial migration executed by the installer via the Yii Migration functionality.
 * It loads the mg.mysql.1.0.sql in the data folder and parses it for SQL statements. Which will
 * be executed against the database. For further MG version create new migration files and add
 * them to this folder.
 *
 * @author Vincent Van Uffelen <novazembla@gmail.com>
 * @link http://www.metadatagames.com/
 * @copyright Copyright &copy; 2008-2012 Tiltfactor
 * @license http://www.metadatagames.com/license/
 * @package MG
 */
class m111119_234450_install_v2_0 extends CDbMigration
{
    public function up()
    {
        $script = "
        RENAME TABLE `image_set` TO `collection` ;
        RENAME TABLE `image` TO `media` ;
        RENAME TABLE `image_set_to_image` TO `collection_to_media` ;
        RENAME TABLE `image_set_to_subject_matter` TO `collection_to_subject_matter` ;
        RENAME TABLE `game_to_image_set` TO `game_to_collection` ;

        ALTER TABLE `game_to_collection` DROP INDEX `fk_games_has_image_sets_games1` ,
        ADD INDEX `fk_games_has_collections_games1` ( `game_id` );
        ALTER TABLE game_to_collection DROP FOREIGN KEY fk_games_has_image_sets_image_sets1;
        ALTER TABLE `game_to_collection` CHANGE `image_set_id` `collection_id` INT( 11 ) NOT NULL;
        ALTER TABLE `game_to_collection` DROP INDEX `fk_games_has_image_sets_image_sets1` ,
            ADD INDEX `fk_games_has_collections_collections1` ( `collection_id` );

        ALTER TABLE `game_to_collection` ADD FOREIGN KEY ( `collection_id` ) REFERENCES `collection` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION ;

        ALTER TABLE  collection_to_subject_matter DROP FOREIGN KEY fk_image_sets_has_subject_matters_image_sets1;
        ALTER TABLE `collection_to_subject_matter` CHANGE `image_set_id` `collection_id` INT( 11 ) NOT NULL ;

        ALTER TABLE `collection_to_subject_matter` DROP INDEX `fk_image_sets_has_subject_matters_image_sets1` ,
            ADD INDEX `fk_collections_has_subject_matters_collection1` ( `collection_id` ) ;
        ALTER TABLE `collection_to_subject_matter` DROP INDEX `fk_image_sets_has_subject_matters_subject_matters1` ,
            ADD INDEX `fk_collections_has_subject_matters_subject_matters1` ( `subject_matter_id` ) ;
        ALTER TABLE `collection_to_subject_matter` ADD FOREIGN KEY ( `collection_id` ) REFERENCES `collection` (
            `id` ) ON DELETE CASCADE ON UPDATE NO ACTION ;

        ALTER TABLE `collection_to_media` DROP INDEX `fk_image_sets_has_images_images1` ,
        ADD INDEX `fk_collections_has_medias_medias1` ( `image_id` ) ;
        ALTER TABLE `collection_to_media` DROP INDEX `fk_image_sets_has_images_image_sets1` ,
        ADD INDEX `fk_collections_has_medias_collections1` ( `image_set_id` ) ;
        ALTER TABLE `collection_to_media` DROP FOREIGN KEY `fk_image_sets_has_images_image_sets1` ;

        ALTER TABLE `collection_to_media` DROP FOREIGN KEY `fk_image_sets_has_images_images1` ;
        ALTER TABLE `collection_to_media` CHANGE `image_set_id` `collection_id` INT( 11 ) NOT NULL ;
        ALTER TABLE `collection_to_media` CHANGE `image_id` `media_id` INT( 11 ) NOT NULL ;
        ALTER TABLE `collection_to_media` ADD FOREIGN KEY ( `collection_id` ) REFERENCES `collection` (
        `id`
        ) ON DELETE CASCADE ON UPDATE NO ACTION ;

        ALTER TABLE `collection_to_media` ADD FOREIGN KEY ( `media_id` ) REFERENCES `media` (
        `id`
        ) ON DELETE CASCADE ON UPDATE NO ACTION ;

        ALTER TABLE `tag_use` DROP INDEX `fk_tag_uses_images1` ,
        ADD INDEX `fk_tag_uses_medias1` ( `image_id` ) ;
        ALTER TABLE `tag_use` DROP FOREIGN KEY `fk_tag_uses_images1` ;
        ALTER TABLE `tag_use` CHANGE `image_id` `media_id` INT( 11 ) NOT NULL ;
        ALTER TABLE `tag_use` ADD FOREIGN KEY ( `media_id` ) REFERENCES `media` (
        `id`
        ) ON DELETE CASCADE ON UPDATE NO ACTION ;

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

        UPDATE `plugin` SET `unique_id` = 'import-CollectionAtImportPlugin' WHERE `plugin`.`unique_id` ='import-ImageSetAtImportPlugin';
        UPDATE `plugin` SET `unique_id` = 'export-MediasExportPlugin' WHERE `plugin`.`unique_id` ='export-ImagesExportPlugin';
	  ";

        if (trim($script) != "") {
            $statements = explode(";\n", $script);

            if (count($statements) > 0) {
                foreach ($statements as $statement) {
                    if (trim($statement) != "")
                        $this->execute($statement);
                }
            }
        }

        Yii::app()->fbvStorage->set("installed", true);
    }

    public function down()
    {
        $this->truncateTable('cron_jobs');
        $this->dropTable('cron_jobs');

    }

    /*
     // Use safeUp/safeDown to do migration with transaction
     public function safeUp()
     {
     }

     public function safeDown()
     {
     }
     */
}