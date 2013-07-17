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

class WordCheckPlugin extends MGWeightingPlugin{
  public $enableOnInstall = true;
  public $hasAdmin = true;
  
  function score(&$game, &$game_model, &$tags, $score) {
    $TrueWordScore = 2;
    $FalseWordScore = 0;
    // what should this point to?
    $python_file = Yii::getPathOfAlias('ext.nlp.python') . DIRECTORY_SEPARATOR .
      "DictCheck.py";
    $is_word = "";
    
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

          // The path of the python file and the string.
          $command = "python $python_file $escaped_tag 2>&1";
          // opens the connection between the python and php files
          
          // reads from the buffer
          $pid = popen( $command, "r");
          $is_word = substr(fread($pid, 256), 0, -1);				
          pclose($pid);

          // add some error message
          // will display true or false if the tag is an actual word based 
          // on going through pyenchant library
          // trigger_error($tag . $is_word);
          
          //change the scores based on the outcome
          switch($is_word){
          case "True":
            $this->addScore($tags[$image_id][$tag], $TrueWordScore);
            $score = $score + $TrueWordScore;
            break;
            
          case "False":
            $this->addScore($tags[$image_id][$tag], $FalseWordScore);
            $score = $score + $FalseWordScore;
            break;
          }
        }
      }
      break; // we expect only one submission.
    }
    return $score;
  }
}
