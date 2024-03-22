<?php
session_start();
require_once 'login.php';
require_once 'dbconnect.php';// Contains the database connection
include 'redir.php';
include 'menuf.php';

echo<<<_HEAD1
<html>
<body>
_HEAD1;

// Query Manufacturer information
try {
  $query_manu = "SELECT * FROM Manufacturers";
  $stmt = $pdo->prepare($query_manu);
  // call the stored procedure
  $stmt->execute();
  // $stmt->debugDumpParams();//debug
    // Fetch all results into an array
  $results = $stmt->fetchAll();
  $rows = count($results);
  // Close the cursor, allowing the statement to be executed again
  $stmt->closeCursor();
  //close the query
  // $pdo = null;

echo '<pre>'."results";
var_dump($results);
echo '</pre>';

} catch (PDOException $e) {
  die("Failed to execute query: " . $e->getMessage());
}
//看看等会要不要加 $_SESSION['supmask'] = 0;
$smask = $_SESSION['supmask'];
echo "<p>Current supplier mask: " . $_SESSION['supmask'] . "</p>";//debug

$firstmn = False;
//等下看看要不要加这一句--$sact = array();
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

echo '<pre>'. "sact";
var_dump($sact);
echo '</pre>';

echo <<<_MAIN1
    <pre>
This is the catalogue retrieval Page  
    </pre>
_MAIN1;

$setpar = isset($_POST['natmax']); 
if($setpar) {
  $conditions = [];//Stores all query conditions
  $params = []; // Stores all parameter values to be bound to the query condition
  
  // Check that 'natmax' and 'natmin' are present in the POST request and that they are not empty
  //number of atoms--natm
  if (!empty($_POST['natmax']) && !empty($_POST['natmin'])) {
    $conditions[] = "(natm > :natmin AND natm < :natmax)";//Add query criteria
    $params[':natmin'] = $_POST['natmin'];//Assign the 'natmin' value in the POST request
    $params[':natmax'] = $_POST['natmax'];
  }

  //number of carbons--ncar
  if (!empty($_POST['ncrmax']) && !empty($_POST['ncrmin'])) {
    $conditions[] = "(ncar > :ncrmin AND ncar < :ncrmax)"; 
    $params[':ncrmin'] = $_POST['ncrmin']; 
    $params[':ncrmax'] = $_POST['ncrmax'];
  }

  //number of nitrogen--nnit
  if (!empty($_POST['nntmax']) && !empty($_POST['nntmin'])) {
    $conditions[] = "(nnit > :nntmin AND nnit < :nntmax)";
    $params[':nntmin'] = $_POST['nntmin']; 
    $params[':nntmax'] = $_POST['nntmax'];
  }

  //number of oxygen--noxy
  if (!empty($_POST['noxmax']) && !empty($_POST['noxmin'])) {
    $conditions[] = "(noxy > :noxmin AND noxy < :noxmax)"; 
    $params[':noxmax'] = $_POST['noxmax'];
  }
  //adding more!--nsul/ncycl/nhdon/nhacc/nrotb

  echo "<pre>";
  // Check if any conditions are added to the condition array
  if (!empty($conditions)) {
    // If available, build a complete SQL query
    // Use the implode() function to concatenate all conditions with 'AND', forming part of the WHERE clause
    $query = "SELECT catn FROM Compounds WHERE " . implode(' AND ', $conditions);

    try {
    // Execute queries using PDO
      $stmt =$pdo-> prepare($query);
      // Bind the PHP variable to the corresponding parameter of the SQL query using the bindParam() method
      //The binding is done only if 'natmax' and 'natmin' are actually present in the POST request
      if (!empty($_POST['natmax']) && !empty($_POST['natmin'])) {
        $stmt->bindParam(':natmin', $natmin);// Bind $natmin variable to the :natmin placeholder in the SQL query
        $stmt->bindParam(':natmax', $natmax); 

      //execute query
      $stmt->execute();
      $results = $stmt->fetchAll();// Get the query result and return all the result rows as an associative array

      $rows = count($results);//count the lines of the rows
      $stmt->closeCursor();
      if($rows > 100) {
        echo "Too many results ",$rows," Max is 100\n";
      } else  {
      for($j = 0 ; $j < $rows ; ++$j)
        {
      $row = $results[$j];
      echo $row[0],"\n";
      $pdo = null;
      }
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

         
          
          

// $setpar = isset($_POST['natmax']); //Check if specific POST parameters are set
// $firstsl = False;//Initializes $firstsl outside of a conditional statement

// //Check if a condition is added and build $compsel
// if($setpar) {
//   //Initializes the query criteria and parameter array
//   $conditions = [];
//   $params = [];  
//   //Build query conditions and parameters based on user input
//   if (!empty($_POST['natmax']) && !empty($_POST['natmin'])) {
//     $conditions[] = "(natm > :natmin AND natm < :natmax)";
//     $params[':natmin'] = $_POST['natmin'];
//     $params[':natmax'] = $_POST['natmax'];  
//   }

//   if (!empty($_POST['ncrmax']) && !empty($_POST['ncrmin'])) {
//     $conditions[] = "(ncar > :ncrmin AND ncar < :ncrmax)";
//     $params[':ncrmin'] = $_POST['ncrmin'];
//     $params[':ncrmax'] = $_POST['ncrmax'];
//   }

//   if (!empty($_POST['nntmax']) && !empty($_POST['nntmin'])) {
//       $conditions[] = "(nnit > :nntmin AND nnit < :nntmax)";
//       $params[':nntmin'] = $_POST['nntmin'];
//       $params[':nntmax'] = $_POST['nntmax'];
//   }

//   if (!empty($_POST['noxmax']) && !empty($_POST['noxmin'])) {
//       $conditions[] = "(noxy > :noxmin AND noxy < :noxmax)";
//       $params[':noxmin'] = $_POST['noxmin'];
//       $params[':noxmax'] = $_POST['noxmax'];
//   }
//   // $firstsl should be set to True only if there is a condition
//   if (count($conditions) > 0) {
//     $firstsl = True;
//     $compsel = "SELECT * FROM Compounds WHERE " . implode(" AND ", $conditions);
//     if ($firstmn) { // If there is a selected manufacturer, it is added to the query
//       $compsel .= " AND " . $mansel;
//     }
//   }
// }


// echo '<pre>';
// var_dump($_POST);
// echo '</pre>';

// echo '<pre>';
// var_dump($params);
// echo '</pre>';





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