<?php
session_start();
require_once 'login.php';
include 'redir.php';
require_once 'dbconnect.php';
include 'menuf.php';
echo <<<_HEAD1
<html>
<body>
_HEAD1;


$manarray = array();  //build an empty array to store manufacture query
try {
  $query = "SELECT * FROM Manufacturers";


  $stmt = $pdo->prepare($query);
  $stmt->execute();
  $results = $stmt->fetchAll();
  $rows = count($results);
  $stmt->closeCursor();

  for ($j = 0; $j < $rows; ++$j) {
    $row = $results[$j]; // Get the current row from the result array
    $manarray[] = $row['name']; // The manufacturer's name is in the second column
}
  } catch (PDOException $e) {
    die("Failed to execute query: " . $e->getMessage());
  }

echo <<<_MAIN1
    <pre>
This is the initial property retrieval page

    </pre>
_MAIN1;


// Here we generate our query
if (($_POST['tgval'] != "") && ($_POST['cval']!="")) {
    $mychoice=get_post('tgval');
    $myvalue=get_post('cval');


    // $compsel = "select * from Compounds where ";
    $compsel = "SELECT Compounds.*, Smiles.smiles FROM Compounds ";
    $compsel .= "LEFT JOIN Smiles ON Compounds.id = Smiles.cid ";
    if($mychoice == "mw") {
        $compsel .= "WHERE mw > ".($myvalue - 1.0)." AND mw < ".($myvalue + 1.0);
    }
    if($mychoice == "TPSA") {
        $compsel .= "WHERE TPSA > ".($myvalue - 0.1)." AND TPSA < ".($myvalue + 0.1);
    }
    if($mychoice == "XlogP") {
        $compsel .= "WHERE XlogP > ".($myvalue - 0.1)." AND XlogP < ".($myvalue + 0.1);
    }
    
    
    
    // if($mychoice == "mw") {
    //   $compsel = $compsel."( mw > ".($myvalue - 1.0)." and  mw < ".($myvalue + 1.0).")";
    // }
    // if($mychoice == "TPSA") {
    //   $compsel = $compsel."( TPSA > ".($myvalue - 0.1)." and  TPSA < ".($myvalue + 0.1).")";
    // }
    // if($mychoice == "XlogP") {
    //   $compsel = $compsel."( XlogP > ".($myvalue - 0.1)." and  XlogP < ".($myvalue + 0.1).")";
    // }
    echo "<pre>";
       echo $compsel;//debug
    echo "\n";
    try {
      $stmt = $pdo->query($compsel);
      $stmt->execute();
      $results = $stmt->fetchAll();
      $rows = count($results);
      $stmt->closeCursor();
      // print_r($results); //debug
    } catch(PDOException $e) {
        die("Unable to process query: " . $e->getMessage());
    }

 if($rows > 10000) {
      echo "Too many results ",$rows," Max is 10000\n";
    } else  {
      echo <<<TABLESET_
    <table border="1">
      <tr>
        <td>CAT Number</td>
        <td>Manufacturer</td>
        <td>Property</td>
      </tr>
    TABLESET_;

// This is the results processing section, which also needs to be recoded for PHP 8 PDO 
      for($j = 0 ; $j < $rows ; ++$j){
      $row = $results[$j];

      echo "<tr>";

    
      printf("<td>%s</td> <td>%s</td>", $row['catn'],$manarray[$row['ManuID'] - 1]);
      if($mychoice == "mw") {
        printf("<td>%s</td> ", $row['mw']);
      }
      if($mychoice == "TPSA") {
        printf("<td>%s</td> ", $row['TPSA']);
      }
      if($mychoice == "XlogP") {
        printf("<td>%s</td> ", $row['XLogP']);
      }     
            echo "</tr>";
      }
        echo "</table>";
      }
      } else {
    echo "No Query Given\n";
  }
echo "</pre>"; 
echo <<<_TAIL1
</body>
</html>
_TAIL1;

// Here we use a function
function get_post( $var)
{
    return $_POST[$var];
}
?>