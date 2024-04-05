<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

session_start();
require_once 'login.php';
require_once 'dbconnect.php';// Contains the database connection
include 'redir.php';
include 'navbar.php';
echo<<<_HEAD1
<html>
<body>
_HEAD1;


// Query Manufacturer information
try {
  $query_manu = "SELECT * FROM Manufacturers";
  $stmt = $pdo->prepare($query_manu);
  // call the stored procedure
  // echo "<pre>Executing SQL for manu: $query_manu</pre>";

  $stmt->execute();
  // $stmt->debugDumpParams();//debug
    // Fetch all results into an array
  $results = $stmt->fetchAll();
  $rows = count($results);
  // Close the cursor, allowing the statement to be executed again
  // $stmt->closeCursor();
  // //close the query


  // echo '<pre>'."result";
  // var_dump($results);
  // echo '</pre>';
  
} catch (PDOException $e) {
  die("Failed to execute query: " . $e->getMessage());
}



$smask = $_SESSION['supmask'];
// echo "<p>Current supplier mask: " . $_SESSION['supmask'] . "</p>";//debug

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
echo "<p>mansel: " . $mansel . "</p>";//debug

// echo '<pre>'. "sact";
// var_dump($sact);
// echo '</pre>';

echo <<<_MAIN1
    <pre>
This is the catalogue retrieval Page  
    </pre>
_MAIN1;

try {
  // Query the maximum and minimum values of the atomic number
  $query_max_min = "SELECT MAX(natm) AS max_natm, MIN(natm) AS min_natm FROM Compounds";
  $stmt_max_min = $pdo->prepare($query_max_min);
  $stmt_max_min->execute();
  $result_max_min = $stmt_max_min->fetch(PDO::FETCH_ASSOC);

  $max_natm = $result_max_min['max_natm'];
  $min_natm = $result_max_min['min_natm'];

  // Output results as a reference
  echo "Reference range for atom count: Min = $min_natm, Max = $max_natm";

} catch (PDOException $e) {
  die("Failed to execute query: " . $e->getMessage());
}

$setpar = isset($_POST['natmax']); 
if($setpar) {
  $firstsl = False;
  $compsel = "select catn from Compounds where (";
  if (($_POST['natmax'] != "") && ($_POST['natmin']!="")) {
    $compsel = $compsel."(natm > ".get_post('natmin')." and  natm < ".get_post('natmax').")";
    $firstsl = True;
  }

  if (($_POST['ncrmax']!="") && ($_POST['ncrmin']!="")) {
    if($firstsl) $compsel = $compsel." and ";
    $compsel = $compsel."(ncar > ".get_post('ncrmin')." and  ncar < ".get_post('ncrmax').")";
    $firstsl = True;
  }

  if (($_POST['nntmax']!="") && ($_POST['nntmin']!="")) {
    if($firstsl) $compsel = $compsel." and ";
    $compsel = $compsel."(nnit > ".get_post('nntmin')." and  nnit < ".get_post('nntmax').")";
    $firstsl = True;
  }

  if (($_POST['noxmax']!="") && ($_POST['noxmin']!="")) {
    if($firstsl) $compsel = $compsel." and ";
    $compsel = $compsel."(noxy > ".get_post('noxmin')." and  noxy < ".get_post('noxmax').")";
    $firstsl = True;
  }

  echo "<pre>";
  if($firstsl) {
    $compsel = $compsel.") and ".$mansel;//End the condition statement and add the manufacturer condition
    echo $compsel . "\n";
    try {
    // Execute queries using PDO
      $stmt =$pdo->prepare($compsel);
      // echo "<pre>Executing SQL for comounds: $compsel</pre>";

      $stmt->execute();
      $results = $stmt->fetchAll();

      $rows = count($results);//count the lines of the rows
      $stmt->closeCursor();

      echo '<pre>'. "results compunds";
      var_dump($results);
      echo '</pre>';      
      echo "finish query for compounds";

      if($rows > 100) {
        echo "Too many results ",$rows," Max is 100\n";
      } else  {
      for($j = 0 ; $j < $rows ; ++$j)
        {
      $row = $results[$j];
      echo $row['catn'],"\n";
      }
    }
  } catch (PDOException $e) {
    die("Failed to execute query: " . $e->getMessage());
}  
  } else {
    echo "No Query Given\n";
  }
  echo "</pre>";
}  
echo <<<_TAIL1
   <form action="p2.php" method="post"><pre>
       Max Atoms      <input type="text" name="natmax"/>    Min Atoms    <input type="text" name="natmin"/>
       Max Carbons    <input type="text" name="ncrmax"/>    Min Carbons  <input type="text" name="ncrmin"/>
       Max Nitrogens  <input type="text" name="nntmax"/>    Min Nitrogens<input type="text" name="nntmin"/>
       Max Oxygens    <input type="text" name="noxmax"/>    Min Oxygens  <input type="text" name="noxmin"/>
                   <input type="submit" value="list" />
</pre></form>
</body>
</html>
_TAIL1;
function get_post( $var)
{
    return $_POST[$var];
}
?>