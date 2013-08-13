<div class="view">

	<?php echo GxHtml::encode($data->getAttributeLabel('id')); ?>:
	<?php echo GxHtml::link(GxHtml::encode($data->id), array('view', 'id' => $data->id)); ?>
	<br />

	<?php echo GxHtml::encode($data->getAttributeLabel('name')); ?>:
	<?php echo GxHtml::encode($data->name); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('size')); ?>:
	<?php echo GxHtml::encode($data->size); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('mime_type')); ?>:
	<?php echo GxHtml::encode($data->mime_type); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('last_access')); ?>:
	<?php echo GxHtml::encode($data->last_access); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('locked')); ?>:
	<?php echo GxHtml::encode($data->locked); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('created')); ?>:
	<?php echo GxHtml::encode($data->created); ?>
	<br />
	<?php /*
	<?php echo GxHtml::encode($data->getAttributeLabel('modified')); ?>:
	<?php echo GxHtml::encode($data->modified); ?>
	<br />
	*/ ?>

</div>