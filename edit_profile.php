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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $birthday = trim($_POST['birthday']);

    // Basic validation
    $errors = [];
    if (empty($full_name)) $errors[] = "Full name is required";
    if (empty($email)) $errors[] = "Email is required";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
    if (empty($phone)) $errors[] = "Phone number is required";
    if (empty($address)) $errors[] = "Address is required";
    if (empty($birthday)) $errors[] = "Birthday is required";

    if (empty($errors)) {
        try {
            $sql = "UPDATE users SET 
                    full_name = :full_name,
                    email = :email,
                    phone = :phone,
                    address = :address,
                    birthday = :birthday
                    WHERE id = :user_id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':full_name' => $full_name,
                ':email' => $email,
                ':phone' => $phone,
                ':address' => $address,
                ':birthday' => $birthday,
                ':user_id' => $user_id
            ]);

            // Redirect to profile page with success message
            header('Location: profile.php?success=1');
            exit();
        } catch (PDOException $e) {
            $errors[] = "Error updating profile: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html class="scroll-smooth" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <title>Edit Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-b from-blue-50 to-white min-h-screen flex flex-col">
    <header class="bg-white shadow-md p-4 sticky top-0 z-30">
        <div class="container mx-auto flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-blue-700 flex items-center gap-2">
                <i class="fas fa-user-edit text-blue-600 text-3xl"></i>
                Edit Profile
            </h1>
            <a class="text-blue-600 hover:text-blue-800 font-medium transition flex items-center gap-1" href="profile.php">
                <i class="fas fa-arrow-left"></i>
                Back to Profile
            </a>
        </div>
    </header>

    <main class="container mx-auto px-4 py-12 flex-grow">
        <div class="bg-white shadow-lg rounded-xl p-8 max-w-3xl mx-auto border border-blue-100">
            <?php if (!empty($errors)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <ul class="list-disc list-inside">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Full Name -->
                    <div class="col-span-2">
                        <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" id="full_name" name="full_name" 
                               value="<?php echo htmlspecialchars($user['full_name']); ?>"
                               class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-600"/>
                    </div>

                    <!-- Email -->
                    <div class="col-span-2 sm:col-span-1">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" name="email" 
                               value="<?php echo htmlspecialchars($user['email']); ?>"
                               class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-600"/>
                    </div>

                    <!-- Phone -->
                    <div class="col-span-2 sm:col-span-1">
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="tel" id="phone" name="phone" 
                               value="<?php echo htmlspecialchars($user['phone']); ?>"
                               class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-600"/>
                    </div>

                    <!-- Address -->
                    <div class="col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <textarea id="address" name="address" rows="3"
                                  class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-600"><?php echo htmlspecialchars($user['address']); ?></textarea>
                    </div>

                    <!-- Birthday -->
                    <div class="col-span-2 sm:col-span-1">
                        <label for="birthday" class="block text-sm font-medium text-gray-700 mb-1">Birthday</label>
                        <input type="date" id="birthday" name="birthday" 
                               value="<?php echo htmlspecialchars($user['birthday']); ?>"
                               class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-600"/>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-end gap-4 mt-8">
                    <a href="profile.php" 
                       class="bg-gray-500 text-white px-8 py-3 rounded-md font-semibold hover:bg-gray-600 transition flex items-center justify-center gap-2">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 text-white px-8 py-3 rounded-md font-semibold hover:bg-blue-700 transition flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </main>

    <footer class="bg-white border-t border-blue-100 py-6 mt-auto">
        <div class="container mx-auto text-center text-gray-500 text-sm select-none">
            © <?php echo date('Y'); ?> Your Company. All rights reserved.
        </div>
    </footer>
</body>
</html>