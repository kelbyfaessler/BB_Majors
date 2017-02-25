<?php
 // ini_set('display_errors', '1');
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


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Draft Team</title>
    
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
	
	function UpdateSalaries()
	{
		var currentSalaryTotal;
		var golfer1;
		var golfer2;
		var golfer3;
		var golfer4;
		 $("#roster table").each(function(){
			
			
				 var total = 0;
			 
				 firstOpenTable=false;
				 $("tr", $(this)).not("thead tr").each(function(){
					 if($(this).attr("isEmpty")==0)
						total += parseInt($(this).children(".Salary").html());
				 });
				 currentSalaryTotal =  total;
				 $("#salaryTotal", $(this)).html(34000 - total);
				 $("#salaryTotal", $(this)).attr("total",total);
						 
		 });
		$("#roster table").each(function(){
			if($(this).attr("edit_this")=="true")
			{
				var total = 0;
				$("tr", $(this)).not("thead tr").each(function(){
					if($(this).attr("isEmpty")==0)
						total +=parseInt($(this).children(".Salary").html());
				
				});
				currentSalaryTotal = total;
			}
		});
		$("#player_list tr").not("thead tr").each(function(){
			if(currentSalaryTotal + parseInt($(".Salary", $(this)).html()) > 34000)
			{
				$(".AddGolfer", $(this)).css("visibility","hidden");
			}
			else
			{
				$(".AddGolfer", $(this)).css("visibility","visible");
			}
		});
	}
	$(document).ready(function(){
		UpdateSalaries();
		$(".copy_down").click(function(){
			var myTable = $(this).parents("table");
			var go = false;
			$("#roster table").each(function(){
			 			var index =0;

			   
			   if(go)
                           {
				$("tr.Golfer_row", $(this)).not("thead tr").each(function(){
					
				    $(this).children(".Name").html($($("tr.Golfer_row", $(myTable)).children(".Name")[index]).html());
                                    $(this).children(".Salary").html($($("tr.Golfer_row", $(myTable)).children(".Salary")[index]).html());
				    $(this).children(".Button").attr("golfer_id", $($("tr.Golfer_row", $(myTable)).children(".Button")[index++]).attr("golfer_id"));
				    $(this).attr("isEmpty", 0);
     				    $("button.RemoveGolfer", $(this)).css("visibility", "show");	
				});
			   }
			   if($(this).is($(myTable)))
			   {
				go = true;	
			   }
			});
			UpdateSalaries();
		});
		$(".edit_roster_round").click(function(){
			var myTable = $(this).parents("table");
			var editMode = $(this).html()=="Lock";
			if(editMode)
			{
				$(myTable).removeClass("activeRound");
				$(this).html("Edit");
				$(myTable).attr("edit_this", false);
			}
			else
			{
			 	

				$(".roster_round_table").each(function(){
					$(".edit_roster_round", $(this)).html("Edit");
					$(this).attr("edit_this", false);
					$(this).removeClass("activeRound");
				});
				$(this).html("Lock");
				$(myTable).attr("edit_this", true);
				$(myTable).addClass("activeRound");
			}
			UpdateSalaries();

		});
		$("#clearEntry").click(function(){
			$("#roster table").each(function(){
				if($(this).attr("locked")==0 && $(this).attr("edit_this")=="true")
				{
					$("tr.Golfer_row", $(this)).not("thead tr").each(function(){
						$(this).removeClass("cutPlayer");
						$(this).children(".Name").html("");
						$(this).children(".Salary").html("");
						$(this).children(".Button").attr("golfer_id", "");
						$("button.RemoveGolfer", $(this)).css("visibility", "hidden")
						$(this).attr("isEmpty", 1)

					});
				}
			});
			UpdateSalaries();
		});
		$(".RemoveGolfer").click(function(){
			var myRow = $(this).parents("tr:first");
			var rowNum = $(myRow).children(".Number").html();
			$(myRow).children(".Name").html("");
			$(myRow).children(".Salary").html("");
			$gID = $(myRow).children(".Button").attr("golfer_id");
			
			$(myRow).children(".Button").attr("golfer_id", "");
			$("button", myRow).css("visibility", "hidden")
			$(myRow).attr("isEmpty", 1)
			
			$("#roster table").each(function(){
				if($(this).attr("locked")==0 && $(this).attr("edit_this")=="true")
				{
					$("tr.Golfer_row", $(this)).not("thead tr").each(function(){
						if($(this).children(".Number").html() === rowNum)
						{
							$(this).removeClass("cutPlayer");
							$(this).children(".Name").html("");
							$(this).children(".Salary").html("");
							$(this).children(".Button").attr("golfer_id", "");
							$("button", $(this)).css("visibility", "hidden")
							$(this).attr("isEmpty", 1)
						
						}
					});
				}
			});
			UpdateSalaries();
			$("#player_list tr.Golfer_row").not("thead tr").each(function(){
				if($(".Button", $(this)).attr("golfer_id")==$gID)
					$(this).css("visibility", "visible");
			});
		});
		$(".AddGolfer").click(function(){
			
			var firstOpenTable = true;
			var golfer_id = $(this).parents("tr").children("td.Button").attr("golfer_id");
			var golfer_name = $(this).parents("tr").children("td.Name").html();
			var salary = $(this).parents("tr").children("td.Salary").html();
			var didItChange = false;
			$("#roster table").each(function(){
				if($(this).attr("locked")==0 && $(this).attr("edit_this")=="true")				
				{

					$("tr.Golfer_row", $(this)).not("thead tr").each(function(){
						if($(this).attr("isEmpty")==1)
						{
							didItChange=true;
							$(this).children(".Name").html(golfer_name);
							$(this).children(".Salary").html(salary);
							$(this).children(".Button").attr("golfer_id", golfer_id);
							$("button", $(this)).css("visibility", "visible")
							$(this).attr("isEmpty", 0)
							return false;
						}
					});
				}
			});
			UpdateSalaries();
			if(didItChange)
			{
				$(this).css("visibility", "hidden");
			}

		});
		$("#submitChanges").click(function(){
			var dataString = "";		
			$("#roster table").each(function(){
				var entryDetails = new Array();
				entryDetails["Player"] = $($(this)).attr("player_id");
				entryDetails["ScorecardID"] = $($(this)).attr("scorecard_id");
				dataString = "Player=" + entryDetails["Player"] + "&ScorecardID=" + entryDetails["ScorecardID"];
				entryDetails["Golfers"] = new Array();
				if($(this).attr("locked")==0)
				{
					var i = 0;
					$("tr.Golfer_row", $(this)).not("thead tr").each(function(){
						if($(this).attr("isEmpty")==0)
						{
							entryDetails["Golfers"][i++] = $(this).children(".Button").attr("golfer_id");							
						}
						else
							entryDetails["Golfers"][i++] = "NULL";
					});
							
					dataString = dataString + "&Golfer1=" + entryDetails["Golfers"][0] + 
											  "&Golfer2=" + entryDetails["Golfers"][1] +
											  "&Golfer3=" + entryDetails["Golfers"][2] + 
											  "&Golfer4=" + entryDetails["Golfers"][3] + 
											  "&Salary=" + $("#salaryTotal", $(this)).attr("total");
					//ajax here to submit the entry
					$.ajax({
						type: "POST",
						url: "submitEntry.php",
						data: dataString,
						cache: false,
						success: function(result){
							//alert(result);
						}
					});
				}
			});
			
		});
	});
	</script>
