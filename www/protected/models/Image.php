<?php

Yii::import('application.models._base.BaseImage');

class Image extends BaseImage
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
  
  public function search() {
    $criteria = new CDbCriteria;

    $criteria->compare('id', $this->id);
    $criteria->compare('name', $this->name, true);
    $criteria->compare('size', $this->size);
    $criteria->compare('mime_type', $this->mime_type, true);
    $criteria->compare('batch_id', $this->batch_id, true);
    $criteria->compare('last_access', $this->last_access, true);
    $criteria->compare('locked', 1);
    $criteria->compare('created', $this->created, true);
    $criteria->compare('modified', $this->modified, true);
    
    if(!Yii::app()->request->isAjaxRequest)
        $criteria->order = 'name ASC';
    
    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination'=>array(
        'pageSize'=>Yii::app()->params['pagination.pageSize'],
      ),
    ));
  }
  
  public function unprocessed() {
    $criteria = new CDbCriteria;

    $criteria->compare('id', $this->id);
    $criteria->compare('name', $this->name, true);
    $criteria->compare('size', $this->size);
    $criteria->compare('mime_type', $this->mime_type, true);
    $criteria->compare('batch_id', $this->batch_id, true);
    $criteria->compare('last_access', $this->last_access, true);
    $criteria->compare('locked', 0);
    $criteria->compare('created', $this->created, true);
    $criteria->compare('modified', $this->modified, true);
    
    if(!Yii::app()->request->isAjaxRequest)
        $criteria->order = 'name ASC';
        
    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination'=>array(
        'pageSize'=>Yii::app()->params['pagination.pageSize'] * 2,
      ),
    ));
  }
  
  /**
   * as images have got files we have to make sure that all files are removed from the file system once an image
   * has been deleted.
   */
  public function afterDelete() {
    $path = realpath(Yii::app()->getBasePath() . Yii::app()->params['upload_path']);
    $path_parts = pathinfo($this->name);
    
    //remove file from .../uploads/images
    if (file_exists($path . "/images/" . $this->name) && is_writable($path . "/images/" . $this->name)) 
      unlink($path . "/images/" . $this->name);
    
    //remove file from .../uploads/images
    if (file_exists($path . "/thumbs/" . $this->name) && is_writable($path . "/thumbs/" . $this->name)) 
      unlink($path . "/thumbs/" . $this->name);
    
    //remove all scaled versions
    $files = glob($path . "/scaled/" . $path_parts["filename"] . ".mg-scaled.*");
    if (is_array($files) && count($files) > 0) {
      foreach ($files as $file) {
        unlink($file);
      }
    }
    
    parent::afterDelete(); 
  }
}