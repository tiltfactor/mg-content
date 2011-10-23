<?php
/**
 * PlayerBadges class file
 *
 * @author Vincent Van Uffelen <novazembla@gmail.com>
 * @link http://www.metadatagames.com/
 * @copyright Copyright &copy; 2008-2011 Tiltfactor
 * @license http://www.metadatagames.com/license/
 */

/**
 * PlayerBadges provides a small widget that lists the badges acquired by the user
 *
 * @author Vincent Van Uffelen <novazembla@gmail.com>
 * @since 1.0
 */
Yii::import('zii.widgets.CPortlet');
 
class AwardedBadges extends CPortlet
{
  public function init() {
    $this->title=Yii::t('app', "Awarded Badges");
    
    parent::init(); // it is important to call this method after you've assigned any new values
  }
 
  protected function renderContent() {
    if (Yii::app()->user->isGuest) {  
      $badges = GamesModule::getAwardedBadges();
      
      $this->render('awardedBadges', array(
        'badges' => $badges
      ));
    }
  }
}