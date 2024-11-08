<?php


$host = 'localhost';     
$dbname = 'dashboard_task'; 
$username = 'root';      
$password = '';          

$conn = new mysqli($host, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


?>


