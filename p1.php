<?php
session_start();
require_once 'login.php'; // Include database login credentials
require_once 'dbconnect.php';// Contains the database connection

include 'redir.php';// Include redirection logic to ensure required session variables are set
include 'menuf.php';// Contains menu and user interface elements


try {
  $query = 'SELECT * FROM Manufacturers';
  $result = $pdo->query($query);//Execute queries using PDO objects

  $rows = $result->fetchAll(PDO::FETCH_ASSOC);//Gets all rows as an associative array
  $smask = isset($_SESSION['supmask']) ? $_SESSION['supmask'] : 0; // Retrieve or initialize the supplier mask

  $sid = [];
  $snm = [];
  $sact = [];
  foreach ($rows as $row) {
    $sid[] = $row['id']; // 'id' is the column name for supplier ID
    $snm[] = $row['name']; // 'name' is the column name for supplier name
    $sact[] = 0; // Initialize activation status as 0
    
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
  $nele = count($supplier);
  foreach ($rows as $k => $row){
    $sact[$k] = in_array($row['name'], $supplier) ? 1 : 0; // Update activation status based on form submission
  }
  // Recalculate the supplier mask based on the selected suppliers
  $smask = 0;
  foreach ($sid as $id){
      if (in_array($id, $supplier)) {
        $smask |= (1 << ($id - 1));
      } 
  }

  $_SESSION['supmask'] = $smask;// Update the supplier mask in the session
}

echo<<<_HEAD1
<html>
<body>
_HEAD1;   

# output the currently selected suppliers
echo 'Currently selected Suppliers: ';
foreach ($snm as $j => $name) {
    if ($sact[$j] == 1) {
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
