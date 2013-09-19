<?php

class ServerProfileController extends GxController
{

    public function filters()
    {
        return array(
            'accessControl',
        );
    }

    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('view'),
                'roles' => array('*'),
            ),
            array('allow',
                'actions' => array('index', 'view', 'batch', 'create', 'update', 'admin', 'delete'),
                'roles' => array(EDITOR, EDITOR), // ammend after creation
            ),
            array('deny',
                'users' => array('*'),
            ),
        );
    }

    public function actionView($id)
    {
        $this->render('view', array(
            'model' => $this->loadModel($id, 'ServerProfile'),
        ));
    }

    public function actionCreate()
    {
        $model = new ServerProfile;


        if (isset($_POST['ServerProfile'])) {
            $model->setAttributes($_POST['ServerProfile']);

            if ($model->validate('create')) {
                $model->save(false);
                if (Yii::app()->getRequest()->getIsAjaxRequest())
                    Yii::app()->end();
                else
                    $this->redirect(array('view', 'id' => $model->id));
            }
        }

        $this->render('create', array('model' => $model));
    }

    public function actionUpdate($id)
    {
        $model = $this->loadModel($id, 'ServerProfile');
        $logoFileName = $model->logo;

        if (isset($_POST['ServerProfile'])) {

            $model->setAttributes($_POST['ServerProfile']);

            $logo = CUploadedFile::getInstance($model, 'logo');
            if ($logo) {
                $model->logo = $logo;
            } else {
                $model->logo = $logoFileName;
            }

            if ($model->validate()) {
                $model->save(false);
                $logoUrl = "";
                if ($logo) {
                    $path = realpath(Yii::app()->getBasePath() . '/..' . UPLOAD_PATH) . "/images/";
                    $name = trim(basename(stripslashes($model->logo->getName())), ".\x00..\x20");
                    $model->logo->saveAs($path . $name);
                    $logoUrl = Yii::app()->getBaseUrl(true) . UPLOAD_PATH . "/images/" . $name;
                } else {
                    $logoUrl = Yii::app()->getBaseUrl(true) . UPLOAD_PATH . "/images/" . $model->logo;
                }

                $institutionDto = new InstitutionDTO;
                $institutionDto->name = $model->name;
                $institutionDto->url = $model->url;
                $institutionDto->description = $model->description;
                $institutionDto->logoUrl = $logoUrl;
                $institutionDto->token = Yii::app()->fbvStorage->get("token");


                $service = new MGGameService();
                $result = $service->updateProfile($institutionDto);

                switch ($result->status->statusCode->name) {
                    case $result->status->statusCode->_SUCCESS:

                        Yii::app()->fbvStorage->set('token', $result->token);


                        break;
                    case $result->status->statusCode->_FATAL_ERROR:
                    case $result->status->statusCode->_ILLEGAL_ARGUMENT:
                        $error = $result->status->status;
                        throw new CHttpException(404, $error);
                        break;
                }

                $this->redirect(array('view', 'id' => $model->id));
            }
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }

    public function actionDelete($id)
    {
        if (Yii::app()->getRequest()->getIsPostRequest()) {
            $this->loadModel($id, 'ServerProfile')->delete();

            if (!Yii::app()->getRequest()->getIsAjaxRequest())
                $this->redirect(array('admin'));
        } else
            throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
    }

    public function actionIndex()
    {
        $model = new ServerProfile('search');
        $model->unsetAttributes();

        if (isset($_GET['ServerProfile']))
            $model->setAttributes($_GET['ServerProfile']);

        $this->render('admin', array(
            'model' => $model,
        ));
    }

    public function actionAdmin()
    {
        $model = new ServerProfile('search');
        $model->unsetAttributes();

        if (isset($_GET['ServerProfile']))
            $model->setAttributes($_GET['ServerProfile']);

        $this->render('admin', array(
            'model' => $model,
        ));
    }

}