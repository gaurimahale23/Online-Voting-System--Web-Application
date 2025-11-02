<?php
$host = "localhost";
$port = "3307";               // your MariaDB port
$user = "phpuser";
$password = "mysecret";       // use the one you just set
$db = "voting";        // replace with actual DB name

$conn = mysqli_connect($host, $user, $password, $db, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "Connected successfully!";
?>
