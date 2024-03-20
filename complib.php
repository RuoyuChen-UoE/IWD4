<?php
session_start();
require_once 'login.php';


// THE CONNECTION AND QUERY SECTIONS NEED TO BE MADE TO WORK FOR PHP 8 USING PDO... //
// Connect to the database using PDO
try {
   $dsn = "mysql:host=$hostname;dbname=$database;charset=utf8";
   $options = [
       PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,// Set error mode to exceptions
       PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,// Set the default fetch mode of PDO 'fetch()' method -- return an associative array
       PDO::ATTR_EMULATE_PREPARES   => false// Use native prepared statements
   ];
   $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
   die("Failed to connect to database: " . $e->getMessage());
}

// Execute a query using PDO
try {
   $query = "SELECT * FROM Manufacturers";
   $result = $pdo->query($query);

   $mask = 0;// Initialize the mask variable to 0. This will be used to build the bitmask.
   $rows = $result->fetchAll(); // Fetch all rows of 'Manufacturers'
   foreach ($rows as $row) {
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





// //finished
// $db_server = mysql_connect($db_hostname,$db_username,$db_password);
// if(!$db_server) die("Unable to connect to database: " . mysql_error());
// mysql_select_db($db_database,$db_server) or die ("Unable to select database: " . mysql_error());     
// // 


// $query = "select * from Manufacturers";
//      $result = mysql_query($query);
//      if(!$result) die("unable to process query: " . mysql_error());


//      $rows = mysql_num_rows($result);
//      $mask = 0;
//      mysql_close($db_server);
//      for($j = 0 ; $j < $rows ; ++$j)
//      {
//        $mask = (2 * $mask) + 1;
//      }
// $_SESSION['supmask'] = $mask;


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
