<?php

$servername="localhost";
$username="anon";
$dbname="MajorsBB";
$password="";
$conn = new mysqli($servername, $username, $password, $dbname);

$tournament = $_GET["tournament"];
$year = $_GET["year"];

echo "Year: " . $year;
echo "Tournament: " . $tournament;
$sql = "SELECT ID FROM SCORECARDS WHERE TOURNAMENT=" . $tournament. " AND YEAR=" . $year;
$result = $conn->query($sql);
if($result->num_rows > 0)
{
  echo $result["ID"];
}
$conn->close();
?>
<head runat="server">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<script>
 $(function(){

});
</script>
</head>
<table>
<tr>
<td>

</td></tr>
</table>



