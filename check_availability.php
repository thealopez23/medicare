<?php
session_start();
include('config.php');

header('Content-Type: application/json');

if (!isset($_GET['doctor'])) {
    echo json_encode(['error' => 'Missing required parameters']);
    exit();
}

$doctor = $_GET['doctor'];
$date = $_GET['date'] ?? null;

try {
    if ($date) {
        // Check specific date availability
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM appointments WHERE doctor = ? AND date = ?");
        $stmt->execute([$doctor, $date]);
        $result = $stmt->fetch();
        
        echo json_encode([
            'available' => $result['count'] == 0,
            'count' => $result['count']
        ]);
    } else {
        // Get all booked dates for the doctor
        $stmt = $pdo->prepare("SELECT date FROM appointments WHERE doctor = ? AND date >= CURDATE()");
        $stmt->execute([$doctor]);
        $bookedDates = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo json_encode([
            'bookedDates' => $bookedDates
        ]);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error']);
}
?> 