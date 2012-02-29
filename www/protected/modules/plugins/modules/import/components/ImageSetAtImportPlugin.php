<?php

/**
 * This is the base implementation of a import plug-in 
 */

class ImageSetAtImportPlugin extends MGImportPlugin {
  public $enableOnInstall = true;
     
  function init() {
    parent::init();
  }
  
  /**
   * Add a the checkboxs to assign images to image sets on import process
   * 
   * @param GxActiveForm Widget object to be manipulated
   */
  function form(&$form) {
    $model = new Image;
    $legend = CHtml::tag("legend", array(), Yii::t('app', 'Assign processed images to the following image sets'));
    $listing = CHtml::checkBoxList( 
        "Image[imageSets]", 
        ((isset($_POST['Image']) && isset($_POST['Image']['imageSets']))? $_POST['Image']['imageSets'] : '1'), 
        GxHtml::encodeEx(GxHtml::listDataEx(ImageSet::model()->findAllAttributes(null, true))), 
        array("template" => '<div class="checkbox">{input} {label}</div>', "separator" => ""));
    $error = $form->error($model,'Image[imageSets]');
    $link = CHtml::link(Yii::t('app', 'Add new image set'), array('imageSet/create'));
    
    return CHtml::tag("fieldset", array(), '<div class="row">' . $legend . $listing . $error . $link . '</div>');
  }
  
  /**
   * Make sure that the images are at least added to the image set all
   * 
   * @param Image $image Image model
   * @param Array $errors Array holding information of errors on each form field in the form
   */
  function validate($image, &$errors) {
    if (!isset($_POST['Image']) || !isset($_POST['Image']['imageSets']) || !is_array($_POST['Image']['imageSets']) || !in_array(1, $_POST['Image']['imageSets'])) {
      $errors["Image[imageSets]"] = array(Yii::t('app', "Please select at least the 'all' image set"));
    }
  }
  
  /**
   * Process all images and assign them to the selected image set. 
   * 
   * @param Array $images List of models of the type Image
   */
  function process($images) {
    if (isset($_POST['Image']) && isset($_POST['Image']['imageSets'])) {
      foreach ($images as $image) {
        $relatedData = array(
          'imageSets' => $_POST['Image']['imageSets'] === '' ? array(1) : array_unique(array_merge($_POST['Image']['imageSets'], array(1))),
        );
        $image->saveWithRelated($relatedData);
      }
    }
  } 
}
