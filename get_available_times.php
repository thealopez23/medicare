<?php
header('Content-Type: application/json');

if (!isset($_GET['doctor']) || !isset($_GET['date'])) {
    echo json_encode(['error' => 'Missing required parameters']);
    exit();
}

$doctor = $_GET['doctor'];
$date = $_GET['date'];

// Get day of week (0 = Sunday, 1 = Monday, etc.)
$dayOfWeek = date('w', strtotime($date));

// Define doctor schedules
$doctorSchedules = [
    'Dr. Alexa - Cardiologist' => [
        1 => ['09:00', '10:00', '11:00'], // Monday
        3 => ['09:00', '10:00', '11:00'], // Wednesday
        5 => ['09:00', '10:00', '11:00'], // Friday
        2 => ['14:00', '15:00', '16:00'], // Tuesday
        4 => ['14:00', '15:00', '16:00'], // Thursday
    ],
    'Dr. Thea - Pediatrician' => [
        1 => ['10:00', '11:00', '12:00'], // Monday
        2 => ['10:00', '11:00', '12:00'], // Tuesday
        3 => ['10:00', '11:00', '12:00'], // Wednesday
        4 => ['10:00', '11:00', '12:00'], // Thursday
        5 => ['10:00', '11:00', '12:00'], // Friday
        6 => ['09:00', '10:00', '11:00'], // Saturday
    ],
    'Dr. Renelyn - Dermatologist' => [
        2 => ['11:00', '12:00', '13:00'], // Tuesday
        4 => ['11:00', '12:00', '13:00'], // Thursday
        6 => ['13:00', '14:00', '15:00'], // Saturday
    ]
];

// Check if doctor exists and has schedule for the selected day
if (!isset($doctorSchedules[$doctor]) || !isset($doctorSchedules[$doctor][$dayOfWeek])) {
    echo json_encode(['available' => false, 'message' => 'Doctor not available on this day']);
    exit();
}

// Get available time slots for the selected day
$availableTimes = $doctorSchedules[$doctor][$dayOfWeek];

echo json_encode([
    'available' => true,
    'times' => $availableTimes
]);
?> 