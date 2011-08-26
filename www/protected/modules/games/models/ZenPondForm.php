<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class ZenPondForm extends CFormModel
{
	public $play_once_and_move_on = false;
  public $turns = 5;
  public $score_new = 2;
  public $score_match = 1;
  public $score_expert = 3;
  
  
}
