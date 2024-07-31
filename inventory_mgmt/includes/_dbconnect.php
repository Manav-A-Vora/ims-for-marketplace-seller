<?php 

$server = "localhost";
$username = "root";
$password = "";
$database = "inventory_mgmt";

$conn = mysqli_connect($server, $username, $password, $database);

if (!$conn){    
    die("connection failed". mysqli_connect_error());
}



