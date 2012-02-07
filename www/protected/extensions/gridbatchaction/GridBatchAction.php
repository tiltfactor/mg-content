<?php
/**
 * GridBatchMenuWidget extension for Yii.
 *
 * this widget adds the needed Javascript to the code to make use of batch actions for 
 * the in CGridViews listed data.
 *
 * @author Vincent Van Uffelen <novazambla@gmail.com>
 * @version 0.1
 * @package MG
 * 
 */
class GridBatchAction extends CWidget {
  
  public $formId;
  public $checkBoxId;
  public $ajaxGridId;
  public $textNoSelection; 
  public $textConfirm;
  public $textNoAction;
  
  /**
   * @var array HTML attributes for the menu's root container tag
   */
  public $htmlOptions=array();
  
  /**
   * @var array HTML attributes for the menu's root container tag
   */
  public $items=array();
  
  /**
   * Initializes the menu widget.
   * This method mainly normalizes the {@link items} property.
   * If this method is overridden, make sure the parent implementation is invoked.
   */
  public function init() {
    parent::init();  
    if (is_null($this->textNoSelection))
      $this->textNoSelection = Yii::t('ui','Please check at least one item you would like to perform this action on!');
    
    if (is_null($this->textConfirm))
      $this->textConfirm = Yii::t('ui','Perform this action on checked item(s)?');
    
    if (is_null($this->textNoAction))
      $this->textNoAction = Yii::t('ui','Please select an action you want to execute!');
  }
  
  /**
   * The run function of the widget
   */
  public function run() {
    if(count($this->items)) {
      echo CHtml::openTag('div',$this->htmlOptions)."\n";
      
      $data = array();
      $data["none"] = Yii::t('app', 'Batch Actions');
      foreach($this->items as $item) {
        if (isset($item["label"]) && isset($item["url"])) {
          $data[CHtml::normalizeUrl($item["url"])] = $item["label"];
        }
      }
      
      echo CHtml::dropDownList($this->formId . "-batch-actions", "", $data);
      echo CHtml::tag("button", array("id"=> $this->formId . "-batch-actions-go"), Yii::t('app', 'Go'), true);
      echo CHtml::closeTag('div');
      
      $javascript = <<<EOD
jQuery('#{$this->formId}-batch-actions-go').click(function() {
  if (\$('#{$this->formId}-batch-actions option:selected"').first().val() == "none") {
    alert('{$this->textNoAction}');
    return false;
  }      
    
  if(\$("input[name='{$this->checkBoxId}\[\]']:checked").length==0) {
    alert('{$this->textNoSelection}');
    return false;
  }
  
  if(confirm('{$this->textConfirm}')) {
    \$.fn.yiiGridView.update('{$this->ajaxGridId}', {
      type:'POST',
      url:\$('#{$this->formId}-batch-actions option:selected"').first().val(),
      data:\$('#{$this->formId}').serialize(),
      success:function() {
        \$.fn.yiiGridView.update('{$this->ajaxGridId}');
      },
    });
  }
});
EOD;
       
      $cs=Yii::app()->getClientScript();
      $cs->registerScript(__CLASS__.'#'.$this->formId, $javascript, CClientScript::POS_END);
    }
  }
}