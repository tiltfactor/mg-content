<?php
Yii::import('zii.widgets.jui.CJuiInputWidget');
/**
 * XUpload extension for Yii.
 *
 * jQuery file upload extension for Yii, allows your users to easily upload files to your server using jquery
 * Its a wrapper of  http://aquantum-demo.appspot.com/file-upload
 *
 * @author Tudor Ilisoi
 * @author AsgarothBelem <asgaroth.belem@gmail.com>
 * @link http://aquantum-demo.appspot.com/file-upload
 * @link https://github.com/blueimp/jQuery-File-Upload
 * @version 0.1
 * 
 * Modified by Vincent Van Uffelen to work with the latest Upload Version
 */
class XUploadWidget extends CJuiInputWidget {

	/**
	 * the url to the upload handler
	 * @var string
	 */
	public $url;
	
  /**
   * the field name of the upload field
   * @var string
   */
  public $field_name = "file";
  
  /**
   * the model handling the upload
   * @var string
   */
  public $model;
  
	/**
	 * Publishes the required assets
	 */
	public function init() {
		parent::init();
		$this->publishAssets();
	}

	/**
	 * Generates the required HTML and Javascript
	 */
	public function run() {

		list($name,$id)=$this->resolveNameID();

		$model = $this->model;

		if( !isset($this->htmlOptions['enctype']) ){
			$this->htmlOptions['enctype'] = 'multipart/form-data';
		}

		if( !isset($this->htmlOptions['class']) ){
			$this->htmlOptions['class'] = '';
		}

		if( !isset($this->htmlOptions['id']) ){
			$this->htmlOptions['id'] = get_class($model)."_form";
		}
    
    echo "\t" . CHtml::tag("div", array("id"=> $this->htmlOptions['id'] . "_container"), FALSE, FALSE);
      echo "\t\t" . CHtml::beginForm($this->url, 'post', array("enctype" => $this->htmlOptions['enctype']));
        echo "\t\t\t" . CHtml::tag("div", array("class"=>"row"), '', FALSE);
          echo "\t\t\t\t" . CHtml::label(Yii::t('app', 'Batch ID'), "batch_id");
          echo "\t\t\t\t" . CHtml::textField("batch_id", "B-" . date('Y-m-d-H:i:s'), array("id" => "batch_id"));
          echo "\t\t\t\t" . CHtml::tag("small", array(), Yii::t('app', 'The batch id will help you to distinguish images on the import process page'), TRUE);
        echo "\t\t\t" . "</div>";
        echo "\t\t\t" . CHtml::tag("div", array("class"=>"fileupload-buttonbar"), '', FALSE);
          echo "\t\t\t\t" . CHtml::tag("label", array("class"=>"fileinput-button"), '', FALSE);
            echo "\t\t\t\t\t" . CHtml::tag("span", array(), Yii::t('app', "Add files..."));
            if($this->hasModel()){
              echo "\t\t\t\t\t" . CHtml::activeFileField($this->model, $this->attribute, array("multiple"=>"multiple"));
            }
            else{
              echo "\t\t\t\t\t" . CHtml::fileField($name,$this->value, array("multiple"=>"multiple"));
            }
          echo "</label>";
          echo "\t\t\t\t" . CHtml::tag("button", array("type"=>"submit", "class"=>"start"), Yii::t('app', 'Start upload'), TRUE);
          echo "\t\t\t\t" . CHtml::tag("button", array("type"=>"reset", "class"=>"cancel"), Yii::t('app', 'Cancel upload'), TRUE);
        echo "\t\t\t" . "</div>";
      echo "\t\t" . CHtml::endForm();
      echo "\t\t" . CHtml::tag("div", array("class"=>"fileupload-content"), FALSE, FALSE);
        echo "\t\t\t" . '<table class="files"></table>';
        echo "\t\t\t" . '<div class="fileupload-progressbar"></div>';
      echo "\t\t" . "</div>";
    echo "\t</div>";
    
    echo $this->_generateTemplates();
    
    Yii::app()->clientScript->registerScript(__CLASS__.'#'.$this->htmlOptions['id'], $this->_getInlineScript($this->htmlOptions['id']. "_container"), CClientScript::POS_READY);
	}

