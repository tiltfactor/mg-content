<ul class="actions">
<?php 
	if (isset($list)) {
		foreach ($list as $item)
			echo "<li>".$item."</li>";
	}
?>
</ul><!-- actions -->