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
   * @return mixed null if no values or array of objects [{id, name, interest, expertise, trust}, ... ]
   */
  public static function listForUser($user_id) {
    $subjectMatters = Yii::app()->db->createCommand()
                      ->select('sm.id, sm.name, usm.interest, usm.expertise, usm.trust')
                      ->from('{{user_to_subject_matter}} usm')
                      ->rightJoin('{{subject_matter}} sm', 'sm.id=usm.subject_matter_id')
                      ->where('usm.user_id=:userID', array('userID' => $user_id))
                      ->order('sm.name')
                      ->queryAll();
                      
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
}