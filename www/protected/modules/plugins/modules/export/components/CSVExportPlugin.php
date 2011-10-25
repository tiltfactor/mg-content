<?php

/**
 * xxx
 */
Yii::import('ext.CSVExport.CSVExport');

class CSVExportPlugin extends MGExportPlugin {
  public $enableOnInstall = true;
     
  function init() {
    parent::init();
  }
  
  function form(&$form, &$model) {
    $legend = CHtml::tag("legend", array(), Yii::t('app', 'Plugin: CSV Export'));
    
    $value = ((isset($_POST['ExportForm']) && isset($_POST['ExportForm']['CSVExportPlugin']) && isset($_POST['ExportForm']['CSVExportPlugin']['active']))? $_POST['ExportForm']['CSVExportPlugin']['active'] : 1);
    $label = CHtml::label(Yii::t('app', 'Active'), 'ExportForm_CSVExportPlugin_active');
    
    $buttons= CHtml::radioButtonList( 
        "ExportForm[CSVExportPlugin][active]", 
        $value, 
        MGHelper::itemAlias("yes-no"), 
        array("template" => '<div class="checkbox">{input} {label}</div>', "separator" => ""));
    
    return CHtml::tag("fieldset", array(), $legend . '<div class="row">' . $label . $buttons . '<div class="description">' . Yii::t('app', "Export tag uses, tag weights, tags, (and usernames) as tab separated CSV file") . '</div></div>');
  }
  
  function preProcess(&$model, &$command, $tmp_folder) {
    if ($model->option_list_user == 1) {
      file_put_contents ($tmp_folder . $model->filename . '.csv', "ImageId\tTagUseCnt\tWeightMin\tWeightMax\tWeightAVG\tWeightSum\tTag\tmageName\tUserName\n");
    } else {
      file_put_contents ($tmp_folder . $model->filename . '.csv', "ImageId\tTagUseCnt\tWeightMin\tWeightMax\tWeightAVG\tWeightSum\tTag\tImageName\n");
    }
  }
  
  function process(&$model, &$command, $tmp_folder, $image_id) {
    if ($model->option_list_user == 1) {
      $command->selectDistinct('tu.image_id, COUNT(tu.id) tu_count, MIN(tu.weight) w_min, MAX(tu.weight) w_max, AVG(tu.weight) w_avg, SUM(tu.weight) as w_sum, t.tag, i.name, u.username');
      
      if (trim($command->group) != "") {
        $groups = array();
        foreach (explode(',', $command->group) as $group) {
          $groups[ str_replace('`', '', $group)] = 1;
        }
        $groups['tu.image_id'] = 1;
        $groups['tu.tag_id'] = 1;
        $command->group = implode(',', array_keys($groups));
      } else {
        $command->group = 'tu.image_id, tu.tag_id';
      }
      
    } else {
      $command->selectDistinct('tu.image_id, COUNT(tu.id) tu_count, MIN(tu.weight) w_min, MAX(tu.weight) w_max, AVG(tu.weight) w_avg, SUM(tu.weight) as w_sum, t.tag, i.name');
    }
    $command->where(array('and', $command->where, 'tu.image_id = :imageID'), array(":imageID" => $image_id));
    $command->order('tu.image_id, t.tag');
    
    $info = $command->queryAll();
    $c = count($info);
    $rows = "";
    for($i=0;$i<$c;$i++) {
      $row = "";
      $row .= $info[$i]['image_id'] . "\t";
      $row .= $info[$i]['tu_count'] . "\t";
      $row .= $info[$i]['w_min'] . "\t";
      $row .= $info[$i]['w_max'] . "\t";
      $row .= number_format($info[$i]['w_avg'], 2) . "\t";
      $row .= $info[$i]['w_sum'] . "\t";
      $row .= $info[$i]['tag'] . "\t";
      $row .= $info[$i]['name'] . "\t";
      if ($model->option_list_user == 1) {
        if (is_null( $info[$i]['username'] )) {
          $row .= Yii::t('app', 'Guest(s)') . "\t";
        } else {
          $row .= $info[$i]['username'] . "\t";
        } 
      }
      $rows .= $row . "\n";
    }
    
    if ($rows != "") {
      file_put_contents ($tmp_folder . $model->filename . '.csv' , $rows, FILE_APPEND ); 
    }
    
  }  
}
