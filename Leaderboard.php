<?php
// ini_set('display_errors', '1');
//ini_set('error_reporting', E_ALL);
 
 
define('INCLUDE_CHECK',true);

require 'connect.php';
require 'functions.php';


//this function so far just finds the DIV that holds the table of hole-by-hole data
		function GetScorecardDataFromHtml($input, $rnd)
		{
			$dom = new DomDocument();
			$dom->loadHTML($input);
			$finder = new DomXPath($dom);
			$nodes = $finder->query("//div[contains(@class, 'Scorecard')]");
			//need to do more selection on the nodes to find the exact numbers
			//print_r($nodes);
			$temp_dom = new DOMDocument();
			foreach($nodes as $n) $temp_dom->appendChild($temp_dom->importNode($n,true));
			
			$finder = new DomXPath($temp_dom);
			$cellValues = array();
			$go = false;
			$count = 0;
			foreach($temp_dom->getElementsByTagName('td') as $td) {
				if(count($cellValues)==18)
					break;
				if($go)
				{
					$count++;
					$cellValues[]= $td->nodeValue;
					//echo $td->nodeValue;
				}
				if($td->nodeValue==("RND ".$rnd))
				{
					$go = true;
				}
				if($count == 9)
				{
					$go = false;
					$count = 0;
			    }
			}

			return $cellValues;
			
		}
		function DashToNull($s)
		{
			if($s=="-")
				return "NULL";
			else
				return $s;
		}
		function CheckIfComplete($v)
		{
			foreach($v as $s)
			{
				if($s=="-")
					return false;				
			}
			return true;
		
		}
		function SumScores($v)
		{
			$tot = 0;
			foreach($v as $s)
			{
				$tot += $s;
			}
			
			return $tot;
		}
		function SubmitGolferScores($v, $id, $scID, $eID)
		{
			$complete = CheckIfComplete($v);
			$values = array();
			foreach($v as $st)
			{
				$values[] = DashToNull($st);
				
			}

			$servername="localhost";
			$username="anon";
			$dbname = "MajorsBB";
			$password="";
			$conn = new mysqli($servername, $username, $password, $dbname);
			if($complete)
			{
				$totalScore = SumScores($values);
				//echo "Player: " .$id. " is done at " .$totalScore;
				$sql = "SELECT TotalPar FROM Scorecards WHERE ID=".$scID;
				//echo $sql;
				$result=$conn->query($sql);
				$row = $result->fetch_assoc();
				$totalPar = $row["TotalPar"];
				
				$sql = "SELECT * FROM GolferScores WHERE Golfer_ID=".$id." AND Scorecard_ID=".$scID;
				$result=$conn->query($sql);
				
				
				if($result->num_rows >0)
				{
					$sql2 = "UPDATE GolferScores SET Hole_1=".$values[0].",Hole_2=". $values[1].",Hole_3=". $values[2].",Hole_4=". $values[3].",Hole_5=". $values[4].",Hole_6=". $values[5].",Hole_7=". $values[6].",Hole_8=". $values[7].",Hole_9=". $values[8].",Hole_10=".$values[9].",Hole_11=".$values[10].",Hole_12=".$values[11].",Hole_13=".$values[12].",Hole_14=".$values[13].",Hole_15=".$values[14].",Hole_16=".$values[15].",Hole_17=".$values[16].",Hole_18=".$values[17].",Total=".$totalScore.", Total_To_Par=".($totalScore-$totalPar)." WHERE Golfer_ID=".$id." AND Scorecard_ID=".$scID;					
				}
				else
				{
					$sql2 = "INSERT INTO GolferScores (Hole_1,Hole_2,Hole_3,Hole_4,Hole_5,Hole_6,Hole_7,Hole_8,Hole_9,Hole_10,Hole_11,Hole_12,Hole_13,Hole_14,Hole_15,Hole_16,Hole_17,Hole_18,Golfer_ID,Scorecard_ID, Total, Total_To_Par) VALUES (".$values[0].",".$values[1].",".$values[2].",".$values[3].",".$values[4].",".$values[5].",".$values[6].",".$values[7].",".$values[8].",".$values[9].",".$values[10].",".$values[11].",".$values[12].",".$values[13].",".$values[14].",".$values[15].",".$values[16].",".$values[17].",".$id.",".$scID.",".$totalScore.",".($totalScore-$totalPar).")";
				}
				if(!$results = $conn->query($sql2))
				{
					//echo "FAILURE: ".$sql2;
				}
				else
				{
					//echo "Success";
				}
					
				
				$conn->close();
			}
			else
			{		
				$sql = "SELECT * FROM GolferScores WHERE Golfer_ID=".$id." AND Scorecard_ID=".$scID;
				$result=$conn->query($sql);
				
				
				if($result->num_rows >0)
				{
					$sql2 = "UPDATE GolferScores SET Hole_1=".$values[0].",Hole_2=". $values[1].",Hole_3=". $values[2].",Hole_4=". $values[3].",Hole_5=". $values[4].",Hole_6=". $values[5].",Hole_7=". $values[6].",Hole_8=". $values[7].",Hole_9=". $values[8].",Hole_10=".$values[9].",Hole_11=".$values[10].",Hole_12=".$values[11].",Hole_13=".$values[12].",Hole_14=".$values[13].",Hole_15=".$values[14].",Hole_16=".$values[15].",Hole_17=".$values[16].",Hole_18=".$values[17]." WHERE Golfer_ID=".$id." AND Scorecard_ID=".$scID;					
				}
				else
				{
					$sql2 = "INSERT INTO GolferScores (Hole_1,Hole_2,Hole_3,Hole_4,Hole_5,Hole_6,Hole_7,Hole_8,Hole_9,Hole_10,Hole_11,Hole_12,Hole_13,Hole_14,Hole_15,Hole_16,Hole_17,Hole_18,Golfer_ID,Scorecard_ID) VALUES (".$values[0].",".$values[1].",".$values[2].",".$values[3].",".$values[4].",".$values[5].",".$values[6].",".$values[7].",".$values[8].",".$values[9].",".$values[10].",".$values[11].",".$values[12].",".$values[13].",".$values[14].",".$values[15].",".$values[16].",".$values[17].",".$id.",".$scID.")";
				}
				if(!$results = $conn->query($sql2))
				{
					//echo "FAILURE: ".$sql2;
				}
				else
				{
					//echo "Success";
					}
				$conn->close();
			}
			
		}
		
		
		
		
