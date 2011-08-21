<div class="view">

	<?php echo GxHtml::encode($data->getAttributeLabel('id')); ?>:
	<?php echo GxHtml::link(GxHtml::encode($data->id), array('view', 'id' => $data->id)); ?>
	<br />

	<?php echo GxHtml::encode($data->getAttributeLabel('file')); ?>:
	<?php echo GxHtml::encode($data->file); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('created')); ?>:
	<?php echo GxHtml::encode($data->created); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('modified')); ?>:
	<?php echo GxHtml::encode($data->modified); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('last_access')); ?>:
	<?php echo GxHtml::encode($data->last_access); ?>
	<br />

</div>