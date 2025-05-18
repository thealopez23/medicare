<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "healthcare_portal";

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Get data from form
$contactName = $_POST['contactName'];
$contactMessage = $_POST['contactMessage'];

$contactName = $conn->real_escape_string($contactName);
$contactMessage = $conn->real_escape_string($contactMessage);

// Insert into messages table
$sql = "INSERT INTO messages (sender_name, message) 
        VALUES ('$contactName', '$contactMessage')";

if ($conn->query($sql) === TRUE) {
  echo "Message sent successfully!";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
