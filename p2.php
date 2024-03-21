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
  $query = 'SELECT * FROM Manufacturers';
  $result = $pdo->query($query);//Execute queries using PDO objects

  $rows = $result->fetchAll(PDO::FETCH_ASSOC);//Gets all rows as an associative array
  $smask = $_SESSION['supmask'];


  $mansel = "(";//Limit query results to contain only compounds from a specific manufacturer
  $firstmn = False;
  foreach ($rows as $row) {
    $sid[] = $row['id']; 
    $snm[] = $row['name'];
    $sact[] = 0;
    $tvl = 1 << ($row['id'] - 1); // Calculate the bit value for this supplier
    if ($tvl == ($tvl & $smask)) { // Check if this supplier is selected
        //$sact[count($sact) - 1] = 1; // Update the activation status
        if ($firstmn) {
          $mansel .= " OR ";
        } else {
          $firstmn = True;
        }
        $mansel .= " (ManuID = " . $row['id'] . ")";
    }
  }
  if ($firstmn) {
    $mansel .= ")"; // Add closing parentheses only at the end
  }
} catch (PDOException $e) {
  die("Failed to execute query: " . $e->getMessage());
}
  

echo <<<_MAIN1
    <pre>
This is the catalogue retrieval Page  
    </pre>
_MAIN1;

$setpar = isset($_POST['natmax']); //Check if specific POST parameters are set
$firstsl = False;//Initializes $firstsl outside of a conditional statement

//Check if a condition is added and build $compsel
if($setpar) {
  //Initializes the query criteria and parameter array
  $conditions = [];
  $params = [];  
  //Build query conditions and parameters based on user input
  if (!empty($_POST['natmax']) && !empty($_POST['natmin'])) {
    $conditions[] = "(natm > :natmin AND natm < :natmax)";
    $params[':natmin'] = $_POST['natmin'];
    $params[':natmax'] = $_POST['natmax'];  
  }

  if (!empty($_POST['ncrmax']) && !empty($_POST['ncrmin'])) {
    $conditions[] = "(ncar > :ncrmin AND ncar < :ncrmax)";
    $params[':ncrmin'] = $_POST['ncrmin'];
    $params[':ncrmax'] = $_POST['ncrmax'];
  }

  if (!empty($_POST['nntmax']) && !empty($_POST['nntmin'])) {
      $conditions[] = "(nnit > :nntmin AND nnit < :nntmax)";
      $params[':nntmin'] = $_POST['nntmin'];
      $params[':nntmax'] = $_POST['nntmax'];
  }

  if (!empty($_POST['noxmax']) && !empty($_POST['noxmin'])) {
      $conditions[] = "(noxy > :noxmin AND noxy < :noxmax)";
      $params[':noxmin'] = $_POST['noxmin'];
      $params[':noxmax'] = $_POST['noxmax'];
  }
  // $firstsl should be set to True only if there is a condition
  if (count($conditions) > 0) {
    $firstsl = True;
    $compsel = "SELECT * FROM Compounds WHERE " . implode(" AND ", $conditions);
    if ($firstmn) { // If there is a selected manufacturer, it is added to the query
      $compsel .= " AND " . $mansel;
    }
  }
}


echo '<pre>';
var_dump($_POST);
echo '</pre>';

echo '<pre>';
var_dump($params);
echo '</pre>';

  echo "<pre>";
  if($firstsl) {
    // $compsel = $compsel.") and ".$mansel;//End the condition statement and add the manufacturer condition
    // echo $compsel . "\n";
    try {
    // Execute queries using PDO
        $result =$pdo->query($compsel);
        $rows = $result->fetchAll(PDO::FETCH_ASSOC);

        $rowCount = count($rows);//count the lines of the rows
        if($rows > 100) {
          echo "Too many results ",$rows," Max is 100\n";
        } else  {
          foreach ($rows as $row) {
            echo $row['catn'] . "\n";
          }
        }
      } catch (PDOException $e) {
        die("Unable to process query: " . $e->getMessage());
      }
  } else {
    echo "No Query Given\n";
  }        
  echo "</pre>";




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