session_name('tzLogin');
// Starting the session

session_set_cookie_params(2*7*24*60*60);
// Making the cookie live for 2 weeks

session_start();

if($_SESSION['id'] && !isset($_COOKIE['tzRemember']) && !$_SESSION['rememberMe'])
{
	// If you are logged in, but you don't have the tzRemember cookie (browser restart)
	// and you have not checked the rememberMe checkbox:

	$_SESSION = array();
	session_destroy();
	
	// Destroy the session
}


if(isset($_GET['logoff']))
{
	$_SESSION = array();
	//session_destroy();
	
	header("Location: MajorsBBHome.php");
	exit;
}

if($_POST['submit']=='Login')
{
	// Checking whether the Login form has been submitted
	
	$err = array();
	// Will hold our errors
	
	
	if(!$_POST['username'] || !$_POST['password'])
		$err[] = 'All the fields must be filled in!';
	
	if(!count($err))
	{
		//$_POST['username'] = mysqli_real_escape_string($_POST['username']);
		//$_POST['password'] = mysqli_real_escape_string($_POST['password']);
		$_POST['rememberMe'] = (int)$_POST['rememberMe'];
		
		// Escaping all input data
		$loginQuery = "SELECT id,usr FROM mbb_members WHERE usr='".$_POST['username']."' AND pass='".md5($_POST['password'])."'";
		$row = mysqli_fetch_assoc($link->query($loginQuery));
		//echo $loginQuery;
		//echo "\r\n";
		//echo $row['usr'];
		if($row['usr'])
		{
			// If everything is OK login
			
			$_SESSION['usr']=$row['usr'];
			$_SESSION['id'] = $row['id'];
			$_SESSION['rememberMe'] = $_POST['rememberMe'];
			
			// Store some data in the session
			
			setcookie('tzRemember',$_POST['rememberMe']);
		}
		else $err[]='Wrong username and/or password!';
	}
	
	if($err)
	$_SESSION['msg']['login-err'] = implode('<br />',$err);
	// Save the error messages in the session

	header("Refresh:0");
	exit;
}
else if($_POST['submit']=='Register')
{
	// If the Register form has been submitted
	
	$err = array();
	
	if(strlen($_POST['username'])<3 || strlen($_POST['username'])>32)
	{
		$err[]='Your username must be between 3 and 32 characters!';
	}
	
	if(preg_match('/[^a-z0-9\-\_\.]+/i',$_POST['username']))
	{
		$err[]='Your username contains invalid characters!';
	}
	
	if(!checkEmail($_POST['email']))
	{
		$err[]='Your email is not valid!';
	}
	 if(!$_POST['password']===$_POST['passwordconfirm'])
	 {
		 $err[]="Passwords do not match";
	 }
	if(!count($err))
	{
		// If there are no errors
		
		$pass = $_POST['password'];
		// Generate a random password
		
		//$_POST['email'] = mysqli_real_escape_string($_POST['email']);
		//$_POST['username'] = mysqli_real_escape_string($_POST['username']);
		// Escape the input data
		
		$sql_query_string = "	INSERT INTO mbb_members(usr,pass,email,dt)
						VALUES(						
							'".$_POST['username']."',
							'".md5($pass)."',
							'".$_POST['email']."',
							NOW()							
						)";
		$message = $sql_query_string;
		echo "<script type='text/javascript'>alert('$message');</script>";
		$link->query($sql_query_string);
		
		if(mysqli_affected_rows($link)==1)
		{
			// if(!send_mail(	'jstella06@gmail.com',
						// $_POST['email'],
						// 'Registration System Demo - Your New Password',
						// 'Your password is: '.$pass))
						// $err[] = "Failed to send email";

			$_SESSION['msg']['reg-success']='Success!';
		}
		else $err[]='This username is already taken!';
	}

	if(count($err))
	{
		$_SESSION['msg']['reg-err'] = implode('<br />',$err);
	}	
	
	header("Refresh:0");
	exit;
}

$script = '';

if($_SESSION['msg'])
{
	// The script below shows the sliding panel on page load
	
	$script = '
	<script type="text/javascript">
	
		$(function(){
		
			$("div#panel").show();
			$("#toggle a").toggle();
		});
	
	</script>';
	
}
?>



<?php
  //ini_set('display_errors', '1');
  //ini_set('error_reporting', E_ALL);
 class Entry {
	 public $player_ID = -1;
	 public $player=-1;
	 public $golfers= array();
	 public $scID=-1;
	 public $rnd = -1;
	 public $entryID = -1;
	

 }

