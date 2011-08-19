<?php

Yii::import('application.models._base.BaseUser');

class User extends BaseUser
{
	const STATUS_NOACTIVE=0;
  const STATUS_ACTIVE=1;
  const STATUS_BANNED=-1;
    
  public static function model($className=__CLASS__) {
    return parent::model($className);
  }
  
  public function tableName() {
    return Yii::app()->getModule('user')->tableUsers;
  }
  
  public function rules() {
    return array(
      array('username, email', 'required'),
      array('status, edited_count', 'numerical', 'integerOnly'=>true),
      array('username', 'length', 'max'=>32),
      array('email', 'email'),
      array('password, email, activkey', 'length', 'max'=>128),
      array('role', 'length', 'max'=>45),
      array('role', 'checkRoleAccess'),
      array('activkey, lastvisit, role, status, edited_count, created, modified', 'default', 'setOnEmpty' => true, 'value' => null),
      array('id, username, password, email, activkey, lastvisit, role, status, edited_count, created, modified', 'safe', 'on'=>'search'),
    );
  }
  
  /**
   * checks if the currently logged in user tries to changer her own role and throws an validation error if so
   */
  public function checkRoleAccess($attribute,$params) {
    if (!Yii::app()->authManager->isAssigned($this->role, Yii::app()->user->id) && Yii::app()->user->id == $this->id) 
      $this->addError('role', Yii::t('app', 'You cannot change your own role.'));
  }
  
  public function relations() {
    $relations = array(
      'logs' => array(self::HAS_MANY, 'Log', 'user_id'),
      'profile' => array(self::HAS_ONE, 'Profile', 'user_id'),
      'sessions' => array(self::HAS_MANY, 'Session', 'user_id'),
      'games' => array(self::MANY_MANY, 'Game', 'user_to_game(user_id, game_id)'),
      'subjectMatters' => array(self::MANY_MANY, 'SubjectMatter', 'user_to_subject_matter(user_id, subject_matter_id)'),
    ); 
    if (isset(Yii::app()->getModule('user')->relations)) $relations = array_merge($relations,Yii::app()->getModule('user')->relations);
    return $relations;
  }
  
  public function scopes()
    {
        return array(
            'active'=>array(
                'condition'=>'status='.self::STATUS_ACTIVE,
            ),
            'notactvie'=>array(
                'condition'=>'status='.self::STATUS_NOACTIVE,
            ),
            'banned'=>array(
                'condition'=>'status='.self::STATUS_BANNED,
            ),
            'player'=>array(
                'condition'=>'role=\'player\'',
            ),
            'editor'=>array(
                'condition'=>'role=\'editor\'',
            ),
            'dbmanager'=>array(
                'condition'=>'role=\'dbmanager\'',
            ),
            'admin'=>array(
                'condition'=>'role=\'admin\'',
            ),
            'notsafe'=>array(
              'select' => 'id, username, password, email, activkey, edited_count, created, modified, lastvisit, role, status',
            ),
        );
    }
  
  public function defaultScope()
    {
        return array(
            'select' => 'id, username, email, edited_count, modified, created, lastvisit, role, status',
        );
    }
  
  public static function itemAlias($type,$code=NULL) {
    $roles = array();
    foreach (Yii::app()->authManager->getRoles() as $role) {
      $roles[$role->name] = Yii::t('app', $role->name);
    }
      
    $_items = array(
      'UserStatus' => array(
        self::STATUS_NOACTIVE => UserModule::t('Not active'),
        self::STATUS_ACTIVE => UserModule::t('Active'),
        self::STATUS_BANNED => UserModule::t('Banned'),
      ),
      'AdminStatus' => $roles,
    );
    if (isset($code))
      return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
    else
      return isset($_items[$type]) ? $_items[$type] : false;
  }
  
  /*
   * lists all roles registered in the system
   * 
   * @return array associative array of the roles array("role" => "role translation", ...)
   */
  public static function listRoles() {
    $roles = array();
    foreach (Yii::app()->authManager->getRoles() as $role) {
      $roles[$role->name] = Yii::t('app', $role->name);
    }
    return $roles; 
  }
}