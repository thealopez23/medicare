<?php
session_start();
include('config.php');

// Check if user is logged in and is a doctor
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get doctor's information
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id AND role = 2");
$stmt->execute([':user_id' => $user_id]);
$doctor = $stmt->fetch();

if (!$doctor) {
    header('Location: index.php');
    exit();
}

// Get doctor's appointments
$stmt = $pdo->prepare("
    SELECT * FROM appointments 
    WHERE doctor = :doctor_name 
    ORDER BY date ASC, time ASC
");
$stmt->execute([':doctor_name' => $doctor['full_name']]);
$appointments = $stmt->fetchAll();

// Split appointments into Done and Upcoming
$current_timestamp = strtotime('2025-05-18 20:57:00'); // 08:57 PM PST, May 18, 2025
$done_appointments = [];
$upcoming_appointments = [];

foreach ($appointments as $appt) {
    $appt_datetime = strtotime($appt['date'] . ' ' . $appt['time']);
    if ($appt_datetime <= $current_timestamp) {
        $done_appointments[] = $appt;
    } else {
        $upcoming_appointments[] = $appt;
    }
}

// Group Done appointments by date
$grouped_done_appointments = [];
foreach ($done_appointments as $appt) {
    $date = date('Y-m-d', strtotime($appt['date']));
    $grouped_done_appointments[$date][] = $appt;
}

// Group Upcoming appointments by date
$grouped_upcoming_appointments = [];
foreach ($upcoming_appointments as $appt) {
    $date = date('Y-m-d', strtotime($appt['date']));
    $grouped_upcoming_appointments[$date][] = $appt;
}

// Define available time slots and color for the profile (hardcoded for now)
$doctor_name = $doctor['full_name'];
$availability = [];
$profile_color = 'bg-gray-500'; // Default color

if ($doctor_name === "Dr. Alexa - Cardiologist") {
    $availability = [
        'Mon, Wed, Fri – 9:00 AM to 12:00 PM (May 19, 21, 23, 2025)',
        'Tue, Thu – 2:00 PM to 5:00 PM (May 20, 22, 2025)'
    ];
    $profile_color = 'bg-blue-500';
} elseif ($doctor_name === "Dr. Thea - Pediatrician") {
    $availability = [
        'Mon – Fri – 10:00 AM to 1:00 PM (May 19–23, 2025)',
        'Sat – 9:00 AM to 12:00 PM (May 24, 2025)'
    ];
    $profile_color = 'bg-green-500';
} elseif ($doctor_name === "Dr. Renelyn - Dermatologist") {
    $availability = [
        'Tue, Thu – 11:00 AM to 2:00 PM (May 20, 22, 2025)',
        'Sat – 1:00 PM to 4:00 PM (May 24, 2025)'
    ];
    $profile_color = 'bg-purple-500';
} else {
    $availability = ['No availability set'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard - Healthcare Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .modal {
            display: none;
            transition: all 0.3s ease;
        }
        .modal.active {
            display: flex;
        }
        .calendar-day {
            transition: all 0.2s ease;
        }
        .calendar-day:hover {
            background-color: #e6f3ff;
        }
        .profile-circle {
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-10">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-blue-600">Doctor Dashboard</h1>
                <div class="flex items-center space-x-6">
                    <span class="text-gray-700 font-medium">Dr. <?php echo htmlspecialchars(explode(' ', $doctor['full_name'])[1]); ?></span>
                    <a href="logout.php" class="text-red-500 hover:text-red-700 transition">
                        <i class="fas fa-sign-out-alt mr-1"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Doctor Profile Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg p-6 sticky top-24">
                    <div class="text-center mb-6">
                        <div class="w-24 h-24 mx-auto rounded-full <?php echo $profile_color; ?> flex items-center justify-center mb-4 profile-circle">
                            <i class="fas fa-user-md text-4xl text-white"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($doctor['full_name']); ?></h2>
                        <p class="text-blue-600 text-sm"><?php echo htmlspecialchars(explode(' - ', $doctor['full_name'])[1]); ?></p>
                        <div class="mt-4 text-left">
                            <p class="text-sm font-semibold text-gray-700">Available Times:</p>
                            <ul class="list-disc list-inside text-sm text-gray-600">
                                <?php foreach ($availability as $slot): ?>
                                    <li><?php echo htmlspecialchars($slot); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-envelope w-5 text-blue-500"></i>
                            <span class="ml-2 text-sm"><?php echo htmlspecialchars($doctor['email']); ?></span>
                        </div>
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-phone w-5 text-blue-500"></i>
                            <span class="ml-2 text-sm"><?php echo htmlspecialchars($doctor['phone']); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Appointments Section -->
            <div class="lg:col-span-3 space-y-6">
                <!-- Upcoming Appointments -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Upcoming Appointments</h2>
                    <?php if (empty($upcoming_appointments)): ?>
                        <p class="text-gray-500 text-center py-4">No upcoming appointments</p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($grouped_upcoming_appointments as $date => $appts): ?>
                                <div class="border-b pb-4 mb-4">
                                    <h3 class="text-lg font-semibold text-gray-700"><?php echo date('F j, Y', strtotime($date)); ?></h3>
                                    <div class="mt-3 space-y-3">
                                        <?php foreach ($appts as $appointment): ?>
                                            <?php
                                            $appt_datetime = strtotime($appointment['date'] . ' ' . $appointment['time']);
                                            $status = $appt_datetime <= $current_timestamp ? 'Done' : 'Upcoming';
                                            $status_color = $status === 'Done' ? 'text-green-600' : 'text-blue-600';
                                            ?>
                                            <div class="border rounded-lg p-4 hover:bg-gray-50 cursor-pointer transition appointment-card" 
                                                 data-patient='<?php echo json_encode($appointment); ?>'>
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        <h4 class="font-semibold text-gray-800"><?php echo htmlspecialchars($appointment['fullName']); ?></h4>
                                                        <p class="text-sm text-gray-600"><?php echo date('g:i A', strtotime($appointment['time'])); ?></p>
                                                        <p class="text-sm <?php echo $status_color; ?> font-medium"><?php echo $status; ?></p>
                                                    </div>
                                                    <div class="text-right">
                                                        <p class="text-blue-600 font-medium"><?php echo htmlspecialchars($appointment['email']); ?></p>
                                                        <p class="text-sm text-gray-500">Booked: <?php echo date('M j, Y', strtotime($appointment['created_at'])); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Done Appointments -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Done Appointments</h2>
                    <?php if (empty($done_appointments)): ?>
                        <p class="text-gray-500 text-center py-4">No past appointments</p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($grouped_done_appointments as $date => $appts): ?>
                                <div class="border-b pb-4 mb-4">
                                    <h3 class="text-lg font-semibold text-gray-700"><?php echo date('F j, Y', strtotime($date)); ?></h3>
                                    <div class="mt-3 space-y-3">
                                        <?php foreach ($appts as $appointment): ?>
                                            <?php
                                            $appt_datetime = strtotime($appointment['date'] . ' ' . $appointment['time']);
                                            $status = $appt_datetime <= $current_timestamp ? 'Done' : 'Upcoming';
                                            $status_color = $status === 'Done' ? 'text-green-600' : 'text-blue-600';
                                            ?>
                                            <div class="border rounded-lg p-4 hover:bg-gray-50 cursor-pointer transition appointment-card" 
                                                 data-patient='<?php echo json_encode($appointment); ?>'>
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        <h4 class="font-semibold text-gray-800"><?php echo htmlspecialchars($appointment['fullName']); ?></h4>
                                                        <p class="text-sm text-gray-600"><?php echo date('g:i A', strtotime($appointment['time'])); ?></p>
                                                        <p class="text-sm <?php echo $status_color; ?> font-medium"><?php echo $status; ?></p>
                                                    </div>
                                                    <div class="text-right">
                                                        <p class="text-blue-600 font-medium"><?php echo htmlspecialchars($appointment['email']); ?></p>
                                                        <p class="text-sm text-gray-500">Booked: <?php echo date('M j, Y', strtotime($appointment['created_at'])); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Patient Details Modal -->
    <div id="patientModal" class="modal fixed inset-0 bg-black bg-opacity-50 items-center justify-center">
        <div class="bg-white rounded-xl p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800">Patient Details</h2>
                <button id="closeModal" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="patientDetails" class="space-y-3">
                <!-- Patient details will be injected here -->
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t mt-8">
        <div class="container mx-auto px-4 py-6">
            <p class="text-center text-gray-600 text-sm">
                © <?php echo date('Y'); ?> Healthcare Portal. All rights reserved.
            </p>
        </div>
    </footer>

    <script>
        const appointmentCards = document.querySelectorAll('.appointment-card');
        const modal = document.getElementById('patientModal');
        const closeModal = document.getElementById('closeModal');
        const patientDetails = document.getElementById('patientDetails');

        appointmentCards.forEach(card => {
            card.addEventListener('click', () => {
                const patient = JSON.parse(card.dataset.patient);
                patientDetails.innerHTML = `
                    <div class="flex items-center">
                        <i class="fas fa-user w-5 text-blue-500"></i>
                        <span class="ml-2 font-semibold">${patient.fullName}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-envelope w-5 text-blue-500"></i>
                        <span class="ml-2">${patient.email}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-phone w-5 text-blue-500"></i>
                        <span class="ml-2">${patient.phone}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-calendar w-5 text-blue-500"></i>
                        <span class="ml-2">${new Date(patient.date).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-clock w-5 text-blue-500"></i>
                        <span class="ml-2">${new Date(`2000-01-01 ${patient.time}`).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true })}</span>
                    </div>
                    ${patient.message ? `
                        <div class="mt-3 pt-3 border-t">
                            <p class="font-semibold">Patient's Message:</p>
                            <p class="text-sm text-gray-600">${patient.message.replace(/\n/g, '<br>')}</p>
                        </div>
                    ` : ''}
                `;
                modal.classList.add('active');
            });
        });

        closeModal.addEventListener('click', () => {
            modal.classList.remove('active');
        });

        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('active');
            }
        });
    </script>
</body>
</html>