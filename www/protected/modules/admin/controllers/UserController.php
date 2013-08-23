<?php

class UserController extends Controller {
    public $defaultAction = 'admin';
    public $layout = '//layouts/column2';
    private $_model;

    /**
     * @return array action filters
     */
    public function filters() {
        return CMap::mergeArray(parent::filters(), array(
            /*'IPBlock',*/
            'accessControl', // perform access control for CRUD operations
        ));
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        return array(
            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array('admin', 'delete', 'create', 'update', 'view', 'batch'),
                'roles' => array('editor', 'admin'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    /**
     * Manages all models.
     */
    public function actionAdmin() {
        $this->pageTitle = Yii::app()->name . ' - ' . Yii::t('app', 'Users');
        $model = new User('search');
        $model->unsetAttributes();
        if (isset($_GET['User']))
            $model->setAttributes($_GET['User']);
        $this->render('admin', array(
            'model' => $model,
        ));
    }

    /**
     * Displays a particular model.
     */
    public function actionView() {
        $this->pageTitle = Yii::app()->name . ' - ' . Yii::t('app', ' View User');
        $model = $this->loadModel();
        $this->render('view', array(
            'model' => $model,
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate() {
        $this->pageTitle = Yii::app()->name . ' - ' . Yii::t('app', ' Create User');
        $model = new User;
        $profile = new Profile;
        // how to enable ajax for users
        if (isset($_POST['User'])) {
            $model->attributes = $_POST['User'];
            $model->activekey = UserModule::encrypting(microtime() . $model->password);
            $model->created = date('Y-m-d H:i:s');
            $model->modified = date('Y-m-d H:i:s');
            $model->lastvisit = date('Y-m-d H:i:s');
            if (isset($_POST['Profile']))
                $profile->attributes = $_POST['Profile'];
            $profile->user_id = 0;
            if ($model->validate() && $profile->validate()) {
                $model->password = UserModule::encrypting($model->password);
                $relatedData = array();
                if ($model->saveWithRelated($relatedData)) {
                    $profile->user_id = $model->id;
                    $profile->save();
                    MGHelper::log('create', 'Created user with ID(' . $model->id . ')');
                    Flash::add('success', Yii::t('app', "User created"));
                }
                if (Yii::app()->getRequest()->getIsAjaxRequest())
                    Yii::app()->end();
                else
                    $this->redirect(array('view', 'id' => $model->id));
            } else $profile->validate();
        }
        $this->render('create', array(
            'model' => $model,
            'profile' => $profile,
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     */
    public function actionUpdate() {
        $this->pageTitle = Yii::app()->name . ' - ' . Yii::t('app', ' Update Player');
        $model = $this->loadModel();
        $profile = $model->profile;
        if (isset($_POST['User'])) {
            if (isset($_POST['User']))
                $model->attributes = $_POST['User'];
            if (isset($_POST['Profile']))
                $profile->attributes = $_POST['Profile'];
            $model->modified = date('Y-m-d H:i:s');
            if ($model->validate() && $profile->validate()) {
                $old_password = User::model()->notsafe()->findByPk($model->id);
                if ($old_password->password != $model->password) {
                    $model->password = UserModule::encrypting($model->password);
                    $model->activekey = UserModule::encrypting(microtime() . $model->password);
                }
                $relatedData = array();
                if (isset($_POST['User']['games']))
                    $relatedData['games'] = $_POST['User']['games'] === '' ? null : $_POST['User']['games'];
                if ($model->saveWithRelated($relatedData)) {
                    if ($model->status == -1) {
                        $this->_banUser($model->id);
                    }
                    $profile->save();
                    /*if (isset($_POST['User']['subjectMatters'])) {
                        UserToSubjectMatter::saveRelationShips($model->id, $_POST['User']['subjectMatters']);
                    }*/
                    MGHelper::log('update', 'Updated user with ID(' . $model->id . ')');
                    Flash::add('success', Yii::t('app', "User updated"));
                    $this->redirect(array('view', 'id' => $model->id));
                }
            } else $profile->validate();
        }
        $this->render('update', array(
            'model' => $model,
            'profile' => $profile,
        ));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     */
    public function actionDelete() {
        if (Yii::app()->request->isPostRequest) {
            // we only allow deletion via POST request
            $model = $this->loadModel();
            if ($model->canDelete()) { // a user can only be deleted if it is not the current session's user and if the user has not submitted any tags
                $profile = Profile::model()->findByPk($model->id);
                $profile->delete();
                $model->delete();
                MGHelper::log('delete', 'Deleted User with ID(' . $model->id . ')');
                Flash::add('success', Yii::t('app', "User deleted"));
                // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
                if (!isset($_POST['ajax']))
                    $this->redirect(array('/admin/user'));
            } else {
                throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
            }
        } else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     */
    public function loadModel() {
        if ($this->_model === null) {
            if (isset($_GET['id'])) {
                $this->_model = User::model()->notsafe()->findbyPk($_GET['id']);
            }
            if ($this->_model === null)
                throw new CHttpException(404, 'The requested page does not exist.');
        }
        return $this->_model;
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the primary key value. Defaults to null, meaning using the 'id' GET variable
     */
    public function loadUser($id = null) {
        if ($this->_model === null) {
            if ($id !== null || isset($_GET['id']))
                $this->_model = User::model()->findbyPk($id !== null ? $id : $_GET['id']);
            if ($this->_model === null)
                throw new CHttpException(404, 'The requested page does not exist.');
        }
        return $this->_model;
    }

    //implentation of the batch interface for the gridview
    public function actionBatch($op) {
        if (Yii::app()->getRequest()->getIsPostRequest()) {
            switch ($op) {
                case "ban":
                    $this->_batchBan();
                    break;
                case "delete":
                    $this->_batchDelete();
                    break;
            }
            if (!Yii::app()->getRequest()->getIsAjaxRequest())
                $this->redirect(array('admin'));
        } else
            throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
    }

    private function _batchBan() {
        if (isset($_POST['users-ids'])) {
            $criteria = new CDbCriteria;
            $criteria->addInCondition("id", $_POST['users-ids']);
            MGHelper::log('batch-ban', 'Banned users with IDs(' . implode(',', $_POST['users-ids']) . ')');
            $model = new User;
            $users = $model->findAll($criteria);
            if ($users) {
                foreach ($users as $user) {
                    $this->_banUser($user->id);
                    $user->status = -1;
                    $user->save();
                }
            }
        }
    }

    private function _batchDelete() {
        if (isset($_POST['users-ids'])) {
            $users = User::model()->findAllByPk($_POST['users-ids']);
            if ($users) {
                foreach ($users as $user) {
                    if ($user->canDelete()) {
                        $user->delete();
                    }
                }
            }
            MGHelper::log('batch-delete', 'Batch deleted Images with IDs(' . implode(',', $_POST['users-ids']) . ')');
        }
    }

    private function _banUser($user_id) {
        TagUse::model()->banUser($user_id);
        UserToGame::model()->banUser($user_id);
        //TODO: how to deal with the problem that the banned user still might be logged in?
    }
}