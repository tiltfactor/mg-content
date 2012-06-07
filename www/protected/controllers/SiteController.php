<?php

class SiteController extends Controller
{
	public function filters() {
    return array( // add blocked IP filter here
      'Installed',
      'IPBlock',
    );
  }
	
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This action displays the arcade it's template can be found under views/site/index.php
	 */
	public function actionIndex()
	{
		MGHelper::setFrontendTheme();
    $this->layout = '//layouts/arcade';
    
    $games = GamesModule::listActiveGames();
    
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$this->render('index', array(
		  'games' => $games,
    ));
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
	  MGHelper::setFrontendTheme();
    
    
    if($error=Yii::app()->errorHandler->error) {
    	if(Yii::app()->request->isAjaxRequest) {
    		 echo $error['message'];
    	}else {
         $this->render('error', $error);
      }
    }
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		MGHelper::setFrontendTheme();
    
		$model=new ContactForm;
		if(isset($_POST['ContactForm'])) {
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$headers="From: {$model->email}\r\nReply-To: {$model->email}";
				mail(Yii::app()->fbvStorage->get("settings.app_email"),$model->subject,$model->body,$headers);
        Flash::add('success', Yii::t('app', 'Thank you for contacting us. We will respond to you as soon as possible.'));
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Displays the Credits page.
	 */
 	public function actionCredits()
	{
		MGHelper::setFrontendTheme();
    
		//$this->render('contact',array('model'=>$model));
		$this->render('credits');
	}

	/**
	 * Displays the About page.
	 */
 	public function actionAbout()
	{
		MGHelper::setFrontendTheme();
		$this->render('about');
	}

}