class Golfer {
	 public $ID=-1;
	 public $Name = -1;
	 public $PlayerPage = -1;
	 public $scores = array();
	 }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Leaderboard</title>
      
    <link rel="stylesheet" type="text/css" href="demo.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="login_panel/css/slide.css" media="screen" />
    
    <script type="text/javascript" src="jquery-3.2.0.min.js"></script>
     
    <script src="login_panel/js/slide.js" type="text/javascript"></script>
    
    <?php echo $script; ?>
	
	
	
  <script src="jquery-ui-1.12.1.custom/jquery-ui.js"></script>
	<script type="text/javascript">
	function giveMaxIfEmpty(score)
	{
		if(score=="")
			return 20;
		else
			return score;
	}
	function resizeTables()
{
    var tableArr = document.getElementsByTagName('table');
    var cellWidths = new Array();

    // get widest
    for(i = 0; i < tableArr.length; i++)
    {
        for(j = 0; j < tableArr[i].rows[0].cells.length; j++)
        {
           var cell = tableArr[i].rows[0].cells[j];

           if(!cellWidths[j] || cellWidths[j] < cell.clientWidth)
                cellWidths[j] = cell.clientWidth;
        }
    }

    // set all columns to the widest width found
    for(i = 0; i < tableArr.length; i++)
    {
        for(j = 0; j < tableArr[i].rows[0].cells.length; j++)
        {
            tableArr[i].rows[0].cells[j].style.width = cellWidths[j]+'px';
        }
    }
}
	function CalculateScore(player, round, hole){
			var par = $(".r" + round + "h" + hole + "par.holePar").html();
			
			var g1score = $("." + player + "> .r" + round + "h" + hole + "g" + 0 + "score.holeScore").html();
			
			if((g1score - par)==0 && g1score != "")
				$("." + player + "> .r" + round + "h" + hole + "g" + 0 + "score.holeScore").addClass("gs_even");
			if((g1score - par)==-1 && g1score != "")
				$("." + player + "> .r" + round + "h" + hole + "g" + 0 + "score.holeScore").addClass("gs_good");
			if((g1score - par)<-1 && g1score != "")
				$("." + player + "> .r" + round + "h" + hole + "g" + 0 + "score.holeScore").addClass("gs_best");
			if((g1score - par)==1 && g1score != "")
				$("." + player + "> .r" + round + "h" + hole + "g" + 0 + "score.holeScore").addClass("gs_bad");
			if((g1score - par)> 1 && g1score != "")
				$("." + player + "> .r" + round + "h" + hole + "g" + 0 + "score.holeScore").addClass("gs_worst");
			
			var g2score = $("." + player + "> .r" + round + "h" + hole + "g" + 1 + "score.holeScore").html();
			
			if((g2score - par)==0 && g2score != "")
				$("." + player + "> .r" + round + "h" + hole + "g" + 1 + "score.holeScore").addClass("gs_even");
			if((g2score - par)==-1 && g2score != "")
				$("." + player + "> .r" + round + "h" + hole + "g" + 1 + "score.holeScore").addClass("gs_good");
			if((g2score - par)<-1 && g2score != "")
				$("." + player + "> .r" + round + "h" + hole + "g" + 1 + "score.holeScore").addClass("gs_best");
			if((g2score - par)==1 && g2score != "")
				$("." + player + "> .r" + round + "h" + hole + "g" + 1 + "score.holeScore").addClass("gs_bad");
			if((g2score - par)> 1 && g2score != "")
				$("." + player + "> .r" + round + "h" + hole + "g" + 1 + "score.holeScore").addClass("gs_worst");
			
			var g3score = $("." + player + "> .r" + round + "h" + hole + "g" + 2 + "score.holeScore").html();
			
			if((g3score - par)==0 && g3score != "")
				$("." + player + "> .r" + round + "h" + hole + "g" + 2 + "score.holeScore").addClass("gs_even");
			if((g3score - par)==-1 && g3score != "")                 
				$("." + player + "> .r" + round + "h" + hole + "g" + 2 + "score.holeScore").addClass("gs_good");
			if((g3score - par)<-1 && g3score != "")                  
				$("." + player + "> .r" + round + "h" + hole + "g" + 2 + "score.holeScore").addClass("gs_best");
			if((g3score - par)==1 && g3score != "")                  
				$("." + player + "> .r" + round + "h" + hole + "g" + 2 + "score.holeScore").addClass("gs_bad");
			if((g3score - par)> 1 && g3score != "")                  
				$("." + player + "> .r" + round + "h" + hole + "g" + 2 + "score.holeScore").addClass("gs_worst");
			
			var g4score = $("." + player + "> .r" + round + "h" + hole + "g" + 3 + "score.holeScore").html();
			
			if((g4score - par)==0 && g4score != "")
				$("." + player + "> .r" + round + "h" + hole + "g" + 3 + "score.holeScore").addClass("gs_even");
			if((g4score - par)==-1 && g4score != "")                 
				$("." + player + "> .r" + round + "h" + hole + "g" + 3 + "score.holeScore").addClass("gs_good");
			if((g4score - par)<-1 && g4score != "")                  
				$("." + player + "> .r" + round + "h" + hole + "g" + 3 + "score.holeScore").addClass("gs_best");
			if((g4score - par)==1 && g4score != "")                  
				$("." + player + "> .r" + round + "h" + hole + "g" + 3 + "score.holeScore").addClass("gs_bad");
			if((g4score - par)> 1 && g4score != "")                  
				$("." + player + "> .r" + round + "h" + hole + "g" + 3 + "score.holeScore").addClass("gs_worst");
			
			var lowBall = Math.min(giveMaxIfEmpty(g1score),
								   giveMaxIfEmpty(g2score),
								   giveMaxIfEmpty(g3score),
								   giveMaxIfEmpty(g4score));
								   
		   if(lowBall==20)
		   {
			   $("." + player + "> .r" + round + "h" + hole + "teamScore").addClass("hs_none");
			   return 0;
		   }
			
			
			
			var score = lowBall - par;
			
			$("." + player + "> .r" + round + "h" + hole + "teamScore").html(score);
			switch(true)
			{
				case (score == 0):
					$("." + player + "> .r" + round + "h" + hole + "teamScore").addClass("hs_even");
					break;
				case (score == 1):
					$("." + player + "> .r" + round + "h" + hole + "teamScore").addClass("hs_bad");
					break;
				case (score > 1):
					$("." + player + "> .r" + round + "h" + hole + "teamScore").addClass("hs_worst");
					break;
				case (score== -1):
					$("." + player + "> .r" + round + "h" + hole + "teamScore").addClass("hs_good");
					break;
				case (score < -1):
					$("." + player + "> .r" + round + "h" + hole + "teamScore").addClass("hs_best");
					break;				
			} 
			
			return score;
		}
	function IsPlayerDone(player, round)
	{
		var g0Done = ''!=($("." + player + "> .r" + round + "totg0score").html());
		var g1Done = ''!=($("." + player + "> .r" + round + "totg1score").html());
		var g2Done = ''!=($("." + player + "> .r" + round + "totg2score").html());
		var g3Done = ''!=($("." + player + "> .r" + round + "totg3score").html());
		if(g0Done && g1Done && g2Done && g3Done)
			return true;
	}
	document.onreadystatechange = function () {
	  var state = document.readyState
	  if (state == 'interactive') {
		   document.getElementById('contentDiv').style.visibility="hidden";
	  } else if (state == 'complete') {
		  setTimeout(function(){
			 document.getElementById('interactive');
			 document.getElementById('loadingSpinner').style.visibility="hidden";
			 document.getElementById('contentDiv').style.visibility="visible";
		  },1000);
	  }
	}
	$(document).ready(function(){
		
		$(".scoreRow").each(function(){
			var player = $(this).attr("player");
			var playerTotalScore=0;
			for(var i = 1; i < 5; i++)
			{
				var roundScore = 0;
				for(var j = 1; j<19;j++)
				{
					roundScore += CalculateScore(player, i, j);				
				}
				$("." + player + "> .r" + i + "totalTeamScore").html(roundScore).addClass("teamRoundScore");				
				if(!isNaN(roundScore))
					playerTotalScore += roundScore;	

					var entry = $(".r" + i, $(this).prev()).attr("entry")
					var dataString = "Entry=" + entry + "&Score="+roundScore;
					$.ajax({
						type: "POST",
						url: "submitEntryScore.php",
						data: dataString,
						cache: false,
						success: function(result){
							//alert(result);
						}
					});
				//}
			}
			$("." + player + " > .playerScore").html(playerTotalScore).addClass("teamTotalScore");
			$("li").each(function(){
				$("tbody." + player).attr("playerScore", playerTotalScore);
			});
			
		
		});
		$("#leaderboardList").accordion();
		$("#leaderboardList li").each(function(){
			$(this).removeClass('active');
			$("tbody.pgroup").hide();
			$("tbody.pgroup_collapsed").show();
		});
	    activeItem = $("#leaderboardList li.activeRound");
	    $(activeItem).addClass('active');
		$("tbody.pgroup", $(activeItem)).show();
		$("tbody.pgroup_collapsed", $(activeItem)).hide();
		// $("#leaderboardList li").hover(function(){
			// //$(activeItem).animate({width: "50px"}, {duration:300, queue:false});
			// //$(this).animate({width: "450px"}, {duration:300, queue:false});
			// activeItem = this;
		// });

		$("#leaderboardList li:not(.static)").click(function(){
			$("#leaderboardList li").each(function(){
				$(this).removeClass('active');
				$("tbody.pgroup").hide();
				$("tbody.pgroup_collapsed").show();
			});
			$(this).addClass('active');
			$("tbody.pgroup", $(this)).show();
			$("tbody.pgroup_collapsed", $(this)).hide();
		});
		
		
		var count = 300 - parseInt($("#refreshInterval").html());
		if(count < 0)
			count = 300;
		var counter = setInterval(timer, 1000);
		function str_pad_left(string,pad,length) {
			return (new Array(length+1).join(pad)+string).slice(-length);
		}
		function timer()
		{
			count = count -1;
			if(count <=0)
			{
				location.reload();
				return;
			}
			var minutes = Math.floor(count/60);
			var seconds = count-minutes*60;
			$("#refreshInterval").html("Refreshing in: " + str_pad_left(minutes,'0',2) + ":" + str_pad_left(seconds,'0',2));
			
		}
		
		// $("tbody.loggedinuser").each(function(){
			// $(this).prependTo($(this).closest('table'));
		// });
		// $("tbody.headerSection").each(function(){
			// $(this).prependTo($(this).closest('table'));
		// });
		resizeTables();
		
		$("li.scoreTab").each(function(){
			var tbodies = $("table tbody:not(.headerSection)", $(this));
			tbodies.sort(function(a, b){
				var an = parseInt($(a).attr("playerScore"));
				var bn = parseInt($(b).attr("playerScore"));
				if(an < bn)
					return -1;
				if(bn < an)
					return 1;
				if(an==bn)
				{
				   if($(a).attr("class") > $(b).attr("class"))
					return -1;
				   else
					return 1;
				}
				return 0;
			});
			$("table", $(this)).append(tbodies);
		});
	});
	</script>
