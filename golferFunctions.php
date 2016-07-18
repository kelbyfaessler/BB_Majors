<?php



	switch($_POST['action']){
	 case 'remove':
		RemoveGolfer($_POST['p'],$_POST['s'],$_POST['g']);
		break;
	 case 'add':
		AddGolfer($_POST['p'],$_POST['s'],$_POST['g']);
		break;
	};


function RemoveGolfer($p, $s, $g)
{
	$servername="localhost";
	$username="anon";
	$dbname = "MajorsBB";
	$password="";
	$conn = new mysqli($servername, $username, $password, $dbname);
	$sqlNaming = "Golfer_".$g;

	$sql = "UPDATE Entries SET ".$sqlNaming."=NULL WHERE Player=".$p." AND Scorecard_ID =".$s;
	$result = $conn->query($sql);
	echo $sql;
	
}

function AddGolfer($p, $s, $g)
{


	
}

?>