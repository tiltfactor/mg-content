<?php

Yii::import('application.models._base.BaseUser');
class User extends BaseUser {
    const STATUS_NOACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_BANNED = -1;

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public static function label($n = 1) {
        return Yii::t('app', 'User|Users', $n);
    }

    public function tableName() {
        return Yii::app()->getModule('user')->tableUsers;
    }

    public function rules() {
        return array(
            array('username, email', 'required'),
            array('username, email', 'unique'),
            array('status, edited_count', 'numerical', 'integerOnly' => true),
            array('username', 'length', 'max' => 32),
            array('email', 'email'),
            array('password', 'required', 'on' => 'insert'),
            array('password, email, activekey', 'length', 'max' => 128, 'min' => 3),
            array('role', 'length', 'max' => 45),
            array('role', 'checkRoleAccess'),
            array('activekey, lastvisit, role, status, edited_count, created, modified', 'default', 'setOnEmpty' => true, 'value' => null),
            array('id, username, password, email, activekey, lastvisit, role, status, edited_count, created, modified', 'safe', 'on' => 'search'),
        );
    }

    /**
     * Create the data proviced for the CGridView
     *
     * @return CActiveDataProvider The dataprovider for the CGridView
     */
    public function search() {
        $criteria = new CDbCriteria;
        $criteria->compare('id', $this->id);
        $criteria->compare('username', $this->username, true);
        $criteria->compare('password', $this->password, true);
        $criteria->compare('email', $this->email, true);
        $criteria->compare('activekey', $this->activekey, true);
        $criteria->compare('lastvisit', $this->lastvisit, true);
        $criteria->compare('role', $this->role, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('edited_count', $this->edited_count);
        $criteria->compare('created', $this->created, true);
        $criteria->compare('modified', $this->modified, true);
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => Yii::app()->fbvStorage->get("settings.pagination_size"),
            ),
        ));
    }

    /**
     * Implementation of a custom rule for the model. Checks if the currently logged
     * in user tries to changer her own role and throws an validation error if so.
     *
     * @param array $attribute Not used
     * @param array $param Not used
     */
    public function checkRoleAccess($attribute, $params) {
        if (!Yii::app()->authManager->isAssigned($this->role, Yii::app()->user->id) && Yii::app()->user->id == $this->id)
            $this->addError('role', Yii::t('app', 'You cannot change your own role.'));
    }

    public function relations() {
        $relations = array(
            'logs' => array(self::HAS_MANY, 'Log', 'user_id'),
            'profile' => array(self::HAS_ONE, 'Profile', 'user_id'),
            'sessions' => array(self::HAS_MANY, 'Session', 'user_id'),
            /*'games' => array(self::MANY_MANY, 'Game', 'user_to_game(user_id, game_id)'),
            'subjectMatters' => array(self::MANY_MANY, 'SubjectMatter', 'user_to_subject_matter(user_id, subject_matter_id)'),*/
        );
        if (isset(Yii::app()->getModule('user')->relations)) $relations = array_merge($relations, Yii::app()->getModule('user')->relations);
        return $relations;
    }

    public function attributeLabels() {
        return array(
            'id' => Yii::t('app', 'ID'),
            'username' => Yii::t('app', 'User Name'),
            'password' => Yii::t('app', 'Password'),
            'email' => Yii::t('app', 'Email'),
            'activekey' => Yii::t('app', 'Activation Key'),
            'lastvisit' => Yii::t('app', 'Lastvisit'),
            'role' => Yii::t('app', 'Role'),
            'status' => Yii::t('app', 'Status'),
            /*'edited_count' => Yii::t('app', 'Banned Tags'),*/
            'created' => Yii::t('app', 'Created'),
            'modified' => Yii::t('app', 'Modified'),
            'logs' => null,
            'profile' => null,
            'sessions' => null,
        );
    }

    public function scopes() {
        return array(
            'active' => array(
                'condition' => 'status=' . self::STATUS_ACTIVE,
            ),
            'notactvie' => array(
                'condition' => 'status=' . self::STATUS_NOACTIVE,
            ),
            'banned' => array(
                'condition' => 'status=' . self::STATUS_BANNED,
            ),
            'editor' => array(
                'condition' => 'role=\'editor\'',
            ),
            'admin' => array(
                'condition' => 'role=\'admin\'',
            ),
            'notsafe' => array(
                'select' => 'id, username, password, email, activekey, edited_count, created, modified, lastvisit, role, status',
            ),
        );
    }

    /**
     * Retrive the most used tags by this user
     *
     * @param int $num_tags The number of top tags that should be retrieved. Default is 10
     * @return string A list of linked tags or empty string
     */
    /*public function getTopTags($num_tags=10) {
      $tags = Yii::app()->db->createCommand()
                    ->select('count(t.id) as counted, t.id, t.tag')
                    ->from('{{session}} s')
                    ->join('{{game_submission}} gs', 'gs.session_id=s.id')
                    ->join('{{tag_use}} tu', 'tu.game_submission_id = gs.id')
                    ->join('{{tag}} t', 'tu.tag_id = t.id')
                    ->where(array('and', 'tu.weight > 0', 's.user_id=:userID'), array(":userID" => $this->id))
                    ->group('t.id, t.tag')
                    ->order('counted DESC')
                    ->limit($num_tags)
                    ->queryAll();

      if ($tags) {
        $out = array();
        foreach ($tags as $tag) {
          $linkEdit = CHtml::link($tag["tag"] . '(' .$tag["counted"] . ')', array("/admin/tag/view", "id" =>$tag["id"]), array('class' => 'edit ir'));
          $linkView = CHtml::link($tag["tag"] . '(' .$tag["counted"] . ')', array("/admin/tag/view", "id" =>$tag["id"]), array('class' => 'tag'));
          $out[] =  '<div class="tag-dialog">' . $linkEdit . $linkView . '</div>';
        }
        return implode("", $out);
      } else {
        return "";
      }
    }*/
    public function defaultScope() {
        return array(
            'select' => 'id, username, email, edited_count, modified, created, lastvisit, role, status',
        );
    }

    /**
     * Retrieve string aliases/translations displayed to the user or list of values
     * for item codes stored in the sytem.
     *
     * @param string The item to be looked up
     * @param $code The particular code that has to be resolved
     * @param mixed string or array of code/string pairs or false if no element could be found
     */
    public static function itemAlias($type, $code = NULL) {
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

    /**
     * Lists user names that contain the passed parameter. It is mainly used for autocomplete
     * widgets
     *
     * @param string $name the begin of the user name that should be found
     * @return mixed array containing the username column or null
     */
    function searchForNames($name) {
        return Yii::app()->db->createCommand()
            ->select('u.username')
            ->from('{{user}} u')
            ->where(array('like', 'username', '%' . $name . '%'))
            ->order('u.username')
            ->limit(50)
            ->queryColumn();
    }

    /**
     * Provide an CArrayDataProvider to allow to browse all users that tagged an media.
     *
     * @param int $media_id The id of the media for which the tagging users should be listed
     * @return CArrayDataProvider The data provider to display the users
     */
    public function searchMediaUsers_($media_id) {
        $command = Yii::app()->db->createCommand()
            ->select('count(u.id) as counted, count(DISTINCT tu.tag_id) as tag_counted, u.id, u.username')
            ->from('{{user}} u')
            ->join('{{session}} s', 's.user_id=u.id')
            /*->join('{{game_submission}} gs', 'gs.session_id=s.id')
            ->join('{{tag_use}} tu', 'tu.game_submission_id = gs.id')
            ->where(array('and', 'tu.weight > 0', 'tu.media_id=:mediaID'), array(":mediaID" => $media_id))*/
            ->group('u.id, u.username')
            /*->order('gs.created DESC')*/;

        $command->distinct = true;
        $tags = $command->queryAll();
        return new CArrayDataProvider($tags, array(
            'id' => 'id',
            'sort' => array(
                'attributes' => array('id', 'username', 'counted'),
            ),
            'pagination' => array('pageSize' => Yii::app()->fbvStorage->get("settings.pagination_size")),
        ));
    }

    /**
     * Provide an CArrayDataProvider to allow to browse all users that used a particula tag.
     *
     * @param int $tag_id The id of the tag for which the tagging users should be listed
     * @return CArrayDataProvider The data provider to display the users
     */
    public function searchTagUsers_($tag_id) {
        $command = Yii::app()->db->createCommand()
            ->select('count(u.id) as counted/*, count(DISTINCT tu.media_id) as media_counted*/, u.id, u.username')
            ->from('{{user}} u')
            ->join('{{session}} s', 's.user_id=u.id')
            /*->join('{{game_submission}} gs', 'gs.session_id=s.id')*/
            ->group('u.id, u.username')
            /*->order('gs.created DESC')*/;
        $command->distinct = true;
        $tags = $command->queryAll();
        return new CArrayDataProvider($tags, array(
            'id' => 'id',
            'sort' => array(
                'attributes' => array(
                    'id', 'username', 'counted'
                ),
            ),
            'pagination' => array(
                'pageSize' => Yii::app()->fbvStorage->get("settings.pagination_size")
            ),
        ));
    }

    /**
     * Checks if the user can be deleted. A user can be deleted if it is not the currently
     * logged in user and the user has not contributed any game submissions
     *
     * @return boolean True if user can be deleted
     */
    public function canDelete() {
        return true;
    }
}