<style>
 .holeNum, .holePar, .holeScore, .gRoundScore, .teamScore {
	 
	 border:none;

	 
 }
 .holeScore {
	 text-align:right;
      width: 14px;
	  max-width:14px;
    height: 14px;
	max-height:14px;
	min-height:14px;
	padding-left: 5px;
	padding-right: 5px;
	overflow:hidden;
	 
 }
 tbody{
	display:block;
    border-top: 2px solid #ccc;
	border-bottom: 2px solid #ccc;
	
	border-left: 2px solid #ccc;
    border-collapse: separate;
    border-spacing: 4px; 
 }
 table{
	 empty-cells:show;
 }
 .gRoundScore{
	 color:#a8a8a8;
 }
  
 .golferName {
	 width: 140px;
	 white-space: nowrap; 
	 overflow: hidden;
     color: #555555;
     font-size: 12px;
     background: #00000;
     font-family: Arial, Helvetica, sans-serif;

 }
 .teamRoundScore{
	 font-size:14px;
	 font-weight:bold;
 }

 .playerName {
	 font-weight:bold;
 }
 td.hs_even{
	color: black;
    width: 14px;
    height: 14px;
    border: none;
    color: #666;
	text-align:center;
    font: 12px Arial, sans-serif;
 }
 td.hs_bad{
	color: black;
    width: 14px;
    height: 14px;
    background: #FCABAB;
    border: 1px solid #666;
    color: #666;
	text-align:center;
    font: 12px Arial, sans-serif;
 }
 td.hs_worst{
	color: black;
    width: 14px;
    height: 14px;
    background: #E44444;
    border: 1px solid #666;
    color: #666;
	text-align:center;
    font: 12px Arial, sans-serif;
 }
 td.hs_good{
	color: black;
    border-radius: 50%;
    width: 14px;
    height: 14px;
    background: #99ccff;
    border: 1px solid #666;
    color: #666;
	text-align:center;
    font: 12px Arial, sans-serif;
}
 
 td.hs_best{
	color: black;
    border-radius: 50%;
    width: 14px;
    height: 14px;
    background: #74A7F2;
    border: 1px solid #666;
    color: #666;
	text-align:center;
    font: 12px Arial, sans-serif;
 }
 td.hs_none{
	color: black;    
    width: 14px;
    height: 14px;
    padding: 5px;
    background: #ccc;
    border: none;
    color: #666;
	text-align:center;
    font: 12px Arial, sans-serif;
 }
 td.gs_even{color:#a8a8a8;}
  td.gs_good{color: #99ccff;}
 td.gs_best{color: #74A7F2;}
 td.gs_bad{color: #FCABAB;}
 td.gs_worst{color: #E44444;}
 
 #leaderboardList{
	list-style: none;
	margin: 30px 0;
	padding: 0;
	height: 100%;
	width:auto;
	overflow: hidden;
	background: #FFFFFF;
}
 
#leaderboardList li{
	float: left;
	border-left:
	display: block;
	height: 100%;
	width: 65px;
	padding: 15px 0;
	overflow: hidden;
	color: #000000;
	text-decoration: none;
	font-size: 16px;
	line-height: 1.5em;
	
	}
 
#leaderboardList li img {
	border: none;
	border-right: 1px solid #fff;
	float: left;
	margin: -15px 15px 0 0;
}
 
#leaderboardList li.active {
	width: auto;
}
#loadingSpinner{
    width:100%;
    height:100%;
    position:fixed;
    z-index:9999;
    background:url("https://www.creditmutuel.fr/cmne/fr/banques/webservices/nswr/images/loading.gif") no-repeat center center rgba(0,0,0,0.25)
}

