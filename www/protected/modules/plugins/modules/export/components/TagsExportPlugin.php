<?php // -*- tab-width:2; indent-tabs-mode:nil -*-
/**
 *
 * @BEGIN_LICENSE
 *
 * Metadata Games - A FOSS Electronic Game for Archival Data Systems
 * Copyright (C) 2011 Mary Flanagan, Tiltfactor Laboratory
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

Yii::import('ext.CSVExport.CSVExport');

class TagsExportPlugin extends MGExportPlugin {
  public $enableOnInstall = true;

  function init() {
    parent::init();
  }

  function form(&$form, &$model) {
    $legend = CHtml::tag("legend", array(),
                         Yii::t('app', 'Plugin: Tags Export'));
    
    $value = ((isset($_POST['ExportForm']) &&
               isset($_POST['ExportForm']['TagsExportPlugin']) &&
               isset($_POST['ExportForm']['TagsExportPlugin']['active'])) ?
              $_POST['ExportForm']['TagsExportPlugin']['active'] :
              1);
    $label = CHtml::label(Yii::t('app', 'Active'),
                          'ExportForm_TagsExportPlugin_active');
    
    $buttons= CHtml::radioButtonList( 
      "ExportForm[TagsExportPlugin][active]", 
      $value, 
      MGHelper::itemAlias("yes-no"), 
      array("template" => '<div class="checkbox">{input} {label}</div>',
            "separator" => ""));
    
    return CHtml::tag("fieldset", array(),
                      $legend .
                      '<div class="row">' . $label . $buttons .
                      '<div class="description">' .
                      Yii::t('app',
                             "Export image tags in a tab-separated CSV file.") .
                      '</div></div>');
  }
  
  function preProcess(&$model, &$command, $tmp_folder) {
    $version = Yii::app()->params['version'];
    $format = Yii::app()->params['tags_csv_format'];
    $date = date("r");
    $system = "some.university.edu/mg/  (TODO: Source the correct value here)";
    
    $header = <<<EOT
# This file contains an export of tag data from an installation of
# Metadata Games, a metadata tagging system from Tiltfactor Laboratory.
# For more information, see http://tiltfactor.org/mg/
#
# This Export:
# ------------
# Version: metadatagames_$version
# Format: $format
# Date: $date
# System: $system
#

EOT;

    // Column labels.
    $labels = array("Image Name", "Tags");
    $labels = join("\t", $labels);
    
    file_put_contents ($tmp_folder . $model->filename . '_tags.csv',
                       $header . $labels . "\n");

  }
  
  function process(&$model, &$command, $tmp_folder, $image_id) {
    $sql = <<<EOT
tu.image_id,
COUNT(tu.id) tu_count,
MIN(tu.weight) w_min,
MAX(tu.weight) w_max,
AVG(tu.weight) w_avg,
SUM(tu.weight) as w_sum,
t.tag,
i.name
EOT;

    $command->selectDistinct($sql);

    $command->where(array('and', $command->where, 'tu.image_id = :imageID'),
                    array(":imageID" => $image_id));
    $command->order('tu.image_id, t.tag');
    
    $info = $command->queryAll();
    $c = count($info);
    $tags = array();

    for($i=0;$i<$c;$i++) {
      $tags[] = $info[$i]['tag'];
    }
    
    if(!empty($tags)) {
      file_put_contents ($tmp_folder . $model->filename . '_tags.csv' ,
                         $info[0]['name'] . "\t" . join(", ", $tags) . "\n",
                         FILE_APPEND ); 
    }

  }  
}

?>
