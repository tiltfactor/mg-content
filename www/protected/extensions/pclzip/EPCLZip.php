<?php
/**
 * create a zip file with help of the pcl zip library
 * 
 * Please download and install the latest version of pcl zip from the server
 * 
 * Add definition to CWebApplication config file (main.php)
 * 'components'=>array(
 *    ...
 *   'zip'=>array(
 *      'class'=>'application.extensions.zip.EZip',
 *   ),
 *  ...
 *  ),
 * 
 * Usage in your controller:
 * $pclzip = Yii::app()->pclzip;
 * $pclzip->createZip($folderToZip, $fileDestination);
 *
 * @author Vincent Van Uffelen
 * @version 0.1
 */
class EPCLZip extends CApplicationComponent {
  
  /**
   * create a zip file
   * 
   * @param string $src The source folder that should be zipped
   * @param string $dest The file path & name of the zip file to be created
   * @return boolean TRUE if file has been created
   */
  public function createZip($src, $dest) {
    $flag = false;
    include_once('pclzip/pclzip.lib.php');
    $archive = new PclZip($dest);
    $flag = ($archive->create($src, PCLZIP_OPT_REMOVE_ALL_PATH) !== 0);
    return $flag; 
  }
  
  /**
   * extract a zip file
   * 
   * @param string $src The location of the zip archive to be extracted
   * @param string $dest The file path & name of the zip file to be extracted to
   * @return mixed false or array of extracted files
   */
  public function extractZip($src, $dest) {
    $flag = false;
    include_once('pclzip/pclzip.lib.php');
    $archive = new PclZip($src);
    
    if (is_dir($dest)) {
      $list = $archive->extract(PCLZIP_OPT_PATH, $dest ,
                           PCLZIP_OPT_REMOVE_ALL_PATH);
      
      if ($list && is_array($list) && count($list) > 0) {
        return $list;
      } 
    }
    return $flag; 
  }
}
  