<?php
/**
 * Top10Players class file
 *
 * @author Vincent Van Uffelen <novazembla@gmail.com>
 * @link http://www.metadatagames.com/
 * @copyright Copyright &copy; 2008-2011 Tiltfactor
 * @license http://www.metadatagames.com/license/
 */

/**
 * Top10Players provides a small widget that lists the top 10 players in the system
 *
 * @author Vincent Van Uffelen <novazembla@gmail.com>
 * @since 1.0
 */
Yii::import('zii.widgets.CPortlet');
 
class Top10Players extends CPortlet
{
  public function init() {
    $this->title=Yii::t('app', "Top 10 Players");
    parent::init();  // it is important to call this method after you've assigned any new values
  }
 
  protected function renderContent() {
    $players = GamesModule::getTopPlayers();
    
    $this->render('top10players', array(
      'players' => $players
    ));
  }
}