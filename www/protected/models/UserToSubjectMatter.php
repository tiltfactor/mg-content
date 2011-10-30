<?php

Yii::import('application.models._base.BaseUserToSubjectMatter');

class UserToSubjectMatter extends BaseUserToSubjectMatter
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
  
  /**
   * Saves the subject matters interest, expertise and trust of an user. 
   * 
   * @param int $user_id The user id
   * @param array $subject_matters Array of array containing the information for each subject matter
   */
  public static function saveRelationShips($user_id, $subject_matters) {
    if (is_array($subject_matters)) {
      self::ensureRelationShips($user_id);
      
      $db_subject_matters = UserToSubjectMatter::model()->findAllByAttributes(array("user_id" => $user_id));
      
      foreach ($db_subject_matters as $model) {
        foreach ($subject_matters as $subject_matter_id => $data) {
          if ($model->subject_matter_id == $subject_matter_id) {
            if (is_array($data)) {
              $found = false;
              if (array_key_exists("interest", $data)) {
                $found = true;
                $model->interest = self::cleanValue($data["interest"]);
              }
                
               
              if (array_key_exists("expertise", $data)) {
                $found = true;
                $model->expertise = self::cleanValue($data["expertise"]);
              }
              
              if (array_key_exists("trust", $data)) {
                $found = true;
                $model->trust = self::cleanValue($data["trust"]);
              }
              
              if ($found)
                $model->save();
            }
            continue;
          }
        }
      }
    }
  }  
  
  /**
   * Sanitize the passed value. 
   * 
   * @param mixed $value the value to be sanitized
   * @param int $min The minimum value the given value can have
   * @param int $max The maximum value teh given value can have
   * @param int $default If the given value is smaller than $min or larger then $max replace it with this value
   * @return int The sanitized value
   * ~
   */
  private static function cleanValue($value, $min=0, $max=100, $default=0) {
    $value = (int)$value;
    
    if ($value < $min || $value > $max)  
      $value = $default;
    
    return $value;
  }
  
  /**
   * Checks if the user subject matter relation ships for all existing subject matters are created
   * 
   * @param int $user_id The id of the user for whom the relationships should be ensured
   */
  public static function ensureRelationShips($user_id) {
    $subjectMatters = Yii::app()->db->createCommand()
                      ->select('sm.id')
                      ->from('{{subject_matter}} sm')
                      ->where('sm.id NOT IN (SELECT usm.subject_matter_id FROM user_to_subject_matter usm WHERE usm.user_id=:userID)', array('userID' => $user_id))
                      ->queryAll();
    if ($subjectMatters) {
      foreach ($subjectMatters as $subjectMatter) {
        $model = new UserToSubjectMatter;
        $model->user_id = $user_id;
        $model->subject_matter_id = $subjectMatter["id"];
        $model->trust = 0;
        $model->expertise = 0;
        $model->interest = 0;
        $model->save();
      }
    }
  }
  
  /**
   * List the subject matters for the user
   * 
   * @param int $user_id The id of the user for whom the list should be generated
   * @param boolean $hide_all hide all subject matter
   * @return mixed null if no values or array of objects [{id, name, interest, expertise, trust}, ... ]
   */
  public static function listForUser($user_id, $hide_all=true) {
    if ($hide_all) {
      $subjectMatters = Yii::app()->db->createCommand()
                      ->select('sm.id, sm.name, usm.interest, usm.expertise, usm.trust')
                      ->from('{{user_to_subject_matter}} usm')
                      ->rightJoin('{{subject_matter}} sm', 'sm.id=usm.subject_matter_id')
                      ->where(array('and', 'sm.id<> 1', 'usm.user_id=:userID'), array('userID' => $user_id))
                      ->order('sm.name')
                      ->queryAll();
    } else {
      $subjectMatters = Yii::app()->db->createCommand()
                      ->select('sm.id, sm.name, usm.interest, usm.expertise, usm.trust')
                      ->from('{{user_to_subject_matter}} usm')
                      ->rightJoin('{{subject_matter}} sm', 'sm.id=usm.subject_matter_id')
                      ->where('usm.user_id=:userID', array('userID' => $user_id))
                      ->order('sm.name')
                      ->queryAll();
    }
    
                      
    if ($subjectMatters) {
      $sm = array();
      foreach ($subjectMatters as $subjectMatter) {
        $sm[] = (object)$subjectMatter;
      }
      return $sm;
    } else {
      return null;
    }
  }
  
  /**
   * List the subject matters values for the user filtered by images. This method returns only subject matters 
   * that belong to the image's image sets
   * 
   * @param int $user_id The id of the user for whom the list should be generated
   * @param array $image_ids list of images for which you would like to retrieve the related subject matters user values
   * @return array null if no values or array of arrays [[image.id, usm.subject_matter_id, usm.interest, usm.expertise, usm.trust], ... ]
   */
  public static function listForUserAndImages($user_id, $image_ids) {
    $command = Yii::app()->db->createCommand()
                  ->select('i.id as image_id, usm.subject_matter_id, usm.interest, usm.expertise, usm.trust')
                  ->from('{{image}} i')
                  ->rightJoin('{{image_set_to_image}} is2i', 'is2i.image_id=i.id')
                  ->rightJoin('{{image_set_to_subject_matter}} is2sm', 'is2sm.image_set_id=is2i.image_set_id')
                  ->rightJoin('{{user_to_subject_matter}} usm', 'usm.subject_matter_id=is2sm.subject_matter_id')
                  ->where(array('and', 'usm.user_id=:userID', array(  'in', 'i.id', array_values($image_ids))), array('userID' => $user_id));
    $command->distinct = true;
    return $command->queryAll();
  }
  
  /**
   * List the subject matters MAX(values) for the user filtered by images. This method returns only subject matters 
   * that belong to the image's image sets. It will only return one row per image. 
   * 
   * @param int $user_id The id of the user for whom the list should be generated
   * @param array $image_ids list of images for which you would like to retrieve the related subject matters user values
   * @return array null if no values or array of arrays [[image.id, usm.subject_matter_id, usm.interest, usm.expertise, usm.trust], ... ]
   */
  public static function listMAXForUserAndImages($user_id, $image_ids) {
    return Yii::app()->db->createCommand()
                  ->select('i.id as image_id, MAX(usm.interest) as interest, MAX(usm.expertise) as expertise, MAX(usm.trust) as trust')
                  ->from('{{image}} i')
                  ->rightJoin('{{image_set_to_image}} is2i', 'is2i.image_id=i.id')
                  ->rightJoin('{{image_set_to_subject_matter}} is2sm', 'is2sm.image_set_id=is2i.image_set_id')
                  ->rightJoin('{{user_to_subject_matter}} usm', 'usm.subject_matter_id=is2sm.subject_matter_id')
                  ->where(array('and', 'usm.user_id=:userID', array(  'in', 'i.id', array_values($image_ids))), array('userID' => $user_id))
                  ->group('i.id')
                  ->queryAll();
  }
}