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

// correlation page
echo <<<_MAIN1
    <pre>
    This is the correlation Page  
    </pre>
_MAIN1;
// Check if form was filled in OK
if(isset($_POST['tgval']) && isset($_POST['tgvalb']))
   {
    // In this section we figure out what columns have been chosen
     $chosen = 0;// Default index for chosen field
     $tgval = $_POST['tgval'];// The chosen field from POST request
     $tgvalb = $_POST['tgvalb'];

     // Loop to find the index of the chosen field in the $dbfs array
     for($j = 0 ; $j <sizeof($dbfs) ; ++$j) {
       if(strcmp($dbfs[$j],$tgval) == 0) $chosen = $j; //string comparison function, if true(0),assign $j to the $chosen variable.
     } 
     for($j = 0 ; $j <sizeof($dbfs) ; ++$j) {
       if(strcmp($dbfs[$j],$tgvalb) == 0) $chosenb = $j;
     }
     // Display the chosen field and its human-readable name
     printf("Statistics for %s (%s)<br />\n",$dbfs[$chosen],$nms[$chosen]);

  // This section is from p2.php, it is the code that figures out what the "where" clause ought to be from the supplier mask
  try {
    $query_manu = "SELECT * FROM Manufacturers";
    $stmt = $pdo->prepare($query_manu);
    // call the stored procedure
    // echo "<pre>Executing SQL for manu: $query_manu</pre>";//debug

    $stmt->execute();
    // $stmt->debugDumpParams();//debug
      // Fetch all results into an array
    $results = $stmt->fetchAll();
    $rows = count($results);

    // echo '<pre>'."result";//debug
    // var_dump($results);
    // echo '</pre>';  
    
  } catch (PDOException $e) {
    die("Failed to execute query: " . $e->getMessage());
  }
  $smask = $_SESSION['supmask'];
//   echo "<p>Current supplier mask: " . $_SESSION['supmask'] . "</p>";//debug

  $firstmn = False;
  $mansel = "(";// If the manufacturer is selected, its ID is added to the $mansel string to build part of the subsequent query.
  for($j = 0 ; $j < $rows ; ++$j) {  // Get the results line by line
    $row = $results[$j];  // Gets the contents of the current row from the $results array
    $sid[$j] = $row['id'];  // The first column is the supplier ID 
    $snm[$j] = $row['name'];  // second colom is supplier name  
    $sact[$j] = 0;  // Initialize the selected status to unselected--selected action
    $tvl = 1 << ($sid[$j] - 1);  // Calculate the bit mask
    //Determine whether the supplier is selected
    if($tvl == ($tvl & $smask)) {
      $sact[$j] = 1;//If yes, set it to selected
      if($firstmn) $mansel = $mansel." or ";
        $firstmn = True;
        $mansel = $mansel." (ManuID = ".$sid[$j].")";
    }
  }
  $mansel = $mansel.")";
  
//   echo "<p>mansel: " . $mansel . "</p>";//debug
  // Here we build up a command line, execute it in Python using the script, output is sent to the webpage
  $comtodo = "./correlate3.py ".$dbfs[$chosen]." ".$dbfs[$chosenb]." \"".$mansel."\"";
    printf(" Correlation for %s (%s) vs %s (%s) \n",$dbfs[$chosen],$nms[$chosen],$dbfs[$chosenb],$nms[$chosenb]);
    $rescor = system($comtodo);//Execute an external program and display the output
    printf("\n");
   }



// This builds our form with two rows of radio buttons

echo '<form action="p4.php" method="post"><pre>';
for($j = 0 ; $j <sizeof($dbfs) ; ++$j) {
  if($j == 0) {
     printf(' %15s <input type="radio" name="tgval" value="%s" checked"/> %15s <input type="radio" name="tgvalb" value="%s" checked"/>',
$nms[$j],$dbfs[$j],$nms[$j],$dbfs[$j]);
  } else {
     printf(' %15s <input type="radio" name="tgval" value="%s"/>  %15s <input type="radio" name="tgvalb" value="%s"/>',$nms[$j],$dbfs[$j],$nms[$j],$dbfs[$j]);
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