</style>
</head>

<body>

<!-- Panel -->
<div id="toppanel">
	<div id="panel">
		<div class="content clearfix">
       
            
            <?php
			
			if(!$_SESSION['id']):
			
			?>
            
			<div class="left">
				<!-- Login Form -->
				<form class="clearfix" action="" method="post">
					<h1>Member Login</h1>
                    
                    <?php
						
						if($_SESSION['msg']['login-err'])
						{
							echo '<div class="err">'.$_SESSION['msg']['login-err'].'</div>';
							unset($_SESSION['msg']['login-err']);
						}
					?>
					
					<label class="grey" for="username">Username:</label>
					<input class="field" type="text" name="username" id="username" value="" size="23" />
					<label class="grey" for="password">Password:</label>
					<input class="field" type="password" name="password" id="password" size="23" />
	            	<label><input name="rememberMe" id="rememberMe" type="checkbox" checked="checked" value="1" /> &nbsp;Remember me</label>
        			<div class="clear"></div>
					<input type="submit" name="submit" value="Login" class="bt_login" />
				</form>
			</div>
			<div class="left right">			
				<!-- Register Form -->
				<form action="" method="post">
					<h1>Not a member yet? Sign Up!</h1>		
                    
                    <?php
						
						if($_SESSION['msg']['reg-err'])
						{
							echo '<div class="err">'.$_SESSION['msg']['reg-err'].'</div>';
							unset($_SESSION['msg']['reg-err']);
						}
						
						if($_SESSION['msg']['reg-success'])
						{
							echo '<div class="success">'.$_SESSION['msg']['reg-success'].'</div>';
							unset($_SESSION['msg']['reg-success']);
						}
					?>
                    		
					<label class="grey" for="username">Username:</label>
					<input class="field" type="text" name="username" id="username" value="" size="23" />
					<label class="grey" for="email">Email:</label>
					<input class="field" type="text" name="email" id="email" size="23" />
					<label class="grey" for="email">Password:</label>
					<input class="field" type="password" name="password" id="password" size="23" />
					<label class="grey" for="email">Confirm:</label>
					<input class="field" type="password" name="passwordconfirm" id="passwordconfirm" size="23" />

					<input type="submit" name="submit" value="Register" class="bt_register" />
				</form>
			</div>
            
            <?php
			
			else:
			
			?>
            
            <div class="left">
            
 
            <a href="?logoff">Log off</a>
            
            </div>
            
            <div class="left right">
            </div>
            
            <?php
			endif;
			?>
		</div>
	</div> <!-- /login -->	

    <!-- The tab on top -->	
	<div class="tab">
		<ul class="login">
	    	<li class="left">&nbsp;</li>
	        <li>Hello <?php echo $_SESSION['usr'] ? $_SESSION['usr'] : 'Guest';?>!</li>
			<li class="sep">|</li>
			<li id="toggle">
				<a id="open" class="open" href="#"><?php echo $_SESSION['id']?'Open Panel':'Log In | Register';?></a>
				<a id="close" style="display: none;" class="close" href="#">Close Panel</a>			
			</li>
	    	<li class="right">&nbsp;</li>
		</ul> 
	</div> <!-- / top -->
	
</div> <!--panel -->

