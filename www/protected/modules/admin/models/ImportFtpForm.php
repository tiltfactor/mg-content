<?php

/**
 * ImportZipForm class.
 * ImportZipForm is the data structure for uploading a zip file to import into the system
 */
class ImportFtpForm extends CFormModel
{
  public $batch_id;
  public $import_per_request = 2;
  public $import_processed = 0;
  public $import_skipped = 0;
  
  /**
   * Declares the validation rules.
   */
  public function rules()
  {
    return array(
      array('batch_id, import_per_request, import_processed, import_skipped', 'required'),
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
      'import_per_request'=>'Number of images to be imported per request',
    );
  }
}