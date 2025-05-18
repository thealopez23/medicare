<?php
session_start();
$servername = "localhost";
$username = "root"; 
$password = "";     
$database = "healthcare_portal";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$fullName = $_POST['fullName'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$doctor = $_POST['doctor'];
$date = $_POST['date'];
$time = $_POST['time'];
$message = $_POST['message'];

$stmt = $conn->prepare("INSERT INTO appointments (fullName, email, phone, doctor, date, time, message) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $fullName, $email, $phone, $doctor, $date, $time, $message);

if ($stmt->execute()) {
  // Clear form data from session
  $_SESSION['form_submitted'] = true;
  header('Location: appointment.php?success=1');
  exit();
} else {
  echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
