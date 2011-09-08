<?php if ($subject_matters) : ?> 
  <table>
    <thead>
      <tr>
        <th>Subject Matter</th>
        <th><?php echo Yii::t('app', 'Interest'); ?></th>
        <?php if ($admin) : ?>
        <th><?php echo Yii::t('app', 'Expertise'); ?></th>
        <th><?php echo Yii::t('app', 'Trust'); ?></th>  
        <?php endif; ?>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($subject_matters as $sm) : ?>
      
      
      
      
      <tr>
        <td class="sm-name"><?php echo $sm->name; ?></td>
        <td class="sm-value interest"><span><?php echo Yii::t('app', 'low'); ?></span><?php $this->widget('zii.widgets.jui.CJuiSliderInput', array(
            'name'=>"User[subjectMatters][{$sm->id}][interest]",
            'value'=>$sm->interest,
            'options'=>array(
                'min'=>0,
                'max'=>100,
            ),
            'htmlOptions'=>array(
            ),
        )); ?><span><?php echo Yii::t('app', 'high'); ?></span></td>
      <?php if ($admin) : ?>
        <td class="sm-value expertise"><span>0</span><?php $this->widget('zii.widgets.jui.CJuiSliderInput', array(
            'name'=>"User[subjectMatters][{$sm->id}][expertise]",
            'value'=>$sm->expertise,
            'options'=>array(
                'min'=>0,
                'max'=>100,
            ),
            'htmlOptions'=>array(
            ),
        )); ?><span>100</span></td>
        <td class="sm-value trust"><span>0</span><?php $this->widget('zii.widgets.jui.CJuiSliderInput', array(
            'name'=>"User[subjectMatters][{$sm->id}][trust]",
            'value'=>$sm->trust,
            'options'=>array(
                'min'=>0,
                'max'=>100,
            ),
            'htmlOptions'=>array(
            ),
        )); ?><span>100</span></td>
      <?php endif; ?>
      </tr>
    <?php endforeach; ?>  
    </tbody>
  </table>
<?php else : ?>
  <p>There are no subject matters added to the system.</p>
<?php endif; 

/**
 * xxx remove <tr>
        <td><?php echo $sm->name; ?></td>
        <td><?php echo CHtml::textField("User[subjectMatters][{$sm->id}][interest]", $sm->interest, array("length" => 3)) ?></td>
      <?php if ($admin) : ?>
        <td><?php echo CHtml::textField("User[subjectMatters][{$sm->id}][expertise]", $sm->expertise, array("length" => 3)) ?></td>
        <td><?php echo CHtml::textField("User[subjectMatters][{$sm->id}][trust]", $sm->trust, array("length" => 3)) ?></td>
      <?php endif; ?>
      </tr>
 */
?>
