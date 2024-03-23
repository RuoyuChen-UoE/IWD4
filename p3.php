<?php
session_start();
include 'redir.php';
require_once 'login.php';
require_once 'dbconnect.php';// Contains the database connection
echo<<<_HEAD1
<html>
<body>
_HEAD1;
include 'menuf.php';
// An array of database field names
$dbfs = array("natm","ncar","nnit","noxy","nsul","ncycl","nhdon","nhacc","nrotb","mw","TPSA","XLogP");
// Corresponding human-readable names for the database fields
$nms = array("n atoms","n carbons","n nitrogens","n oxygens","n sulphurs","n cycles","n H donors","n H acceptors","n rot bonds","mol wt","TPSA","XLogP");

// Introduction message for the Statistics Page
echo <<<_MAIN1
    <pre>
This is the Statistics Page  (not Complete)
    </pre>
_MAIN1;
// Check if 'tgval' is set in POST request to process statistics for a chosen database field
if(isset($_POST['tgval'])) 
   {
     $chosen = 0;// Default index for chosen field
     $tgval = $_POST['tgval'];// The chosen field from POST request

     // Loop to find the index of the chosen field in the $dbfs array
     for($j = 0 ; $j <sizeof($dbfs) ; ++$j) {
       if(strcmp($dbfs[$j],$tgval) == 0) $chosen = $j; //string comparison function, if true(0),assign $j to the $chosen variable.
     } 
     // Display the chosen field and its human-readable name
     printf(" Statistics for %s (%s)<br />\n",$dbfs[$chosen],$nms[$chosen]);

    // connect  the database, file included in dbconnection
    //query from Compounds
    try {
      $query="SELECT AVG($dbfs[$chosen]), STD($dbfs[$chosen]) FROM Compounds";
      $stmt= $pdo -> prepare($query);
      $stmt->execute();// execute query
      $result = $stmt->fetch(PDO::FETCH_NUM);
      $stmt->closeCursor();

      if ($result) {
        // If the query is successful and returns results, use the printf function to format and output the mean and standard deviation
        printf("Average: %f  Standard Dev: %f <br />\n", $result[0], $result[1]);
    } else {
      // If the query succeeds but returns no results (e.g., the query table is empty), output "No data found".
        echo "No data found.<br />\n";
    }
} catch (PDOException $e) {
    die("Failed to execute query: " . $e->getMessage());
    }
  }

// Form for selecting a database field to view statistics
echo '<form action="p3.php" method="post"><pre>';
for($j = 0 ; $j <sizeof($dbfs) ; ++$j) {//Use a for loop to dynamically generate form elements based on the number of elements in the array.
  if($j == 0) {//'%15s' ensures that there are enough spaces before "n carbons" to make the entire string 15 characters long.
     printf(' %15s <input type="radio" name="tgval" value="%s" checked"/>',$nms[$j],$dbfs[$j]);
  } else {
     printf(' %15s <input type="radio" name="tgval" value="%s"/>',$nms[$j],$dbfs[$j]);
  }
  echo "\n";
} 
echo '<input type="submit" value="OK" />';
echo '</pre></form>';
echo <<<_TAIL1
</body>
</html>
_TAIL1;

?>