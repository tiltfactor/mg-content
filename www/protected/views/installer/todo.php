<?php $this->pageTitle = Yii::app()->name . '- Further Steps'; ?>

<h1><?php echo CHtml::encode(Yii::app()->name); ?> - Further Steps</h1>

<p><b>Success! The database has been set up and Metadata Games has been installed.</b></p>

<p>Now to configure your install of Metadata Games:</p>

<p><b>All links below will open in a new tab or window to allow you to return to this list.</b></p>
<ol>
  <li><a href="<?php echo Yii::app()->baseUrl; ?>/index.php/user/login" target="_blank">Login</a> to gain access to the <a href="<?php echo Yii::app()->baseUrl; ?>/index.php/admin" target="_blank">admin tool</a>. </li>
  <li>Create <a href="<?php echo Yii::app()->baseUrl; ?>/index.php/admin/license" target="_blank">licenses</a> to assign to media collections.</li>
  <li><a href="<?php echo Yii::app()->baseUrl; ?>/index.php/admin/collection" target="_blank">Create collections</a> and <a href="<?php echo Yii::app()->baseUrl; ?>/index.php/admin/import" target="_blank">import media</a>.</li>
  <li><a href="<?php echo Yii::app()->baseUrl; ?>/index.php/admin/import/uploadprocess" target="_blank">Process media</a> (may need to wait for audio, video media to <a href="<?php echo Yii::app()->baseUrl; ?>/index.php/admin/import/transcodingprocess" target="_blank">finish transcoding</a>).</li>
</ol>

