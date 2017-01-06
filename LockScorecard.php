<?php
$year = $_GET["y"];
$tournament = $_GET["t"];
$round = $_GET["r"];
$locked = $_GET["l"];

$servername="localhost";
$username="anon";
$dbname = "MajorsBB";
$password="";
$conn = new mysqli($servername, $username, $password, $dbname);

$sql = "UPDATE Scorecards SET IsLocked=".$locked." WHERE Year=".$year." AND Tournament=".$tournament." AND Round =".$round;
$result = $conn->query($sql);
?>