<!-- PUT ALL CONTENT HERE -->
<div id='loadingSpinner'></div>
<div id='contentDiv' style='width: 1000px;margin:auto; margin-top:75px;background-color:#FFFFFF;top: 0;right: 0;bottom: 0;left: 0;height:auto;border-radius: 8px;overflow:hidden;'>
	<div><span style='float-left'><a href="MajorsBBHome.php">&#60&#60Return Home</a></span>
	<?php
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
	$sql = "SELECT * FROM Scorecards WHERE Year=". $y . " AND Tournament=". $t;

	$result = $conn->query($sql);
	$scorecard_id = -1;
	if($result->num_rows > 0)
	{
		$row = $result->fetch_assoc();
		$tourn =  $row["Name"];
	}
	
	//echo "<table style='border:solid; ' BORDER=2 RULES=GROUPS id='leaderboard'>";
	//echo "<colgroup span=21></colgroup>";
	//echo "<colgroup span=21></colgroup>";
	//echo "<colgroup span=21></colgroup>";
	//echo "<colgroup span=21></colgroup>";
	//echo "<tr>";
	//for($j=1; $j<5; $j++)
	//{
	//	echo "<td>&nbsp</td><td>&nbsp</td><td colspan=19 style='text-align:center'>Round ".$j."</td>";
	//}
	//echo "</tr><tr>";
	
	$roundNumber = 0;
	
	for($j=1; $j<5; $j++)
	{
		$sql = "SELECT * FROM Scorecards WHERE Year=". $y . " AND Tournament=". $t . " AND Round=" .$j;

		$result = $conn->query($sql);
		$row = $result->fetch_assoc();
		$l = $row["IsLocked"];
		
		if($l==1)
		{
			
			$sql = "SELECT * FROM Scorecards WHERE Year=". $y . " AND Tournament=". $t . " AND Round=" .$j;
			$result = $conn->query($sql);
			$row = $result->fetch_assoc();
			$lastScrapeTimeStamp=$row["LastRefreshed"];
			$lastScrapeTime=strtotime($lastScrapeTimeStamp);					
			$curTime = time();
			$interval = $curTime - $lastScrapeTime;
			$roundNumber++;
		}
	}
	if(!$intervalPrinted)
	{
		echo "<span style='margin-right:10px;float:right' id='refreshInterval'>$interval</span>";
		$intervalPrinted = true;
	}
	if($interval > 300 || $letsRefresh)
	{
		$letsRefresh = true;
		//echo "New Scrape Happening...";
		if(!$lastRefreshUpdated)
		{
			$sql = "UPDATE Scorecards SET LastRefreshed= now() WHERE ID = " .$j;
			$result = $conn->query($sql);
			$lastRefreshUpdated = true;
		}
	}
	echo "</div>";
	echo "<div style='width:100%;text-align:center'><h1>".$y." ".$tourn." Leaderboard</h1></div>";
	echo "<div style='width:100%; text-align:center'>Click on a round's column to expand</div>";
	echo "<ul id='leaderboardList'>";
	for($j=0; $j<5; $j++)
	{
		
		//need to scorecard ID from the Tournament + Year + Round info in order to find the right entries
		$sql = "SELECT * FROM Scorecards WHERE Year=". $y . " AND Tournament=". $t . " AND Round=" .$j;

		$result = $conn->query($sql);
		$scorecard_id = -1;
		if($result->num_rows > 0)
		{
			$row = $result->fetch_assoc();
			$scorecard_id =  $row["ID"];
		}
		if($j>0)
		{
		//start the list item containing the round's table
		if($roundNumber==$j)
			echo "<li class='scoreTab activeRound ".$roundNumber."'><table id='round_".$j."' class='roundTable' style='table-layout:fixed;'>";
		else
			echo "<li class='scoreTab'><table id='round_".$j."' class='roundTable' style='table-layout:fixed;'>";
	    echo "<tbody class='headerSection'><tr><td><h3>RND".$j."</h3></td></tr><tr>";
		//create a row containing number 1-18 and Total
		echo "
			  <td class='golferName'></td>
			  <td class='holeNum holeScore'>1</td>
			  <td class='holeNum holeScore'>2</td>
			  <td class='holeNum holeScore'>3</td>
			  <td class='holeNum holeScore'>4</td>
			  <td class='holeNum holeScore'>5</td>
			  <td class='holeNum holeScore'>6</td>
			  <td class='holeNum holeScore'>7</td>
			  <td class='holeNum holeScore'>8</td>
			  <td class='holeNum holeScore'>9</td>
			  <td class='holeNum holeScore'>10</td>
			  <td class='holeNum holeScore'>11</td>
			  <td class='holeNum holeScore'>12</td>
			  <td class='holeNum holeScore'>13</td>
			  <td class='holeNum holeScore'>14</td>
			  <td class='holeNum holeScore'>15</td>
			  <td class='holeNum holeScore'>16</td>
			  <td class='holeNum holeScore'>17</td>
			  <td class='holeNum holeScore'>18</td>
			  <td class='holeNum holeScore'>Tot</td>";
		echo "</tr>";
		
		echo "<tr id='holePars'>";

		//fill row with each hole's par and total par
		echo "
		      <td class='golferName'></td>
		      <td class='r".$j."h1par holePar holeScore'>".$row["Hole_1"]."</td>
		      <td class='r".$j."h2par holePar holeScore'>".$row["Hole_2"]."</td>
			  <td class='r".$j."h3par holePar holeScore'>".$row["Hole_3"]."</td>
			  <td class='r".$j."h4par holePar holeScore'>".$row["Hole_4"]."</td>
			  <td class='r".$j."h5par holePar holeScore'>".$row["Hole_5"]."</td>
			  <td class='r".$j."h6par holePar holeScore'>".$row["Hole_6"]."</td>
			  <td class='r".$j."h7par holePar holeScore'>".$row["Hole_7"]."</td>
			  <td class='r".$j."h8par holePar holeScore'>".$row["Hole_8"]."</td>
			  <td class='r".$j."h9par holePar holeScore'>".$row["Hole_9"]."</td>
			  <td class='r".$j."h10par holePar holeScore'>".$row["Hole_10"]."</td>
			  <td class='r".$j."h11par holePar holeScore'>".$row["Hole_11"]."</td>
			  <td class='r".$j."h12par holePar holeScore'>".$row["Hole_12"]."</td>
			  <td class='r".$j."h13par holePar holeScore'>".$row["Hole_13"]."</td>
			  <td class='r".$j."h14par holePar holeScore'>".$row["Hole_14"]."</td>
			  <td class='r".$j."h15par holePar holeScore'>".$row["Hole_15"]."</td>
			  <td class='r".$j."h16par holePar holeScore'>".$row["Hole_16"]."</td>
			  <td class='r".$j."h17par holePar holeScore'>".$row["Hole_17"]."</td>
			  <td class='r".$j."h18par holePar holeScore'>".$row["Hole_18"]."</td>
			  <td class='r".$j."totPar holePar holeScore'>".$row["TotalPar"]."</td>";
	
		echo "</tr>";
		}
		else
		{
			echo "<li class='scoreTab static'><table><tbody class='headerSection'><tr><td>&nbsp</td></tr><tr><td>&nbsp</td></tr><tr><td>&nbsp</td></tr>";
		}
		//iterate through each player
		$intervalPrinted = false;
		$letsRefresh = false;
		$sql = "SELECT COUNT(*) as num_players FROM mbb_members";
		$result=$conn->query($sql);
		$row = $result->fetch_assoc();
		$num_players = $row["num_players"];
		for($k=1; $k<=$num_players; $k++)
		{
			//print player name on new row
			$sql = "SELECT usr FROM mbb_members WHERE id=" .$k;
			$result=$conn->query($sql);
			if($result->num_rows > 0)
			{
				$row = $result->fetch_assoc();
				$playerName=$row["usr"];
				$playerLoggedIn = "";
				if($playerName==$_SESSION['usr'])
					$playerLoggedIn = " loggedinuser";

				if($j==0)
				{
					echo "<tbody class='staticPlayerGroup ".$playerName."'><tr class='".$playerName."'>";
					echo "<td>".$playerName."</td>";
				}
				else{
					echo "<tbody class='pgroup ".$playerName."'>";
				}
			}

			//get all entries from this player for this tournament
			$sql = "SELECT Entries.Player as P, Entries.Golfer_1 as G1, Entries.Golfer_2 as G2, Entries.Golfer_3 as G3, Entries.Golfer_4 as G4, Entries.ID as eID, Scorecards.ID as scID, Scorecards.Year as Year, Scorecards.Tournament as Tournament, Scorecards.Round as Round  FROM Entries INNER JOIN Scorecards ON Entries.Scorecard_ID=Scorecards.ID WHERE ((Scorecards.Year=".$y.") AND (Scorecards.Tournament=".$t.") AND (Entries.Player=".$k.") AND (Scorecards.Round=".$j.")) ORDER BY Entries.Scorecard_ID";
			
			$result = $conn->query($sql);
			

			//fill an entries array with all entries for this player for this tournament
			$entries = array();
			$noEntry = false;
			if($j > 0)
			{
				if($result->num_rows > 0)
				{
					while($row = $result->fetch_assoc()) 
					{	
						$en = new Entry();
						$en->scID = $row["scID"];
						//populate Entries array which contains a Golfers array
						$en->golfers = array();
						$en->rnd = $row["Round"]; 
						$en->entryID = $row["eID"];
						$en->golfers[0]["ID"] =  $row["G1"];
						$en->golfers[1]["ID"] =  $row["G2"];
						$en->golfers[2]["ID"] =  $row["G3"];
						$en->golfers[3]["ID"] =  $row["G4"];	
						$entries[] = $en;
					}
					 
				}
				else
				{
					$noEntry=true;
					// $en = new Entry();
					// $en->scID = $row["scID"];
					// //populate Entries array which contains a Golfers array
					// $en->golfers = array();
					// $en->rnd = $row["Round"]; 
					// $en->entryID = $row["eID"];
					// $en->golfers[0]["ID"] =  0;
					// $en->golfers[1]["ID"] =  0;
					// $en->golfers[2]["ID"] =  0;
					// $en->golfers[3]["ID"] =  0;	
					// $en->golfers[0]["Name"] =  "";
					// $en->golfers[1]["Name"] =  "";
					// $en->golfers[2]["Name"] =  "";
					// $en->golfers[3]["Name"] =  "";	
					// $en->golfers[0]["PlayerPage"] =  "";
					// $en->golfers[1]["PlayerPage"] =  "";
					// $en->golfers[2]["PlayerPage"] =  "";
					// $en->golfers[3]["PlayerPage"] =  "";	
					// $entries[] = $en;
				}
			}

			//go get the friendly golfer name for each golfer in each entry
			if(!$noEntry)
			{
				foreach($entries as $e)
				{
					 for($i=0; $i < 4 ; $i++)
					 {
						 $sql = "SELECT Golfer_Name, PlayerPage FROM Golfers WHERE ID=". $e->golfers[$i]["ID"];
						 
						 if($result = $conn->query($sql))
						 {
							 $row=$result->fetch_assoc();
							 $e->golfers[$i]["Name"] = $row["Golfer_Name"];	
							 $e->golfers[$i]["PlayerPage"] = $row["PlayerPage"];					 
						 }
					 }
				}
			}
			 


			 
			 
			 
			 
			 
			 //this gives us the direct link to the player page, from which we can get their hole by hole data
			 
				foreach($entries as $e)
				{
					$sql = "SELECT IsLocked FROM Scorecards WHERE ID = " .$e->scID;
					$result = $conn->query($sql);
					$row = $result->fetch_assoc();
					$l = $row["IsLocked"];
					
					if($l==1)
					{
						for($i=0; $i < 4; $i++)
						{
							$sql = "SELECT Total FROM GolferScores WHERE Golfer_ID=".$e->golfers[$i]["ID"]." AND Scorecard_ID=".$e->scID;
							
							$result = $conn->query($sql);
							$done = false;
							if($result->num_rows >0)
							{
								if($row = $result->fetch_assoc())
								{
									if($row["Total"] !="" )
									{
										$done= true;
									}	
								}
							}
							if(!$done)
							{
								if(strpos($e->golfers[$i]["PlayerPage"], 'cbssports') === false)
								{
										if($e->golfers[$i]["Name"]!="")
											echo "</br>Need URL for ".$e->golfers[$i]["Name"]."(".$e->golfers[$i]["ID"].")";
									/*
									// The request also includes the userip parameter which provides the end
									// user's IP address. Doing so will help distinguish this legitimate
									// server-side traffic from traffic which doesn't come from an end-user.
									$url = 'https://www.googleapis.com/customsearch/v1?key=AIzaSyC4e4L8fQX6rVWXQvTwXqLRa2WGPGEEGWk&cx=012557125435830414214:wuek1hiu0n8&alt=atom' . "&q=cbs+player+page+" . str_replace(' ', '+', $e->golfers[$i]["Name"]);
									
									//echo "<br>" . $url;
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
									foreach($arr as $v)
									{
										//echo "<br>" . $v;
										if(strpos($v, 'href') !== false)
										{
											if(strpos($v, "playerpage") && strpos($v, "cbssports"))
											{
												$a = new SimpleXMLElement("<a " . $v . "></a>");
												$playerPageUrl= $a['href'];
												//echo $playerPageUrl;
											}
										}
									}
									//store the player page URL
									$e->golfers[$i]["PlayerPage"] = $playerPageUrl;*/
								}
							}
						}
					}
				}
			 
			//unset($e);		
			//iterate on ROUNDS here.  Need to print a whole row at a time (R1-G1 scores R2-G1 scores R3-G1 scores R4-G1 scores)

			for($i=0; $i < 4 ; $i++)
			{
				if($j>0)
				{	echo "<tr class='".$playerName."'>";				
				//go through each entry and use only the indexed golfer

				$lastRefreshUpdated = false;
				if(count($entries)==0)
					{
						echo "<td colspan=2 class='golferName'>No Entry</td>
										<td class='holeScore'></td>
										<td class='holeScore'></td>
										<td class='holeScore'></td>
										<td class='holeScore'></td>
										<td class='holeScore'></td>
										<td class='holeScore'></td>
										<td class='holeScore'></td>
										<td class='holeScore'></td>
										<td class='holeScore'></td>
										<td class='holeScore'></td>
										<td class='holeScore'></td>
										<td class='holeScore'></td>
										<td class='holeScore'></td>
										<td class='holeScore'></td>
										<td class='holeScore'></td>
										<td class='holeScore'></td>
										<td class='holeScore'></td>
										<td class='holeScore'></td>
										<td class='holeScore'></td>
										";
					}
				foreach($entries as $e)
				{
					
					if(count($entries)==0)
					{
						echo "<td colspan=2 class='golferName'>No Entry</td>
										<td class='holeScore'></td>
										<td class='holeScore'></td>
										<td class='holeScore'></td>
										<td class='holeScore'></td>
										<td class='holeScore'></td>
										<td class='holeScore'></td>
										<td class='holeScore'></td>
										<td class='holeScore'></td>
										<td class='holeScore'></td>
										<td class='holeScore'></td>
										<td class='holeScore'></td>
										<td class='holeScore'></td>
										<td class='holeScore'></td>
										<td class='holeScore'></td>
										<td class='holeScore'></td>
										<td class='holeScore'></td>
										<td class='holeScore'></td>
										<td class='holeScore'></td>
										<td class='holeScore'></td>
										";
					}
					else
					{
						
							$sql = "SELECT IsLocked FROM Scorecards WHERE ID = " .$e->scID;

							$result = $conn->query($sql);
							$row = $result->fetch_assoc();
							$l = $row["IsLocked"];
							
							if($l==1)
							{
								$sql = "SELECT Hole_1,
												Hole_2,
												Hole_3,
												Hole_4,
												Hole_5,
												Hole_6,
												Hole_7,
												Hole_8,
												Hole_9,
												Hole_10,
												Hole_11,
												Hole_12,
												Hole_13,
												Hole_14,
												Hole_15,
												Hole_16,
												Hole_17,
												Hole_18,
												Total,
												Total_To_Par FROM GolferScores WHERE Golfer_ID=". $e->golfers[$i]["ID"]. " AND Scorecard_ID=".$e->scID;
								 $result = $conn->query($sql);				
								 $row=$result->fetch_assoc();
								 echo "<td colspan=2 class='golferName r".$j."' entry='".$e->entryID."'>".$e->golfers[$i]["Name"]."</td>
											<td class='r".$j."h1g".$i."score holeScore'>".$row["Hole_1"]."</td>
											<td class='r".$j."h2g".$i."score holeScore'>".$row["Hole_2"]."</td>
											<td class='r".$j."h3g".$i."score holeScore'>".$row["Hole_3"]."</td>
											<td class='r".$j."h4g".$i."score holeScore'>".$row["Hole_4"]."</td>
											<td class='r".$j."h5g".$i."score holeScore'>".$row["Hole_5"]."</td>
											<td class='r".$j."h6g".$i."score holeScore'>".$row["Hole_6"]."</td>
											<td class='r".$j."h7g".$i."score holeScore'>".$row["Hole_7"]."</td>
											<td class='r".$j."h8g".$i."score holeScore'>".$row["Hole_8"]."</td>
											<td class='r".$j."h9g".$i."score holeScore'>".$row["Hole_9"]."</td>
											<td class='r".$j."h10g".$i."score holeScore'>".$row["Hole_10"]."</td>
											<td class='r".$j."h11g".$i."score holeScore'>".$row["Hole_11"]."</td>
											<td class='r".$j."h12g".$i."score holeScore'>".$row["Hole_12"]."</td>
											<td class='r".$j."h13g".$i."score holeScore'>".$row["Hole_13"]."</td>
											<td class='r".$j."h14g".$i."score holeScore'>".$row["Hole_14"]."</td>
											<td class='r".$j."h15g".$i."score holeScore'>".$row["Hole_15"]."</td>
											<td class='r".$j."h16g".$i."score holeScore'>".$row["Hole_16"]."</td>
											<td class='r".$j."h17g".$i."score holeScore'>".$row["Hole_17"]."</td>
											<td class='r".$j."h18g".$i."score holeScore'>".$row["Hole_18"]."</td>
											<td class='r".$j."totg".$i."score  gRoundScore holeScore'>".$row["Total"]."</td>
											";
								
							}
							else
							{
								
								echo "<td colspan=2 class='golferName'>locked</td>
											<td class='holeScore'></td>
											<td class='holeScore'></td>
											<td class='holeScore'></td>
											<td class='holeScore'></td>
											<td class='holeScore'></td>
											<td class='holeScore'></td>
											<td class='holeScore'></td>
											<td class='holeScore'></td>
											<td class='holeScore'></td>
											<td class='holeScore'></td>
											<td class='holeScore'></td>
											<td class='holeScore'></td>
											<td class='holeScore'></td>
											<td class='holeScore'></td>
											<td class='holeScore'></td>
											<td class='holeScore'></td>
											<td class='holeScore'></td>
											<td class='holeScore'></td>
											<td class='holeScore'></td>
											";
								
							}
							//$roundNumber++;
						
					}
				}
				if($j>0)
					echo "</tr>";	
				}
				
			}
			
			//this is the per hole scoring row for the PLAYER/TEAM
			if($j==0)
				echo "<tr><td>&nbsp</td></tr>
						<tr><td>&nbsp</td></tr>
						<tr><td>&nbsp</td></tr></tr>
						<tr player='".$playerName."' class='".$playerName." scoreRow'><td colspan=2 class='playerScore'>Score:</td>";
						
			else
			{
				echo "<tr player='".$playerName."' class='".$playerName." scoreRow'><td>&nbsp</td>";
			
				

				echo   "<td>&nbsp</td><td class='r".$j."h1teamScore teamScore'></td>
						<td class='r".$j."h2teamScore teamScore'></td>
						<td class='r".$j."h3teamScore teamScore'></td>
						<td class='r".$j."h4teamScore teamScore'></td>
						<td class='r".$j."h5teamScore teamScore'></td>
						<td class='r".$j."h6teamScore teamScore'></td>
						<td class='r".$j."h7teamScore teamScore'></td>
						<td class='r".$j."h8teamScore teamScore'></td>
						<td class='r".$j."h9teamScore teamScore'></td>
						<td class='r".$j."h10teamScore teamScore'></td>
						<td class='r".$j."h11teamScore teamScore'></td>
						<td class='r".$j."h12teamScore teamScore'></td>
						<td class='r".$j."h13teamScore teamScore'></td>
						<td class='r".$j."h14teamScore teamScore'></td>
						<td class='r".$j."h15teamScore teamScore'></td>
						<td class='r".$j."h16teamScore teamScore'></td>
						<td class='r".$j."h17teamScore teamScore'></td>
						<td class='r".$j."h18teamScore holeScore'></td>
						<td class='r".$j."totalTeamScore holeScore'></td>
						<td>&nbsp</td><td>&nbsp</td>"; 
			}

				 echo "</tr></tbody>";
				 if($j>0)
				 {
					 echo "<tbody class='pgroup_collapsed ".$playerName."'>
							<tr><td>&nbsp</td></tr>
							<tr><td>&nbsp</td></tr>
							<tr><td>&nbsp</td></tr>
							<tr><td>&nbsp</td></tr>
							<tr player='".$playerName."' class='".$playerName." scoreRow'><td style='height:24px;' class='r".$j."totalTeamScore holeScore'></td></tr>
						   </tbody>";
				 }
		}
		$roundnumber++;
		echo "</table></li>";
	}
		
	
	echo "</ul>";
	?>
	</div>
</body>

</html>
