<?php // -*- tab-width:2; indent-tabs-mode:nil -*-
/**
 *
 * @BEGIN_LICENSE
 *
 * Metadata Games - A FOSS Electronic Game for Archival Data Systems
 * Copyright (C) 2013 Mary Flanagan, Tiltfactor Laboratory
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Affero General Public License
 * as published by the Free Software Foundation, either version 3 of
 * the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this program.  If not, see
 * <http://www.gnu.org/licenses/>.
 *
 * @END_LICENSE
 * 
 */

// Returns 0 if we've seen a similar tag before in this submission set.
class StemmingPlugin extends MGWeightingPlugin{
  public $enableOnInstall = true;
  public $hasAdmin = true;
  
  
  function score(&$game, &$game_model, &$tags, $score) {
    $python_file = Yii::getPathOfAlias('ext.nlp.python') . DIRECTORY_SEPARATOR .
      "DictStem.py";
    $mytag = "";
    $mytags = array();
    $i = 0;
    
    foreach ($game->request->submissions as $submission) {
      foreach ($tags as $image_id => $image_tags) {
	foreach ($image_tags as $tag => $tag_info) {
	  
          // We love our users, but don't trust them to not not try to
          // do naughty things, so we escape any special characters,
          // quotes, etc.. before we pass this information along to be
          // run on the command-line.
          //
          // THIS IS REALLY IMPORTANT! DO NOT TRY TO CIRCUMVENT OR
          // REMOVE THIS MARKUP UNLESS YOU FULLY UNDERSTAND HOW AND
          // WHY IT WORKS, AND ARE 100% SURE THAT THE INPUT IS
          // SANITIZED ELSEWHERE!
          $escaped_tag = escapeshellarg($tag);
	  
	  // the path of the python file and the string
	  $command = "python $python_file $escaped_tag 2>&1";
	  
	  // standard fare for popopen
	  $pid = popen( $command,"r");
	  
	  // we want to add the previous tag
	  $mytags[] = $mytag;
	  // reads the python output and slices out the appropriate word
	  $mytag = substr(fread($pid, 256), 0, -1);
	  pclose($pid);
	  
	  
	  // have we seen this before?
	  switch(in_array($mytag, $mytags)){
	    
	    // returns 0 if we've seen it before
	  case True:
	    return 0;
	    break;
	    
	    // if not, carry on.
	  case False:
	    // return 10;
	    break;
	    
	  }
	  
	}
      }
      break; // We expect only one submission.
    }
    return $score;
  }
}
