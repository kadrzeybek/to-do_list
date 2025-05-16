<?php
$hostName = "localhost";
$dbUserName = "root";
$dbPassword = "";
$dbName = "toDoList";

$conn = mysqli_connect($hostName, $dbUserName, $dbPassword, $dbName);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>