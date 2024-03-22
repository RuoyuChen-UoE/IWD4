<?php
session_start();
require_once 'login.php'; // Include database login credentials
require_once 'dbconnect.php';// Contains the database connection

include 'redir.php';// Include redirection logic to ensure required session variables are set
include 'menuf.php';// Contains menu and user interface elements

echo<<<_HEAD1
<html>
<body>
_HEAD1;   

try {
  $query_manu = "SELECT * FROM Manufacturers";
  $stmt = $pdo->prepare($query_manu);
  // call the stored procedure
  $stmt->execute();
  $stmt->debugDumpParams();//debug
    // Fetch all results into an array
  $result = $stmt->fetchAll();
  // Close the cursor, allowing the statement to be executed again
  $stmt->closeCursor();
  //close the query
  $pdo = null;

  $smask = $_SESSION['supmask'];

  $sid = [];
  $snm = [];
  $sact = [];
  foreach ($result as $row) {
    $sid[] = $row['id']; // 'id' is the column name for supplier ID
    $snm[] = $row['name']; // 'name' is the column name for supplier name
    $sact[] = 0; // Initialize activation status as 0--(inactivated)
    
    $tvl = 1 << ($row['id'] - 1); // Calculate the bit value for this supplier
    if ($tvl == ($tvl & $smask)) { // Check if this supplier is selected
        $sact[count($sact) - 1] = 1; // Update the activation status
    }
  }
} catch (PDOException $e) {
  die("Failed to execute query: " . $e->getMessage());
}


// Handling form submission to update selected suppliers
if(isset($_POST['supplier'])){
  $supplier = $_POST['supplier'];
  
  // debug
  echo "<pre>";
  var_dump($supplier);
  echo "</pre>";

  $nele = count($supplier);//nele--number of emelents selected
  foreach ($result as $k => $row){
    //using 'in_array' to check if  the current suppliers' name is in the arry of suppliers selected by the user
    $sact[$k] = in_array($row['name'], $supplier) ? 1 : 0; // Update activation status based on form submission
  } // true--'1' represents selected;
  // Recalculate the supplier mask based on the selected suppliers
  $smask = 0;
  foreach ($sid as $id){
      if (in_array($id, $supplier)) {
        $smask |= (1 << ($id - 1));
      } 
  }
  $_SESSION['supmask'] = $smask;// Update the supplier mask in the session
}


# output the currently selected suppliers
  echo 'Currently selected Suppliers: ';
  foreach ($snm as $j => $name) {//foreach ($array as $key => $value)
      if ($sact[$j]) {
          echo $name . " ";
      }
  }

  // Display the form with checkboxes for each supplier
  echo '<br><pre><form action="p1.php" method="post">';
  foreach ($snm as $j => $name) {
      echo $name . '<input type="checkbox" name="supplier[]" value="' . $sid[$j] . '"' . ($sact[$j] ? ' checked' : '') . '/><br>';
  }
  echo '<input type="submit" value="OK" /></form></pre>';

echo <<<_TAIL1
</body>
</html>
_TAIL1;
?>
