<?php
//Turn on error reporting
ini_set('display_errors', 'On');
//Connect to database
$servername="localhost";
$username="anon";
$dbname = "MajorsBB";
$password="";
$conn = new mysqli($servername, $username, $password, $dbname);

if($conn->connect_errno){
	echo "Connection error " . $conn->connect_errno . " " . $conn->connect_error;
}

if(!($sql = $conn->prepare(
    "SELECT Golfers.Golfer_Name, 
    		GolferScores.Hole_1, GolferScores.Hole_2, GolferScores.Hole_3, GolferScores.Hole_4,
    		GolferScores.Hole_5, GolferScores.Hole_6, GolferScores.Hole_7, GolferScores.Hole_8,
    		GolferScores.Hole_9, GolferScores.Hole_10, GolferScores.Hole_11, GolferScores.Hole_12,
    		GolferScores.Hole_13, GolferScores.Hole_14, GolferScores.Hole_15, GolferScores.Hole_16,
    		GolferScores.Hole_17, GolferScores.Hole_18
     FROM GolferScores
     INNER JOIN Golfers ON GolferScores.Golfer_ID = Golfers.ID"
))){
	echo "Prepare failed: "  . $sql->errno . " " . $sql->error;
}

if(!$sql->execute()){
      echo "Execute failed: "  . $conn->connect_errno . " " . $conn->connect_error;
}

if(!$sql->bind_result($pname, $h1, $h2, $h3, $h4, $h5, $h6, $h7, $h8, $h9, 
							  $h10, $h11, $h12, $h13, $h14, $h15, $h16, $h17, $h18)){
      echo "Bind failed: "  . $conn->connect_errno . " " . $conn->connect_error;
}

echo "<table border='1'>";

echo "	<tr>
			<th></th>
			<th>Hole</th>
			<th>1</th>
			<th>2</th>
			<th>3</th>
			<th>4</th>
			<th>5</th>
			<th>6</th>
			<th>7</th>
			<th>8</th>
			<th>9</th>
			<th>10</th>
			<th>11</th>
			<th>12</th>
			<th>13</th>
			<th>14</th>
			<th>15</th>
			<th>16</th>
			<th>17</th>
			<th>18</th>
		</tr>";

echo "	<tr>
			<th>Golfer</th>
			<th>Par</th>
			<th>4</th>
			<th>4</th>
			<th>4</th>
			<th>4</th>
			<th>4</th>
			<th>4</th>
			<th>4</th>
			<th>4</th>
			<th>4</th>
			<th>4</th>
			<th>4</th>
			<th>4</th>
			<th>4</th>
			<th>4</th>
			<th>4</th>
			<th>4</th>
			<th>4</th>
			<th>4</th>
		</tr>";		


while($sql->fetch()) {
  echo  "<tr>" .
  		"<td colspan='2'>" . $pname . "</td>" .
  		"<td>" . $h1    . "</td>" .
  		"<td>" . $h2    . "</td>" .
  		"<td>" . $h3    . "</td>" .
  		"<td>" . $h4    . "</td>" .
  		"<td>" . $h5    . "</td>" .
  		"<td>" . $h6    . "</td>" .
  		"<td>" . $h7    . "</td>" .
  		"<td>" . $h8    . "</td>" .
  		"<td>" . $h9    . "</td>" .
  		"<td>" . $h10   . "</td>" .
  		"<td>" . $h11   . "</td>" .
  		"<td>" . $h12   . "</td>" .
  		"<td>" . $h13   . "</td>" .
  		"<td>" . $h14   . "</td>" .
  		"<td>" . $h15   . "</td>" .
  		"<td>" . $h16   . "</td>" .
  		"<td>" . $h17   . "</td>" .
  		"<td>" . $h18   . "</td>" .
  		"</tr>";
}


echo "</table>";

$conn->close();

?>