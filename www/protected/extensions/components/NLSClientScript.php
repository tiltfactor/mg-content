<?php
/**
 * NLSClientScript class v2.1
 * Implements a smart lazy loading of js+css files. It means that the resource file 
 * will be loaded only if it has not loaded yet into the DOM.
 * 
 * @author nlac <nlacsoft@gmail.com>
 * @link http://nlacsoft.net
 * @license BSD



History:
2.1
 - dirty fix script rendering bug of jquery.js v1.6.1 affecting binline=true mode
2.0
 - brand new approach: resource hash stored at the server side, in the webuser state. All these info deleted when a non-ajax request comes
 - no $.ajax usage - better performance
 - js/css files can be linked from other domain
 - new parameter: bInlineJs
     if true, the scriptFile method will insert the js file content into the html instead of linking the file
     what can result even better performance
1.3
 - added cache:true to the ajax js load
 - compressed core js code
 - possibility to link a resource statically (old way) by appending an ?nls_static url param to the end of the file name
   eg. Yii::app()->getClientScript()->registerScriptFile( $publishedBasePath . '/ext/yii_ext.js?nls_static');
1.2
 - fixed js error when app not in YII_DEBUG mode
1.1
 - hash key generated on server side
 - two hash key mode: PATH and CONTENT
 - shortened client-side code
1.0
 - base version



Usage of the latest version:
1. in /protected/config/main.js, configure the 'clientScript' component:
'clientScript' => array(
	'class' => 'ext.nlacsoft.NLSClientScript',
	'hashMode' => 'PATH', //PATH|CONTENT
	'bInlineJs' => true
)

2. in the entry script /index.php of your webapp, replace this row:
Yii::createWebApplication($config)->run();

to these lines:
class WebApplication extends CWebApplication {
	public function beforeControllerAction($controller,$action) {
		if (!Yii::app()->request->isAjaxRequest) {
			Yii::app()->clientScript->clearCache();
		}
		return true;
	}
}
Yii::createApplication('WebApplication',$config)->run();

 */

/**
 * CClientScript extension for handling managed lazy loading of javascript files
 */
