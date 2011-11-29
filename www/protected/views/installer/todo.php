<?php $this->pageTitle = Yii::app()->name . '- Further Steps'; ?>

<h1><?php echo CHtml::encode(Yii::app()->name); ?> - Further Steps</h1>

<p><b>Success! The database has been set up and MetaData Games has been configured.</b></p>

<p>Some things are however left to to do:</p>

<p><b>All links below will open in a new tab or window to allow you to return to this list.</b></p>
<ol>
  <li><a href="<?php echo Yii::app()->baseUrl; ?>/index.php/user/login" target="_blank">Login</a> gain access to the <a href="<?php echo Yii::app()->baseUrl; ?>/index.php/admin" target="_blank">admin tool</a>. </li>
  <li>Visit the <a href="<?php echo Yii::app()->baseUrl; ?>/index.php/plugins" target="_blank">plugin tool</a></li>
  <li><a href="<?php echo Yii::app()->baseUrl; ?>/index.php/admin/imageSet" target="_blank">Create image sets</a> and <a href="<?php echo Yii::app()->baseUrl; ?>/index.php/admin/import" target="_blank">import images</a></li>
  <li>Visit the <a href="<?php echo Yii::app()->baseUrl; ?>/index.php/games" target="_blank">games tool</a> and activate the ones you want to make use of</li>
  <li>Goto the <a href="<?php echo Yii::app()->baseUrl; ?>" target="_blank">Arcade</a> and play!</li>
</ol>

