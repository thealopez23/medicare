<?php
session_start();
include('config.php');

// Set time zone for consistency
date_default_timezone_set('America/Los_Angeles');

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Book an Appointment - Medicare</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #f0f4f8 0%, #e0e7ef 100%);
    }
    .doctor-card {
      background: white;
      border-radius: 1rem;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .doctor-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
    }
    .doctor-img {
      transition: transform 0.3s ease;
    }
    .doctor-card:hover .doctor-img {
      transform: scale(1.03);
    }
    .nav-link {
      transition: color 0.3s ease, transform 0.3s ease;
    }
    .nav-link:hover {
      transform: translateY(-2px);
    }
    .fade-in {
      animation: fadeIn 0.5s ease-in;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .btn-book {
      background: linear-gradient(45deg, #10B981, #059669);
      transition: all 0.3s ease;
    }
    .btn-book:hover {
      background: linear-gradient(45deg, #059669, #10B981);
      transform: scale(1.05);
      box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
    }
  </style>
</head>
<body class="min-h-screen">
  <!-- Navbar -->
  <header class="bg-white shadow-lg sticky top-0 z-50 backdrop-blur-sm">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
      <div class="flex items-center space-x-3">
        <img src="Images/Logo.png" alt="Medicare Logo" class="h-10 w-10">
        <h1 class="text-2xl font-bold text-teal-600">Medicare</h1>
      </div>
      <nav class="space-x-6">
        <a href="index.php" class="nav-link text-gray-700 hover:text-teal-600 text-lg font-medium transition">
          <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
      </nav>
    </div>
  </header>

  <!-- Doctor Section -->
  <!-- Doctor Section -->
<section class="mt-12 mb-16 px-4 sm:px-6 lg:px-8" id="doctors">
  <h3 class="text-3xl font-bold text-teal-600 mb-10 text-center fade-in">Meet Our Doctors</h3>
  
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
    <!-- Doctor Card: Dr. Alexa -->
    <div class="doctor-card p-6 text-center fade-in">
      <img src="Images/alexa.png" alt="Dr. Alexa" class="doctor-img w-full h-64 object-cover rounded-lg mb-4">
      <h4 class="text-xl font-semibold text-gray-800">Dr. Alexa</h4>
      <p class="text-teal-600 font-medium">Cardiologist</p>
      <p class="text-gray-600 mt-2 text-base">Expert in heart health and cardiovascular diseases with 15 years of experience.</p>
      <div class="mt-4 text-sm text-left">
        <p class="font-semibold text-gray-700">Available Times:</p>
        <ul class="list-disc list-inside text-gray-600 space-y-1">
          <li>Mon, Wed, Fri – 9:00 AM to 11:00 AM</li>
          <li>Tue, Thu – 2:00 PM to 4:00 PM</li>
        </ul>
      </div>
      <a href="<?php echo $is_logged_in ? 'view_profile.php?doctor=Dr.+Alexa&specialty=Cardiologist' : 'login.php'; ?>" class="btn-book inline-block mt-4 text-white px-6 py-2 rounded-lg font-medium">View Profile</a>
    </div>

    <!-- Doctor Card: Dr. Thea -->
    <div class="doctor-card p-6 text-center fade-in">
      <img src="Images/Thea.png" alt="Dr. Thea" class="doctor-img w-full h-64 object-cover rounded-lg mb-4">
      <h4 class="text-xl font-semibold text-gray-800">Dr. Thea</h4>
      <p class="text-teal-600 font-medium">Pediatrician</p>
      <p class="text-gray-600 mt-2 text-base">Caring for children’s health and wellness with a gentle approach.</p>
      <div class="mt-4 text-sm text-left">
        <p class="font-semibold text-gray-700">Available Times:</p>
        <ul class="list-disc list-inside text-gray-600 space-y-1">
          <li>Mon – Fri – 10:00 AM to 12:00 PM</li>
          <li>Sat – 9:00 AM to 11:00 AM</li>
        </ul>
      </div>
      <a href="<?php echo $is_logged_in ? 'view_profile.php?doctor=Dr.+Thea&specialty=Pediatrician' : 'login.php'; ?>" class="btn-book inline-block mt-4 text-white px-6 py-2 rounded-lg font-medium">View Profile</a>
    </div>

    <!-- Doctor Card: Dr. Renelyn -->
    <div class="doctor-card p-6 text-center fade-in">
      <img src="Images/Renelyn.png" alt="Dr. Renelyn" class="doctor-img w-full h-64 object-cover rounded-lg mb-4">
      <h4 class="text-xl font-semibold text-gray-800">Dr. Renelyn</h4>
      <p class="text-teal-600 font-medium">Dermatologist</p>
      <p class="text-gray-600 mt-2 text-base">Specialist in skin care and treatment of skin conditions.</p>
      <div class="mt-4 text-sm text-left">
        <p class="font-semibold text-gray-700">Available Times:</p>
        <ul class="list-disc list-inside text-gray-600 space-y-1">
          <li>Tue, Thu – 11:00 AM to 1:00 PM</li>
          <li>Sat – 1:00 PM to 3:00 PM</li>
        </ul>
      </div>
      <a href="<?php echo $is_logged_in ? 'view_profile.php?doctor=Dr.+Renelyn&specialty=Dermatologist' : 'login.php'; ?>" class="btn-book inline-block mt-4 text-white px-6 py-2 rounded-lg font-medium">View Profile</a>
    </div>
  </div>
</section>

  <!-- Footer -->
  <footer class="bg-teal-800 text-white py-8">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div>
          <h3 class="text-xl font-semibold mb-4">Medicare</h3>
          <p class="text-teal-100 text-base">Providing quality healthcare services with compassion and expertise.</p>
        </div>
        <div>
          <h3 class="text-xl font-semibold mb-4">Quick Links</h3>
          <ul class="space-y-3">
            <li><a class="hover:text-teal-300 transition text-base" href="services.php">Services</a></li>
            <li><a class="hover:text-teal-300 transition text-base" href="doctors.php">Doctors</a></li>
            <li><a class="hover:text-teal-300 transition text-base" href="appointment.php">Appointments</a></li>
            <li><a class="hover:text-teal-300 transition text-base" href="contact.php">Contact</a></li>
          </ul>
        </div>
        <div>
          <h3 class="text-xl font-semibold mb-4">Stay Connected</h3>
          <div class="flex space-x-4 mb-4">
            <a href="#" class="hover:text-teal-300 transition text-lg"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="hover:text-teal-300 transition text-lg"><i class="fab fa-twitter"></i></a>
            <a href="#" class="hover:text-teal-300 transition text-lg"><i class="fab fa-instagram"></i></a>
          </div>
        </div>
      </div>
      <div class="mt-8 pt-6 border-t border-teal-700 text-center">
        <p class="text-teal-100 text-base">© <?php echo date("Y"); ?> Medicare. All rights reserved.</p>
      </div>
    </div>
  </footer>
</body>
</html>
<?php
// Close the database connection