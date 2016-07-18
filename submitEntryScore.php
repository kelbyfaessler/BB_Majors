<?php
	$servername="localhost";
	$username="anon";
	$dbname = "MajorsBB";
	$password="";
	$conn = new mysqli($servername, $username, $password, $dbname);
	$sql2 = " UPDATE Entries SET EntryScore=".$_POST["Score"]." WHERE ID=".$_POST["Entry"];
	
	
	if(!$results = $conn->query($sql2))
		echo "FAILURE: ".$sql2;
	else
		echo "Success";
	$conn->close();


?>