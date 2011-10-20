<?php

/**
 * ImportZipForm class.
 * ImportZipForm is the data structure for uploading a zip file to import into the system
 */
class TagUseReWeightForm extends CFormModel
{
  
  public $weight;
  public $applyTo;
  
  /**
   * Declares the validation rules.
   */
  public function rules() {
    return array(
      // name, email, subject and body are required
      array('weight, applyTo', 'required'),
      array('weight', 'numerical', 'min'=>0, 'max'=>10000),
    );
  }

  /**
   * Declares customized attribute labels.
   * If not declared here, an attribute would have a label that is
   * the same as its name with the first letter in upper case.
   */
  public function attributeLabels() {
    return array(
      'weight'=> Yii::t('app', 'Weight'),
      'applyTo'=> Yii::t('app', 'Apply New Weight to'),
    );
  }

}