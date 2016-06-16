<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Tournament Scores</title>
    <link rel="stylesheet" href="css/styles.css?v=1.0">

    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>

</head>

<script type="text/javascript">

$(document).ready(function() {
    $("#display").click(function() {
        $.ajax({
            type: "GET",
            url: "http://174.129.171.133/tournamentscores_joe.php",
            dataType: "html",
            success: function(response) {
                $("#table").html(response);
            }
        })
    });
});

</script>

<body>
    <div id="toptitle">
    <p>Title here</p>
    </div>
    <div id="playerscores">
        <input type="button" id="display" value="Query Database" />
        <table id="table">
            <tr>
                <td>Player</td>
                <td>
            </tr>
        </table>
    </div>
    <div id="responsecontainer" align="center">
    </div>
</body>

</html>
