<?php
/**
 * PlayerScores class file
 *
 * @author Vincent Van Uffelen <novazembla@gmail.com>
 * @link http://www.metadatagames.com/
 * @copyright Copyright &copy; 2008-2011 Tiltfactor
 * @license http://www.metadatagames.com/license/
 */

/**
 * PlayerScores provides a small widget that lists the scores of the current player for each active game
 *
 * @author Vincent Van Uffelen <novazembla@gmail.com>
 * @since 1.0
 */
Yii::import('zii.widgets.CPortlet');
 
class PlayerScores extends CPortlet
{
  /**
   * @var int the user id of whom the scores should be retrieved.  
   */  
  public $user_id;
  
  /**
   * @var boolean If true list only active games.  
   */  
  public $active = true;
    
    
  public function init() {
    $this->title=Yii::t('app', "Your Scores");
    
    if (is_null($this->user_id)) 
      $this->user_id = Yii::app()->user->id;
    
    parent::init(); // it is important to call this method after you've assigned any new values
  }
 
  protected function renderContent() {
    if ($this->user_id) {
      $games = GamesModule::getPlayerScores($this->user_id, $this->active);
      
      if (is_null($games))
        $games = array();
      
      $this->render('playerScores', array(
        'games' => $games
      ));  
    }
  }
}