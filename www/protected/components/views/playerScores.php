<table>
  <thead>
    <tr>
      <th>Game</th>
      <th>Score</th>
      <th>Number of Games</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($games as $game) : ?>
    <tr>
      <td><?php echo $game->name; ?></td>
      <td><?php echo $game->score; ?></td>
      <td><?php echo $game->number_played; ?></td>
    </tr>
  <?php endforeach; ?>  
  </tbody>
</table>

