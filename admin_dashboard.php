<?php
session_start();
include('config.php');

// Set time zone for Philippines
date_default_timezone_set('Asia/Manila');

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit;
}

// Current date for comparison
$current_date = date('Y-m-d');

// Fetch appointments and calculate counts
try {
    $stmt = $pdo->query("SELECT * FROM appointments ORDER BY date ASC, time ASC");
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate appointment counts
    $counts = ['total' => 0, 'upcoming' => 0, 'done' => 0];
    foreach ($appointments as $appt) {
        $counts['total']++;
        if ($appt['date'] >= $current_date) {
            $counts['upcoming']++;
        } else {
            $counts['done']++;
        }
    }
} catch (PDOException $e) {
    die("Error retrieving appointments: " . $e->getMessage());
}

// Group appointments by date
$appointments_by_date = [];
foreach ($appointments as $appt) {
    $date = $appt['date'];
    if (!isset($appointments_by_date[$date])) {
        $appointments_by_date[$date] = [];
    }
    $appointments_by_date[$date][] = $appt;
}

// Fetch messages and count
try {
    $stmt = $pdo->query("SELECT * FROM messages ORDER BY sent_at DESC");
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $counts['messages'] = count($messages);
} catch (PDOException $e) {
    die("Error retrieving messages: " . $e->getMessage());
}

