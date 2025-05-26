<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id AND role = 2");
    $stmt->execute([':user_id' => $user_id]);
    $doctor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$doctor) {
        header('Location: index.php');
        exit();
    }
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage();
    exit();
}

$doctor_name = $doctor['full_name'];
$availability = [];
$profile_color = 'bg-gray-500';

if ($doctor_name === "Dr. Alexa - Cardiologist") {
    $availability = ['Mon, Wed, Fri – 9:00 AM to 11:00 AM', 'Tue, Thu – 2:00 PM to 4:00 PM'];
    $profile_color = 'bg-blue-600';
} elseif ($doctor_name === "Dr. Thea - Pediatrician") {
    $availability = ['Mon – Fri – 10:00 AM to 12:00 PM', 'Sat – 9:00 AM to 11:00 AM'];
    $profile_color = 'bg-green-600';
} elseif ($doctor_name === "Dr. Renelyn - Dermatologist") {
    $availability = ['Tue, Thu – 11:00 AM to 1:00 PM', 'Sat – 1:00 PM to 3:00 PM'];
    $profile_color = 'bg-purple-600';
} else {
    $availability = ['No availability set'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Profile - Healthcare Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gradient-to-tr from-blue-50 to-purple-100 min-h-screen">
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <img src="Images/Logo.png" alt="Logo" class="h-10 w-10">
                    <span class="text-2xl font-bold text-blue-800">Healthcare Portal</span>
                </div>
                <div class="flex items-center space-x-6">
                    <a href="doctor_dashboard.php" class="text-blue-700 hover:text-blue-900 font-medium transition"><i class="fas fa-tachometer-alt mr-1"></i>Dashboard</a>
                    <a href="logout.php" class="text-red-600 hover:text-red-800 font-medium transition"><i class="fas fa-sign-out-alt mr-1"></i>Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-12">
        <div class="bg-white shadow-xl rounded-2xl p-10 max-w-4xl mx-auto fade-in">
            <h1 class="text-4xl font-bold text-center text-gray-800 mb-10">Doctor Profile</h1>
            <div class="text-center">
                <div class="w-32 h-32 rounded-full <?php echo $profile_color; ?> mx-auto flex items-center justify-center text-white text-5xl mb-4 shadow-lg">
                    <i class="fas fa-user-md"></i>
                </div>
                <h2 class="text-2xl font-semibold text-gray-900"><?php echo htmlspecialchars($doctor['full_name']); ?></h2>
                <p class="text-indigo-600 text-sm font-medium mb-4"><?php echo htmlspecialchars(explode(' - ', $doctor['full_name'])[1]); ?></p>
            </div>

            <div class="grid md:grid-cols-2 gap-6 mt-8">
                <div class="bg-blue-50 rounded-lg p-5">
                    <h3 class="text-lg font-semibold text-blue-700 mb-2">Contact Info</h3>
                    <p><i class="fas fa-envelope mr-2"></i><?php echo htmlspecialchars($doctor['email']); ?></p>
                    <p><i class="fas fa-phone mr-2"></i><?php echo htmlspecialchars($doctor['phone']); ?></p>
                </div>

                <div class="bg-green-50 rounded-lg p-5">
                    <h3 class="text-lg font-semibold text-green-700 mb-2">Available Times</h3>
                    <ul class="list-disc list-inside">
                        <?php foreach ($availability as $slot): ?>
                            <li><?php echo htmlspecialchars($slot); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="bg-yellow-50 rounded-lg p-5">
                    <h3 class="text-lg font-semibold text-yellow-700 mb-2">Education</h3>
                    <p>Doctor of Medicine, XYZ University</p>
                    <p>Board Certified in Specialty</p>
                </div>

                <div class="bg-purple-50 rounded-lg p-5">
                    <h3 class="text-lg font-semibold text-purple-700 mb-2">Experience</h3>
                    <p>10+ years in the medical field</p>
                    <p>Worked at ABC Hospital and DEF Clinic</p>
                </div>

                <div class="bg-red-50 rounded-lg p-5">
                    <h3 class="text-lg font-semibold text-red-700 mb-2">Clinic Location</h3>
                    <p>Main Clinic: 123 Wellness Street, MedCity</p>
                </div>

                <div class="bg-indigo-50 rounded-lg p-5">
                    <h3 class="text-lg font-semibold text-indigo-700 mb-2">Languages</h3>
                    <p>English, Filipino, Spanish</p>
                </div>
            </div>

            <div class="mt-10 p-6 bg-white rounded-xl shadow-inner">
                <h3 class="text-xl font-bold text-gray-700 mb-2">About</h3>
                <p class="text-gray-600">Dr. <?php echo htmlspecialchars(explode(' - ', $doctor['full_name'])[0]); ?> is passionate about providing excellent healthcare. Known for a caring attitude and patient-centered approach, they focus on making sure every patient is heard and receives the best treatment possible.</p>
            </div>

            <div class="mt-8 text-center">
                <a href="doctor_dashboard.php" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-full transition duration-300 shadow-lg">Back to Dashboard</a>
            </div>
        </div>
    </main>

    <footer class="bg-white border-t mt-12 py-6">
        <div class="text-center text-gray-500 text-sm">
            © <?php echo date('Y'); ?> Healthcare Portal. All rights reserved.
        </div>
    </footer>
</body>
</html>
