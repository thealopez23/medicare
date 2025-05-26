<?php
session_start();
include('config.php');

// Check if user is logged in and is a doctor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id']) && isset($_POST['status'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];
    $doctor_name = $_SESSION['full_name'];

    try {
        // Verify that the appointment belongs to the doctor
        $stmt = $pdo->prepare("SELECT * FROM appointments WHERE id = ? AND doctor = ?");
        $stmt->execute([$id, $doctor_name]);
        
        if ($stmt->rowCount() > 0) {
            // Update the appointment status
            $stmt = $pdo->prepare("UPDATE appointments SET is_approved = ? WHERE id = ?");
            $stmt->execute([$status, $id]);
            
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