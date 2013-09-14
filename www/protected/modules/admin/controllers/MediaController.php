<?php

class MediaController extends GxController {
    public $defaultAction = 'admin';

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
                'actions' => array('view', 'batch', 'create', 'update', 'admin', 'delete', 'searchUser'),
                'roles' => array(EDITOR, ADMIN),
            ),
            array('deny',
                'users' => array('*'),
            ),
        );
    }

    public function actionView($id) {
        $cs = Yii::app()->clientScript;
        $cs->registerCoreScript('jquery');
        $cs->registerCssFile(Yii::app()->baseUrl . '/css/jquery.fancybox-1.3.4.css');
        $cs->registerScriptFile(Yii::app()->baseUrl . '/js/jquery.fancybox-1.3.4.pack.js', CClientScript::POS_END);
        $js = <<<EOD
    \$("a.zoom").fancybox({overlayColor: '#000'});
EOD;
        Yii::app()->clientScript->registerScript(__CLASS__ . '#game', $js, CClientScript::POS_READY);
        $this->render('view', array(
            'model' => $this->loadModel($id, 'Media'),
        ));
    }

    public function actionCreate() {
        $model = new Media;
        $model->created = date('Y-m-d H:i:s');
        $model->modified = date('Y-m-d H:i:s');
        $this->performAjaxValidation($model, 'media-form');
        if (isset($_POST['Media'])) {
            $model->setAttributes($_POST['Media']);
            $relatedData = array(
                'collections' => $_POST['Media']['collections'] === '' ? null : $_POST['Media']['collections'],
            );
            if ($model->saveWithRelated($relatedData)) {
                MGHelper::log('create', 'Created Media with ID(' . $model->id . ')');
                Flash::add('success', Yii::t('app', "Media created"));
                if (Yii::app()->getRequest()->getIsAjaxRequest())
                    Yii::app()->end();
                else
                    $this->redirect(array('view', 'id' => $model->id));
            }
        }
        $this->render('create', array('model' => $model));
    }

    public function actionUpdate($id) {
        $model = $this->loadModel($id, 'Media');
        $model->modified = date('Y-m-d H:i:s');
        $this->performAjaxValidation($model, 'media-form');
        if (isset($_POST['Media'])) {
            $model->setAttributes($_POST['Media']);
            $relatedData = array(
                'collections' => $_POST['Media']['collections'] === '' ? null : $_POST['Media']['collections'],
            );
            if ($model->saveWithRelated($relatedData)) {
                MGHelper::log('update', 'Updated Media with ID(' . $id . ')');
                Flash::add('success', Yii::t('app', "Media updated"));
                $this->redirect(array('view', 'id' => $model->id));
            }
        }
        $this->render('update', array(
            'model' => $model,
        ));
    }

    public function actionDelete($id) {
        if (Yii::app()->getRequest()->getIsPostRequest()) {
            $model = $this->loadModel($id, 'Media');
            if ($model->hasAttribute("locked") && $model->locked) {
                throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
            } else {
                $model->delete_pending = 1;
                if ($model->save()) {
                    $token = Yii::app()->fbvStorage->get("token");
                    $service = new MGGameService();
                    $result = $service->deleteMedia($token, $model->id);
                    switch($result->statusCode->name) {
                        case $result->statusCode->_SUCCESS:
                            $model->delete();
                            break;
                    }
                }
                MGHelper::log('delete', 'Deleted Media with ID(' . $id . ')');
                Flash::add('success', Yii::t('app', "Media deleted"));
                if (!Yii::app()->getRequest()->getIsAjaxRequest())
                    $this->redirect(array('admin'));
            }
        } else
            throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
    }

    public function actionAdmin() {
        $this->layout = '//layouts/column1';
        $model = new Media('search');
        $model->unsetAttributes();
        if (isset($_GET['Media']))
            $model->setAttributes($_GET['Media']);
        $this->render('admin', array(
            'model' => $model,
        ));
    }

    public function actionBatch($op) {
        if (Yii::app()->getRequest()->getIsPostRequest()) {
            switch ($op) {
                case "collection-add":
                    $this->_batchAddCollection("add");
                    break;
                case "collection-remove":
                    $this->_batchAddCollection("remove");
                    break;
            }
            if (!Yii::app()->getRequest()->getIsAjaxRequest())
                $this->redirect(array('admin'));
        } else
            throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
    }

    private function _batchAddCollection($action) {
        if (isset($_POST['media-ids']) && isset($_GET['isid']) && (int)$_GET['isid'] > 0) {
            $medias = Media::model()->findAllByPk($_POST['media-ids']);
            $collection = Collection::model()->findByPk($_GET['isid']);
            if ($medias && $collection) {
                foreach ($medias as $media) {
                    $mediaCollection = array();
                    foreach ($media->collections as $is) {
                        $mediaCollection[] = $is->id;
                    }
                    switch ($action) {
                        case "add":
                            $mediaCollection = array_merge($mediaCollection, array((int)$_GET['isid']));
                            break;
                        case "remove":
                            $mediaCollection = array_diff($mediaCollection, array((int)$_GET['isid']));
                            break;
                    }
                    $relatedData = array(
                        'collections' => $mediaCollection
                    );
                    $media->assignment_sync = 0;
                    if ($media->saveWithRelated($relatedData)) {
                        $assignment = new AssignMediaDTO();
                        $assignment->id = $media->id;
                        $assignment->collections = $mediaCollection;
                        $service = new MGGameService();
                        $token = Yii::app()->fbvStorage->get("token");
                        $result = $service->assignMediaToCollections($token, $assignment);
                        switch($result->statusCode->name) {
                            case $result->statusCode->_SUCCESS:
                                $media->assignment_sync = 1;
                                $media->save();
                                break;
                        }
                    }
                }
                MGHelper::log('batch-addcollection', 'Batch assigned Medias with IDs(' . implode(',', $_POST['media-ids']) . ') to collection with the ID(' . $_GET['isid'] . ')');
            }
        }
    }

    public function actionSearchUser() {
        $res = array();
        if (isset($_GET["term"])) {
            $res = User::model()->searchForNames((string)$_GET["term"]);
        }
        $this->jsonResponse($res);
    }
}