<style>
 .Name {
	 width: 140px;
	 
 }
 .Salary{
	 width: 65px;
 }
 .Button {
	 width:15px;
 }
 table {
  border-collapse: collapse;
}
 th
 {
	 text-align:left;
	 
 }
 .cutPlayer{
	 border-style:solid;
	 border-color:red;
 }
 .roster_round_table{
	 border: 1px solid #000000;
	 border-collapse: collapse;
	 padding: 5px;
	 margin: 5px;
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


   <!-- PUT ALL PAGE CONTENT HERE --> 

<div style="padding-top:45px">
<div id="players" style="float:left;">
		<a href="MajorsBBHome.php">&#60&#60Return Home</a>
        <table id="player_list">
			<thead>
				<tr>
					<th>Player</th>
					<th>Salary</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
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


				//need to scorecard ID from the Tournament + Year + Round info in order to find the right entries
				$sql = "SELECT * FROM Golfers WHERE Year=". $y . " AND Tournament=". $t. " AND Cut=0";
				$result = $conn->query($sql);

				if($result->num_rows > 0)
				{
					while($golfer = $result->fetch_array()){
						echo "
								<tr>
									<td class='Name'>".$golfer['Golfer_Name']."</td>
									<td class='Salary'>".$golfer['Salary']."</td>
									<td class='Button' golfer_id='".$golfer['ID']."'><button class='AddGolfer' >+</button></td>
								</tr>
						";
					}
				}
							
			?>
			</tbody>
        </table>
    </div>
    <div id="roster" style="padding-left:400px;">
	<button id="submitChanges">Submit Changes</button>
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
		$sql = "SELECT ID FROM mbb_members WHERE usr='".$_SESSION['usr']."'";
		
		$result = $conn->query($sql);

		if($result->num_rows > 0)
		{
			$row = $result->fetch_array();
			$player = $row['ID'];
		}
		else
		{
			$player = -1;
			echo "</br><h3>Please log in to draft a team</h3>";
			exit;
		}
		
		for($i=1; $i < 5; $i++)
		{
			$sql = "SELECT * FROM Scorecards WHERE Year=". $y . " AND Tournament=". $t . " AND Round=".$i;
			
			$result = $conn->query($sql);
			$scorecard_id = -1;
			if($result->num_rows > 0)
			{
				$row = $result->fetch_assoc();
				$scorecard_id =  $row["ID"];
				$isLocked = $row["IsLocked"];
			}


			//get entries for this scorecardID
			$sql = "SELECT Golfer_1, Golfer_2, Golfer_3, Golfer_4 FROM Entries WHERE Scorecard_ID=". $scorecard_id. " AND Player=".$player;
			$result = $conn->query($sql);
			echo "
					
					<table class='roster_round_table' player_id='".$player."' scorecard_id='".$scorecard_id."' locked=".$isLocked.">
							<caption>Round ".$i."</caption>
							<thead>
								<tr>";
								if(!$isLocked)
									echo "<td><button class='edit_roster_round' id='edit_round_".$i."'>Edit</button></td>";
								else
									echo "<td></td>";
								
								echo	"<th></th>
									<th>Golfer</th>
									<th>Salary</th>
									<th></th>
								</tr>
							</thead>
							<tbody>";
			if($result->num_rows > 0)
			{
				echo "<tr></tr>";

				$entry = $result->fetch_array();
				for($j=1; $j < 5; $j++)
				{
					$sqlNaming =  'Golfer_' . $j;					
					$sql2 = "SELECT * FROM Golfers WHERE ID=".$entry[$sqlNaming];	
					
					$golferDetails = $conn->query($sql2);
					
					if($golferDetails->num_rows > 0)					
					{
						$gd = $golferDetails->fetch_array();
						$classString="";
						if($gd["Cut"]==1)
							$classString="class='cutPlayer'";
						echo "<tr class='Golfer_row' isEmpty=0 ".$classString.">
							<td></td>
							<td class='Number'>".$j."</td>
							<td class='Name' style='padding-left:10px'>".$gd['Golfer_Name']."</td>
							<td class='Salary'>".$gd['Salary']."</td>";
							
							if(!$isLocked)
								echo "<td class='Button Remove' golfer_id='".$gd['ID']."'><button class='RemoveGolfer'  >-</button></td>";
							else
								echo "<td></td>";
						echo "</tr>";
					}
					else
					{
							echo "
							<tr isEmpty=1 class='Golfer_row'>
								<td class='Number'>".$j."</td>
								<td class='Name' style='padding-left:10px'></td>
								<td class='Salary'></td>
								<td class='Button Remove'><button class='RemoveGolfer' style='visibility:hidden;'>-</button></td>
							</tr>";
					}				
				}
				echo "<tr><td></td><td>Remaining:</td><td id='salaryTotal'></td><td></td><td></td></tr>";
				if(!$isLocked)
					echo "<tr><td><button class='copy_down' id='copy_down_round_".$i."'>Copy Down</button></td><td></td><td></td><td></td><td></td></tr></tbody></table>";
				
			}
			else
			{
				$sql = "INSERT INTO `Entries`(`Player`, `Scorecard_ID`) VALUES (".$player.",".$scorecard_id.")";
				//echo $sql;
				$conn->query($sql);
				echo '<script type="text/javascript">location.reload();</script>';				
			}
			
		}
		?>
		<button id="clearEntry">Clear</button>
    </div>
	
	</div>
</body>
</html>
