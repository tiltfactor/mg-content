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
        <td class="sm-value interest">
          <?php if (!$admin) : ?><span><?php echo Yii::t('app', 'low'); ?></span><?php endif; ?>
          <?php $this->widget('application.components.MGJuiSliderInput', array(
            'name'=>"User[subjectMatters][{$sm->id}][interest]",
            'value'=>$sm->interest,
            'admin' => $admin,
            'options'=>array(
                'min'=>0,
                'max'=>100,
            ),
            'htmlOptions'=>array(
            ),
        )); ?>
          <?php if (!$admin) : ?><span><?php echo Yii::t('app', 'high'); ?></span><?php endif; ?>
        </td>
      <?php if ($admin) : ?>
        <td class="sm-value expertise">
          <?php if (!$admin) : ?><span>0</span><?php endif; ?>
          <?php $this->widget('application.components.MGJuiSliderInput', array(
            'name'=>"User[subjectMatters][{$sm->id}][expertise]",
            'value'=>$sm->expertise,
            'admin' => $admin,
            'options'=>array(
                'min'=>0,
                'max'=>100,
            ),
            'htmlOptions'=>array(
            ),
        )); ?>
          <?php if (!$admin) : ?><span>100</span><?php endif; ?>
        </td>
        <td class="sm-value trust">
          <?php if (!$admin) : ?><span>0</span><?php endif; ?>
          <?php $this->widget('application.components.MGJuiSliderInput', array(
            'name'=>"User[subjectMatters][{$sm->id}][trust]",
            'value'=>$sm->trust,
            'admin' => $admin,
            'options'=>array(
                'min'=>0,
                'max'=>100,
            ),
            'htmlOptions'=>array(
            ),
        )); ?>
          <?php if (!$admin) : ?><span>100</span><?php endif; ?>
        </td>
      <?php endif; ?>
      </tr>
    <?php endforeach; ?>  
    </tbody>
  </table>
<?php else : ?>
  <p>There are no subject matters added to the system.</p>
<?php endif; 

?>
