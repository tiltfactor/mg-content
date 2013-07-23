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

Yii::import('ext.CSVExport.CSVExport');

class TagsByGameExportPlugin extends MGExportPlugin {
  // Disable until fully tested.
  public $enableOnInstall = false;

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
                         Yii::t('app', 'Plugin: Tags Export by Game'));
    
    $value = $this->is_active() ? 1 : 0;
    $label = CHtml::label(Yii::t('app', 'Active'),
                          'ExportForm_TagsByGameExportPlugin_active');
    
    $buttons= CHtml::radioButtonList( 
      "ExportForm[TagsByGameExportPlugin][active]", 
      $value, 
      MGHelper::itemAlias("yes-no"), 
      array("template" => '<div class="checkbox">{input} {label}</div>',
            "separator" => ""));
    
    return CHtml::tag("fieldset", array(),
                      $legend .
                      '<div class="row">' . $label . $buttons .
                      '<div class="description">' .
                      Yii::t('app',
                             "Export media tags in a tab-separated CSV file, separated by game.") .
                      '</div></div>');
  }
  
  /**
   * Creates the CSV export file in the temporary folder and add the header row  
   * and the statistics for each game in the file.
   * 
   * @param object $model the ExportForm instance
   * @param object $command the CDbCommand instance holding all information needed to retrieve the medias' data
   * @param string $tmp_folder the full path to the temporary folder
   */
  function preProcess(&$model, &$command, $tmp_folder) {
    if(!$this->is_active()) {
      return 0;
    }

    $version = Yii::app()->params['version'];
    $format = Yii::app()->params['tags_by_game_csv_format'];
    $date = date("r");
    $system = "some.university.edu/mg/  (TODO: Source the correct value here)";
    $filepath = $tmp_folder . $model->filename . '_tags_by_game.csv';
    
    $header = <<<EOT
# This file contains an export of tag data from an installation of
# Metadata Games, a metadata tagging system from Tiltfactor Laboratory.
# For more information, see http://tiltfactor.org/mg/
#
# This Export:
# ------------
# Version: metadatagames_$version
# Plugin: TagsByGameExportPlugin
# Format: $format
# Date: $date
# System: $system
#

EOT;

    // Column labels.
    $labels = array("Game", "Collections", "Media Name", "Media ID", "Tags");
    $labels = join("\t", $labels);
    
    file_put_contents ($filepath, $header . $labels . "\n");

    // If particular collections are specified, only query those sets.
    $collections_filter = "";
    if ($model->collections) {
      $collections_filter = "AND collection.id IN (" . join($model->collections, ", ") . ") ";
    }

    $sql = <<<EOT
SELECT game.unique_id AS 'game.uid',
GROUP_CONCAT(DISTINCT collection.name
             ORDER BY collection.name SEPARATOR ', ') AS 'Collections',
media.name,
tag_use.media_id,
GROUP_CONCAT(DISTINCT tag
             ORDER BY tag SEPARATOR ', ') AS Tags
FROM game, game_submission, played_game, collection, collection_to_media,
     tag_use, media, tag
WHERE game_submission.played_game_id = played_game.id
AND played_game.game_id = game.id
AND tag_use.game_submission_id = game_submission.id
AND tag_use.media_id = media.id
AND tag_use.tag_id = tag.id
AND collection_to_media.collection_id = collection.id
AND collection_to_media.media_id = media.id
$collections_filter
GROUP BY media.name
ORDER BY game.unique_id;

EOT;

    // Because we want to sort our output by Game name, we'll need to
    // do our processing in preProcess() instead of in process(),
    // which iterates by media.
    $cmd = Yii::app()->db->createCommand($sql);
        
    $tags_by_game = $cmd->queryAll();
    foreach($tags_by_game AS $row_number => $row) {
      file_put_contents($filepath, join("\t", $row) . "\n", FILE_APPEND);
    }
  }
  
  /**
   * @param object $model the ExportForm instance
   *
   * @param object $command the CDbCommand instance holding all
   * information needed to retrieve the medias' data
   *
   * @param string $tmp_folder the full path to the temporary folder
   *
   * @param int $media_id the id of the media that should be exported
   */
  function process(&$model, &$command, $tmp_folder, $media_id) {

    // NOTE: We won't process medias during the process step because
    // of how we order by game.

  }
}

?>