// Group messages by date
$messages_by_date = [];
foreach ($messages as $msg) {
    $date = date('Y-m-d', strtotime($msg['sent_at']));
    if (!isset($messages_by_date[$date])) {
        $messages_by_date[$date] = [];
    }
    $messages_by_date[$date][] = $msg;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Admin Dashboard - Healthcare Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f0f4f8 0%, #e0e7ef 100%);
        }
        .primary-bg {
            background-color: #10B981;
        }
        .nav-link {
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            transform: translateY(-2px);
        }
        #preloader {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: #10B981;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .dashboard-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            border-radius: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        .text-readable {
            color: #1E40AF;
        }
        .btn-primary {
            background-color: #10B981;
            color: white;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.5);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #059669;
            box-shadow: 0 6px 20px rgba(5, 150, 105, 0.7);
            transform: scale(1.05);
        }
        .btn-secondary {
            background-color: #6B7280;
            color: white;
            box-shadow: 0 4px 15px rgba(107, 114, 128, 0.5);
            transition: all 0.3s ease;
        }
        .btn-secondary:hover {
            box-shadow: 0 6px 20px rgba(107, 114, 128, 0.7);
            transform: scale(1.05);
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 250px;
            background: #fff;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            z-index: 40;
        }
        .sidebar-hidden {
            transform: translateX(-100%);
        }
        a:focus, button:focus, input:focus {
            outline: 3px solid #10B981;
            outline-offset: 2px;
        }
        .accordion-toggle:checked ~ .accordion-content {
            display: block;
        }
        .message-feedback {
            display: none;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .status-upcoming { background-color: #FEF3C7; color: #D97706; }
        .status-done { background-color: #D1FAE5; color: #10B981; }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <!-- Preloader -->
    <div id="preloader">
        <div class="flex flex-col items-center">
            <img src="Images/Logo.png" alt="Loading Logo" class="w-16 h-16 animate-pulse mb-4"/>
            <div class="animate-spin rounded-full h-12 w-12 border-t-4 border-white"></div>
        </div>
    </div>

    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar sidebar-hidden md:sidebar-visible">
        <div class="p-6">
            <h2 class="text-2xl font-bold text-green-600 mb-6">Navigation</h2>
            <ul class="space-y-4">
                <li>
                    <a href="index.php" class="nav-link flex items-center text-lg text-gray-700 hover:text-green-600">
                        <i class="fas fa-home mr-2"></i> Home Dashboard
                    </a>
                </li>
                <li>
                    <a href="logout.php" class="nav-link flex items-center text-lg text-gray-700 hover:text-red-600">
                        <i class="fas fa-sign-out-alt mr-2"></i> Log Out
                    </a>
                </li>
            </ul>
        </div>
    </aside>

    <!-- Header (Navigation) -->
    <header class="bg-white/90 shadow-lg sticky top-0 z-50 backdrop-blur-sm">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <img alt="Healthcare Portal Logo" class="w-14 h-14" src="Images/Logo.png"/>
                <h1 class="text-3xl font-bold text-green-600">Admin Dashboard</h1>
            </div>
            <div class="flex items-center space-x-4">
                <a href="index.php" class="hidden md:block nav-link text-gray-700 hover:text-green-600 text-lg">
                    <i class="fas fa-home mr-1"></i> Home
                </a>
                <a href="logout.php" class="hidden md:block nav-link text-gray-700 hover:text-red-600 text-lg">
                    <i class="fas fa-sign-out-alt mr-1"></i> Log Out
                </a>
                <button aria-label="Toggle sidebar" class="md:hidden text-gray-800 focus:outline-none" id="sidebar-toggle">
                    <i class="fas fa-bars fa-lg"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 container mx-auto px-6 py-12">
        <!-- Welcome Header -->
        <header class="mb-8 text-center">
            <h1 class="text-4xl font-bold text-readable">Welcome, Admin</h1>
        </header>

        <!-- Summary Section -->
        <section class="dashboard-section p-8 mb-12">
            <h2 class="text-3xl font-semibold text-readable mb-6">Summary</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-700">Total Appointments</h3>
                    <p class="text-2xl font-bold text-green-600"><?php echo $counts['total']; ?></p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-700">Upcoming Appointments</h3>
                    <p class="text-2xl font-bold text-amber-600"><?php echo $counts['upcoming']; ?></p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-700">Done Appointments</h3>
                    <p class="text-2xl font-bold text-green-600"><?php echo $counts['done']; ?></p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-700">Total Messages</h3>
                    <p class="text-2xl font-bold text-blue-600"><?php echo $counts['messages']; ?></p>
                </div>
            </div>
        </section>

        <!-- Appointments and Messages -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Appointments Section (Left) -->
            <section id="appointments" class="dashboard-section p-8">
                <h2 class="text-3xl font-semibold text-readable mb-6">Appointments</h2>
                <div class="mb-6">
                    <input type="text" id="appointment-search" placeholder="Search by name or date..."
                           class="w-full max-w-md px-4 py-3 border border-gray-300 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-green-600"/>
                </div>
                <div class="space-y-4">
                    <?php if (empty($appointments_by_date)): ?>
                        <p class="text-gray-500 text-lg">No appointments available.</p>
                    <?php else: ?>
                        <?php foreach ($appointments_by_date as $date => $appts): ?>
                            <div class="accordion">
                                <input type="checkbox" id="appt-date-<?php echo htmlspecialchars($date); ?>" class="accordion-toggle hidden"/>
                                <label for="appt-date-<?php echo htmlspecialchars($date); ?>" class="flex items-center justify-between px-4 py-3 bg-green-100 text-green-800 font-semibold rounded-lg cursor-pointer">
                                    <span><?php echo htmlspecialchars($date); ?> (<?php echo count($appts); ?>)</span>
                                    <i class="fas fa-chevron-down transform transition-transform accordion-toggle-icon"></i>
                                </label>
                                <div class="accordion-content hidden overflow-x-auto">
                                    <table class="w-full table-auto text-left mt-2">
                                        <thead>
                                            <tr class="text-gray-700 border-b">
                                                <th class="pb-2">Full Name</th>
                                                <th class="pb-2">Time</th>
                                                <th class="pb-2">Doctor</th>
                                                <th class="pb-2">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($appts as $row): ?>
                                                <?php $status = $row['date'] >= $current_date ? 'Upcoming' : 'Done'; ?>
                                                <tr class="border-b hover:bg-gray-50">
                                                    <td class="py-2"><?php echo htmlspecialchars($row['fullName']); ?></td>
                                                    <td class="py-2"><?php echo htmlspecialchars($row['time']); ?></td>
                                                    <td class="py-2"><?php echo htmlspecialchars($row['doctor']); ?></td>
                                                    <td class="py-2">
                                                        <span class="status-badge status-<?php echo strtolower($status); ?>">
                                                            <?php echo $status; ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Messages Section (Right) -->
            <section id="messages" class="dashboard-section p-8">
                <h2 class="text-3xl font-semibold text-readable mb-6">Messages</h2>
                <div class="space-y-4">
                    <?php if (empty($messages_by_date)): ?>
                        <p class="text-gray-500 text-lg">No messages available.</p>
                    <?php else: ?>
                        <?php foreach ($messages_by_date as $date => $msgs): ?>
                            <div class="accordion">
                                <input type="checkbox" id="msg-date-<?php echo htmlspecialchars($date); ?>" class="accordion-toggle hidden"/>
                                <label for="msg-date-<?php echo htmlspecialchars($date); ?>" class="flex items-center justify-between px-4 py-3 bg-blue-100 text-blue-800 font-semibold rounded-lg cursor-pointer">
                                    <span><?php echo htmlspecialchars($date); ?> (<?php echo count($msgs); ?>)</span>
                                    <i class="fas fa-chevron-down transform transition-transform accordion-toggle-icon"></i>
                                </label>
                                <div class="accordion-content hidden">
                                    <ul class="space-y-6 mt-2">
                                        <?php foreach ($msgs as $row): ?>
                                            <li class="border-b pb-4" data-message-id="<?php echo $row['id']; ?>">
                                                <p><strong class="text-green-700"><?php echo htmlspecialchars($row['sender_name']); ?></strong>
                                                    <span class="text-sm text-gray-500">(<?php echo $row['sent_at']; ?>)</span></p>
                                                <p class="text-gray-700 mb-2"><?php echo htmlspecialchars($row['message']); ?></p>
                                                <div class="message-feedback mt-2 text-sm"></div>
                                                <button class="btn-secondary px-4 py-2 rounded-lg text-lg mark-read" data-message-id="<?php echo $row['id']; ?>">Mark as Read</button>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12 mt-auto">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-2xl font-semibold mb-4">Healthcare Portal</h3>
                    <p class="text-gray-300 text-lg leading-relaxed">Providing quality healthcare services with compassion and expertise.</p>
                </div>
                <div>
                    <h3 class="text-2xl font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-3">
                        <li><a class="hover:text-green-300 transition text-lg" href="services.php">Services</a></li>
                        <li><a class="hover:text-green-300 transition text-lg" href="doctors.php">Doctors</a></li>
                        <li><a class="hover:text-green-300 transition text-lg" href="appointment.php">Appointments</a></li>
                        <li><a class="hover:text-green-300 transition text-lg" href="contact.php">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-2xl font-semibold mb-4">Stay Connected</h3>
                    <div class="flex space-x-4 mb-4">
                        <a href="#" class="hover:text-green-300 transition text-xl"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="hover:text-green-300 transition text-xl"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="hover:text-green-300 transition text-xl"><i class="fab fa-instagram"></i></a>
                    </div>
                    <div>
                        <p class="mb-2 text-lg">Subscribe to our newsletter</p>
                        <div class="flex">
                            <input type="email" placeholder="Enter your email" class="px-4 py-3 rounded-l-lg text-gray-800 focus:outline-none w-full text-lg" aria-label="Newsletter email input"/>
                            <button class="bg-green-600 px-4 py-3 rounded-r-lg hover:bg-green-700 text-lg">Subscribe</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-8 pt-8 border-t border-gray-700 text-center">
                <p class="text-lg">© <?php echo date("Y"); ?> Healthcare Portal. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Preloader
        window.addEventListener('load', () => {
            document.getElementById('preloader').style.display = 'none';
        });

        // Sidebar Toggle
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('sidebar');
        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('sidebar-hidden');
                sidebar.classList.toggle('sidebar-visible');
            });
        }

        // Accordion Toggle
        document.querySelectorAll('.accordion-toggle').forEach(toggle => {
            toggle.addEventListener('change', () => {
                const icon = toggle.nextElementSibling.querySelector('.accordion-toggle-icon');
                icon.classList.toggle('rotate-180');
            });
        });

        // Appointment Search
        const searchInput = document.getElementById('appointment-search');
        searchInput.addEventListener('input', () => {
            const query = searchInput.value.toLowerCase();
            document.querySelectorAll('#appointments .accordion').forEach(accordion => {
                const date = accordion.querySelector('label').textContent.toLowerCase();
                const rows = accordion.querySelectorAll('tbody tr');
                let hasMatch = date.includes(query);
                rows.forEach(row => {
                    const name = row.querySelector('td').textContent.toLowerCase();
                    if (name.includes(query)) hasMatch = true;
                });
                accordion.style.display = hasMatch ? 'block' : 'none';
            });
        });

        // Mark as Read (UI-only)
        document.querySelectorAll('.mark-read').forEach(button => {
            button.addEventListener('click', () => {
                const messageId = button.dataset.messageId;
                const feedback = button.closest('li').querySelector('.message-feedback');
                feedback.style.display = 'none';
                feedback.className = 'message-feedback mt-2 text-sm';

                button.style.display = 'none';
                feedback.textContent = 'Message marked as read successfully.';
                feedback.classList.add('text-green-600');
                feedback.style.display = 'block';
            });
        });
    </script>
</body>
</html>
<?php
// Close the database connection