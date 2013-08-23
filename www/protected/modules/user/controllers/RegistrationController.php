<?php

class RegistrationController_ extends Controller
{
	public $defaultAction = 'registration';
	
  public function filters() {
    return array( // add blocked IP filter here
       /*'IPBlock',*/
    );
  }

	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return (isset($_POST['ajax']) && $_POST['ajax']==='registration-form')?array():array(
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
		);
	}
	/**
	 * Registration user
	 */
	public function actionRegistration() {
	  MGHelper::setFrontendTheme();
    
    $model = new RegistrationForm;
    $profile=new Profile;
    $profile->regMode = true;
            
  	// ajax validator
  	if(isset($_POST['ajax']) && $_POST['ajax']==='registration-form')
  	{
  		echo UActiveForm::validate(array($model,$profile));
  		Yii::app()->end();
  	}
  	
    if (Yii::app()->user->id) {
    	$this->redirect(Yii::app()->controller->module->profileUrl);
    } else {
    	if(isset($_POST['RegistrationForm'])) {
			$model->attributes=$_POST['RegistrationForm'];
			$profile->attributes=((isset($_POST['Profile'])?$_POST['Profile']:array()));
			if($model->validate()&&$profile->validate())
			{
				$soucePassword = $model->password;
				$model->activekey=UserModule::encrypting(microtime().$model->password);
				$model->password=UserModule::encrypting($model->password);
				$model->verifyPassword=UserModule::encrypting($model->verifyPassword);
				$model->created = date('Y-m-d H:i:s');
        $model->modified = date('Y-m-d H:i:s');
				$model->lastvisit=((Yii::app()->controller->module->loginNotActiv||(Yii::app()->controller->module->activeAfterRegister&&Yii::app()->controller->module->sendActivationMail==false))&&Yii::app()->controller->module->autoLogin)?date('Y-m-d H:i:s'):NULL;
				$model->role='player';
				$model->status=((Yii::app()->controller->module->activeAfterRegister)?User::STATUS_ACTIVE:User::STATUS_NOACTIVE);
				
				if ($model->save()) {
					$profile->user_id=$model->id;
					$profile->save();
					if (Yii::app()->controller->module->sendActivationMail) {
						$activation_url = $this->createAbsoluteUrl('/user/activation/activation',array("activekey" => $model->activekey, "email" => $model->email));
						
						$message = new YiiMailMessage;
            $message->view = 'userRegistrationConfirmation';
            $message->setSubject(UserModule::t("You registered from {site_name}",array('{site_name}'=>Yii::app()->fbvStorage->get("settings.app_name"))));
            //userModel is passed to the view
            $message->setBody(array(
              'site_name' => Yii::app()->fbvStorage->get("settings.app_name"),
              'user' => $model,
              'activation_url' => $activation_url
            ), 'text/html');
             
            $message->addTo($model->email);
            $message->from = Yii::app()->fbvStorage->get("settings.app_email");
            Yii::app()->mail->send($message);
					}
					
					if ((Yii::app()->controller->module->loginNotActiv||(Yii::app()->controller->module->activeAfterRegister&&Yii::app()->controller->module->sendActivationMail==false))&&Yii::app()->controller->module->autoLogin) {
							$identity=new UserIdentity($model->username,$soucePassword);
							$identity->authenticate();
							Yii::app()->user->login($identity,0);
							$this->redirect(Yii::app()->controller->module->returnUrl);
					} else {
						if (!Yii::app()->controller->module->activeAfterRegister&&!Yii::app()->controller->module->sendActivationMail) {
						  $message = UserModule::t("Thank you for your registration. Contact Admin to activate your account.");
						} elseif(Yii::app()->controller->module->activeAfterRegister&&Yii::app()->controller->module->sendActivationMail==false) {
							$message = UserModule::t("Thank you for your registration. Please {{login}}.",array('{{login}}'=>CHtml::link(UserModule::t('Login'),Yii::app()->controller->module->loginUrl)));
						} elseif(Yii::app()->controller->module->loginNotActiv) {
						  $message = UserModule::t("Thank you for your registration. Please check your email or login.");
						} else {
						  $message = UserModule::t("Thank you for your registration. Please check your email.");
						}
            $this->render('/user/registrationThankYou', array(
              'model'=>$model,
              'profile'=>$profile,
              'message' => $message
            ));
            Yii::app()->end();
					}
				}
			} else $profile->validate();
		}
	    $this->render('/user/registration',array('model'=>$model,'profile'=>$profile));
    }
	}
}