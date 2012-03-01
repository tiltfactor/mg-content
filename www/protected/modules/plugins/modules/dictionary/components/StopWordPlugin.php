<?php
/**
 * This is the implementation of a dictionary plugin. 
 * This plugin provides a stopword list.
 *  
 */

class StopWordPlugin extends MGDictionaryPlugin  {
  public $hasAdmin = TRUE;
  public $accessRole = "editor";
  public $enableOnInstall = true;
  
  /**
   * Checks whether one or more of the given tags are in the stop word list. It will return the tags
   * as an array in this form
   * 
   * array(
   *  'tag1', true/false // true if found
   *  'tag2', true/false // true if found
   *   ...
   * )
   * 
   * @param object $game The game object
   * @param object $game_model The game model
   * @param array $tags the tags to be looked up as a single dimension array array('tag1', 'tag2', ...)
   * @param array $user_ids optional user_ids that might influence the lookup
   * @param array $image_ids optional image_ids that might influence the lookup
   * @return array the checked tags
   */
  function lookup(&$game, &$game_model, $tags, $user_id=null, $image_ids=null) {
    $tags_return = array();
    $tags_lookup = array();
    
    if (is_array($tags) && count($tags) > 0) {
      foreach ($tags as $tag) {
        $tags_return[strtolower($tag)] = false;
        $tags_lookup[] = $tag;
      }
      
      $stopped_words = Yii::app()->db->createCommand()
                        ->select('s.word')
                        ->from('{{stop_word}} s')
                        ->where(array('in', 's.word', array_values($tags_lookup))) 
                        ->queryAll();
      
      if ($stopped_words) {
        foreach ($stopped_words as $stop_word) {
          if (array_key_exists(strtolower($stop_word["word"]), $tags_return)) {
            $tags_return[strtolower($stop_word["word"])] = true;
          }
        }  
      }
    }
    
    return $tags_return;
  }
  
  /**
   * Adds a new tag to the stopword list
   * 
   * @param string $tag the tag to be stored
   * @param string $info a short info about the tag.
   * return boolean true if the tag has been successfully stored
   */
  function add($tag, $info) {
    $model = new StopWord;
    $model->word = $tag;
    $model->counter = 0; 
    $model->modified = date('Y-m-d H:i:s'); 
    $model->source = $info;
    
    if (!$model->save()) {
      print_r($model->getErrors());
    } 
  }
  
  /**
   * Retrieves the registered stop words and compares them with the turns tags. If a tag 
   * is a stopword the tag's weight is set to 0 and the type as marked as 'stopword'. Each
   * stopped tag will increase the stop words counter.  
   * 
   * @param object $game The game object
   * @param object $game_model The game model
   * @param array $tags the tags to be looked up as a single dimension array array('tag1', 'tag2', ...)
   */
  function setWeights(&$game, &$game_model, $tags) {
    $stop_words = StopWord::getStopWordList();
    
    if (count($stop_words)) {
      $arr_stopwords = array();
      $arr_used_stopwords = array();
      
      foreach ($stop_words as $stop_word) {
        $arr_stopwords[strtolower($stop_word["word"])] = $stop_word;
      }
      
      foreach ($tags as $image_id => $image_tags) {
        foreach ($image_tags as $tag) {
          if (array_key_exists(strtolower($tag["tag"]), $arr_stopwords)) { // tag is in stopword list
            $tags[$image_id][$tag["tag"]]["type"] = 'stopword';
            $this->adjustWeight($tags[$image_id][$tag["tag"]], 0);
            $arr_used_stopwords[] = $arr_stopwords[strtolower($tag["tag"])]["id"];
          }
        }
      }
      
      if (count($arr_used_stopwords) > 0) { // update stop word counter
        $criteria = new CDbCriteria;
        $criteria->addInCondition("id", $arr_used_stopwords);
        StopWord::model()->updateCounters(array("counter"=>1), $criteria);
      }
    }
    return $tags;
  }
  
  /**
   * Inserts the initial stop word list to the system.
   */
  function install() {
    // taken from http://truereader.com/manuals/onix/stopwords1.html many thanks!
    $stopwords = array(
      'a','about','above','across','after','again','against','all','almost','alone','along','already','also','although','always','among','an','and','another','any','anybody','anyone','anything','anywhere','are','area','areas','around','as','ask','asked','asking','asks','at','away',
      'b','back','backed','backing','backs','be','became','because','become','becomes','been','before','began','behind','being','beings','best','better','between','big','both','but','by',
      'c','came','can','cannot','case','cases','certain','certainly','clear','clearly','come','could',
      'd','did','differ','different','differently','do','does','done','down','down','downed','downing','downs','during',
      'e','each','early','either','end','ended','ending','ends','enough','even','evenly','ever','every','everybody','everyone','everything','everywhere',
      'f','face','faces','fact','facts','far','felt','few','find','finds','first','for','four','from','full','fully','further','furthered','furthering','furthers',
      'g','gave','general','generally','get','gets','give','given','gives','go','going','good','goods','got','great','greater','greatest','group','grouped','grouping','groups',
      'h','had','has','have','having','he','her','here','herself','high','higher','highest','him','himself','his','how','however',
      'i','if','important','in','interest','interested','interesting','interests','into','is','it','its','itself',
      'j','just',
      'k','keep','keeps','kind','knew','know','known','knows',
      'l','large','largely','last','later','latest','least','less','let','lets','like','likely','long','longer','longest',
      'm','made','make','making','man','many','may','me','member','members','men','might','more','most','mostly','mr','mrs','much','must','my','myself',
      'n','necessary','need','needed','needing','needs','never','new','new','newer','newest','next','no','nobody','non','noone','not','nothing','now','nowhere','number','numbers',
      'o','of','off','often','old','older','oldest','on','once','one','only','open','opened','opening','opens','or','order','ordered','ordering','orders','other','others','our','out','over',
      'p','part','parted','parting','parts','per','perhaps','place','places','point','pointed','pointing','points','possible','present','presented','presenting','presents','problem','problems','put','puts',
      'q','quite',
      'r','rather','really','right','right','room','rooms',
      's','said','same','saw','say','says','second','seconds','see','seem','seemed','seeming','seems','sees','several','shall','she','should','show','showed','showing','shows','side','sides','since','small','smaller','smallest','so','some','somebody','someone','something','somewhere','state','states','still','still','such','sure',
      't','take','taken','than','that','the','their','them','then','there','therefore','these','they','thing','things','think','thinks','this','those','though','thought','thoughts','three','through','thus','to','today','together','too','took','toward','turn','turned','turning','turns','two',
      'u','under','until','up','upon','us','use','used','uses',
      'v','very',
      'w','want','wanted','wanting','wants','was','way','ways','we','well','wells','went','were','what','when','where','whether','which','while','who','whole','whose','why','will','with','within','without','work','worked','working','works','would',
      'x',
      'y','year','years','yet','you','young','younger','youngest','your','yours',
      'z');  
    foreach($stopwords as $stopword) {
      $model = new StopWord;
      $model->modified = date('Y-m-d H:i:s');
      $model->source = 'import';
      $model->word = $stopword;
      $model->counter = 0;
      $model->save();
    }
    return TRUE;
  }
}
