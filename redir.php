<?php
// Check if the first and last names are already stored
if(!(isset($_SESSION['forname']) &&
     isset($_SESSION['surname'])))
  {
  // If not, redirect back to complib.php
  header('location: https://bioinfmsc8.bio.ed.ac.uk/~s2552113/complib.php');
  }
?>
