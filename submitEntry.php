<?php
	$servername="localhost";
	$username="anon";
	$dbname = "MajorsBB";
	$password="";
	$conn = new mysqli($servername, $username, $password, $dbname);
	$sql = "SELECT * FROM Entries WHERE Player=".$_POST['Player']." AND Scorecard_ID=".$_POST['ScorecardID'];
	$result = $conn->query($sql);
	if($result->num_rows > 0)
	{
		$sql2 = " UPDATE `Entries`SET `Golfer_1`=".$_POST['Golfer1'].", `Golfer_2`=".$_POST['Golfer2'].", `Golfer_3`=".$_POST['Golfer3'].", `Golfer_4`=".$_POST['Golfer4'].", `Total_Salary`=".$_POST['Salary']." WHERE Player=".$_POST['Player']." AND Scorecard_ID=".$_POST['ScorecardID'];
	}
	else
	{
		$sql2 = "INSERT INTO `Entries`(`Player`, `Scorecard_ID`, `Golfer_1`, `Golfer_2`, `Golfer_3`, `Golfer_4`, `Total_Salary`) VALUES ("
				.$_POST['Player'].","
				.$_POST['ScorecardID'].","
				.$_POST['Golfer1'].","
				.$_POST['Golfer2'].","
				.$_POST['Golfer3'].","
				.$_POST['Golfer4'].","
				.$_POST['Salary'].")";
	}
	if(!$results = $conn->query($sql2))
		echo "FAILURE: ".$sql2;
	else
		echo "Success";
	$conn->close();


?>