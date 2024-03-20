<?php
// dbconnect.php
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
?>