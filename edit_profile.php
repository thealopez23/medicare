<?php
session_start();
include('config.php');

// Redirect if not logged in
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

// Handle form submission
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $birthday = trim($_POST['birthday']);

    if (empty($full_name)) $errors[] = "Full name is required.";
    if (empty($email)) $errors[] = "Email is required.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";
    if (empty($phone)) $errors[] = "Phone number is required.";
    if (empty($address)) $errors[] = "Address is required.";
    if (empty($birthday)) $errors[] = "Birthday is required.";

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
            $success = true;
            $user = array_merge($user, $_POST);
        } catch (PDOException $e) {
            $errors[] = "Error updating profile: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-blue-50 min-h-screen flex flex-col">
    <header class="bg-white shadow p-4 sticky top-0 z-50">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-blue-700 flex items-center gap-2">
                <i class="fas fa-user-edit text-blue-600 text-3xl"></i>
                Edit Your Profile
            </h1>
            <a href="profile.php" class="text-blue-600 hover:underline flex items-center gap-1">
                <i class="fas fa-arrow-left"></i> Back to Profile
            </a>
        </div>
    </header>

    <main class="container mx-auto flex-grow px-4 py-10">
        <div class="bg-white p-8 rounded-xl shadow-lg max-w-3xl mx-auto border border-blue-100">
            <?php if ($success): ?>
                <div class="bg-green-100 text-green-800 border border-green-300 p-4 rounded mb-6">
                    <i class="fas fa-check-circle mr-2"></i> Profile updated successfully!
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="bg-red-100 text-red-800 border border-red-300 p-4 rounded mb-6">
                    <ul class="list-disc list-inside">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="col-span-2">
                        <label for="full_name" class="block font-medium text-gray-700">Full Name</label>
                        <input type="text" name="full_name" id="full_name"
                               value="<?php echo htmlspecialchars($user['full_name']); ?>"
                               class="w-full mt-1 border border-gray-300 rounded-md px-4 py-2 focus:ring-blue-600 focus:outline-none" required>
                    </div>

                    <div>
                        <label for="email" class="block font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email"
                               value="<?php echo htmlspecialchars($user['email']); ?>"
                               class="w-full mt-1 border border-gray-300 rounded-md px-4 py-2 focus:ring-blue-600 focus:outline-none" required>
                    </div>

                    <div>
                        <label for="phone" class="block font-medium text-gray-700">Phone</label>
                        <input type="tel" name="phone" id="phone"
                               value="<?php echo htmlspecialchars($user['phone']); ?>"
                               class="w-full mt-1 border border-gray-300 rounded-md px-4 py-2 focus:ring-blue-600 focus:outline-none" required>
                    </div>

                    <div class="col-span-2">
                        <label for="address" class="block font-medium text-gray-700">Address</label>
                        <textarea name="address" id="address" rows="3"
                                  class="w-full mt-1 border border-gray-300 rounded-md px-4 py-2 focus:ring-blue-600 focus:outline-none" required><?php echo htmlspecialchars($user['address']); ?></textarea>
                    </div>

                    <div>
                        <label for="birthday" class="block font-medium text-gray-700">Birthday</label>
                        <input type="date" name="birthday" id="birthday"
                               value="<?php echo htmlspecialchars($user['birthday']); ?>"
                               class="w-full mt-1 border border-gray-300 rounded-md px-4 py-2 focus:ring-blue-600 focus:outline-none" required>
                    </div>
                </div>

                <div class="flex justify-end gap-4 pt-6">
                    <a href="profile.php"
                       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md font-semibold flex items-center gap-2">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-semibold flex items-center gap-2">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </main>

    <footer class="bg-white border-t mt-auto py-6 text-center text-sm text-gray-500">
        &copy; <?php echo date('Y'); ?> Your Company. All rights reserved.
    </footer>
</body>
</html>
