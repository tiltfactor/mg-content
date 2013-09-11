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
class m111119_234451_install_v2_0 extends CDbMigration
{
    public function up()
    {
	  $script = "
	  CREATE TABLE IF NOT EXISTS `server_profile` (
	    `id` int(11) NOT NULL AUTO_INCREMENT,
	    `name` varchar(128) NOT NULL,
	    `logo_url` varchar(128) NOT NULL,
	    `description` text,
	    PRIMARY KEY (`id`)
	  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
    }

    public function down()
    {
        $script = "
     	  DROP TABLE IF EXISTS server_profile;
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