<pre>
<?php


# include parseCSV class.
require_once('../parsecsv.lib.php');


# create new parseCSV object.
$csv = new parseCSV();


# Parse '_books.csv' using automatic delimiter detection...
$csv->auto('reevoo_agdata_28012016.csv');

# ...or if you know the delimiter, set the delimiter character
# if its not the default comma...
// $csv->delimiter = "\t";   # tab delimited

# ...and then use the parse() function.
// $csv->parse('_books.csv');


# Output result.
// print_r($csv->data);


?>
</pre>

<?php 
foreach ($csv->data as $key => $row): 

	foreach ($row as $key1 => $value):

	echo $key1.' '.$value


	 endforeach; 

endforeach;



?>

