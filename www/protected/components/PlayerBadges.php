<?php
/**
 * PlayerBadges class file
 *
 * @author Vincent Van Uffelen <novazembla@gmail.com>
 * @link http://www.metadatagames.com/
 * @copyright Copyright &copy; 2008-2012 Tiltfactor
 * @license http://www.metadatagames.com/license/
 * @package MG
 */

/**
 * PlayerBadges provides a small widget that lists the badges acquired by the user
 *
 * @author Vincent Van Uffelen <novazembla@gmail.com>
 * @since 1.0
 */
Yii::import('zii.widgets.CPortlet');
 
class PlayerBadges extends CPortlet
{
  public function init() {
    $this->title=Yii::t('app', "Your Badges");
    
    parent::init(); // it is important to call this method after you've assigned any new values
  }
 
  protected function renderContent() {
    if ($user_id = Yii::app()->user->id) {  
      $games = GamesModule::getPlayerScores($user_id);
      $badges = GamesModule::getBadges();
      
      $user_score = 0;
      
      if ($games) {
        foreach ($games as $game) {
          $user_score += $game->score;
        }
      }
      
      $this->render('playerBadges', array(
        'user_score' => $user_score,
        'badges' => $badges
      ));
    }
  }
}