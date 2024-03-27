<?php
session_start();
include 'redir.php';
require_once 'login.php'; 
require_once 'dbconnect.php';
echo<<<_HEAD1
<html>
<body>
_HEAD1;
include 'menuf.php';
// Set labels and names

$dbfs = array("natm","ncar","nnit","noxy","nsul","ncycl","nhdon","nhacc","nrotb","mw","TPSA","XLogP"); 
$nms = array("n atoms","n carbons","n nitrogens","n oxygens","n sulphurs","n cycles","n H donors","n H acceptors","n rot bonds","mol wt","TPSA","XLogP");
echo <<<_MAIN1
    <pre>
This is the histogram page
    </pre>
_MAIN1;
// Check if form is filled in

if(isset($_POST['tgval']))                      
   {
     $chosen = 0;
     $tgval = $_POST['tgval'];
// Figure out which radio button was chosen

     for($j = 0 ; $j <sizeof($dbfs) ; ++$j) {
       if(strcmp($dbfs[$j],$tgval) == 0) $chosen = $j;
     }
    // Query Manufacturer information
    try {
        $query_manu = "SELECT * FROM Manufacturers";
        $stmt = $pdo->prepare($query_manu);
        // call the stored procedure
        echo "<pre>Executing SQL for manu: $query_manu</pre>";
    
        $stmt->execute();
        $stmt->debugDumpParams();//debug
        // Fetch all results into an array
        $results = $stmt->fetchAll();
        $rows = count($results);
        // Close the cursor, allowing the statement to be executed again
        // $stmt->closeCursor();
        // //close the query
    } catch (PDOException $e) {
        die("Failed to execute query: " . $e->getMessage());
    }
    
    $smask = $_SESSION['supmask'];
    $firstmn = False;
    // Figure out the manufacturer clause for the where statement

    $mansel = "(";
    for($j = 0 ; $j < $rows ; ++$j) {
        $row = $results[$j];
        $sid[$j] = $row['id'];
        $snm[$j] = $row['name'];
        $sact[$j] = 0;
        $tvl = 1 << ($sid[$j] - 1);
        if($tvl == ($tvl & $smask)) {
            $sact[$j] = 1;
            if($firstmn) $mansel = $mansel." or ";
                $firstmn = True;
                $mansel = $mansel." (ManuID = ".$sid[$j].")";
    }
    }
    $mansel = $mansel.")";
    // Prepare command to run external program

    $comtodo = "./histog.py ".$dbfs[$chosen]." \"".$nms[$chosen]."\" \"".$mansel."\"";
    // Run command and capture output converting to base64 encoding
// var_dump($comtodo);//debug

    $output = base64_encode(shell_exec($comtodo)); 
    
echo <<<_IMGPUT
<pre>
<img src="data:image/png;base64,$output" />                                              
</pre>
_IMGPUT ;
}
// Set up the form

echo '<form action="histogram.php" method="post"><pre>';                                            
for($j = 0 ; $j <sizeof($dbfs) ; ++$j) {
  if($j == 0) {
     printf(' %15s <input type="radio" name="tgval" value="%s" checked"/>',$nms[$j],$dbfs[$j]);
  } else {
     printf(' %15s <input type="radio" name="tgval" value="%s"/>',$nms[$j],$dbfs[$j]);
  }
  echo "\n";
}
echo '<input type="submit" value="OK" />';
echo '</pre></form>';
echo <<<_TAIL1
</body>
</html>
_TAIL1;

?>
