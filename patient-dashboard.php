<?php
session_start();
include('config.php');

// Check if user is logged in and is a patient
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 3) {
    header('Location: login.php');
    exit();
}

// Fetch patient information
$patient_id = $_SESSION['user_id'];
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$patient_id]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$patient) {
        echo "Error: Patient not found.";
        exit();
    }

    // Fetch patient's appointments
    $stmt = $pdo->prepare("
        SELECT *
        FROM appointments
        WHERE email = ?
        ORDER BY CONCAT(date, ' ', time) DESC
    ");
    if (!$stmt->execute([$patient['email']])) {
        $errorInfo = $stmt->errorInfo();
        echo "Query Error: " . $errorInfo[2];
        exit();
    }
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard - Healthcare Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .dashboard-card {
            transition: transform 0.3s ease;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        .nav-link {
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Top Navigation Bar -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <img src="Images/Logo.png" alt="Logo" class="h-8 w-8 mr-2">
                    <span class="text-xl font-semibold text-gray-800">Healthcare Portal</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="index.php" class="nav-link text-gray-600 hover:text-green-600">
                        <i class="fas fa-home mr-2"></i>Home
                    </a>
                    <a href="appointment.php" class="nav-link bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-calendar-plus mr-2"></i>Book Appointment
                    </a>
                    <a href="profile.php" class="nav-link text-gray-600 hover:text-gray-800">
                        <i class="fas fa-user-edit mr-2"></i>View Profile
                    </a>
                    <a href="logout.php" class="nav-link text-red-600 hover:text-red-800">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Welcome Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Welcome, <?php echo htmlspecialchars($patient['full_name']); ?>!</h1>
            <p class="text-gray-600">Here's an overview of your appointments and medical information.</p>
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
                        <p class="text-2xl font-semibold text-gray-800">
                            <?php 
                            $upcoming = array_filter($appointments, function($apt) {
                                return strtotime($apt['date']) > time();
                            });
                            echo count($upcoming);
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 dashboard-card">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-gray-600 text-sm">Completed Appointments</h2>
                        <p class="text-2xl font-semibold text-gray-800">
                            <?php 
                            $completed = array_filter($appointments, function($apt) {
                                return strtotime($apt['date']) < time();
                            });
                            echo count($completed);
                            ?>
                        </p>
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
                        <p class="text-2xl font-semibold text-gray-800">
                            <?php 
                            $approved = array_filter($appointments, function($apt) {
                                return $apt['is_approved'] == 2;
                            });
                            echo count($approved);
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 dashboard-card">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <i class="fas fa-times-circle text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-gray-600 text-sm">Rejected Appointments</h2>
                        <p class="text-2xl font-semibold text-gray-800">
                            <?php 
                            $rejected = array_filter($appointments, function($apt) {
                                return $apt['is_approved'] == 3;
                            });
                            echo count($rejected);
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointments Section -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Your Appointments</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($appointments as $appointment): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php $datetime = $appointment['date'] . ' ' . $appointment['time'];
                                    echo date('M d, Y h:i A', strtotime($datetime)); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    Dr. <?php echo htmlspecialchars($appointment['doctor']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php echo htmlspecialchars($appointment['message']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $status_class = '';
                                    $status_text = '';
                                    if ($appointment['is_approved'] == 1) {
                                        $status_class = 'bg-yellow-100 text-yellow-800';
                                        $status_text = 'Pending';
                                    } elseif ($appointment['is_approved'] == 2) {
                                        $status_class = 'bg-green-100 text-green-800';
                                        $status_text = 'Approved';
                                    } elseif ($appointment['is_approved'] == 3) {
                                        $status_class = 'bg-red-100 text-red-800';
                                        $status_text = 'Rejected';
                                    } else {
                                        if (strtotime($appointment['date']) > time()) {
                                            $status_class = 'bg-yellow-100 text-yellow-800';
                                            $status_text = 'Upcoming';
                                        } else {
                                            $status_class = 'bg-green-100 text-green-800';
                                            $status_text = 'Completed';
                                        }
                                    }
                                    ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $status_class; ?>">
                                        <?php echo $status_text; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <?php if (strtotime($appointment['date']) > time()): ?>
                                        <button onclick="cancelAppointment(<?php echo $appointment['id']; ?>)" 
                                                class="text-red-600 hover:text-red-900">
                                            Cancel
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function cancelAppointment(id) {
            Swal.fire({
                title: 'Cancel Appointment?',
                text: "Are you sure you want to cancel this appointment?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#10B981',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, cancel it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Send AJAX request to cancel appointment
                    fetch('cancel-appointment.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'id=' + id
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire(
                                'Cancelled!',
                                'Your appointment has been cancelled.',
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire(
                                'Error!',
                                'Failed to cancel appointment. Please try again.',
                                'error'
                            );
                        }
                    });
                }
            });
        }
    </script>
</body>
</html>