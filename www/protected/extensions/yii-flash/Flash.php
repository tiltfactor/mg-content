<?php
/**
 * Flash widget class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2011-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * 
 * Modified By Vincent Van Uffelen, THANKS!
 */

/**
 * Flash displays web user flash messages.
 */
class Flash extends CWidget
{
	/**
	 * @property array the keys for which to get flash messages.
	 */
	public $keys;
	/**
	 * @property string the template to use for displaying flash messages.
	 */
	public $template='<div class="{key}">{message}</div>';
	/**
	 * @property array the html options.
	 */
	public $htmlOptions=array();
	/**
	 * @property string the associated JavaScript.
	 */
	public $js="jQuery('.flash').hide().fadeIn(500).animate({opacity: 1.0}, 3000, function () {jQuery('.flash .fade').fadeOut('slow');});";

	/**
	 * Runs the widget.
	 */
	public function run()
	{
		$id=$this->getId();

		if (isset($this->htmlOptions['id']))
			$id=$this->htmlOptions['id'];
		else
			$this->htmlOptions['id']=$id;

		if(is_string($this->keys))
			$this->keys=array($this->keys);

		$markup='';
		foreach(Yii::app()->user->getFlashes() as $flash) {
  		foreach($this->keys as $key) {
  			if (is_array($flash)) {
  			  if ($flash["scope"] == $key) {
    			  $markup.=strtr($this->template,array(
              '{key}'=>$flash["scope"] . (($flash["fixed"])? ' fixed':' fade'),
              '{message}'=>$flash["message"],
            ));
            break;  
  			  }
        } else {
          $markup.=strtr($this->template,array(
            '{key}'=>$key . " fade",
            '{message}'=>$flash,
          ));
          break;
        }
  		}
    }
		if($markup!=='')
		{
			echo CHtml::openTag('div',$this->htmlOptions);
			echo $markup;
			echo CHtml::closeTag('div');
		}

		Yii::app()->clientScript->registerScript(__CLASS__.'#'.$id,
				strtr($this->js,array('{id}'=>$id)),CClientScript::POS_READY);
	}
}
