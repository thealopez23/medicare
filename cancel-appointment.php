<?php
session_start();
include('config.php');

// Check if user is logged in and is a patient
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 3) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $email = $_SESSION['email'];

    try {
        // Verify that the appointment belongs to the patient
        $stmt = $pdo->prepare("SELECT * FROM appointments WHERE id = ? AND email = ?");
        $stmt->execute([$id, $email]);
        
        if ($stmt->rowCount() > 0) {
            // Delete the appointment
            $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Appointment not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?> 