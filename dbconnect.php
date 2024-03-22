<?php
// dbconnect.php
// Connect to the database using PDO
try {
   $conn = "mysql:host=$hostname;dbname=$database;charset=utf8";
   $options = [
       PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,// Set error mode to exceptions
       PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,// Set the default fetch mode of PDO 'fetch()' method -- return an associative array
       PDO::ATTR_EMULATE_PREPARES   => false// Use native prepared statements to prevents SQL injection attacks
   ];
   $pdo = new PDO($conn, $username, $password, $options);
   echo "Connected successfully";  // Used for debugging, should be removed in practical applications
   //here can add some database manipulation
} catch (PDOException $e) {
   die("Failed to connect to database: " . $e->getMessage());
}

?>