	/**
	 * Publishes and registers the required CSS and Javascript
	 * @throws CHttpException if the assets folder was not found
	 */
	public function publishAssets() {
	  $this->registerCoreScripts();
		$assets = dirname(__FILE__) . '/assets';
		$baseUrl = Yii::app()->assetManager->publish($assets);
		if (is_dir($assets)) {
			Yii::app()->clientScript->registerScriptFile($baseUrl . '/fileupload-ui/jquery.iframe-transport.js', CClientScript::POS_END);
      Yii::app()->clientScript->registerScriptFile($baseUrl . '/fileupload-ui/jquery.fileupload.js', CClientScript::POS_END);
      Yii::app()->clientScript->registerScriptFile($baseUrl . '/fileupload-ui/jquery.fileupload-ui.js', CClientScript::POS_END);
      Yii::app()->clientScript->registerScriptFile($baseUrl . '/jquery.tmpl.min.js', CClientScript::POS_END);
			Yii::app()->clientScript->registerCssFile($baseUrl . '/fileupload-ui/jquery.fileupload-ui.css');
		} else {
			throw new CHttpException(500, 'XUpload - Error: Couldn\'t find assets to publish.');
		}
	}
  
  private function _getInlineScript($id) {
    
    $size_str = ini_get('upload_max_filesize');
    $int_size = 0;
    switch (substr ($size_str, -1))
    {
        case 'M': case 'm': 
          $int_size = (int)$size_str * 1048576;
          break;
        case 'K': case 'k': $int_size = (int)$size_str * 1024;
          break;
        case 'G': case 'g': $int_size = (int)$size_str * 1073741824;
          break;
        default: $int_size = (int)$size_str;
          break;
    }
    $output = <<<EOD
    // Initialize the jQuery File Upload widget:
    \$('#$id').fileupload({
      acceptFileTypes :/^image\\/(jpg|jpeg)\$/,
      send : function () {if (\$('#batch_id').val().trim() == "") {return false;}},
      maxFileSize : $int_size
    });

    // Open download dialogs via iframes,
    // to prevent aborting current uploads:
    \$('#$id .files a:not([target^=_blank])').live('click', function (e) {
        e.preventDefault();
        \$('<iframe style="display:none;"></iframe>')
            .prop('src', this.href)
            .appendTo('body');
    });
EOD;
  return $output;
  }
	
	private function _generateTemplates() {
	  $output = <<<EOD
<script id="template-upload" type="text/x-jquery-tmpl">
    <tr class="template-upload{{if error}} ui-state-error{{/if}}">
        <td class="preview"></td>
        <td class="name">\${name}</td>
        <td class="size">\${sizef}</td>
        {{if error}}
            <td class="error" colspan="2">Error:
                {{if error === 'maxFileSize'}}File is too big
                {{else error === 'minFileSize'}}File is too small
                {{else error === 'acceptFileTypes'}}Filetype not allowed
                {{else error === 'maxNumberOfFiles'}}Max number of files exceeded
                {{else}}\${error}
                {{/if}}
            </td>
            <td class="cancel"><button>Cancel</button></td>
        {{else}}
            <td class="progress"><div></div></td>
            <td class="start"><button>Start</button></td>
            <td class="cancel"><button>Cancel</button></td>
        {{/if}}
    </tr>
</script>
<script id="template-download" type="text/x-jquery-tmpl">
    <tr class="template-download{{if error}} ui-state-error{{/if}}">
        {{if error}}
            <td></td>
            <td class="name">\${name}</td>
            <td class="size">\${sizef}</td>
            <td class="error" colspan="2">Error:
                {{if error === 1}}File exceeds upload_max_filesize (php.ini directive)
                {{else error === 2}}File exceeds MAX_FILE_SIZE (HTML form directive)
                {{else error === 3}}File was only partially uploaded
                {{else error === 4}}No File was uploaded
                {{else error === 5}}Missing a temporary folder
                {{else error === 6}}Failed to write file to disk
                {{else error === 7}}File upload stopped by extension
                {{else error === 'maxFileSize'}}File is too big
                {{else error === 'minFileSize'}}File is too small
                {{else error === 'acceptFileTypes'}}Filetype not allowed
                {{else error === 'maxNumberOfFiles'}}Max number of files exceeded
                {{else error === 'uploadedBytes'}}Uploaded bytes exceed file size
                {{else error === 'emptyResult'}}Empty file upload result
                {{else}}\${error}
                {{/if}}
            </td>
            <td class="delete">
                <button data-type="\${delete_type}" data-url="\${delete_url}">Delete</button>
            </td>
        {{else}}
            <td class="preview">
                {{if thumbnail_url}}
                    <img src="\${thumbnail_url}">
                {{/if}}
            </td>
            <td class="name">
                \${name}
            </td>
            <td class="size">\${sizef}</td>
            <td colspan="3"></td>
        {{/if}}
        
    </tr>
</script>
EOD;
  return $output;
	}
}