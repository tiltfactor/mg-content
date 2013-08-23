<?php

/**
 * InstallConfigurationForm class.
 * Used by the installer. Collects the needed data to install MG
 */
class InstallConfigurationForm_ extends User
{
  public $app_name = "Metadata Games";  
  public $email = "";
  public $verifyPassword;
  
  public function rules() {
    return array(
        array('app_name, email, username, password, verifyPassword', 'required'),
        array('username', 'length', 'max'=>20, 'min' => 3,'message' => UserModule::t("Incorrect username (length between 3 and 20 characters).")),
        array('password', 'length', 'max'=>128, 'min' => 4,'message' => UserModule::t("Incorrect password (minimal length 4 symbols).")),
        array('email', 'email'),
        array('username', 'unique', 'message' => UserModule::t("This user's name already exists.")),
        array('email', 'unique', 'message' => UserModule::t("This user's email address already exists.")),
        array('verifyPassword', 'compare', 'compareAttribute'=>'password', 'message' => UserModule::t("Retype Password is incorrect.")),
        array('username', 'match', 'pattern' => '/^[A-Za-z0-9_]+$/u','message' => UserModule::t("Incorrect symbols (A-z0-9).")),
    );
  }
  
  public function attributeLabels() {
    return array(
      'app_name' => Yii::t('app', 'Application Name'), 
      'id' => Yii::t('app', 'ID'),
      'username' => Yii::t('app', 'Administator/Player Name'),
      'password' => Yii::t('app', 'Password'),
      'verifyPassword' => Yii::t('app', 'Verify Password'),
      'email' => Yii::t('app', 'Email'),
      'activekey' => Yii::t('app', 'Activation Key'),
      'lastvisit' => Yii::t('app', 'Lastvisit'),
      'role' => Yii::t('app', 'Role'),
      'status' => Yii::t('app', 'Status'),
      'edited_count' => Yii::t('app', 'Banned Tags'),
      'created' => Yii::t('app', 'Created'),
      'modified' => Yii::t('app', 'Modified'),
      'logs' => null,
      'profile' => null,
      'sessions' => null,
      'games' => null,
      'subjectMatters' => null,
    );
  }
  
  public function fbvSave() {
    Yii::app()->fbvStorage->set("installed", true);
    Yii::app()->fbvStorage->set("settings.app_name", $this->app_name);
    Yii::app()->fbvStorage->set("settings.app_email", $this->email);
  }
}
