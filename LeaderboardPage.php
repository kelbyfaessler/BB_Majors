<?php

class Entry {
	public $player_ID = -1;
	public $player=-1;
	public $golfers= array();
	

}

class Golfer {
	public $ID=-1;
	public $Name = -1;
	public $PlayerPage = -1;
}


//our local DB params
$servername="localhost";
$username="anon";
$dbname = "MajorsBB";
$password="";
$conn = new mysqli($servername, $username, $password, $dbname);

if($conn->connect_error){
 echo "Error connecting to DB";
}

//query string params from URL
$y = $_GET["y"];
$t = $_GET["t"];
$r = $_GET["r"];

//need to scorecard ID from the Tournament + Year + Round info in order to find the right entries
$sql = "SELECT ID FROM Scorecards WHERE Year=". $y . " AND Tournament=". $t . " AND Round=".$r;

$result = $conn->query($sql);
$scorecard_id = -1;
if($result->num_rows > 0)
{
	$row = $result->fetch_assoc();
    $scorecard_id =  $row["ID"];
}

//get entries for this scorecardID
$sql = "SELECT Player, Golfer_1, Golfer_2, Golfer_3, Golfer_4 FROM Entries WHERE Scorecard_ID=". $scorecard_id;
$result = $conn->query($sql);
$entries = array();
 if($result->num_rows > 0){
	while($row = $result->fetch_assoc()) {	
		  $en = new Entry();
	      //populate Entries array which contains a Golfers array
		  $en->player_ID =  $row["Player"];
		  $en->golfers[0]["ID"] =  $row["Golfer_1"];
		  $en->golfers[1]["ID"] =  $row["Golfer_2"];
		  $en->golfers[2]["ID"] =  $row["Golfer_3"];
		  $en->golfers[3]["ID"] =  $row["Golfer_4"];	
		  $entries[] = $en;
	 }
}

//get friendly names for player and all 4 golfers
foreach($entries as $e)
{
	$sql = "SELECT Name FROM Players WHERE ID=". $e->player_ID;
	$result = $conn->query($sql);
	$row=$result->fetch_assoc();
	$e->player = $row["Name"];
	for($i=0; $i < 4 ; $i++)
	{
		$sql = "SELECT Golfer_Name FROM Golfers WHERE ID=". $e->golfers[$i]["ID"];
		$result = $conn->query($sql);
		$row=$result->fetch_assoc();
		$e->golfers[$i]["Name"] = $row["Golfer_Name"];
	}
}
unset($e);

//here is the magic
//we google the player name and "CBSSPORTS" and "PLAYERPAGE"
//this gives us the direct link to the player page, from which we can get their hole by hole data
foreach($entries as $e)
{
	for($i=0; $i < 4; $i++)
	{
		// The request also includes the userip parameter which provides the end
		// user's IP address. Doing so will help distinguish this legitimate
		// server-side traffic from traffic which doesn't come from an end-user.
		$url = 'https://www.googleapis.com/customsearch/v1?key=AIzaSyCtYDYedySqc3VyEOsU2LyqC6l67MANF98&cx=012557125435830414214:wuek1hiu0n8&alt=atom' . "&q=cbs+player+page+" . str_replace(' ', '+', $e->golfers[$i]["Name"]);
		
		echo "<br>" . $url;
		// sendRequest
		// note how referer is set manually
		 $ch = curl_init();
		 curl_setopt($ch, CURLOPT_URL, $url);
		 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		 curl_setopt($ch, CURLOPT_REFERER, 'http://174.129.171.133/LeaderboardPage.php');
		 $body = curl_exec($ch);
		 curl_close($ch);

		$arr =  explode(" ", $body);
		$playerPageUrl = "";
		foreach($arr as $v){
			echo "<br>" . $v;
			if(strpos($v, 'href') !== false)
			{
				if(strpos($v, "playerpage") && strpos($v, "cbssports"))
				{
					$a = new SimpleXMLElement("<a " . $v . "></a>");
					$playerPageUrl= $a['href'];
					echo $playerPageUrl;
				}
			}
		}
		//store the player page URL
		$e->golfers[$i]["PlayerPage"] = $playerPageUrl;

	}
}
unset($e);

//go get the full player page.
foreach($entries as $e)
{
	echo "<br>Getting data for " . $e->player;
	for($i=0; $i < 4; $i++)
	{
		echo "<br>..." . $e->golfers[$i]["Name"] . " ---- " . $e->golfers[$i]["PlayerPage"];
		$url = $e->golfers[$i]["PlayerPage"];
		$output = file_get_contents($url);
		//pass to the parsing function
		$sd = GetScorecardDataFromHtml($output);
	}
}
unset($e);

?>
<?php
//this function so far just finds the DIV that holds the table of hole-by-hole data
function GetScorecardDataFromHtml($input)
{
	$dom = new DomDocument();
	$dom->loadHTML($input);
	$finder = new DomXPath($dom);
	$nodes = $finder->query("//div[contains(@class, 'Scorecard')]");
	//need to do more selection on the nodes to find the exact numbers
	//print_r($nodes);
	$temp_dom = new DOMDocument();
	foreach($nodes as $n) $temp_dom->appendChild($temp_dom->importNode($n,true));
	//print_r($temp_dom->saveHTML());
}
?>
<!DOCTYPE html>

<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
    <script>
        $(function () {

        });
    </script>
    <meta charset="utf-8" />
    <title></title>
</head>
<body>
<div id = "dataHolder"></div>
</body>
</html>