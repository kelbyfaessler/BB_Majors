<?php
  //ini_set('display_errors', '1');
  //ini_set('error_reporting', E_ALL);
 
 
define('INCLUDE_CHECK',true);

require 'connect.php';
require 'functions.php';
// Those two files can be included only if INCLUDE_CHECK is defined


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
	session_destroy();
	
	header("Refresh:0");
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


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Best Ball Majors Home Page</title>
    
    <link rel="stylesheet" type="text/css" href="demo.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="login_panel/css/slide.css" media="screen" />
    
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    
    <!-- PNG FIX for IE6 -->
    <!-- http://24ways.org/2007/supersleight-transparent-png-in-ie6 -->
    <!--[if lte IE 6]>
        <script type="text/javascript" src="login_panel/js/pngfix/supersleight-min.js"></script>
    <![endif]-->
    
    <script src="login_panel/js/slide.js" type="text/javascript"></script>
    <script src="sortable.js" type="text/javascript"></script>
    <?php echo $script; ?>
	<script type=text/javascript>
	
	function sort_table(){
		var table = $('#scoreboard');

		$('#totPlayer')
			.wrapInner('<span title="sort this column"/>')
			.each(function(){

				var th = $(this),
					thIndex = th.index(),
					inverse = false;

				th.each(function(){

					table.find('td').filter(function(){

						return $(this).index() === thIndex;

					}).sortElements(function(a, b){

						if( parseInt($.text([a])) == parseInt($.text([b])) )
							return 0;

						return parseInt($.text([a])) > parseInt($.text([b])) ?
							inverse ? -1 : 1
							: inverse ? 1 : -1;

					}, function(){

						// parentNode is the element we want to move
						return this.parentNode; 

					});

					inverse = !inverse;

				});

			});
	}
	$(document).ready(function(){
		sort_table();
	});
	</script>
	<style>
	.tournyHeader{
		width:200px;
	}
	.center-align-text td{
			text-align: center;
	
	}
	td.left-align-text {
		text-align: left; 
	}
	#rules li {
		text-align: left; !important
		padding-left: 35px;
}		
	#scoreboard td{
		border: 1px solid #888888;
	}
	#scoreboard
	{
		border-collapse:collapse;
		border: 1px solid #888888;
	}

	</style>
</head>

<body style="background-color:#006b54">

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


   <!-- PUT ALL PAGE CONTENT HERE --> 

<div class="pageContent" >
    <div id="main">
	
      <div class="container" style="text-align : center">

<table >
<tr>

<td>
<h1>Best Ball 2017 Scoreboard</h1>

		<table id="scoreboard" >
			<thead>
				<tr>
					<th></th>
					<th class="tournyHeader"><a href="Leaderboard.php?y=2017&t=1">1</a></th>
					<th class="tournyHeader"><a href="Leaderboard.php?y=2017&t=2">2</a>	</th>
					<th class="tournyHeader"><a href="Leaderboard.php?y=2017&t=3">3</a>	</th>
					<th class="tournyHeader"><a href="Leaderboard.php?y=2017&t=4">4</a></th>
					<th class="tournyHeader"><a href="Leaderboard.php?y=2017&t=5">5</a></th>
					<th class="tournyHeader"><a href="Leaderboard.php?y=2017&t=6">6</a>	</th>
					<th class="tournyHeader"><a href="Leaderboard.php?y=2017&t=7">7</a>	</th>
					<th class="tournyHeader"><a href="Leaderboard.php?y=2017&t=9">8</a></th>
					<th class="tournyHeader"><a href="Leaderboard.php?y=2017&t=9">9</a></th>
					<th id="totPlayer" class="tournyHeader playerTotal">Total</th>
				</tr>
				<tbody>
				<?php
					$servername="localhost";
					$username="anon";
					$dbname = "MajorsBB";
					$password="";
					$conn = new mysqli($servername, $username, $password, $dbname);
					$sql = "SELECT * FROM mbb_members";
					$result = $conn->query($sql);
					$tournament_totals = array();
					if($result->num_rows >0)
					{						
						$tournament_totals = array();
						while($row=$result->fetch_assoc())
						{
							$playerTotal = 0;
							echo "<tr>";
							echo "<td class='center-align-text'>".$row["usr"]."</td>";
							for($i=1; $i<10;$i++)
							{
								$s = "SELECT sum(Entries.EntryScore)as ScoreTotal, Major FROM Entries INNER JOIN Scorecards ON Entries.Scorecard_ID = Scorecards.ID WHERE Entries.Player=".$row["id"]." AND Scorecards.Year=2017 AND Scorecards.Tournament=".$i;
								$r = $conn->query($s);
								if($r->num_rows >0)
								{
									$d = $r->fetch_assoc();
									//$playerTotal +=$d["ScoreTotal"];
									if($d["Major"]==1)
										$playerTotal += $d["ScoreTotal"];
									else
										$tournament_totals[$i] = $d["ScoreTotal"];
									
									echo "<td class='center-align-text'>".$d["ScoreTotal"]."</td>";								
								}
								else
									echo "<td class='center-align-text'>0</td>";
							}
							//sort($tournament_totals);
							//$playerTotal = 0;
							for($j = 0; $j < count($tournament_totals); $j++)
							{
								$playerTotal += $tournament_totals[$j];
							}
							echo "<td class='playerTotal center-align-text'>$playerTotal</td></tr>";
						}						
					}
				?>
			</tbody>
		</table>
