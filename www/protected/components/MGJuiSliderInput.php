<?php
/**
 * MGJuiSliderInput class file.
 *
 * @author Vincent Van Uffelen <novazembla@gmail.com>
 * @link http://www.metadatagames.com/
 * @copyright Copyright &copy; 2008-2011 Yii Software LLC
 * @license http://www.metadatagames.com/license/
 */

Yii::import('zii.widgets.jui.CJuiSliderInput');

/**
 * MGJuiSliderInput extends MGJuiSliderInput. By allowing to show the input field.
 *
 * CJuiSlider encapsulates the {@link http://jqueryui.com/demos/slider/ JUI
 * slider} plugin.
 *
 * To use this widget, you may insert the following code in a view:
 * <pre>
 * $this->widget('zii.widgets.jui.MGJuiSliderInput', array(
 *     'name'=>'rate',
 *     'value'=>37,
 *     'admin' => true/false, // if admin input fields will be shown
 *     // additional javascript options for the slider plugin
 *     'options'=>array(
 *         'min'=>10,
 *         'max'=>50,
 *     ),
 *     'htmlOptions'=>array(
 *         'style'=>'height:20px;'
 *     ),
 * ));
 * </pre>
 *
 * The widget can also be used in range mode which uses 2 sliders to set a range.
 * In this mode, {@link attribute} and {@link maxAttribute} will define the attribute
 * names for the minimum and maximum range values, respectively. For example:
 *
 * <pre>
 * $this->widget('zii.widgets.jui.CJuiSliderInput', array(
 *     'model'=>$model,
 *     'attribute'=>'timeMin',
 *     'maxAttribute'=>'timeMax,
 *     'admin' => true/false, // if admin input fields will be shown
 *     // additional javascript options for the slider plugin
 *     'options'=>array(
 *         'range'=>true,
 *         'min'=>0,
 *         'max'=>24,
 *     ),
 * ));
 *
 * If you need to use the slider event, please change the event value for 'stop' or 'change'.
 *
 * By configuring the {@link options} property, you may specify the options
 * that need to be passed to the JUI slider plugin. Please refer to
 * the {@link http://jqueryui.com/demos/slider/ JUI slider} documentation
 * for possible options (name-value pairs).
 *
 * @author Sebastian Thierer <sebathi@gmail.com>
 * @version $Id: CJuiSliderInput.php 2948 2011-02-09 13:27:05Z haertl.mike $
 * @package zii.widgets.jui
 * @since 1.1
 */
class MGJuiSliderInput extends CJuiSliderInput
{
	/**
	 * @var boolean toggle to show the input field 
	 */
	public $admin= false;
	

	/**
	 * Run this widget.
	 * This method registers necessary javascript and renders the needed HTML code.
	 */
	public function run()
	{
		list($name,$id)=$this->resolveNameID();

		$isRange=isset($this->options['range']) && $this->options['range'];

		if(isset($this->htmlOptions['id']))
			$id=$this->htmlOptions['id'];
		else
			$this->htmlOptions['id']=$id;
		if(isset($this->htmlOptions['name']))
			$name=$this->htmlOptions['name'];

    if ($this->admin)
      $this->htmlOptions['disabled'] = 'disabled';

		if($this->hasModel())
		{
			$attribute=$this->attribute;
			if ($isRange)
			{
				$options=$this->htmlOptions;
        
        if ($this->admin) {
          echo CHtml::activeTextField($this->model,$this->attribute,$options);
          $options['id']=$options['id'].'_end';
          echo CHtml::activeTextField($this->model,$this->maxAttribute,$options);  
        } else {
          echo CHtml::activeHiddenField($this->model,$this->attribute,$options);
          $options['id']=$options['id'].'_end';
          echo CHtml::activeHiddenField($this->model,$this->maxAttribute,$options);
        }
				
				$attrMax=$this->maxAttribute;
				$this->options['values']=array($this->model->$attribute,$this->model->$attrMax);
			}
			else
			{
			  if ($this->admin)
				  echo CHtml::activeTextField($this->model,$this->attribute,$this->htmlOptions);
        else
          echo CHtml::activeHiddenField($this->model,$this->attribute,$this->htmlOptions);        
				$this->options['value']=$this->model->$attribute;
			}
		}
		else
		{
			if ($this->admin)
        echo CHtml::textField($name,$this->value,$this->htmlOptions);
      else
        echo CHtml::hiddenField($name,$this->value,$this->htmlOptions);
			
			if($this->value!==null)
				$this->options['value']=$this->value;
		}
		

		$idHidden = $this->htmlOptions['id'];
		$nameHidden = $name;

		$this->htmlOptions['id']=$idHidden.'_slider';
		$this->htmlOptions['name']=$nameHidden.'_slider';

		echo CHtml::openTag($this->tagName,$this->htmlOptions);
		echo CHtml::closeTag($this->tagName);

		$this->options[$this->event]= $isRange ?
			"js:function(e,ui){ v=ui.values; jQuery('#{$idHidden}').val(v[0]); jQuery('#{$idHidden}_end').val(v[1]); }":
			'js:function(event, ui) { jQuery(\'#'. $idHidden .'\').val(ui.value); }';

		$options=empty($this->options) ? '' : CJavaScript::encode($this->options);

		$js = "jQuery('#{$id}_slider').slider($options);\n";
		Yii::app()->getClientScript()->registerScript(__CLASS__.'#'.$id, $js);
	}

}
