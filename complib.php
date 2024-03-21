<?php
session_start();
require_once 'login.php';
require_once 'dbconnect.php'; // Include dbconnect.php to establish a database connection

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

   $mask=0;
   foreach ($result as $row) {
      // In each iteration, multiply the current value of mask by 2 (equivalent to a left bit shift by 1 position), and then add 1.
      // This operation effectively sets the next lowest bit to 1, gradually building up the bitmask based on the number of rows.
      $mask = (2 * $mask) + 1;
   }
   $_SESSION['supmask'] = $mask;// store the final bitmask value under the key 'supmask'
} catch (PDOException $e) {
   die("Failed to execute query: " . $e->getMessage());
}


echo<<<_HEAD1
<html>
<body>
_HEAD1;


echo <<<_EOP
<script>
   function validate(form) {
   fail = ""
   if(form.fn.value =="") fail = "Must Give Forname "
   if(form.sn.value == "") fail += "Must Give Surname"
   if(fail =="") return true
       else {alert(fail); return false}
   }
</script>
<form action="indexp.php" method="post" onSubmit="return validate(this)">
  <pre>
       First Name<input type="text" name="fn"/>
       Second Name <input type="text" name="sn"/>
                   <input type="submit" value="go" />
</pre></form>
_EOP;

echo <<<_TAIL1
</pre>
</body>
</html>
_TAIL1;

?>
