<?php
/**
 * PlayerSubjectMatter class file
 *
 * @author Vincent Van Uffelen <novazembla@gmail.com>
 * @link http://www.metadatagames.com/
 * @copyright Copyright &copy; 2008-2011 Tiltfactor
 * @license http://www.metadatagames.com/license/
 */

/**
 * PlayerSubjectMatter provides the ui to show and update interest, expertise, and trust for each 
 * existing subject matter for one user
 *
 * @author Vincent Van Uffelen <novazembla@gmail.com>
 * @since 1.0
 */
Yii::import('zii.widgets.CPortlet');
 
class PlayerSubjectMatter extends CPortlet
{
  /**
   * @var int the user id of whom the scores should be retrieved.  
   */  
  public $user_id;
  
  /**
   * @var boolean If true to edit the interest values.  
   */  
  public $update = false; 
  
  /**
   * @var boolean If true show trust/expertise and allow to edit the values.  
   */  
  public $admin = false; 
    
  public function init() {
    parent::init();
    
    $this->title=Yii::t('app', "Subject Matters");
  }
 
  protected function renderContent() {
    if ($this->user_id) {
      UserToSubjectMatter::ensureRelationShips($this->user_id);
      
      $subject_matters = UserToSubjectMatter::listForUser($this->user_id);
      
      if ($this->update) {
        $this->render('playerSubjectMatterForm', array(
          'subject_matters' => $subject_matters,
          'admin' => $this->admin,
        ));
      } else {
        $this->render('playerSubjectMatterView', array(
          'subject_matters' => $subject_matters,
          'admin' => $this->admin,
        ));
      }
    }
  }
}