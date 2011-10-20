<?php
/**
 * CJuiDialog class file.
 *
 * @author Vincent Van Uffelen based on CJuiDialog from Sebastian Thierer <sebathi@gmail.com>
 */

Yii::import('zii.widgets.jui.CJuiWidget');

/**
 * MGTagJuiDialog renders the needed code to display the tag edit dialog. 
 *
 * MGTagJuiDialog encapsulates the {@link http://jqueryui.com/demos/dialog/ JUI Dialog}
 * plugin.
 * 
 * It loads the needed JavaScript sources and adds the modal window first screen's content as a 
 * JQuery template. 
 * 
 * It also provides methods that allow you to add the needed JavaScript code to reactivate links
 * after a GridView or ListView ajax refresh.
 *
 * You'll have to give the links that should activated the class 'tagDialog'
 * 
 * The id of the link clicked will be retrieved from the link href. It has to be the last element in the url
 * http://metadatagames.com/admin/tag/view/id/395 == 395
 * 
 * To use this widget, you may insert the following code in a view:
 *
 * @author Vincent Van Uffelen
 */
class MGTagJuiDialog extends CJuiWidget
{
	/**
	 * @var string the name of the container element that contains all panels. Defaults to 'div'.
	 */
	public $tagName='div';

	/**
	 * Renders the open tag of the dialog.
	 * This method also registers the necessary javascript code.
	 */
	public function init()
	{
		parent::init();

		$options=empty($this->options) ? '' : CJavaScript::encode($this->options);
    Yii::app()->getClientScript()->registerScriptFile(Yii::app()->baseUrl . '/js/jquery.tmpl.min.js', CClientScript::POS_END);
    Yii::app()->getClientScript()->registerScriptFile(Yii::app()->baseUrl . '/js/mg.tagdialog.js', CClientScript::POS_END);
    
    $options = "{admin_base_url: '" . Yii::app()->createUrl("/admin") . "'}";
    
		Yii::app()->getClientScript()->registerScript(__CLASS__.'#tag-dialog', "jQuery(document).ready(function (){MG_TAGDIALOG.init($options);});");
	}
  
  /**
	 * Renders the close tag of the dialog.
	 */
	public function run()
	{
	  
    $view_tag = Yii::t('app', 'view tag');
    $rename_tag = Yii::t('app', 'rename tag');
    $weight_tag = Yii::t('app', 'change tag weight');
    $ban_tag = Yii::t('app', 'ban tag');
		echo <<<EOD
<script id="template-tag-dialog" type="text/x-jquery-tmpl">
 <div id="modalTagDialog" title="Tag: \${tag}">
  <ul>
    <li><a href="\${admin_base_url}/tag/view/id/\${id}" class="view">$view_tag</a></li>
    <li><a href="\${admin_base_url}/tag/update/id/\${id}" class="change">$rename_tag</a></li>
    <li><a href="\${admin_base_url}/tag/weight/id/\${id}" class="weight">$weight_tag</a></li>
    <li><a href="\${admin_base_url}/tag/ban/id/\${id}" class="ban">$ban_tag</a></li>
  </ul>
 </div>
</script>		
EOD;

	}
  
  public function gridViewUpdate() {
    return 'function (id, data) {MG_TAGDIALOG.refresh();}';
  }
  
  public function listViewUpdate() {
    return 'function (id, data) {MG_TAGDIALOG.refresh();}';
  }
}
