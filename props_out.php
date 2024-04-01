<?php
session_start();
require_once 'login.php';
include 'redir.php';
require_once 'dbconnect.php';
include 'menuf.php';
echo <<<_HEAD1
<html>
<head>
<link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<script src="https://unpkg.com/smiles-drawer@2.0.1/dist/smiles-drawer.min.js"></script>
<script>
function getAndDrawSmiles(smiles) {
    var smilesDrawer = new SmilesDrawer.Drawer({ width: 300, height: 200 });
    SmilesDrawer.parse(smiles, function(tree) {
        smilesDrawer.draw(tree, 'smiles-canvas', 'light', false);
    }, function (err) {
        console.error(err);
    });
}
</script>
<canvas id="smiles-canvas" width="300" height="200"></canvas>
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

if (isset($_POST['tgval']) && isset($_POST['cval'])) {
  $_SESSION['tgval'] = $_POST['tgval'];
  $_SESSION['cval'] = $_POST['cval'];
}
// Paging parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$pageSize = 30; // number of items per page
$offset = ($page - 1) * $pageSize;



// Here we generate our query
if (isset($_SESSION['tgval']) && $_SESSION['tgval'] != "" && isset($_SESSION['cval']) && $_SESSION['cval'] != "") {
  $mychoice = $_SESSION['tgval'];
  $myvalue = $_SESSION['cval'];
    

    // total query
    $totalQuery = "SELECT COUNT(*) FROM Compounds ";
    $totalQuery .= "LEFT JOIN Smiles ON Compounds.id = Smiles.cid ";
    
    if($mychoice == "mw") {
        $totalQuery .= "WHERE mw > ".($myvalue - 1.0)." AND mw < ".($myvalue + 1.0);
    }
    if($mychoice == "TPSA") {
        $totalQuery .= "WHERE TPSA > ".($myvalue - 0.1)." AND TPSA < ".($myvalue + 0.1);
    }
    if($mychoice == "XlogP") {
        $totalQuery .= "WHERE XlogP > ".($myvalue - 0.1)." AND XlogP < ".($myvalue + 0.1);
    }
    
    // Execute a query to get the total number of records
    // Execute a query to get the total number of records
    $stmt = $pdo->query($totalQuery);
    $totalRows = $stmt->fetchColumn(); 
    $totalPages = ceil($totalRows / $pageSize); // calculate total pages
    echo"total rows:$totalRows";
    echo"total pages: $totalPages";
    echo"page size: $pageSize";
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
    
    $compsel .= " LIMIT $pageSize OFFSET $offset";//modify sql query to add limit and offset
    
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
        <td>Structure</td>
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
      // Add smile drawing button
      echo "<td><button onclick=\"getAndDrawSmiles('{$row['smiles']}')\">Draw Structure</button></td>";     
            echo "</tr>";
            
      }
        echo "</table>";
      }
      // echo "<div id='pagination'>";
      // for ($i = 1; $i <= $totalPages; $i++) {
      //   echo "<a href='?page=$i'>$i</a> ";
      // }
      echo "</div>";


      echo <<<_STYLE
      <style>
      #pagination {
          font-family: 'Arial', sans-serif; /* Change the font */
          font-size: 14px; /* Change font size */
          color: #333; /* Font color */
          text-align: center; /* Center the pagination */
          margin: 20px 0; /* Add some top and bottom margin */
      }
      
      #pagination a {
          text-decoration: none; /* Remove underline */
          margin: 0 5px; /* Space on the sides */
          padding: 5px 10px; /* Padding */
          color: #337ab7; /* Link color */
          border: 1px solid #ddd; /* Border */
          background-color: #f8f8f8; /* Background color */
      }
      
      #pagination a:hover {
          background-color: #e9e9e9; /* Background color on hover */
      }
      
      #pagination a.active {
          background-color: #337ab7; /* Background color for the active link */
          color: white; /* Font color for the active link */
          border-color: #337ab7; /* Border color for the active link */
      }
      </style>
      _STYLE;

      echo "<div id='pagination'>";

      // Previous button
      if ($page > 1) {
        echo "<a href='?page=" . ($page - 1) . "'>< Previous</a> ";
      }
      // Always show the first page
      echo "<a href='?page=1'>1</a> ";
      // If there are more than 5 pages, add "..."
      if ($totalPages > 5) {
          echo " ... ";
      }
      
      // Show some pages before current page
      for ($i = max(2, $page - 2); $i < $page; $i++) {
          echo "<a href='?page=$i'>$i</a> ";
      }
      
      // Show the current page
      if ($page > 1 && $page < $totalPages) {
          echo "<a href='?page=$page'>$page</a> ";
      }
      
      // Show some pages after current page
      for ($i = $page + 1; $i < min($page + 3, $totalPages); $i++) {
          echo "<a href='?page=$i'>$i</a> ";
      }
      
      // If there's a gap between the loops and the last page, add "..."
      if ($totalPages - $page > 3) {
          echo " ... ";
      }
      
      // Always show the last page
      if ($page < $totalPages) {
          echo "<a href='?page=$totalPages'>$totalPages</a> ";
      }
      
      // Next button
      if ($page < $totalPages) {
          echo "<a href='?page=" . ($page + 1) . "'>Next ></a>";
      }
      
      echo "</div>";
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