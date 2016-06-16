<?php 
echo "Salaries";
$servername="localhost";
$username="anon";
$dbname = "MajorsBB";
$password="";
$conn = new mysqli($servername, $username, $password, $dbname);

if($conn->connect_error){
 echo "Error connecting to DB";
}

$sql = "SELECT * FROM Golfers";
$result = $conn->query($sql);
echo "<table border='1'><tr><th>Golfer</th><th>Salary</th></tr>";

if($result->num_rows > 0)
{
 while($row = $result->fetch_assoc()) {
  echo "<tr><td>" . $row["Golfer_Name"] . "</td><td> " . $row["Salary"] . "</td></tr>";
  }
}
echo "</table";
$conn->close();

?>

