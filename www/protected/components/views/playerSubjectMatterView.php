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
        <td><?php echo $sm->name; ?></td>
        <td><?php echo $sm->interest; ?></td>
      <?php if ($admin) : ?>
        <td><?php echo $sm->expertise; ?></td>
        <td><?php echo $sm->trust; ?></td>
      <?php endif; ?>
      </tr>
    <?php endforeach; ?>  
    </tbody>
  </table>
<?php else : ?>
  <p>There are no subject matters added to the system.</p>
<?php endif; ?>
