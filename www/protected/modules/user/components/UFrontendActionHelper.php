<?php
/**
 * This file contains the UFrontendActions class.
 *
 * @author Vincent Van Uffelen
 */

/**
 * UFrontendActions is a collection of user related actions that are shared between the user module and the api.
 *
 * @author Vincent Van Uffelen <novazembla@gmail.com>
 */
class UFrontendActionHelper extends CApplicationComponent {
  
  public function passwordRecovery($controller) {
      
    $form = new UserRecoveryForm;
    if (Yii::app()->user->id) {
      
      // user is logged in we don't have to do anythings
      if(Yii::app()->getRequest()->getIsAjaxRequest()) {
        throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
      } else {
        $controller->redirect(Yii::app()->controller->module->returnUrl);  
      } 
      
    } else {
        
      $email = ((isset($_GET['email']))? $_GET['email']:'');
      $activekey = ((isset($_GET['activekey']))? $_GET['activekey']:'');
      
      
      if ($email && $email) {
        $form2 = new UserChangePassword;
        $find = User::model()->notsafe()->findByAttributes(array('email'=>$email));
        
        if(isset($find) && $find->activekey==$activekey) {
          if(isset($_POST['UserChangePassword'])) {
            $form2->attributes=$_POST['UserChangePassword'];
              
            if($form2->validate()) {
              $find->password = Yii::app()->controller->module->encrypting($form2->password);
              $find->activekey=Yii::app()->controller->module->encrypting(microtime().$form2->password);
                
              if ($find->status==0) {
                $find->status = 1;
              }
              $find->save();
              Flash::add('success', UserModule::t("New password is saved. Pleas login you can now login using the new password."));
              $controller->redirect(Yii::app()->controller->module->loginUrl);
            } 
          } 
          if (count($form2->errors) == 0)
            Flash::add('success', UserModule::t("You have requested a password reset. Please choose a new password with help of the form below."), true);
          
          $controller->render('changepassword',array('form'=>$form2));
        
        } else {
          Flash::add('error', UserModule::t("Incorrect recovery link."));
          $controller->redirect(Yii::app()->controller->module->recoveryUrl);
        }
      
      } else {
        
        $valid = false;
        if(Yii::app()->getRequest()->getIsAjaxRequest()) {
          if(isset($_POST['login_or_email'])) {
            $form->login_or_email = $_POST['login_or_email'];
            $valid = $form->validate();
          }
        } else {
          if(isset($_POST['UserRecoveryForm'])) {
            $form->attributes=$_POST['UserRecoveryForm'];
            $valid = $form->validate();
          }
        }

        if ($valid) {
          $user = User::model()->notsafe()->findbyPk($form->user_id);
          
          $activation_url = 'http://' . $_SERVER['HTTP_HOST'].$controller->createUrl(implode(Yii::app()->controller->module->recoveryUrl), array("activekey" => $user->activekey, "email" => $user->email));
          
          $message = new YiiMailMessage;
          $message->view = 'userPasswordRestore';
          $message->setSubject(UserModule::t("You have requested a password reset for {site_name}", array(
            '{site_name}'=>Yii::app()->fbvStorage->get("settings.app_name"),)));
          
          //userModel is passed to the view
          $message->setBody(array(
            'user' => $user,
            'site_name' => Yii::app()->fbvStorage->get("settings.app_name"),
            'activation_url' => $activation_url
          ), 'text/html');
           
          $message->addTo($user->email);
          $message->from = Yii::app()->fbvStorage->get("settings.app_email");
          Yii::app()->mail->send($message);
       
          if(Yii::app()->getRequest()->getIsAjaxRequest()) {
            $data = array();
            $data['status'] = "ok";
            $controller->sendResponse($data);
          } else {
            Yii::app()->user->setFlash('recoveryMessage',UserModule::t("Please check your email. An instructions was sent to your email address."));
            $controller->refresh(); 
          }
       
        } else {
          if(Yii::app()->getRequest()->getIsAjaxRequest()) {
            $data = array();
            $data['status'] = "error";
            $data['errors'] = $form->getErrors();
            $controller->sendResponse($data, 400);
          } else {
            $controller->render('recovery',array('form'=>$form));  
          }
        }
      }
    } 
  }
}
