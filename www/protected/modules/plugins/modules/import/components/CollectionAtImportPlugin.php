<?php

/**
 * This is the base implementation of a import plug-in
 */

class CollectionAtImportPlugin extends MGImportPlugin
{
    public $enableOnInstall = true;

    function init()
    {
        parent::init();
    }


    /**
     * Add a the checkboxs to assign media to collections on import process
     *
     * @param GxActiveForm $form   Widget object to be manipulated
     * @return string
     */
    function form(&$form)
    {
        $model = new Media;
        $legend = CHtml::tag("legend", array(), Yii::t('app', 'Assign processed media to the following collections'));
        $listing = CHtml::checkBoxList(
            "Media[collections]",
            ((isset($_POST['Media']) && isset($_POST['Media']['collections'])) ? $_POST['Media']['collections'] : '1'),
            GxHtml::encodeEx(GxHtml::listDataEx(Collection::model()->findAllAttributes(null, true))),
            array("template" => '<div class="checkbox">{input} {label}</div>', "separator" => ""));
        $error = $form->error($model, 'Media[collection]');
        $link = CHtml::link(Yii::t('app', 'Add new collection'), array('collection/create'));

        return CHtml::tag("fieldset", array(), '<div class="row">' . $legend . $listing . $error . $link . '</div>');
    }

    /**
     * Make sure that the media are at least added to the collection all
     *
     * @param Media $media Media model
     * @param Array $errors Array holding information of errors on each form field in the form
     */
    function validate($media, &$errors)
    {
        if (!isset($_POST['Media']) || !isset($_POST['Media']['collections']) || !is_array($_POST['Media']['collections']) || !in_array(1, $_POST['Media']['collections'])) {
            $errors["Media[collections]"] = array(Yii::t('app', "Please select at least the 'all' collection"));
        }
    }

    /**
     * Process all media and assign them to the selected collection.
     *
     * @param Array $media List of models of the type Media
     */
    function process($media)
    {
        if (isset($_POST['Media']) && isset($_POST['Media']['collections'])) {
            $service = new MGGameService();
            $token = Yii::app()->fbvStorage->get("token");
            foreach ($media as $media) {
                $mediaCollection = $_POST['Media']['collections'] === '' ? array(1) : array_unique(array_merge($_POST['Media']['collections'], array(1)));
                $relatedData = array(
                    'collections' => $mediaCollection,
                );
                $media->saveWithRelated($relatedData);

                $model = new MediaDTO();
                $model->id = $media->id;
                $model->name = $media->name;
                $model->size = $media->size;
                $model->mimeType = $media->mime_type;
                $model->batchId = $media->batch_id;
                $model->locked = $media->locked;
                $model->batchId = $media->batch_id;
                $result = $service->createMedia($token, $model);
                switch($result->statusCode->name) {
                    case $result->statusCode->_SUCCESS:
                        $media->synchronized = 1;
                        $media->save();
                        $assignment = new AssignMediaDTO();
                        $assignment->id = $media->id;
                        $assignment->collections = $mediaCollection;
                        $service->assignMediaToCollections($token, $assignment);
                        break;
                    default:
                        $media->assignment_sync = 0;
                        $media->save();
                        break;

                }
            }
        }
    }
}
