<?php

class m111119_234449_install_v1_0 extends CDbMigration
{
	public function up() {
	  $script = file_get_contents(Yii::getPathOfAlias('application.data') . DIRECTORY_SEPARATOR . 'mg.mysql.1.0.sql');
    
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
		return false;
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