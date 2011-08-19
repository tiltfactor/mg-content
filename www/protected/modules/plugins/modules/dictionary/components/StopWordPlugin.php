<?php
/**
 * This is the implementation of a dictionary plugin. 
 * This plugin provides a stopword list.
 *  
 */

class StopWordPlugin extends DictionaryPlugin  {
  
  function lookup(array $tags, $user_id=NULL, $image_id=NULL) {
    foreach ($tags as $key=>$tag) {
      if ($tag == "x") {
        Yii::trace("aha");
      }
    }
    
    return $tags;
  }
}
