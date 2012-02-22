<?php

/**
 * DatabaseForm class.
 * DatabaseForm is the form model used by the installer to collect the needed
 * data to setup the database.
 */
class DatabaseForm extends CFormModel
{
  
	public $host = 'localhost';
	public $port;
	public $database;
	public $user;
	public $password;
  public $tablePrefix;
  
	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			// name, email, subject and body are required
			array('host, database, user', 'required'),
			// email has to be a valid email address
			array('port', 'numerical', 'integerOnly'=>true, 'min'=>0, 'max'=>100000),
      array('password', 'safe'),
		);
	}

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'tablePrefix'=> Yii::t('app', 'Database Table Prefix'),
			'database' => Yii::t('app', 'Database Name'),
			'user' => Yii::t('app', 'Database User Name'),
			'password' => Yii::t('app', 'Database User Password'),
			'host' => Yii::t('app', 'Database Host Name'),
			'port' => Yii::t('app', 'Database Host Port'),
		);
	}
}