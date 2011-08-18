<div class="view">

	<?php echo GxHtml::encode($data->getAttributeLabel('id')); ?>:
	<?php echo GxHtml::link(GxHtml::encode($data->id), array('view', 'id' => $data->id)); ?>
	<br />

	<?php echo GxHtml::encode($data->getAttributeLabel('name')); ?>:
	<?php echo GxHtml::encode($data->name); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('locked')); ?>:
	<?php echo GxHtml::encode($data->locked); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('more_information')); ?>:
	<?php echo GxHtml::encode($data->more_information); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('licence_id')); ?>:
		<?php echo GxHtml::encode(GxHtml::valueEx($data->licence)); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('created')); ?>:
	<?php echo GxHtml::encode($data->created); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('modified')); ?>:
	<?php echo GxHtml::encode($data->modified); ?>
	<br />

</div>