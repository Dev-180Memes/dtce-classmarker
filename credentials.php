<?php

$api_key = "XXXXXX";
$api_secret = "XXXXXXXXX";

$mysql_host = "localhost";
$mysql_username = "";
$mysql_password = "";
$mysql_database = "";

$administrator_email = "";

$mysqli = @mysqli_connect($mysql_host, $mysql_username, $mysql_password, $mysql_database);
if (!$mysqli) {
    echo '<p>Error: Unable to connect to MySQL.</p>';
    echo '<p>Debugging errno: ' . mysqli_connect_errno() . '</p>';
    echo '<p>Debugging error: ' . mysqli_connect_error() . '</p>';
    exit;
}

?>