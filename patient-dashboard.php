<?php
// Initialize session and dependencies
session_start();
include('config.php');

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Validate user session
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 3) {
    header('Location: login.php');
    exit();
}

// Fetch patient data
$patient_id = $_SESSION['user_id'];
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$patient_id]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$patient) {
        echo "Error: Patient not found.";
        exit();
    }

    // Fetch appointments with decline_reason
    $stmt = $pdo->prepare("
        SELECT id, date, time, doctor, message, is_approved, decline_reason, created_at
        FROM appointments
        WHERE email = ?
        ORDER BY CONCAT(date, ' ', time) DESC
    ");
    if (!$stmt->execute([$patient['email']])) {
        $errorInfo = $stmt->errorInfo();
        echo "Query Error: " . $errorInfo[2];
        exit;
    }
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

    // Split appointments into pending, approved, and declined
    $pending_appointments = array_filter($appointments, function($apt) {
        return $apt['is_approved'] == 1;
    });
    $approved_appointments = array_filter($appointments, function($apt) {
        return $apt['is_approved'] == 2;
    });
    $declined_appointments = array_filter($appointments, function($apt) {
        return $apt['is_approved'] == 3;
    });

    // Debug: Log fetched appointments
    error_log("Appointments fetched for {$patient['email']}: " . print_r($appointments, true));
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
    <title>Patient Dashboard - Medicare</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            @apply bg-gray-100;
        }
        .dashboard-card {
            @apply transition-transform duration-300 ease-in-out cursor-pointer;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        .nav-link {
            @apply transition-transform duration-300 ease-in-out;
        }
        .nav-link:hover {
            transform: translateY(-2px);
        }
        .table-row:hover {
            @apply bg-gray-50;
        }
        .cancel-btn {
            @apply relative flex items-center;
        }
        .spinner {
            @apply hidden w-4 h-4 mr-2 border-2 border-t-transparent border-white rounded-full animate-spin;
        }
        .tooltip {
            @apply relative;
        }
        .tooltip .tooltip-text {
            @apply invisible absolute z-10 w-32 bg-gray-800 text-white text-xs rounded py-1 px-2 bottom-full left-1/2 transform -translate-x-1/2 mb-2 opacity-0 transition-opacity duration-300;
        }
        .tooltip:hover .tooltip-text {
            @apply visible opacity-100;
        }
        .stat-section {
            @apply hidden;
        }
        .stat-section.active {
            @apply block;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <img src="Images/Logo.png" alt="Logo" class="h-8 w-8 mr-2">
                    <span class="text-xl font-bold text-gray-900">Medicare</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="index.php" class="nav-link text-gray-600 hover:text-blue-600 font-medium">
                        <i class="fas fa-home mr-1"></i> Home
                    </a>
                    <a href="appointment.php" class="nav-link bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 font-medium">
                        <i class="fas fa-calendar-plus mr-1"></i> Book Appointment
                    </a>
                    <a href="profile.php" class="nav-link text-gray-600 hover:text-gray-800 font-medium">
                        <i class="fas fa-user-edit mr-1"></i> View Profile
                    </a>
                    <a href="logout.php" class="nav-link text-red-600 hover:text-red-800 font-medium">
                        <i class="fas fa-sign-out-alt mr-1"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Welcome Section -->
        <section class="bg-gradient-to-r from-blue-50 to-green-50 rounded-lg shadow-md p-6 mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Welcome, <?php echo htmlspecialchars($patient['full_name']); ?>!</h1>
            <p class="text-gray-600 font-light">Here's an overview of my appointments and medical information.</p>
        </section>

        <!-- Divider -->
        <hr class="border-t border-gray-200 my-8">

        <!-- Quick Stats -->
        <section class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Quick Stats</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-lg shadow-md p-6 dashboard-card" onclick="showAppointments('upcoming')">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-calendar-check text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-gray-600 text-sm font-medium">Upcoming</h3>
                            <p class="text-2xl font-bold text-gray-800">
                                <?php 
                                $current_time = strtotime('2025-05-26 16:29:00'); // 4:29 PM PST, May 26, 2025
                                $upcoming = array_filter($appointments, function($apt) use ($current_time) {
                                    return strtotime($apt['date'] . ' ' . $apt['time']) > $current_time;
                                });
                                echo count($upcoming);
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6 dashboard-card" onclick="showAppointments('completed')">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <i class="fas fa-check-circle text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-gray-600 text-sm font-medium">Completed</h3>
                            <p class="text-2xl font-bold text-gray-800">
                                <?php 
                                $completed = array_filter($appointments, function($apt) use ($current_time) {
                                    return strtotime($apt['date'] . ' ' . $apt['time']) <= $current_time;
                                });
                                echo count($completed);
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6 dashboard-card" onclick="showAppointments('approved')">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <i class="fas fa-check-double text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-gray-600 text-sm font-medium">Approved</h3>
                            <p class="text-2xl font-bold text-gray-800">
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
                <div class="bg-white rounded-lg shadow-md p-6 dashboard-card" onclick="showAppointments('declined')">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-100 text-red-600">
                            <i class="fas fa-times-circle text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-gray-600 text-sm font-medium">Declined</h3>
                            <p class="text-2xl font-bold text-gray-800">
                                <?php 
                                $declined = array_filter($appointments, function($apt) {
                                    return $apt['is_approved'] == 3;
                                });
                                echo count($declined);
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Filtered Appointments Section -->
        <section id="filtered-appointments" class="mb-8 hidden">
            <h2 id="filtered-title" class="text-2xl font-semibold text-gray-800 mb-4"></h2>
            <div id="filtered-table" class="bg-white rounded-lg shadow-md p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead id="filtered-thead"></thead>
                        <tbody id="filtered-tbody" class="bg-white divide-y divide-gray-200"></tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Divider -->
        <hr class="border-t border-gray-200 my-8">

        <!-- Pending Appointments Section -->
        <section class="mb-8 stat-section" id="pending-section">
            <h2 class="text-2xl font-semibold text-yellow-600 mb-4">Pending Appointments</h2>
            <?php if (empty($pending_appointments)): ?>
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <p class="text-gray-600 font-light">No pending appointments. <a href="appointment.php" class="text-green-600 hover:underline">Book one now!</a></p>
                </div>
            <?php else: ?>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-yellow-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Date & Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Doctor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Message</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($pending_appointments as $appointment): ?>
                                    <tr class="table-row">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php 
                                            $datetime = $appointment['date'] . ' ' . $appointment['time'];
                                            echo date('M d, Y h:i A', strtotime($datetime)); 
                                            ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo htmlspecialchars($appointment['doctor']); ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            <?php echo htmlspecialchars($appointment['message']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Pending
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <?php if (strtotime($appointment['date'] . ' ' . $appointment['time']) > $current_time): ?>
                                                <div class="tooltip">
                                                    <button onclick="cancelAppointment(<?php echo $appointment['id']; ?>, this)" 
                                                            class="cancel-btn bg-red-600 text-white px-3 py-1 rounded-lg hover:bg-red-700">
                                                        <span class="spinner"></span> Cancel
                                                    </button>
                                                    <span class="tooltip-text"></span>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </section>

        <!-- Divider -->
        <hr class="border-t border-gray-200 my-8">

        <!-- Approved Appointments Section -->
        <section class="mb-8 stat-section" id="approved-section">
            <h2 class="text-2xl font-semibold text-green-600 mb-4">Approved Appointments</h2>
            <?php if (empty($approved_appointments)): ?>
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <p class="text-gray-600 font-light">No approved appointments. <a href="appointment.php" class="text-green-600 hover:underline">Book one now!</a></p>
                </div>
            <?php else: ?>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-green-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Date & Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Doctor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Message</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($approved_appointments as $appointment): ?>
                                    <tr class="table-row">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php 
                                            $datetime = $appointment['date'] . ' ' . $appointment['time'];
                                            echo date('M d, Y h:i A', strtotime($datetime)); 
                                            ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo htmlspecialchars($appointment['doctor']); ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            <?php echo htmlspecialchars($appointment['message']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Approved
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <?php if (strtotime($appointment['date'] . ' ' . $appointment['time']) > $current_time): ?>
                                                <div class="tooltip">
                                                    <button onclick="cancelAppointment(<?php echo $appointment['id']; ?>, this)" 
                                                            class="cancel-btn bg-red-600 text-white px-3 py-1 rounded-lg hover:bg-red-700">
                                                        <span class="spinner"></span> Cancel
                                                    </button>
                                                    <span class="tooltip-text"></span>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </section>

        <!-- Divider -->
        <hr class="border-t border-gray-200 my-8">

        <!-- Declined Appointments Section -->
        <section class="mb-8 stat-section" id="declined-section">
            <h2 class="text-2xl font-semibold text-red-600 mb-4">Declined Appointments</h2>
            <?php if (empty($declined_appointments)): ?>
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <p class="text-gray-600 font-light">No declined appointments.</p>
                </div>
            <?php else: ?>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-red-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Date & Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Doctor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Message</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Reason</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($declined_appointments as $appointment): ?>
                                    <tr class="table-row">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php 
                                            $datetime = $appointment['date'] . ' ' . $appointment['time'];
                                            echo date('M d, Y h:i A', strtotime($datetime)); 
                                            ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo htmlspecialchars($appointment['doctor']); ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            <?php echo htmlspecialchars($appointment['message']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Declined
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <p class="text-gray-500 italic">
                                                <?php echo !empty($appointment['decline_reason']) ? htmlspecialchars($appointment['decline_reason']) : '-'; ?>
                                            </p>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <!-- JavaScript -->
    <script>
        const appointments = <?php echo json_encode($appointments); ?>;
        const currentTime = <?php echo $current_time; ?> * 1000; // Convert to milliseconds

        function formatDateTime(date, time) {
            return new Date(`${date} ${time}`).toLocaleString('en-US', {
                month: 'short',
                day: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            });
        }

        function showAppointments(type) {
    const filteredSection = document.getElementById('filtered-appointments');
    const filteredTitle = document.getElementById('filtered-title');
    const filteredThead = document.getElementById('filtered-thead');
    const filteredTbody = document.getElementById('filtered-tbody');

    // Show filtered appointments section
    filteredSection.classList.remove('hidden');
    filteredTbody.innerHTML = '';

    let filteredAppointments = [];
    let title = '';
    let headers = `
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Date & Time</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Doctor</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Message</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
    `;
    let bgClass = '';

    switch (type) {
        case 'upcoming':
            filteredAppointments = appointments.filter(apt => new Date(`${apt.date} ${apt.time}`).getTime() > currentTime);
            title = 'Upcoming Appointments';
            headers += `
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Actions</th>
            </tr>`;
            bgClass = 'bg-blue-50';
            break;
        case 'completed':
            filteredAppointments = appointments.filter(apt => new Date(`${apt.date} ${apt.time}`).getTime() <= currentTime);
            title = 'Completed Appointments';
            headers += `
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Actions</th>
            </tr>`;
            bgClass = 'bg-green-50';
            break;
        case 'approved':
            filteredAppointments = appointments.filter(apt => apt.is_approved == 2);
            title = 'Approved Appointments';
            headers += `
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Actions</th>
            </tr>`;
            bgClass = 'bg-green-50';
            break;
        case 'declined':
            filteredAppointments = appointments.filter(apt => apt.is_approved == 3);
            title = 'Declined Appointments';
            headers += `
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Reason</th>
            </tr>`;
            bgClass = 'bg-red-50';
            break;
    }

    filteredTitle.textContent = title;
    filteredThead.innerHTML = headers;
    filteredThead.className = bgClass;

    if (filteredAppointments.length === 0) {
        filteredTbody.innerHTML = `
            <tr>
                <td colspan="${type === 'declined' ? 5 : 5}" class="px-6 py-4 text-center text-gray-600 font-light">
                    No ${title.toLowerCase()}.
                    ${type !== 'declined' ? '<a href="appointment.php" class="text-green-600 hover:underline">Book one now!</a>' : ''}
                </td>
            </tr>`;
        return;
    }

    // Sort appointments by date and time
    filteredAppointments.sort((a, b) => new Date(`${b.date} ${b.time}`).getTime() - new Date(`${a.date} ${a.time}`).getTime());

    filteredAppointments.forEach(apt => {
        const status = apt.is_approved == 1 ? 'Pending' : apt.is_approved == 2 ? 'Approved' : 'Declined';
        const statusClass = apt.is_approved == 1 ? 'bg-yellow-100 text-yellow-800' : 
                           apt.is_approved == 2 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
        let row = `
            <tr class="table-row">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${formatDateTime(apt.date, apt.time)}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${apt.doctor}</td>
                <td class="px-6 py-4 text-sm text-gray-600">${apt.message}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">
                        ${status}
                    </span>
                </td>
        `;
        if (type === 'declined') {
            row += `
                <td class="px-6 py-4 text-sm">
                    <p class="text-gray-500 italic">${apt.decline_reason || '-'}</p>
                </td>
            `;
        } else {
            row += `
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    ${new Date(`${apt.date} ${apt.time}`).getTime() > currentTime && apt.is_approved != 3 ? `
                        <div class="tooltip">
                            <button onclick="cancelAppointment(${apt.id}, this)" 
                                    class="cancel-btn bg-red-600 text-white px-3 py-1 rounded-lg hover:bg-red-700">
                                <span class="spinner"></span> Cancel
                            </button>
                            <span class="tooltip-text"></span>
                        </div>
                    ` : ''}
                </td>
            `;
        }
        row += `</tr>`;
        filteredTbody.innerHTML += row;
    });
}

        function cancelAppointment(id, button) {
            const spinner = button.querySelector('.spinner');
            const originalText = button.textContent.trim();

            Swal.fire({
                title: 'Cancel Appointment?',
                text: 'Are you sure you want to cancel this appointment?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#10B981',
                cancelButtonColor: '#EF4444',
                confirmButtonText: 'Yes, cancel it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    spinner.classList.remove('hidden');
                    button.textContent = 'Cancelling...';
                    button.disabled = true;

                    fetch('cancel-appointment.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'id=' + id
                    })
                    .then(response => response.json())
                    .then(data => {
                        spinner.classList.add('hidden');
                        button.textContent = originalText;
                        button.disabled = false;

                        if (data.success) {
                            Swal.fire({
                                title: 'Cancelled!',
                                text: 'Your appointment has been cancelled.',
                                icon: 'success',
                                confirmButtonColor: '#10B981'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Failed to cancel appointment. Please try again.',
                                icon: 'error',
                                confirmButtonColor: '#EF4444'
                            });
                        }
                    })
                    .catch(error => {
                        spinner.classList.add('hidden');
                        button.textContent = originalText;
                        button.disabled = false;

                        Swal.fire({
                            title: 'Error!',
                            text: 'An error occurred. Please try again.',
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
// Close database connection
$pdo = null;
?>