</td>
</tr>
<tr>
<td>
	<h1>2017 Schedule</h1>
</td>
</tr>
<tr>
<td>
	<table id="schedule">
		<thead>
				<tr>
					<th class="tournyHeader">Tournament</th>
					<th class="tournyHeader">Date</th>
					<th class="tournyHeader">Draft</th>
					<th class="tournyHeader">Results</th>
					<th id="winnerPlayer" class="tournyHeader">Winner</th>
				</tr>
				<tbody>
				<?php
					$servername="localhost";
					$username="anon";
					$dbname = "MajorsBB";
					$password="";
					$conn = new mysqli($servername, $username, $password, $dbname);
					$sql = "SELECT Year, Tournament, Name, StartDate, Winner FROM Scorecards WHERE YEAR(StartDate)=2017 GROUP BY Name ORDER BY StartDate";
					$result = $conn->query($sql);
					$counter= 0;
					if($result->num_rows >0)
					{						
						while($row=$result->fetch_assoc())
						{
							$counter=  $counter +1;
							$playerTotal = 0;
							echo "<tr>";
							echo "<td class='left-align-text'>".$row["Name"]."</td>";
							echo "<td class='left-align-text'>".date("l, F j", strtotime($row["StartDate"]))."</td>";
							if((strtotime($row["StartDate"]) > strtotime('-7 day')) & (strtotime($row["StartDate"]) < strtotime('+4 day')))
								echo "<td><a href='draft.php?y=2017&t=".$counter."'>Draft</a></td>";
							else
								echo "<td>--</td>";
							if((strtotime($row["StartDate"]) < strtotime('now') ))
								echo "<td><a href='Leaderboard.php?y=2017&t=".$counter."'>Leaderboard</td>";
							else 
								echo "<td>--</td>";
							if(strtotime($row["StartDate"]) < strtotime('-4 day'))
							{
								echo "<td class='center-align-text'>".$row["Winner"]."</td>";	
							}
							else
								echo "<td>--</td>";
							echo "</tr>";
						}						
					}
				?>
			</tbody>
	</table>

        <h1>Best Ball Majors 2016</h1>


		<table id="scoreboard_old">
			<thead>
				<tr>
					<th></th>
					<th class="tournyHeader"><a href="Leaderboard.php?y=2016&t=1">Masters</a></th>
					<th class="tournyHeader"><a href="Leaderboard.php?y=2016&t=2">US Open</a>	</th>
					<th class="tournyHeader"><a href="Leaderboard.php?y=2016&t=3">British Open</a>	</th>
					<th class="tournyHeader"><a href="Leaderboard.php?y=2016&t=4">PGA Champ</a></th>
					<th id="totPlayer" class="tournyHeader playerTotal">Total</th>
				</tr>
				<tbody>
				<?php
					$servername="localhost";
					$username="anon";
					$dbname = "MajorsBB";
					$password="";
					$conn = new mysqli($servername, $username, $password, $dbname);
					$sql = "SELECT * FROM Players";
					$result = $conn->query($sql);
					if($result->num_rows >0)
					{						
						while($row=$result->fetch_assoc())
						{
							$playerTotal = 0;
							echo "<tr>";
							echo "<td class='center-align-text'>".$row["Name"]."</td>";
							for($i=1; $i<5;$i++)
							{
								$s = "SELECT sum(Entries.EntryScore) as ScoreTotal FROM Entries INNER JOIN Scorecards ON Entries.Scorecard_ID = Scorecards.ID WHERE Entries.Player=".$row["ID"]." AND Scorecards.Year=2016 AND Scorecards.Tournament=".$i;
								$r = $conn->query($s);
								if($r->num_rows >0)
								{
									$d = $r->fetch_assoc();
									$playerTotal +=$d["ScoreTotal"];
									echo "<td class='center-align-text'>".$d["ScoreTotal"]."</td>";								
								}
								else
									echo "<td class='center-align-text'>0</td>";
							}
							echo "<td class='playerTotal center-align-text'>$playerTotal</td></tr>";
						}						
					}
				?>
			</tbody>
		</table>
</td>
<td>
</td>
<td>
<table style='padding-left:30px; padding-right:30px;'><tr><td><div><img src='http://www.linkslifegolf.com/wp-content/uploads/2016/03/masters-flag-hole-250x175.jpg'></div></td></tr>
<tr>
<td rowspan=5>
<br>
<ul id='rules'>
<li>Winner of Non-Majors: $15</li>
<li>Winner of Majors: $30</li>
<li>Season 2nd Place: $60</li>
<li>Season Winner: $165</li>
<br>
<br>
<li>Scored as best ball: lowest score on the hole by your team is your score on the hole</li>
<li>Your season long score will be your score from all 4 Majors plus your best 3 scores from the non-Majors</li>
<li>Tie breakers:</li>

<ol>
<li>Lowest round score for tournament</li>
<li>Eagle count for tournament</li>
<li>Birdie count for tournament</li>
<li>Highest finish of rostered player in 4th round</li>
</ol>
<br>
<br>
</ul>
</h3>
</table>
</td>
</tr>
</table>
		</div>
</div>

</body>
</html>
