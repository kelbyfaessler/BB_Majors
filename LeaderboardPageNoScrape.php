<?php
 ini_set('display_errors', '1');
ini_set('error_reporting', E_ALL);
 
 
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
    
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
    
    <!-- PNG FIX for IE6 -->
    <!-- http://24ways.org/2007/supersleight-transparent-png-in-ie6 -->
    <!--[if lte IE 6]>
        <script type="text/javascript" src="login_panel/js/pngfix/supersleight-min.js"></script>
    <![endif]-->
    
    <script src="login_panel/js/slide.js" type="text/javascript"></script>
    
    <?php echo $script; ?>
	
	<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
	<script type="text/javascript">
	function giveMaxIfEmpty(score)
	{
		if(score=="")
			return 20;
		else
			return score;
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
	$(document).ready(function(){
		var count = 300 - parseInt($("#refreshInterval").html());
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
			$("." + player + " > .playerScore").html(playerTotalScore).addClass("teamRoundScore");
		});
	});
	</script>
<style>
 .holeNum, .holePar, .holeScore, .gRoundScore, .teamScore {
	 min-width: 11px !important;
	 text-align: center;
	 border:none;

	 
 }
 .gRoundScore{
	 color:#a8a8a8;
 }
  
 .golferName {
	 width: 150px;
	 white-space: nowrap; 
	 overflow: hidden;
     color: #555555;
     font-size: 12px;
     background: #eeeeee;
     font-family: Arial, Helvetica, sans-serif;
     width: 100px;
	 padding-left:8px;

 }
 .teamRoundScore{
	 font-weight:bold;
 }

 .playerName {
	 font-weight:bold;
 }
 td.hs_even{
	 background-color:#EEEEEE;
	 color: black;
 }
 td.hs_bad{
	 background-color:#FCABAB;
	 color: black;
 }
 td.hs_worst{
	 background-color:#E44444;
	 color: black;
 }
 td.hs_good{
	 background-color:#99ccff;
	 color: black;
 }
 td.hs_best{
	 background-color:#74A7F2;
	 color: black;
 }
 td.hs_none{
	 color: #EEEEEE;
	 background-color:#EEEEEE;
 }
 td.gs_even{color:#a8a8a8;}
  td.gs_good{color: #99ccff;}
 td.gs_best{color: #74A7F2;}
 td.gs_bad{color: #FCABAB;}
 td.gs_worst{color: #E44444;}
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

<div style="padding-top:45px">
			<a href="MajorsBBHome.php">&#60&#60Return Home</a>
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
	switch($t)
	{
		case 1:
		 $tourn = "Masters";
		 break;
		case 2:
		 $tourn = "US Open";
		 break;
		case 3:
		 $tourn = "British Open";
		 break;
		case 4:
		 $tourn = "PGA Championship";		 
		 break;
	}
	echo "<h1>".$y." ".$tourn."</h1>";
	echo "<table style='border:solid; ' BORDER=2 RULES=GROUPS id='leaderboard'>";
	echo "<colgroup span=21></colgroup>";
	echo "<colgroup span=21></colgroup>";
	echo "<colgroup span=21></colgroup>";
	echo "<colgroup span=21></colgroup>";
	echo "<tr>";
	for($j=1; $j<5; $j++)
	{
		echo "<td></td><td></td><td colspan=19 style='text-align:center'>Round ".$j."</td>";
	}
	echo "</tr><tr>";
	for($j=1; $j<5; $j++)
	{
		echo "<td class='holeNum'></td>
			  <td class='holeNum'></td>
			  <td class='holeNum'>1</td>
			  <td class='holeNum'>2</td>
			  <td class='holeNum'>3</td>
			  <td class='holeNum'>4</td>
			  <td class='holeNum'>5</td>
			  <td class='holeNum'>6</td>
			  <td class='holeNum'>7</td>
			  <td class='holeNum'>8</td>
			  <td class='holeNum'>9</td>
			  <td class='holeNum'>10</td>
			  <td class='holeNum'>11</td>
			  <td class='holeNum'>12</td>
			  <td class='holeNum'>13</td>
			  <td class='holeNum'>14</td>
			  <td class='holeNum'>15</td>
			  <td class='holeNum'>16</td>
			  <td class='holeNum'>17</td>
			  <td class='holeNum'>18</td>
			  <td class='holeNum'>Tot</td>";
	}
	echo "</tr><tr id='holePars'>";
	for($j=1; $j<5; $j++)
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
		echo "<td class='holePar'></td>
		      <td class='holePar'></td>
		      <td class='r".$j."h1par holePar'>".$row["Hole_1"]."</td>
		      <td class='r".$j."h2par holePar'>".$row["Hole_2"]."</td>
			  <td class='r".$j."h3par holePar'>".$row["Hole_3"]."</td>
			  <td class='r".$j."h4par holePar'>".$row["Hole_4"]."</td>
			  <td class='r".$j."h5par holePar'>".$row["Hole_5"]."</td>
			  <td class='r".$j."h6par holePar'>".$row["Hole_6"]."</td>
			  <td class='r".$j."h7par holePar'>".$row["Hole_7"]."</td>
			  <td class='r".$j."h8par holePar'>".$row["Hole_8"]."</td>
			  <td class='r".$j."h9par holePar'>".$row["Hole_9"]."</td>
			  <td class='r".$j."h10par holePar'>".$row["Hole_10"]."</td>
			  <td class='r".$j."h11par holePar'>".$row["Hole_11"]."</td>
			  <td class='r".$j."h12par holePar'>".$row["Hole_12"]."</td>
			  <td class='r".$j."h13par holePar'>".$row["Hole_13"]."</td>
			  <td class='r".$j."h14par holePar'>".$row["Hole_14"]."</td>
			  <td class='r".$j."h15par holePar'>".$row["Hole_15"]."</td>
			  <td class='r".$j."h16par holePar'>".$row["Hole_16"]."</td>
			  <td class='r".$j."h17par holePar'>".$row["Hole_17"]."</td>
			  <td class='r".$j."h18par holePar'>".$row["Hole_18"]."</td>
			  <td class='r".$j."totPar holePar'>".$row["TotalPar"]."</td>";
	}
	echo "</tr>";
	//iterate through each player
	echo "</tbody>";
	$intervalPrinted = false;
	$letsRefresh = false;
$sql = "SELECT COUNT(*) as num_players FROM mbb_members";
$result=$conn->query($sql);
$row = $result->fetch_assoc();
$num_players = $row["num_players"];
	for($j=1; $j<=$num_players; $j++)
	{

		echo "<tbody class='pGroup'>";
		//print player name on new row
		$sql = "SELECT usr FROM mbb_members WHERE id=" .$j;
		$result=$conn->query($sql);
		if($result->num_rows > 0)
		{
			$row = $result->fetch_assoc();
			$playerName=$row["usr"];
			echo "<tr class='".$playerName."'>";
			echo "<td class='playerName' colspan=21>".$playerName."</td>";
			echo "<td class='playerName' colspan=21>".$playerName."</td>";
			echo "<td class='playerName' colspan=21>".$playerName."</td>";
			echo "<td class='playerName' colspan=21>".$playerName."</td>";
			echo "</tr>";
		}

		//get all entries from this player for this tournament
		$sql = "SELECT Entries.Player as P, Entries.Golfer_1 as G1, Entries.Golfer_2 as G2, Entries.Golfer_3 as G3, Entries.Golfer_4 as G4, Entries.ID as eID, Scorecards.ID as scID, Scorecards.Year as Year, Scorecards.Tournament as Tournament, Scorecards.Round as Round  FROM Entries INNER JOIN Scorecards ON Entries.Scorecard_ID=Scorecards.ID WHERE ((Scorecards.Year=".$y.") AND (Scorecards.Tournament=".$t.") AND (Entries.Player=".$j.")) ORDER BY Entries.Scorecard_ID";
		$result = $conn->query($sql);
		

		//fill an entries array with all entries for this player for this tournament
		$entries = array();
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

		//go get the friendly golfer name for each golfer in each entry
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
		unset($e);

		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		//iterate on ROUNDS here.  Need to print a whole row at a time (R1-G1 scores R2-G1 scores R3-G1 scores R4-G1 scores)

		for($i=0; $i < 4 ; $i++)
		{
			echo "<tr class='".$playerName."'>";
			//go through each entry and use only the indexed golfer
			$roundNumber = 1;
			$lastRefreshUpdated = false;
			foreach($entries as $e)
			{
				
				$sql = "SELECT IsLocked FROM Scorecards WHERE ID = " .$e->scID;

				$result = $conn->query($sql);
				$row = $result->fetch_assoc();
				$l = $row["IsLocked"];
				
				if($l==1)
				{

					if(!empty($e->golfers[$i]["PlayerPage"]))
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
							$sql = "SELECT LastRefreshed FROM Scorecards WHERE ID = " .$e->scID;
							$result = $conn->query($sql);
							$row = $result->fetch_assoc();
							$lastScrapeTimeStamp=$row["LastRefreshed"];
							$lastScrapeTime=strtotime($lastScrapeTimeStamp);					
							$curTime = time();
							$interval = $curTime - $lastScrapeTime;
							if(!$intervalPrinted)
							{
								echo "</br><span id='refreshInterval'>$interval</span>";
								$intervalPrinted = true;
							}
							if($interval > 300 || $letsRefresh)
							{
								$letsRefresh = true;
								//echo "New Scrape Happening...";
								if(!$lastRefreshUpdated)
								{
									$sql = "UPDATE Scorecards SET LastRefreshed= now() WHERE ID = " .$e->scID;
									$result = $conn->query($sql);
									$lastRefreshUpdated = true;
								}
								if($e->rnd == $roundNumber)
								{
									//echo "</br>Here comes data for ".$e->golfers[$i]["Name"];
									$url = $e->golfers[$i]["PlayerPage"];
									$output = file_get_contents($url);
									//pass to the parsing function
									$hScores = GetScorecardDataFromHtml($output, $roundNumber);
									//echo $hScores;
									SubmitGolferScores($hScores, $e->golfers[$i]["ID"], $e->scID, $e->entryID);
								}
							}
						}
					}
					
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
					 echo "<td colspan=2 class='golferName r".$roundNumber."' entry='".$e->entryID."'>".$e->golfers[$i]["Name"]."</td>
								<td class='r".$roundNumber."h1g".$i."score holeScore'>".$row["Hole_1"]."</td>
								<td class='r".$roundNumber."h2g".$i."score holeScore'>".$row["Hole_2"]."</td>
								<td class='r".$roundNumber."h3g".$i."score holeScore'>".$row["Hole_3"]."</td>
								<td class='r".$roundNumber."h4g".$i."score holeScore'>".$row["Hole_4"]."</td>
								<td class='r".$roundNumber."h5g".$i."score holeScore'>".$row["Hole_5"]."</td>
								<td class='r".$roundNumber."h6g".$i."score holeScore'>".$row["Hole_6"]."</td>
								<td class='r".$roundNumber."h7g".$i."score holeScore'>".$row["Hole_7"]."</td>
								<td class='r".$roundNumber."h8g".$i."score holeScore'>".$row["Hole_8"]."</td>
								<td class='r".$roundNumber."h9g".$i."score holeScore'>".$row["Hole_9"]."</td>
								<td class='r".$roundNumber."h10g".$i."score holeScore'>".$row["Hole_10"]."</td>
								<td class='r".$roundNumber."h11g".$i."score holeScore'>".$row["Hole_11"]."</td>
								<td class='r".$roundNumber."h12g".$i."score holeScore'>".$row["Hole_12"]."</td>
								<td class='r".$roundNumber."h13g".$i."score holeScore'>".$row["Hole_13"]."</td>
								<td class='r".$roundNumber."h14g".$i."score holeScore'>".$row["Hole_14"]."</td>
								<td class='r".$roundNumber."h15g".$i."score holeScore'>".$row["Hole_15"]."</td>
								<td class='r".$roundNumber."h16g".$i."score holeScore'>".$row["Hole_16"]."</td>
								<td class='r".$roundNumber."h17g".$i."score holeScore'>".$row["Hole_17"]."</td>
								<td class='r".$roundNumber."h18g".$i."score holeScore'>".$row["Hole_18"]."</td>
								<td class='r".$roundNumber."totg".$i."score  gRoundScore'>".$row["Total"]."</td>
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
				$roundNumber++;
			}
			echo "</tr>";	
		 }//this is the per hole scoring row for the PLAYER/TEAM
		 echo "<tr player='".$playerName."' class='".$playerName." scoreRow'><td colspan=2 class='playerScore'>Score:</td>";
		 for($i=1;$i<5;$i++)
		 {
						echo "<td class='r".$i."h1teamScore teamScore'></td>
								<td class='r".$i."h2teamScore teamScore'></td>
								<td class='r".$i."h3teamScore teamScore'></td>
								<td class='r".$i."h4teamScore teamScore'></td>
								<td class='r".$i."h5teamScore teamScore'></td>
								<td class='r".$i."h6teamScore teamScore'></td>
								<td class='r".$i."h7teamScore teamScore'></td>
								<td class='r".$i."h8teamScore teamScore'></td>
								<td class='r".$i."h9teamScore teamScore'></td>
								<td class='r".$i."h10teamScore teamScore'></td>
								<td class='r".$i."h11teamScore teamScore'></td>
								<td class='r".$i."h12teamScore teamScore'></td>
								<td class='r".$i."h13teamScore teamScore'></td>
								<td class='r".$i."h14teamScore teamScore'></td>
								<td class='r".$i."h15teamScore teamScore'></td>
								<td class='r".$i."h16teamScore teamScore'></td>
								<td class='r".$i."h17teamScore teamScore'></td>
								<td class='r".$i."h18teamScore holeScore'></td>
								<td class='r".$i."totalTeamScore holeScore'></td>
								<td></td><td></td>"; 
		 }
		 echo "</tbody>";
	}
		
	
	//echo "</tr>";
	echo "</table>";
	?>
	</div>
</body>
</html>
