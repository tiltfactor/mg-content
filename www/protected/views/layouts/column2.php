<?php $this->beginContent('//layouts/main'); ?>
<div class="container">
	<div class="span-18">
		<div id="content">
			<?php echo $content; ?>
		</div><!-- content -->
	</div>
	<div class="span-6 last">
		<div id="sidebar">
		<?php
			$this->beginWidget('zii.widgets.CPortlet', array(
				'title'=>Yii::t('app', 'Actions'),
			));
			$this->widget('zii.widgets.CMenu', array(
				'items'=>$this->menu,
				'htmlOptions'=>array('class'=>'operations'),
				'encodeLabel'=>false,
			));
			$this->endWidget();
		?>
		</div><!-- sidebar -->
	</div>
</div>
<?php $this->endContent(); ?>