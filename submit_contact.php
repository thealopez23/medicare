<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$database = "healthcare_portal";

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Connection failed: ' . $conn->connect_error
    ]);
    exit;
}

// Get data from form
$contactName = $_POST['contactName'] ?? '';
$contactEmail = $_POST['contactEmail'] ?? '';
$contactMessage = $_POST['contactMessage'] ?? '';

// Validate inputs
if (empty($contactName) || empty($contactEmail) || empty($contactMessage)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'All fields are required'
    ]);
    exit;
}

// Sanitize inputs
$contactName = $conn->real_escape_string($contactName);
$contactEmail = $conn->real_escape_string($contactEmail);
$contactMessage = $conn->real_escape_string($contactMessage);

// Insert into messages table
$sql = "INSERT INTO messages (sender_name, email, message) 
        VALUES ('$contactName', '$contactEmail', '$contactMessage')";

if ($conn->query($sql) === TRUE) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Message sent successfully!'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error: ' . $conn->error
    ]);
}

$conn->close();
?>
