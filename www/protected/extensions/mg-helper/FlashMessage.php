<?php
/**
 * Helper class to handle flash dialogs to the user
 * 
 * @autor Vincent Van Uffelen
 * @link http://www.tiltfactor.org
 * @licence AGPL 3.0
 *  
 */
class FlashMessage {
    
    /**
     * Adds a message that will be shown to the user 
     * 
     * @param string $scope the scope of the message (success|warning|error)
     * @param string $message the message to be displayed to the user
     * @param boolean $fixed the message fades out after a certain interval if false
     */
    public static function add($scope, $message, $fixed=FALSE) {
      Yii::app()->user->setflash(uniqid(), array('scope' => $scope, 'message' => $message, 'fixed' => $fixed) );
    }
}