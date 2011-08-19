<?php

class NLSClientScriptBehaviour extends CBehavior {
    public function attach($owner){
        $owner->attachEventHandler('onBeginRequest', array($this, 'beginRequest'));
    }

    public function beginRequest(CEvent $event){
      if (!Yii::app()->request->isAjaxRequest) {
        Yii::app()->clientScript->clearCache();
      }
    }
}