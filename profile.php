<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - Medicare</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f0f9ff, #e0f2f1);
        }
        .primary-color {
            color: #16A34A;
        }
        .btn-primary {
            background-color: #16A34A;
            color: #fff;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #15803D;
            transform: scale(1.05);
        }
        .btn-secondary {
            background-color: #DC2626;
            color: white;
            transition: all 0.3s ease;
        }
        .btn-secondary:hover {
            background-color: #B91C1C;
            transform: scale(1.05);
        }
        .profile-card {
            background: linear-gradient(to top left, #ffffff, #e0f7fa);
            border-radius: 1.5rem;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
            padding: 2.5rem;
            border: 2px solid #10B981;
        }
        .profile-card img {
            border: 4px solid #10B981;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .section-title {
            color: #047857;
            font-size: 2.25rem;
        }
        .fade-in {
            animation: fadeIn 0.4s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-15px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-gradient-to-r from-green-500 to-emerald-600 shadow-md py-4 px-6 flex items-center justify-between text-white">
            <div class="flex items-center gap-3">
                <img src="Images/Logo.png" alt="Logo" class="w-12 h-12">
                <h1 class="text-2xl font-bold">Medicare</h1>
            </div>
            <a href="patient-dashboard.php" class="hover:underline font-medium flex items-center gap-1"><i class="fas fa-home"></i> Dashboard</a>
        </header>

        <!-- Profile Section -->
        <main class="flex-1 flex items-center justify-center py-12 px-4">
            <div class="max-w-4xl w-full profile-card fade-in">
                <div class="text-center">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['full_name']); ?>&background=10B981&color=fff&size=192&font-size=0.5&bold=true" alt="Avatar" class="w-40 h-40 rounded-full mx-auto mb-4">
                    <h2 class="text-3xl font-bold text-gray-800"><?php echo htmlspecialchars($user['full_name']); ?></h2>
                    <p class="text-gray-600">Manage your personal information and update your details anytime.</p>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="bg-green-100 text-green-800 text-center p-4 mt-6 rounded-lg">Profile updated successfully!</div>
                <?php endif; ?>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-10">
                    <div>
                        <p class="text-gray-700 font-medium">Email:</p>
                        <p class="text-gray-800"><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>
                    <div>
                        <p class="text-gray-700 font-medium">Phone:</p>
                        <p class="text-gray-800"><?php echo htmlspecialchars($user['phone'] ?? 'Not provided'); ?></p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-gray-700 font-medium">Address:</p>
                        <p class="text-gray-800"><?php echo htmlspecialchars($user['address'] ?? 'Not provided'); ?></p>
                    </div>
                    <div>
                        <p class="text-gray-700 font-medium">Birthday:</p>
                        <p class="text-gray-800"><?php echo !empty($user['birthday']) ? htmlspecialchars(date('F j, Y', strtotime($user['birthday']))) : 'Not provided'; ?></p>
                    </div>
                    <div>
                        <p class="text-gray-700 font-medium">Age:</p>
                        <p class="text-gray-800">
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

                <div class="flex flex-col sm:flex-row gap-4 mt-10 justify-center">
                    <a href="edit_profile.php" class="btn-primary px-6 py-3 rounded-lg text-lg font-semibold text-center"><i class="fas fa-edit mr-2"></i>Edit Profile</a>
                    <form action="logout.php" method="POST">
                        <button type="submit" class="btn-secondary px-6 py-3 rounded-lg text-lg font-semibold w-full sm:w-auto"><i class="fas fa-sign-out-alt mr-2"></i>Log Out</button>
                    </form>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-green-800 text-white text-center py-6 mt-auto">
            <p class="text-lg">© <?php echo date("Y"); ?> Medicare. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>
