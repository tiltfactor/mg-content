<?php
/**
 * ColorBox
 *
 * @version 1.00
 * @author maimairel <maimairel@yahoo.com>
 */
class JColorBox extends CWidget
{
	protected $scriptUrl;
	
	protected $scriptFile;
	
	public $cssFile;

	public function init()
	{	
		$this->scriptUrl = Yii::app()->getAssetManager()->publish(dirname(__FILE__).'/assets');

		$this->scriptFile = YII_DEBUG ? '/js/jquery.colorbox.js' : '/js/jquery.colorbox-min.js';
		
		if($this->cssFile == NULL)
			$this->cssFile = '/colorbox.css';
		
		$this->registerClientScript();
	}

	public function registerClientScript()
	{
		$cs = Yii::app()->clientScript;
		
		$cs->registerCoreScript('jquery');
		$cs->registerScriptFile($this->scriptUrl.$this->scriptFile);
		
		$cs->registerCssFile($this->scriptUrl.$this->cssFile);
	}
	
	public function addInstance($selector, $options = array())
	{
		$options = CJavaScript::encode($options);
		$id = __CLASS__.'_'.sprintf("%x", crc32($selector.$options));
		
		Yii::app()->clientScript->registerScript($id, "jQuery('$selector').colorbox($options);");
		
		return $this;
	}
}