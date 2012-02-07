<?php
/**
 * Extension of the CJuiAutoComplete class file.
 *
 * @author Sebastian Thierer <sebathi@gmail.com>, Vincent Van Uffelen <novazembla@gmail.com>
 * @package MG
 */

Yii::import('zii.widgets.jui.CJuiInputWidget');

/**
 * CJuiAutoComplete displays an autocomplete field.
 *
 * CJuiAutoComplete encapsulates the {@link http://jqueryui.com/demos/autocomplete/ JUI
 * autocomplete} plugin.
 *
 * To use this widget, you may insert the following code in a view:
 * <pre>
 * $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
 *     'name'=>'city',
 *     'source'=>array('ac1', 'ac2', 'ac3'),
 *     // additional javascript options for the autocomplete plugin
 *     'options'=>array(
 *         'minLength'=>'2',
 *     ),
 *     'htmlOptions'=>array(
 *         'style'=>'height:20px;'
 *     ),
 * ));
 * </pre>
 *
 * By configuring the {@link options} property, you may specify the options
 * that need to be passed to the JUI autocomplete plugin. Please refer to
 * the {@link http://jqueryui.com/demos/autocomplete/ JUI
 * autocomplete} documentation for possible options (name-value pairs).
 *
 * By configuring the {@link source} property, you may specify where to search
 * the autocomplete options for each item. If source is an array, the list is
 * used for autocomplete. You may also configure {@link sourceUrl} to retrieve
 * autocomplete items from an ajax response.
 *
 * @author Sebastian Thierer <sebathi@gmail.com>
 * @version $Id: CJuiAutoComplete.php 2799 2011-01-01 19:31:13Z qiang.xue $
 * @package zii.widgets.jui
 * @since 1.1.2
 */
class MGJuiAutoCompleteMultiple extends CJuiInputWidget
{
	/**
	 * @var mixed the entries that the autocomplete should choose from. This can be
	 * <ul>
	 * <li>an Array with local data</li>
     * <li>a String, specifying a URL that returns JSON data as the entries.</li>
     * <li>a javascript callback. Please make sure you prefix the callback name with "js:" in this case.</li>
     * </ul>
	 */
	public $source = array();
	/**
	 * @var mixed the URL that will return JSON data as the autocomplete items.
	 * CHtml::normalizeUrl() will be applied to this property to convert the property
	 * into a proper URL. When this property is set, the {@link source} property will be ignored.
	 */
	public $sourceUrl;

	/**
	 * Run this widget.
	 * This method registers necessary javascript and renders the needed HTML code.
	 */
	public function run()
	{
		list($name,$id)=$this->resolveNameID();

		if(isset($this->htmlOptions['id']))
			$id=$this->htmlOptions['id'];
		else
			$this->htmlOptions['id']=$id;

		if(isset($this->htmlOptions['name']))
			$name=$this->htmlOptions['name'];

		if($this->hasModel())
			echo CHtml::activeTextField($this->model,$this->attribute,$this->htmlOptions);
		else
			echo CHtml::textField($name,$this->value,$this->htmlOptions);

		if($this->sourceUrl!==null)
			$this->options['source']=CHtml::normalizeUrl($this->sourceUrl);
		else
			$this->options['source']=$this->source;
    
    $this->options['source'] = 'js:function( request, response ) {$.getJSON( "' . $this->options['source'] . '", {term: extractLast( request.term )}, response );}';
    $this->options['search'] = 'js:function() {var term = extractLast( this.value );if ( term.length < 2 ) {return false;}}';
    $this->options['focus'] = 'js:function() {return false;}';
    $this->options['select'] = 'js:function( event, ui ) {var terms = split( this.value );terms.pop();terms.push( ui.item.value );terms.push( "" );this.value = terms.join( ", " );return false;}';
        
    $options=CJavaScript::encode($this->options);

		$js  = <<<EOD
;(function (\$) {
  function split( val ) {
    return val.split( /,\s*/ );
  }
  function extractLast( term ) {
    return split( term ).pop();
  }
  \$(document).ready(function () {
    \$('#{$id}').bind( "keydown", function( event ) {
        if ( event.keyCode === \$.ui.keyCode.TAB &&
            \$(this).data( "autocomplete" ).menu.active ) {event.preventDefault();}
      }).autocomplete($options);
  });
})(jQuery)
		
EOD;
		$cs = Yii::app()->getClientScript();
		$cs->registerScript(__CLASS__.'#'.$id, $js);
	}
}
