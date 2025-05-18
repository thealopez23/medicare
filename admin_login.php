<?php
session_start();
$conn = new mysqli("localhost", "root", "", "healthcare_portal");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and trim user input to avoid issues with spaces and special characters
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Prepare the query to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the query returned a result
    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();

        // Check if the password matches the hashed password in the database
        if (password_verify($password, $admin['password'])) {
            // Store the admin ID in session and redirect to the dashboard
            $_SESSION['admin'] = $admin['id'];
            header("Location: admin_dashboard.php");
            exit();
        } else {
            // Invalid password
            $error = "Invalid credentials.";
        }
    } else {
        // No admin found with that username
        $error = "Invalid credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Login</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
  <form method="POST" class="bg-white p-8 rounded shadow-md w-full max-w-sm">
    <h2 class="text-2xl font-bold mb-6 text-center text-blue-700">Admin Login</h2>

    <?php if (isset($error)): ?>
      <div class="mb-4 text-red-600 text-sm"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="mb-4">
      <label for="username" class="block text-gray-700 mb-1">Username</label>
      <input id="username" name="username" type="text" required
             class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300">
    </div>

    <div class="mb-6">
      <label for="password" class="block text-gray-700 mb-1">Password</label>
      <input id="password" name="password" type="password" required
             class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300">
    </div>

    <button type="submit"
            class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition duration-200">
      Login
    </button>
  </form>
</body>
</html>
