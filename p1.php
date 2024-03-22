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
  // $stmt->debugDumpParams();//debug
    // Fetch all results into an array
  $results = $stmt->fetchAll();
  $rows = count($results);
  // Close the cursor, allowing the statement to be executed again
  $stmt->closeCursor();
  //close the query
  $pdo = null;

// echo '<pre>';
// var_dump($results);
// echo '</pre>';

} catch (PDOException $e) {
  die("Failed to execute query: " . $e->getMessage());
}



$_SESSION['supmask'] = 0;
$smask = $_SESSION['supmask'];
// echo "<p>Current supplier mask: " . $_SESSION['supmask'] . "</p>";//debug

$sact = array();
for($j = 0 ; $j < $rows ; ++$j) {  // Get the results line by line
  $row = $results[$j];  // Gets the contents of the current row from the $results array
  $sid[$j] = $row['id'];  // The first column is the supplier ID 
  $snm[$j] = $row['name'];  // second colom is supplier name
  
  
  $sact[$j] = 0;  // Initialize the selected status to unselected--selected action
  $tvl = 1 << ($sid[$j] - 1);  // Calculate the bit mask
  //Determine whether the supplier is selected
  if($tvl == ($tvl & $smask)) {
    $sact[$j] = 1;//If yes, set it to selected
  }
}

// echo '<pre>';
// var_dump($sact);
// echo '</pre>';

if(isset($_POST['supplier'])) //Check that the supplier form is set
   {
     //Get submitted supplier data
     $supplier = $_POST['supplier'];//Assign the 'supplier' array of the form submission to the variable
     $nele = sizeof($supplier);//calculate the size of the '$supplier' array and store in '$nele'
      //reset the status of supplier selection 
      for($k = 0; $k <$rows; ++$k) {//Iterate over all suppliers previously retrieved from the database
       $sact[$k] = 0;
       //renew the status of supplier selection
       for($j = 0 ; $j < $nele ; ++$j) {//Iterate over the supplier names submitted by the user from the form (the $supplier array).
	 if(strcmp($supplier[$j],$snm[$k]) == 0) $sact[$k] = 1;
       }//if strcmp(...)is ture, then execute $sact[$k]=1,means selected
     }
     //calculate new bit mask
     $smask = 0;//initialise
     for($j = 0 ; $j < $rows ; ++$j)
       {
	 if($sact[$j] == 1) {
	   $smask = $smask + (1 << ($sid[$j] - 1));
	 }
       }
     $_SESSION['supmask'] = $smask;//save into session for later use or use in other pages
   }
   echo 'Currently selected Suppliers: ';
   // show selected suppliers
   for($j = 0 ; $j < $rows ; ++$j)
      {
    	if($sact[$j] == 1) {
	  echo $snm[$j] ;
	  echo " ";
	}
      }

    //Build a supplier selection form:
    echo  '<br><pre> <form action="p1.php" method="post">';
    for($j = 0 ; $j < $rows ; ++$j)
      {
      //// Check whether it is selected, and if so, add the "checked" attribute to the checkbox
      $checked = $sact[$j] ? "checked" : "";
      echo "<label>{$snm[$j]} <input type='checkbox' name='supplier[]' value='{$snm[$j]}' $checked/></label><br>";
      }



echo <<<_TAIL1
 <input type="submit" value="OK" />
</pre></form>
</body>
</html>
_TAIL1;
?>
