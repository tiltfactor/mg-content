<?php
/**
 *
 * @package
 */
class ImageController extends Controller
{

    public function actionScale($token, $name, $width, $height)
    {
        $institutionToken = Yii::app()->fbvStorage->get("token");

        $name = urldecode($name);

        $verifyToken = md5($institutionToken . "_" . $width . "_" . $height . "_" . $name);
        if ($verifyToken == $token) {
            $path = realpath(Yii::app()->getBasePath() . Yii::app()->fbvStorage->get("settings.app_upload_path"));
            $imgName = MGHelper::createScaledMedia($name, "", "scaled", $width, $height, 80, 10);

            $file = $path . "/scaled/" . $imgName;
            $request = Yii::app()->getRequest();
            $request->sendFile($imgName, file_get_contents($file));
        }
    }
}
