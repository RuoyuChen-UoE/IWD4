<?php
session_start();
require_once 'login.php';
echo<<<_HEAD1
<html>
<body>
_HEAD1;

// THE CONNECTION AND QUERY SECTIONS NEED TO BE MADE TO WORK FOR PHP 8 USING PDO... //
$db_server = mysql_connect($db_hostname,$db_username,$db_password);
if(!$db_server) die("Unable to connect to database: " . mysql_error());
mysql_select_db($db_database,$db_server) or die ("Unable to select database: " . mysql_error());     
$query = "select * from Manufacturers";
     $result = mysql_query($query);
     if(!$result) die("unable to process query: " . mysql_error());


     $rows = mysql_num_rows($result);
     $mask = 0;
     mysql_close($db_server);
     for($j = 0 ; $j < $rows ; ++$j)
     {
       $mask = (2 * $mask) + 1;
     }
$_SESSION['supmask'] = $mask;
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
