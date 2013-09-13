<?php

/**
 * InstallConfigurationForm class.
 * Used by the installer. Collects the needed data to install MG
 */
class InstallConfigurationForm extends User
{
  public $app_name = "";
  public $email = "";
  public $verifyPassword;
  public $url;
  public $description;
  public $logo;

  public function rules() {
    return array(
        array('app_name, email, username, password, verifyPassword,url,logo', 'required'),
        array('username', 'length', 'max'=>20, 'min' => 3,'message' => UserModule::t("Incorrect username (length between 3 and 20 characters).")),
        array('password', 'length', 'max'=>128, 'min' => 4,'message' => UserModule::t("Incorrect password (minimal length 4 symbols).")),
        array('email', 'email'),
        //array('url', 'url'),
        //array('url', 'match', 'pattern' => '/^(http(?:s)?\:\/\/([a-zA-Z0-9\-]+(?:\.[a-zA-Z0-9\-]+)*\.[a-zA-Z]{2,6}|localhost)(?:\/?|(?:\/[\w\-]+)*)(?:\/?|\/\w+\.[a-zA-Z]{2,4}(?:\?[\w]+\=[\w\-]+)?)?(?:\&[\w]+\=[\w\-]+)*)$/'),
        array('username', 'unique', 'message' => UserModule::t("This user's name already exists.")),
        array('email', 'unique', 'message' => UserModule::t("This user's email address already exists.")),
        array('verifyPassword', 'compare', 'compareAttribute'=>'password', 'message' => UserModule::t("Retype Password is incorrect.")),
        array('username', 'match', 'pattern' => '/^[A-Za-z0-9_]+$/u','message' => UserModule::t("Incorrect symbols (A...z,0...9,_).")),
    );
  }
  
  public function attributeLabels() {
    return array(
      'app_name' => Yii::t('app', 'Application Name'),
      'url' => Yii::t('app', 'Application URL'),
      'description' => Yii::t('app', 'Application URL'),
      'logo' => Yii::t('app', 'Application URL'),
      'id' => Yii::t('app', 'ID'),
      'username' => Yii::t('app', 'Administator Username'),
      'password' => Yii::t('app', 'Password'),
      'verifyPassword' => Yii::t('app', 'Verify Password'),
      'email' => Yii::t('app', 'Email'),
      'activekey' => Yii::t('app', 'Activation Key'),
      'lastvisit' => Yii::t('app', 'Lastvisit'),
      'role' => Yii::t('app', 'Role'),
      'status' => Yii::t('app', 'Status'),
      'created' => Yii::t('app', 'Created'),
      'modified' => Yii::t('app', 'Modified'),
      'logs' => null,
      'sessions' => null,
        'description'=>Yii::t('app', 'Description'),
        'logo'=>Yii::t('app', 'Logo'),
    );
  }
  
  public function fbvSave() {
    Yii::app()->fbvStorage->set("installed", true);
    Yii::app()->fbvStorage->set("settings.app_name", $this->app_name);
    Yii::app()->fbvStorage->set("settings.app_email", $this->email);
  }
}
