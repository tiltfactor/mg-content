<?php

/**
 * ImportZipForm class.
 * ImportZipForm is the data structure for uploading a zip file to import into the system
 */
class ImportFtpForm extends CFormModel
{
  public $batch_id;

  /**
   * Declares the validation rules.
   */
  public function rules()
  {
    return array(
      // name, email, subject and body are required
      array('batch_id', 'required'),
    );
  }

  /**
   * Declares customized attribute labels.
   * If not declared here, an attribute would have a label that is
   * the same as its name with the first letter in upper case.
   */
  public function attributeLabels()
  {
    return array(
      'batch_id'=>'Batch ID',
    );
  }
}