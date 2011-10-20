<?php

/**
 * ImportZipForm class.
 * ImportZipForm is the data structure for uploading a zip file to import into the system
 */
class TagReWeightForm extends CFormModel
{
  
  public $weight;
  public $user_id;
  public $applyTo;
  
  /**
   * Declares the validation rules.
   */
  public function rules() {
    return array(
      // name, email, subject and body are required
      array('weight, applyTo', 'required'),
      array('user_id', 'numerical', 'min'=>-1, 'integerOnly' => true),
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
      'user_id'=> Yii::t('app', 'Submitted By'),
      'applyTo'=> Yii::t('app', 'Apply New Weight to'),
    );
  }

}