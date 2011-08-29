<?php $this->beginContent('//layouts/page'); ?>
<div id="header">
  <a id="page_title" class="ir" href="<?php echo MGHelper::bu("/"); ?>"><?php echo CHtml::encode(Yii::app()->name); ?></a>
<div id="mainmenu">
  <?php $this->widget('application.components.MGMenu',array(
    'items'=>array(
      array('label'=>'Arcade', 'url'=>array('/site/index')),
      array('label'=>'Contact', 'url'=>array('/site/contact')),
      array('url'=>Yii::app()->getModule('user')->loginUrl, 'label'=>Yii::app()->getModule('user')->t("Login"), 'visible'=>Yii::app()->user->isGuest),
      array('url'=>Yii::app()->getModule('user')->registrationUrl, 'label'=>Yii::app()->getModule('user')->t("Register"), 'visible'=>Yii::app()->user->isGuest),
      array('url'=>array('/admin'), 'label'=>Yii::t('app', 'Admin'), 'visible'=>Yii::app()->user->checkAccess('editor')),
      array('url'=>Yii::app()->getModule('user')->profileUrl, 'label'=>Yii::app()->getModule('user')->t("Profile"), 'visible'=>!Yii::app()->user->isGuest),
      array('url'=>Yii::app()->getModule('user')->logoutUrl, 'label'=>Yii::app()->getModule('user')->t("Logout").' ('.Yii::app()->user->name.')', 'visible'=>!Yii::app()->user->isGuest)
    ),
    
  )); 
  ?></div><!-- mainmenu -->
</div>
<div id="submenu" class="clearfix">
    <?php $this->widget('application.components.MGMenu',array(
      'items'=>Yii::app()->getModule("admin")->getAdminToolsSubMenuLinks(),
    )); 
    ?>
  </div>
<div class="container" id="page">
  <?php if(isset($this->breadcrumbs)):?>
    <?php $this->widget('zii.widgets.CBreadcrumbs', array(
      'links'=>$this->breadcrumbs,
    )); ?><!-- breadcrumbs -->
  <?php endif?>
  <?php $this->widget('application.extensions.yii-flash.Flash', array(
    'keys'=>array('success', 'warning','error'), 
    'htmlOptions'=>array('class'=>'flash'),
  )); ?><!-- flashes -->

  
  
  <?php echo $content; ?>

  <div id="footer">
    © <?php echo date('Y'); ?> <a href="http://www.tiltfactor.org/">tiltfactor</a>, all rights reserved
  </div><!-- footer -->

</div><!-- page -->
<?php $this->endContent(); ?>