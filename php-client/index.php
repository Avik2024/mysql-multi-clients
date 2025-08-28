<?php
$servername = "localhost";
$username = "root";
$password = "password";
$dbname = "testdb";

try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  echo "Connected to MySQL from PHP";
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}
?>
