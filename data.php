<?php
header('Content-Type: application/json');

$mysqli = new mysqli("localhost","USER","PASS","USER");

if ($mysqli -> connect_errno) {
  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
  exit();
}

$sqlQuery = "SELECT * FROM `TABLE 1` WHERE `SlideName`='" . $_GET['slide'] . "'";

$result = mysqli_query($mysqli,$sqlQuery);

$data = array();
foreach ($result as $row) {
	$data[] = $row;
}

mysqli_close($mysqli);

echo json_encode($data);
?>
