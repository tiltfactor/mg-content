<?php 
/**
 * $tools = {name, url, description}
 */

$this->pageTitle=Yii::app()->fbvStorage->get("settings.app_name"). " - " . Yii::t('app', 'Admin'); 
$this->breadcrumbs = array(
  Yii::t('app', 'Admin')
);
?>

<p>This is the Admin Overview. It lists all tools you have got access to.</p>

<?php foreach ($tool_groups as $group => $tools) : ?>
<fieldset class="admin-tools">
  <legend><?php echo $group; ?></legend>
  <?php foreach ($tools as $id => $tool) : ?>
  <div class="tool" id="<?php echo $id; ?>">
    <h3><a href="<?php echo $tool->url; ?>"><?php echo $tool->name; ?></a></h3>
    <p><?php echo $tool->description; ?></p>
  </div>
  <?php endforeach;?>
</fieldset>
<?php endforeach;?>