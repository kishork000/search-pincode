<?php
$hostname = "localhost";
$username = "codpin";
$password = "passw0rd@098";
$dbname = "indianpincode";

$con = mysqli_connect($hostname, $username, $password, $dbname);
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
?>