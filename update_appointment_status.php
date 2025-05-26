<?php
header('Content-Type: application/json');

session_start();
include('config.php');

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in and is a doctor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id']) && isset($_POST['status'])) {
    $id = (int)$_POST['id'];
    $status = (int)$_POST['status'];
    $decline_reason = isset($_POST['decline_reason']) ? trim($_POST['decline_reason']) : '';
    $doctor_name = $_SESSION['full_name'];

    // Debug: Log received inputs
    error_log("update_appointment_status.php - ID: $id, Status: $status, Decline Reason: $decline_reason, Doctor: $doctor_name");

    // Validate inputs
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid appointment ID']);
        exit;
    }

    if (!in_array($status, [2, 3])) {
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        exit;
    }

    if ($status === 3 && empty($decline_reason)) {
        echo json_encode(['success' => false, 'message' => 'Decline reason is required']);
        exit;
    }

    try {
        // Verify that the appointment belongs to the doctor
        $stmt = $pdo->prepare("SELECT * FROM appointments WHERE id = ? AND doctor = ?");
        $stmt->execute([$id, $doctor_name]);
        
        if ($stmt->rowCount() > 0) {
            // Update the appointment status and decline reason
            $stmt = $pdo->prepare("
                UPDATE appointments 
                SET is_approved = ?, decline_reason = ? 
                WHERE id = ?
            ");
            $stmt->execute([
                $status,
                $status === 3 ? $decline_reason : null,
                $id
            ]);

            // Debug: Log successful update
            error_log("update_appointment_status.php - Appointment ID: $id updated with status: $status, decline_reason: " . ($status === 3 ? $decline_reason : 'NULL'));

            echo json_encode(['success' => true]);
        } else {
            // Debug: Log appointment not found
            error_log("update_appointment_status.php - Appointment ID: $id not found or not assigned to doctor: $doctor_name");
            echo json_encode(['success' => false, 'message' => 'Appointment not found or not assigned to you']);
        }
    } catch (PDOException $e) {
        // Log error
        error_log("update_appointment_status.php - Database Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    // Debug: Log invalid request
    error_log("update_appointment_status.php - Invalid request method or missing parameters");
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}

// Close the database connection
$pdo = null;
?>