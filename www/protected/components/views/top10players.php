<table>
  <thead>
    <tr>
      <th>Player</th>
      <th>Score</th>
      <th>Number of Games</th>  
    </tr>
  </thead>
  <tbody>
  <?php foreach ($players as $player) : ?>
    <tr>
      <td><?php echo $player->username; ?></td>
      <td><?php echo $player->score; ?></td>
      <td><?php echo $player->number_played; ?></td>
    </tr>
  <?php endforeach; ?>  
  </tbody>
</table>

