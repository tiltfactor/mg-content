<?php

class LicenceController extends GxController {

    public function filters() {
        return array(
            /*'IPBlock',*/
            'accessControl',
        );
    }

    public function accessRules() {
        return array(
            array('allow',
                'actions' => array('view'),
                'roles' => array('*'),
            ),
            array('allow',
                'actions' => array('index', 'view', 'batch', 'create', 'update', 'admin', 'delete'),
                'roles' => array('editor', 'admin'),
            ),
            array('deny',
                'users' => array('*'),
            ),
        );
    }

    public function actionView($id) {
        $this->render('view', array(
            'model' => $this->loadModel($id, 'Licence'),
        ));
    }

    public function actionCreate() {
        $model = new Licence;
        $model->created = date('Y-m-d H:i:s');
        $model->modified = date('Y-m-d H:i:s');
        $this->performAjaxValidation($model, 'licence-form');
        if (isset($_POST['Licence'])) {
            $model->setAttributes($_POST['Licence']);
            if ($model->save()) {
                $token = Yii::app()->fbvStorage->get("token");
                $service = new MGGameService();
                $licence = new LicenceDTO();
                $licence->id = $model->id;
                $licence->name = $model->name;
                $licence->description = $model->description;
                $result = $service->createLicence($token, $licence);
                switch($result->statusCode->name) {
                    case $result->statusCode->_SUCCESS:
                        $model->synchronized = 1;
                        $model->save();
                        break;
                }

                MGHelper::log('create', 'Created Licence with ID(' . $model->id . ')');
                Flash::add('success', Yii::t('app', "Licence created"));
                if (Yii::app()->getRequest()->getIsAjaxRequest())
                    Yii::app()->end();
                else
                    $this->redirect(array('view', 'id' => $model->id));
            }
        }
        $this->render('create', array('model' => $model));
    }

    public function actionUpdate($id) {
        $model = $this->loadModel($id, 'Licence');
        $model->modified = date('Y-m-d H:i:s');
        $this->performAjaxValidation($model, 'licence-form');
        if (isset($_POST['Licence'])) {
            $model->setAttributes($_POST['Licence']);
            if ($model->save()) {
                $token = Yii::app()->fbvStorage->get("token");
                $service = new MGGameService();
                $licence = new LicenceDTO();
                $licence->id = $model->id;
                $licence->name = $model->name;
                $licence->description = $model->description;
                $model->synchronized = 0;
                $result = $service->updateLicence($token, $licence);
                switch($result->statusCode->name) {
                    case $result->statusCode->_SUCCESS:
                        $model->synchronized = 1;
                        $model->save();
                        break;
                }
                MGHelper::log('update', 'Updated Licence with ID(' . $id . ')');
                Flash::add('success', Yii::t('app', "Licence updated"));
                $this->redirect(array('view', 'id' => $model->id));
            }
        }
        $this->render('update', array(
            'model' => $model,
        ));
    }

    public function actionDelete($id) {
        if (Yii::app()->getRequest()->getIsPostRequest()) {
            $model = $this->loadModel($id, 'Licence');
            if ($model->hasAttribute("locked") && $model->locked) {
                throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
            } else {
                $model->delete_pending = 1;
                if ($model->save()) {
                    $token = Yii::app()->fbvStorage->get("token");
                    $service = new MGGameService();
                    $result = $service->deleteLicence($token, $model->id);
                    switch($result->statusCode->name) {
                        case $result->statusCode->_SUCCESS:
                            $model->delete();
                            break;
                    }
                }

                MGHelper::log('delete', 'Deleted Licence with ID(' . $id . ')');
                Flash::add('success', Yii::t('app', "Licence deleted"));
                if (!Yii::app()->getRequest()->getIsAjaxRequest())
                    $this->redirect(array('admin'));
            }
        } else
            throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
    }

    public function actionIndex() {
        $model = new Licence('search');
        $model->unsetAttributes();
        if (isset($_GET['Licence']))
            $model->setAttributes($_GET['Licence']);
        $this->render('admin', array(
            'model' => $model,
        ));
    }

    public function actionAdmin() {
        $model = new Licence('search');
        $model->unsetAttributes();
        if (isset($_GET['Licence']))
            $model->setAttributes($_GET['Licence']);
        $this->render('admin', array(
            'model' => $model,
        ));
    }

    public function actionBatch($op) {
        if (Yii::app()->getRequest()->getIsPostRequest()) {
            switch ($op) {
                case "delete":
                    $this->_batchDelete();
                    break;
            }
            if (!Yii::app()->getRequest()->getIsAjaxRequest())
                $this->redirect(array('admin'));
        } else
            throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
    }

    private function _batchDelete() {
        if (isset($_POST['licence-ids'])) {
            $criteria = new CDbCriteria;
            $criteria->addInCondition("id", $_POST['licence-ids']);
            MGHelper::log('batch-delete', 'Batch deleted Licence with IDs(' . implode(',', $_POST['licence-ids']) . ')');
            $model = new Licence;
            $model->deleteAll($criteria);
        }
    }
}