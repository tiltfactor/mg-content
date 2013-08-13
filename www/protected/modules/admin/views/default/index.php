<?php 
/**
 * $tools = {name, url, description}
 */
$this->pageTitle=Yii::app()->fbvStorage->get("settings.app_name"). " - " . Yii::t('app', 'Admin');
$this->breadcrumbs = array(
  Yii::t('app', 'Admin')
);
try {
    $now = new DateTime('now');
} catch (Exception $e) {
    echo 'Since PHP 5.1.0 (when the date/time functions were rewritten). Update your php.ini file and restart.';
}
?>

<p>This is the Admin Overview. It lists all tools you have access to.</p>

<p>You're running Yii version [ <?php echo Yii::getVersion(); ?> ].</p>

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