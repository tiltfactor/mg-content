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

Yii::import('ext.php-metadata-toolkit.XMPAppend');

class ImagesExportPlugin extends MGExportPlugin {
  public $enableOnInstall = true;

  private $output_directory = NULL;

  function init() {
    parent::init();
  }

  function form(&$form, &$model) {
    $legend = CHtml::tag("legend", array(),
                         Yii::t('app', 'Plugin: Images Export'));
    
    $value = ((isset($_POST['ExportForm']) &&
               isset($_POST['ExportForm']['ImagesExportPlugin']) &&
               isset($_POST['ExportForm']['ImagesExportPlugin']['active'])) ?
              $_POST['ExportForm']['ImagesExportPlugin']['active'] :
              1);
    $label = CHtml::label(Yii::t('app', 'Active'),
                          'ExportForm_ImagesExportPlugin_active');
    
    $buttons= CHtml::radioButtonList( 
      "ExportForm[ImagesExportPlugin][active]", 
      $value, 
      MGHelper::itemAlias("yes-no"), 
      array("template" => '<div class="checkbox">{input} {label}</div>',
            "separator" => ""));
    
    return CHtml::tag("fieldset", array(),
                      $legend .
                      '<div class="row">' . $label . $buttons .
                      '<div class="description">' .
                      Yii::t('app',
                             "Export images in a zipped-up directory.") .
                      '</div></div>');
  }
  
  // Provide pieces of information about the install for embedding
  // into files and images.
  function systemInformation() {
   	return array(// version
                 Yii::app()->params['version'],
                 // format
                 Yii::app()->params['tags_csv_format'],
                 // date
                 date("r"),
                 // system
                 "some.university.edu/mg/ " .
                 "(TODO: Source the correct value here)");
  }
  
  function preProcess(&$model, &$command, $tmp_folder) {
    // Create the output directory for the images.
    $d = $tmp_folder . "images/";
    if(mkdir($d)) {
      $this->output_directory = $d;
    } else {
      // Can we throw an exception here?
    }
    
    list($version, $format, $date, $system) = $this->systemInformation();
    
    // Include a brief note in this new directory.
    
    $note = <<<EOT
# This directory contains an export of images from an installation of
# Metadata Games, a metadata tagging system from Tiltfactor Laboratory.
# For more information, see http://tiltfactor.org/mg/
#
# The export process formats the tags stored in mg for each image, and
# appends that formatted string to the XMP dc:description field as
# embedded metadata.
#
# This Export
# ------------
# Version: metadatagames_$version
# Format: $format
# Date: $date
# System: $system
#

EOT;

    file_put_contents ($this->output_directory ."/README.txt", $note);
  }
  
  function process(&$model, &$command, $tmp_folder, $image_id) {
    error_log("starting to process");

    // Just stubbed-in for now.
  }
  
}

?>
