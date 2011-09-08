<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class ZenTagPlayOnceMoveOn extends ZenTag implements MGGameModelInterface
{
  public $active = 0; //active will never be saved in the games FBVStorage settings it is just a handler for the Game database entry
  public $name = "Zen Tag (Play Once Move On)";
	public $play_once_and_move_on = 1;
  public $play_once_and_move_on_url = "http://www.metadatagames.com";
  
  public function getGameID() {
    return "ZenTagPlayOnceMoveOn";    
  }
  
}
