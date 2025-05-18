<?php
session_start();
include('config.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch the user details from the database
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if user exists
if (!$user) {
    echo "User not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>User Profile - Healthcare Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f0f0;
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
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }
        .btn-primary {
            background-color: #10B981;
            color: white;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.5);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.7);
            transform: scale(1.05);
        }
        .btn-secondary {
            background-color: #EF4444;
            color: white;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.5);
            transition: all 0.3s ease;
        }
        .btn-secondary:hover {
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.7);
            transform: scale(1.05);
        }
        .sidebar {
            transition: transform 0.3s ease;
        }
        .sidebar-hidden {
            transform: translateX(-100%);
        }
        a:focus, button:focus, input:focus {
            outline: 3px solid #10B981;
            outline-offset: 2px;
        }
        .success-alert {
            animation: fadeIn 0.3s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
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

    <!-- Header -->
    <header class="bg-white/90 shadow-lg sticky top-0 z-50 backdrop-blur-sm">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <img alt="Healthcare Portal Logo" class="w-14 h-14" src="Images/Logo.png"/>
                <h1 class="text-3xl font-bold text-green-600">User Profile</h1>
            </div>
            <a class="text-blue-600 hover:text-blue-800 font-medium transition flex items-center gap-1" href="index.php">
                    <i class="fas fa-home"></i>
                Back to Home
                </a>
            <button aria-label="Toggle sidebar" class="md:hidden text-gray-800 focus:outline-none focus:ring-2 focus:ring-green-600" id="sidebar-toggle">
                <i class="fas fa-bars fa-lg"></i>
            </button>
        </div>
    </header>

    <!-- Main Content -->
    <div class="flex flex-1 container mx-auto px-6 py-8 max-w-full">
        <!-- Sidebar -->
        <!-- <aside class="sidebar w-64 bg-white shadow-lg rounded-lg p-6 mr-6 hidden md:block sidebar-hidden md:sidebar-visible" id="sidebar">
            <nav class="space-y-4">
                <a href="#profile" class="nav-link block px-4 py-2 text-lg text-gray-800 hover:bg-green-100 hover:text-green-600 rounded">Profile</a>
                <a href="appointment.php" class="nav-link block px-4 py-2 text-lg text-gray-800 hover:bg-green-100 hover:text-green-600 rounded">Appointments</a>
                <a href="index.php" class="nav-link block px-4 py-2 text-lg text-gray-800 hover:bg-green-100 hover:text-green-600 rounded">Home</a>
                <a href="logout.php" class="nav-link block px-4 py-2 text-lg text-red-600 hover:bg-red-100 hover:text-red-800 rounded">Logout</a>
            </nav>
        </aside> -->

        <!-- Content -->
        <main class="flex-1 dashboard-section p-8 md:p-12">
            <section id="profile">
                <h2 class="text-4xl font-semibold text-readable mb-6">Your Profile</h2>
                <?php if (isset($_GET['success'])): ?>
                    <div class="success-alert mb-6 p-4 bg-green-100 text-green-700 rounded-lg text-lg border border-green-400 animate-fadeIn">
                        Profile updated successfully!
                    </div>
                <?php endif; ?>
                <div class="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-8">
                    <div class="flex flex-col items-center">
                        <div class="mb-6">
                            <img alt="User avatar" class="rounded-full border-4 border-green-600 w-48 h-48 object-cover shadow-md" 
                                 src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['full_name']); ?>&background=10B981&color=fff&size=192&font-size=0.5&bold=true" 
                                 loading="lazy"/>
                        </div>
                        <div class="text-center">
                            <h3 class="text-3xl font-semibold text-gray-800 mb-2 break-words">
                                <?php echo htmlspecialchars($user['full_name']); ?>
                            </h3>
                            <p class="text-gray-600 text-lg max-w-md">
                                Manage your personal information and update your details anytime.
                            </p>
                        </div>
                    </div>
                    <div class="mt-10 grid grid-cols-1 sm:grid-cols-2 gap-8 text-gray-700">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-envelope text-green-600 text-xl w-6"></i>
                            <div>
                                <p class="font-semibold text-green-700">Email</p>
                                <p class="break-words"><?php echo htmlspecialchars($user['email']); ?></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="fas fa-phone-alt text-green-600 text-xl w-6"></i>
                            <div>
                                <p class="font-semibold text-green-700">Phone</p>
                                <p class="break-words"><?php echo htmlspecialchars($user['phone'] ?? 'Not provided'); ?></p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 sm:col-span-2">
                            <i class="fas fa-map-marker-alt text-green-600 text-xl w-6 mt-1"></i>
                            <div>
                                <p class="font-semibold text-green-700">Address</p>
                                <p class="break-words"><?php echo htmlspecialchars($user['address'] ?? 'Not provided'); ?></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="fas fa-birthday-cake text-green-600 text-xl w-6"></i>
                            <div>
                                <p class="font-semibold text-green-700">Birthday</p>
                                <p class="break-words">
                                    <?php echo !empty($user['birthday']) ? htmlspecialchars(date('F j, Y', strtotime($user['birthday']))) : 'Not provided'; ?>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="fas fa-user-clock text-green-600 text-xl w-6"></i>
                            <div>
                                <p class="font-semibold text-green-700">Age</p>
                                <p class="break-words">
                                    <?php
                                    if (!empty($user['birthday'])) {
                                        $birthDate = new DateTime($user['birthday']);
                                        $today = new DateTime('today');
                                        $age = $birthDate->diff($today)->y;
                                        echo $age . " years old";
                                    } else {
                                        echo "Not provided";
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-12 flex flex-col sm:flex-row sm:justify-center gap-4">
                        <a href="edit_profile.php" class="btn-primary px-8 py-3 rounded-lg font-semibold text-lg flex items-center justify-center gap-2">
                            <i class="fas fa-edit"></i> Edit Profile
                        </a>
                        <form action="logout.php" method="POST" class="inline-block w-full sm:w-auto">
                            <button type="submit" class="btn-secondary px-8 py-3 rounded-lg font-semibold text-lg flex items-center justify-center gap-2 w-full">
                                <i class="fas fa-sign-out-alt"></i> Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </section>
        </main>
    </div>

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
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('sidebar-hidden');
            sidebar.classList.toggle('sidebar-visible');
        });
    </script>
</body>
</html>