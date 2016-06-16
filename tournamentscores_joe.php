<?php
$servername="localhost";
$username="anon";
$dbname = "MajorsBB";
$password="";
$conn = new mysqli($servername, $username, $password, $dbname);

if($conn->connect_error){
 echo "Error connecting to DB";
}

$sql = "SELECT * FROM GolferScores";
$result = $conn->query($sql);

echo "<table border='1'>
		<tr>
			<th></th>
			<th colspan='18'>Holes</th>
		</tr>";

echo "	<tr>
			<th>Golfer</th>
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

if($result->num_rows > 0)
{
 while($row = $result->fetch_assoc()) {
  echo "<tr>
  		<td></td>
  		<td>" . $row["Hole_1"] . "</td> 
  		<td>" . $row["Hole_2"] . "</td> 
  		<td>" . $row["Hole_3"] . "</td>
  		<td>" . $row["Hole_4"] . "</td>
  		<td>" . $row["Hole_5"] . "</td>
  		<td>" . $row["Hole_6"] . "</td>
  		<td>" . $row["Hole_7"] . "</td>
  		<td>" . $row["Hole_8"] . "</td>
  		<td>" . $row["Hole_9"] . "</td>
  		<td>" . $row["Hole_10"] . "</td>
  		<td>" . $row["Hole_11"] . "</td>
  		<td>" . $row["Hole_12"] . "</td>
  		<td>" . $row["Hole_13"] . "</td>
  		<td>" . $row["Hole_14"] . "</td>
  		<td>" . $row["Hole_15"] . "</td>
  		<td>" . $row["Hole_16"] . "</td>
  		<td>" . $row["Hole_17"] . "</td>
  		<td>" . $row["Hole_18"] . "</td>
  		</tr>";
  }
}

$conn->close();

?>