class NLSClientScript extends CClientScript
{

/**
 * This value determines how the hash key should be composed for a resource file.
 * Possible values:
 *   CONTENT: a bit slower but safe
 *   PATH: 
 *     - the key is composed from the file name
 *     - there may be problem if the names of two different resource files used in the same view are equal
 *      ( eg. two different "main.js" in the same view)
 *      this case causes that the loading of the 2nd one will be denied -> rename the file or use CONTENT mode
 * 
 *   CONTENT:
 *     - the key is computed from the file content
 *     - a bit more processing on the server side
 * 
 * 
 * 
 **/
	public $hashMode = 'PATH';//PATH|CONTENT
	public $bInlineJs = false;//<script>...</script> or <script src="..."></script>

/**
 * Gets the content of the resource file (js|css)
**/
	private function getResourceContent($f) {

		//$fullPath = $_SERVER['DOCUMENT_ROOT'] . preg_replace('/[\\/\\\\]/',DIRECTORY_SEPARATOR,$f);
		$fullPath = $_SERVER['DOCUMENT_ROOT'] . $f;
		$c = file_get_contents( $fullPath );
		if ($c === false) {
			Yii::log('no resource found on path ' . $fullPath, 'warning');
			return '';
		}
		
		//dirty fix script rendering bug of jquery.js v1.6.1
		$c = preg_replace('/"<!doctype><html><body><\\/body><\\/html>"/', '"<!doctype><html><body"+"><"+"/body></html>"', $c);
		
		return $c;
	}
	
  
  
/**
 * Composing the key from the file content
 */
	private static function getHashKey($f) {
		
		//normalize path: removing nlac_static from the end if there is
		//$f = preg_replace('/\\??&*nls_static$/','',$f); 

		if (Yii::app()->clientScript->hashMode == 'PATH') {
			//Composing thekey from the file name
			
			//If the file name contains "jquery" then chunk the path 
			//(assuming that the file name is unique over the project)
			if (stristr($f,'jquery')!==false) {
				preg_match('/[^\\/\\\\]+$/', $f, $matches);
				return @$matches[0];
			}
			return $f;
		}
		
		//Composing the key from the file content
		$c = $this->getResourceContent($f);
		//Yii::log('fullpath: ' . $fullPath . ', dirsep='.DIRECTORY_SEPARATOR, 'warning');
		return substr(md5( $c ),0,8);
	}

/**
 * css files should be loaded into the header if we want to lazy load them
**/
	private static function defCssLoader() {

		$loadedResources = Yii::app()->user->getState('nlsLoadedResources');
		if (!isset($loadedResources))
			$loadedResources = array();		
		
		$hk = '__defCssLoader';
		$inCache = isset($loadedResources[$hk]);

		if ($inCache)
			return '';
	
		$loadedResources[$hk] = $hk;
		Yii::app()->user->setState('nlsLoadedResources', $loadedResources);

		return CHtml::script('
//css loader
__loadCss = function(f, media) {
	var a = document.createElement("link");
	a.rel="stylesheet";
	a.type="text/css";
	a.media=media||"screen";
	a.href=f;
	(document.getElementsByTagName("head"))[0].appendChild(a);
};
');
			
	}

	private static function _loadResource($f, $media = null) {

		$loadedResources = Yii::app()->user->getState('nlsLoadedResources');
		if (!isset($loadedResources))
			$loadedResources = array();		
		
		$hk = self::getHashKey($f);
		$inCache = isset($loadedResources[$hk]);

		//Yii::log('incache:' . $inCache .', hk:' . $hk  .', f:'. $f);
		if ($inCache)
			return '';

		$loadedResources[$hk] = $hk;
		Yii::app()->user->setState('nlsLoadedResources', $loadedResources);
		
		//js
		if ($media === null) {

			if (Yii::app()->clientScript->bInlineJs) {
				$comment = '/* content of ' . $f . ': */';
				return CHtml::script( $comment . self::getResourceContent($f) );
			}
			
			return CHtml::scriptFile( $f );
		}
		
		//css
		return CHtml::script('__loadCss("'.$f.'","'.$media.'");');
	}

	private static function scriptFile($f) {
		
		//making sure using proper version of jquery.js
		$f = preg_replace('/\\/jquery\\.js$/', defined('YII_DEBUG') && YII_DEBUG ? '/jquery.js' : '/jquery.min.js', $f);
		return self::_loadResource($f);
	
	}

	private static function cssFile($f, $media) {
		
		return self::_loadResource($f, $media);
	
	}

	//This method should be called before every non-ajax action
	public function clearCache() {
		Yii::app()->user->setState('nlsLoadedResources', array());
	}

	

/**
 * 
 **************************  Overriding parent functions  *******************************
 * 
 * In the rest, the only change compared to the parent methods 
 * is replacing CHtml::scriptFile -> self::scriptFile
 * 
 * 
**/


	/**
	 * Inserts the scripts in the head section.
	 * @param string $output the output to be inserted with scripts.
	 */
	 
	 
	public function renderHead(&$output)
	{
		$html='';
		foreach($this->metaTags as $meta)
			$html.=CHtml::metaTag($meta['content'],null,null,$meta)."\n";
		foreach($this->linkTags as $link)
			$html.=CHtml::linkTag(null,null,null,null,$link)."\n";
			
		//nlac:
		$html.= self::defCssLoader();

		foreach($this->cssFiles as $url=>$media)
			$html.=self::cssFile($url,$media)."\n";
		foreach($this->css as $css)
			$html.=CHtml::css($css[0],$css[1])."\n";
		if($this->enableJavaScript)
		{
			if(isset($this->scriptFiles[self::POS_HEAD]))
			{
				foreach($this->scriptFiles[self::POS_HEAD] as $scriptFile)
					$html.=self::scriptFile($scriptFile)."\n";
			}

			if(isset($this->scripts[self::POS_HEAD]))
				$html.=CHtml::script(implode("\n",$this->scripts[self::POS_HEAD]))."\n";
		}

		if($html!=='')
		{
			$count=0;
			$output=preg_replace('/(<title\b[^>]*>|<\\/head\s*>)/is','<###head###>$1',$output,1,$count);
			if($count)
				$output=str_replace('<###head###>',$html,$output);
			else
				$output=$html.$output;
		}
	}



	/**
	 * Inserts the scripts at the beginning of the body section.
	 * @param string $output the output to be inserted with scripts.
	 */
	public function renderBodyBegin(&$output)
	{
		$html='';
		if(isset($this->scriptFiles[self::POS_BEGIN]))
		{
			foreach($this->scriptFiles[self::POS_BEGIN] as $scriptFile)
				$html.=self::scriptFile($scriptFile)."\n";
		}
		if(isset($this->scripts[self::POS_BEGIN]))
			$html.=CHtml::script(implode("\n",$this->scripts[self::POS_BEGIN]))."\n";

		if($html!=='')
		{
			$count=0;
			$output=preg_replace('/(<body\b[^>]*>)/is','$1<###begin###>',$output,1,$count);
			if($count)
				$output=str_replace('<###begin###>',$html,$output);
			else
				$output=$html.$output;
		}
	}


	/**
	 * Inserts the scripts at the end of the body section.
	 * @param string $output the output to be inserted with scripts.
	 */
	public function renderBodyEnd(&$output)
	{
		if(!isset($this->scriptFiles[self::POS_END]) && !isset($this->scripts[self::POS_END])
			&& !isset($this->scripts[self::POS_READY]) && !isset($this->scripts[self::POS_LOAD]))
			return;

		$fullPage=0;
		$output=preg_replace('/(<\\/body\s*>)/is','<###end###>$1',$output,1,$fullPage);
		$html='';
		if(isset($this->scriptFiles[self::POS_END]))
		{
			foreach($this->scriptFiles[self::POS_END] as $scriptFile)
				$html.=self::scriptFile($scriptFile)."\n";
		}
		$scripts=isset($this->scripts[self::POS_END]) ? $this->scripts[self::POS_END] : array();
		if(isset($this->scripts[self::POS_READY]))
		{
			if($fullPage)
				$scripts[]="jQuery(function($) {\n".implode("\n",$this->scripts[self::POS_READY])."\n});";
			else
				$scripts[]=implode("\n",$this->scripts[self::POS_READY]);
		}
		if(isset($this->scripts[self::POS_LOAD]))
		{
			if($fullPage)
				$scripts[]="jQuery(window).load(function() {\n".implode("\n",$this->scripts[self::POS_LOAD])."\n});";
			else
				$scripts[]=implode("\n",$this->scripts[self::POS_LOAD]);
		}
		if(!empty($scripts))
			$html.=CHtml::script(implode("\n",$scripts))."\n";

		if($fullPage)
			$output=str_replace('<###end###>',$html,$output);
		else
			$output=$output.$html;
	}
 

}