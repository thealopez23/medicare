<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "healthcare_portal");

// Check DB connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message_id = $_POST['message_id'];
    $reply = $_POST['reply_text'];

    // Save the reply to the database
    $stmt = $conn->prepare("INSERT INTO message_replies (message_id, reply_text, replied_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("is", $message_id, $reply);

    if ($stmt->execute()) {
        // Fetch sender email and name
        $stmt = $conn->prepare("SELECT sender_name, email FROM messages WHERE id = ?");
        $stmt->bind_param("i", $message_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $email = $user['email'];
        $name = $user['sender_name'];

        // Email reply to user
        $subject = "Reply from Healthcare Portal Admin";
        $message_body = "Hello $name,\n\nHere is the admin's reply to your message:\n\n$reply\n\nBest regards,\nHealthcare Portal Team";
        $headers = "From: admin@yourdomain.com"; // Change to your real admin email

        if (mail($email, $subject, $message_body, $headers)) {
            header("Location: admin_dashboard.php?sent=1");
            exit;
        } else {
            echo "Reply saved, but failed to send email.";
        }
    } else {
        echo "Error saving reply: " . $conn->error;
    }
}
?>
