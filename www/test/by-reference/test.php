<?

function printy($label, $value = "") {
  print "<br /><br />$label: " . print_r($value, true) . "<br /><br />\n";
}

printy("This tests returning values by reference");


$my_array = array("one" => "1",
		  "two" => "2",
		  "three" => "3");

printy("my array", $my_array);

function & returny(&$a) {
  return $a["two"];
}

$returned = & returny($my_array);

printy("returned", $returned);

// Now change the original array.
$my_array["two"] = "dog";

Print "Changed the original array value to 'dog'";

printy("my array is now", $my_array);
printy("returned is now", $returned);

// Next, change the returned value.
$returned = "A peck of pickled peppers!";

print "changed the return value to 'a peck of pickled peppers!'";

printy("my array is now", $my_array);
printy("returned is now", $returned);

?>