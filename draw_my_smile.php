<?php
session_start();
echo <<<_HEAD
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>This is draw_my_smile.php</title>
<link href="https://bioinfmsc8.bio.ed.ac.uk/Als_stylesheet_2324.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</script>
</head>
_HEAD ;

echo <<<_EOF
<body>
<div style="padding-left:100px;">
_EOF;

if ( isset($_POST['smile'])  )
{
$smile=$_POST['smile'] ;
$bigsmile=iconv_strlen($smile, 'utf8') ;
echo "<h3>Canvas for <br/><font style=\"font-size: 20px;\">$smile<br/>($bigsmile characters)</font></h3>" ;
echo <<<_DRAW
<img data-smiles=$smile data-smiles-options="{ 'width': 800, 'height': 800 }" /> 
<script type="text/javascript" src="https://unpkg.com/smiles-drawer@2.0.1/dist/smiles-drawer.min.js"></script>
    <script>
        SmiDrawer.apply();
    </script>
_DRAW ;
unset($_POST['smile']);
} 

echo <<<_TAIL
</body>
</html>
_TAIL;
session_destroy() ;
?>
