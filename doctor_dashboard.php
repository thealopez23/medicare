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
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id AND role = 2");
    $stmt->execute([':user_id' => $user_id]);
    $doctor = $stmt->fetch(PDO::FETCH_ASSOC);

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
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: []; // Ensure $appointments is an array
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage();
    exit();
}

// Split appointments into Done and Upcoming
$current_timestamp = strtotime('2025-05-26 15:29:00'); // Current date and time: 3:29 PM PST, May 26, 2025
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

// Count Approved and Declined Appointments
$approved_appointments = array_filter($appointments, function($apt) {
    return $apt['is_approved'] == 2;
});
$declined_appointments = array_filter($appointments, function($apt) {
    return $apt['is_approved'] == 3;
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard - Medicare</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        .dashboard-card {
            transition: transform 0.3s ease;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Top Navigation Bar -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <img src="Images/Logo.png" alt="Logo" class="h-8 w-8 mr-2">
                    <span class="text-xl font-semibold text-gray-800">Medicare</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="doctor-profile.php" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-user-edit mr-2"></i>View Profile
                    </a>
                    <a href="logout.php" class="text-red-600 hover:text-red-800">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-8">
        <!-- Welcome Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Welcome, <?php echo htmlspecialchars($doctor['full_name']); ?>!</h1>
            <p class="text-gray-600">Here's an overview of my appointments and schedule.</p>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 dashboard-card">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-calendar-check text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-gray-600 text-sm">Upcoming Appointments</h2>
                        <p class="text-2xl font-semibold text-gray-800"><?php echo count($upcoming_appointments); ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 dashboard-card">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-gray-600 text-sm">Done Appointments</h2>
                        <p class="text-2xl font-semibold text-gray-800"><?php echo count($done_appointments); ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 dashboard-card">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-check-double text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-gray-600 text-sm">Approved Appointments</h2>
                        <p class="text-2xl font-semibold text-gray-800"><?php echo count($approved_appointments); ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 dashboard-card">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <i class="fas fa-times-circle text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-gray-600 text-sm">Declined Appointments</h2>
                        <p class="text-2xl font-semibold text-gray-800"><?php echo count($declined_appointments); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointments Section -->
        <div class="space-y-6">
            <!-- Upcoming Appointments -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">My Upcoming Appointments</h2>
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
                                                    <?php
                                                    $status_color = '';
                                                    $status_text = '';
                                                    if ($appointment['is_approved'] == 1) {
                                                        $status_color = 'text-yellow-600';
                                                        $status_text = 'Pending';
                                                    } elseif ($appointment['is_approved'] == 2) {
                                                        $status_color = 'text-green-600';
                                                        $status_text = 'Approved';
                                                    } elseif ($appointment['is_approved'] == 3) {
                                                        $status_color = 'text-red-600';
                                                        $status_text = 'Declined';
                                                    }
                                                    ?>
                                                    <p class="text-sm <?php echo $status_color; ?> font-medium"><?php echo $status_text; ?></p>
                                                    <?php if ($appointment['is_approved'] == 3 && !empty($appointment['decline_reason'])): ?>
                                                        <p class="text-sm text-gray-500 italic">Reason: <?php echo htmlspecialchars($appointment['decline_reason']); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-blue-600 font-medium"><?php echo htmlspecialchars($appointment['email']); ?></p>
                                                    <p class="text-sm text-gray-500">Booked: <?php echo date('M j, Y', strtotime($appointment['created_at'])); ?></p>
                                                    <?php if ($appointment['is_approved'] == 1): ?>
                                                        <div class="mt-2 space-x-2">
                                                            <button onclick="event.stopPropagation(); updateAppointmentStatus(<?php echo $appointment['id']; ?>, 2)" 
                                                                    class="text-sm bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 transition">
                                                                Approve
                                                            </button>
                                                            <button onclick="event.stopPropagation(); declineAppointment(<?php echo $appointment['id']; ?>)" 
                                                                    class="text-sm bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition">
                                                                Decline
                                                            </button>
                                                        </div>
                                                    <?php endif; ?>
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
                <h2 class="text-xl font-bold text-gray-800 mb-4">My Done Appointments</h2>
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
                                                    <?php
                                                    $status_color = '';
                                                    $status_text = '';
                                                    if ($appointment['is_approved'] == 1) {
                                                        $status_color = 'text-yellow-600';
                                                        $status_text = 'Pending';
                                                    } elseif ($appointment['is_approved'] == 2) {
                                                        $status_color = 'text-green-600';
                                                        $status_text = 'Approved';
                                                    } elseif ($appointment['is_approved'] == 3) {
                                                        $status_color = 'text-red-600';
                                                        $status_text = 'Declined';
                                                    }
                                                    ?>
                                                    <p class="text-sm <?php echo $status_color; ?> font-medium"><?php echo $status_text; ?></p>
                                                    <?php if ($appointment['is_approved'] == 3 && !empty($appointment['decline_reason'])): ?>
                                                        <p class="text-sm text-gray-500 italic">Reason: <?php echo htmlspecialchars($appointment['decline_reason']); ?></p>
                                                    <?php endif; ?>
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
                © <?php echo date('Y'); ?> Medicare. All rights reserved.
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
                    ${patient.decline_reason ? `
                        <div class="mt-3 pt-3 border-t">
                            <p class="font-semibold">Decline Reason:</p>
                            <p class="text-sm text-gray-600">${patient.decline_reason.replace(/\n/g, '<br>')}</p>
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

        function updateAppointmentStatus(appointmentId, status) {
            const action = status === 2 ? 'approve' : 'decline';
            const actionColor = status === 2 ? '#10B981' : '#EF4444';
            
            Swal.fire({
                title: `Are you sure you want to ${action} this appointment?`,
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: actionColor,
                cancelButtonColor: '#6B7280',
                confirmButtonText: `Yes, ${action} it!`,
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    Swal.fire({
                        title: 'Processing...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const formData = new FormData();
                    formData.append('id', appointmentId);
                    formData.append('status', status);

                    fetch('update_appointment_status.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: `Appointment has been ${action}d successfully.`,
                                icon: 'success',
                                confirmButtonColor: actionColor
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: data.message || 'Unknown error occurred',
                                icon: 'error',
                                confirmButtonColor: actionColor
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Failed to update appointment status',
                            icon: 'error',
                            confirmButtonColor: actionColor
                        });
                    });
                }
            });
        }

        function declineAppointment(appointmentId) {
            Swal.fire({
                title: 'Decline Appointment',
                html: `
                    <p class="text-gray-600 mb-4">Please provide a reason for declining this appointment:</p>
                    <textarea id="declineReason" class="w-full p-2 border rounded-lg" rows="4" placeholder="Enter reason for declining"></textarea>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Decline Appointment',
                cancelButtonText: 'Cancel',
                preConfirm: () => {
                    const reason = document.getElementById('declineReason').value;
                    if (!reason.trim()) {
                        Swal.showValidationMessage('Please provide a reason for declining');
                        return false;
                    }
                    return reason;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    Swal.fire({
                        title: 'Processing...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const formData = new FormData();
                    formData.append('id', appointmentId);
                    formData.append('status', 3);
                    formData.append('decline_reason', result.value);

                    fetch('update_appointment_status.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Appointment has been declined successfully.',
                                icon: 'success',
                                confirmButtonColor: '#EF4444'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: data.message || 'Unknown error occurred',
                                icon: 'error',
                                confirmButtonColor: '#EF4444'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Failed to update appointment status',
                            icon: 'error',
                            confirmButtonColor: '#EF4444'
                        });
                    });
                }
            });
        }
    </script>
</body>
</html>
<?php
// Close the database connection
$pdo = null;
?>