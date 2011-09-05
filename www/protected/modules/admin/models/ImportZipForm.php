<?php

/**
 * ImportZipForm class.
 * ImportZipForm is the data structure for uploading a zip file to import into the system
 */
class ImportZipForm extends CFormModel
{
  public $zipfile;
  public $batch_id;

  /**
   * Declares the validation rules.
   */
  public function rules()
  {
    return array(
      // name, email, subject and body are required
      array('batch_id', 'required'),
      array('zipfile', 'file', 'types'=>'zip', 'maxFiles'=>1, 'maxSize' =>  $this->return_bytes(ini_get('upload_max_filesize')), 'allowEmpty'=>false, 'wrongType'=> Yii::t('app', 'You can only upload .zip files')),
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
      'zipfile'=>'Zip File',
    );
  }
  
  function return_bytes ($size_str) {
    switch (substr ($size_str, -1))
    {
        case 'M': case 'm': return (int)$size_str * 1048576;
        case 'K': case 'k': return (int)$size_str * 1024;
        case 'G': case 'g': return (int)$size_str * 1073741824;
        default: return $size_str;
    }
  }
}