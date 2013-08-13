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

/**
 * Implentation of the an export plugin. It allows to export the tags, 
 * tag uses for medias in a CSV file (tab separated)
 */
Yii::import('ext.CSVExport.CSVExport');

class CSVExportPlugin extends MGExportPlugin {
  public $enableOnInstall = true;
     
  function init() {
    parent::init();
  }
  
  /**
   * Adds a checkbox that allows to activate/disactivate the use of the plugin on the 
   * export form.
   * 
   * @param object $form the GxActiveForm rendering the export form
   * @param object $model the ExportForm instance holding the forms values
   */
  function form(&$form, &$model) {
    $this->activeByDefault = true;
    $legend = CHtml::tag("legend", array(),
                         Yii::t('app', 'Plugin: CSV Export'));
    
    $value = $this->is_active() ? 1 : 0;
    $label = CHtml::label(Yii::t('app', 'Active'),
                          'ExportForm_CSVExportPlugin_active');
    
    $buttons= CHtml::radioButtonList( 
        "ExportForm[CSVExportPlugin][active]", 
        $value, 
        MGHelper::itemAlias("yes-no"), 
        array("template" => '<div class="checkbox">{input} {label}</div>', "separator" => ""));
    
    return CHtml::tag("fieldset", array(), $legend . '<div class="row">' . $label . $buttons . '<div class="description">' . Yii::t('app', "Export tag uses, tag weights, tags, (and usernames) as tab separated CSV file") . '</div></div>');
  }
  
  /**
   * Create the CSV export file in the temporary folder and add the header row in this 
   * file
   * 
   * @param object $model the ExportForm instance
   * @param object $command the CDbCommand instance holding all information needed to retrieve the medias' data
   * @param string $tmp_folder the full path to the temporary folder
   */
  function preProcess(&$model, &$command, $tmp_folder) {
    if(!$this->is_active()) {
      return 0;
    }

    if ($model->option_list_user == 1) {
      file_put_contents ($tmp_folder . $model->filename . '.csv', "ImageId\tTagUseCnt\tWeightMin\tWeightMax\tWeightAVG\tWeightSum\tTag\tmageName\tUserName\n");
    } else {
      file_put_contents ($tmp_folder . $model->filename . '.csv', "ImageId\tTagUseCnt\tWeightMin\tWeightMax\tWeightAVG\tWeightSum\tTag\tImageName\n");
    }
  }
  
  /**
   * Retrieves the compound use statistics for a media (according to the settings)
   * on the export form and adds it to the export file
   * 
   * @param object $model the ExportForm instance
   * @param object $command the CDbCommand instance holding all information needed to retrieve the medias' data
   * @param string $tmp_folder the full path to the temporary folder
   * @param int $media_id the id of the media that should be exported
   */
  function process(&$model, &$command, $tmp_folder, $media_id) {
    if(!$this->is_active()) {
      return 0;
    }

    if ($model->option_list_user == 1) {
      $command->selectDistinct('tu.media_id, COUNT(tu.id) tu_count, MIN(tu.weight) w_min, MAX(tu.weight) w_max, AVG(tu.weight) w_avg, SUM(tu.weight) as w_sum, t.tag, i.name, u.username');
      
      if (trim($command->group) != "") {
        $groups = array();
        foreach (explode(',', $command->group) as $group) {
          $groups[ str_replace('`', '', $group)] = 1;
        }
        $groups['tu.media_id'] = 1;
        $groups['tu.tag_id'] = 1;
        $command->group = implode(',', array_keys($groups));
      } else {
        $command->group = 'tu.media_id, tu.tag_id';
      }
      
    } else {
      $command->selectDistinct('tu.media_id, COUNT(tu.id) tu_count, MIN(tu.weight) w_min, MAX(tu.weight) w_max, AVG(tu.weight) w_avg, SUM(tu.weight) as w_sum, t.tag, i.name');
    }
    $command->where(array('and', $command->where, 'tu.media_id = :mediaID'), array(":mediaID" => $media_id));
    $command->order('tu.media_id, t.tag');
    
    $info = $command->queryAll();
    $c = count($info);
    $rows = "";
    for($i=0;$i<$c;$i++) {
      $row = "";
      $row .= $info[$i]['media_id'] . "\t";
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

?>
