<?php

if(!defined('INCLUDE_CHECK')) die('You are not allowed to execute this file directly');


/* Database config */

$db_host		= 'localhost';
$db_user		= 'anon';
$db_pass		= '';
$db_database	= 'MajorsBB'; 

/* End config */



$link = new mysqli($db_host,$db_user,$db_pass, $db_database);


?>