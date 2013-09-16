<?php
/**
 *
 * @package
 */
class ImageController extends Controller
{

    public function actionScaled($token,$name,$width,$height){
        $institutionToken = Yii::app()->fbvStorage->get("token");

        $verifyToken = md5($institutionToken."_".$width."_".$height."_name");
        if($verifyToken == $token){
            $path= realpath(Yii::app()->getBasePath() . Yii::app()->fbvStorage->get("settings.app_upload_path"));
            $imgName = MGHelper::createScaledMedia($name, "", "scaled", $width, $height, 80, 10);

            $file = $path . "/scaled/" . $imgName;
            $request = Yii::app()->getRequest();
            $request->sendFile($imgName,file_get_contents($file));
        }
    }
}
