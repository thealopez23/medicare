<?php
session_start();
include('config.php');

// Set time zone for consistency
date_default_timezone_set('America/Los_Angeles');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get doctor information from URL parameters
$doctor_name = isset($_GET['doctor']) ? urldecode($_GET['doctor']) : '';
$specialty = isset($_GET['specialty']) ? urldecode($_GET['specialty']) : '';

// Static doctor data
$doctors = [
    'Dr. Alexa_Cardiologist' => [
        'name' => 'Dr. Alexa',
        'full_name' => 'Dr. Alexa - Cardiologist',
        'specialty' => 'Cardiologist',
        'email' => 'alexa@healthcareportal.com',
        'phone' => '(555) 123-4567',
        'bio' => 'Dr. Alexa is passionate about providing excellent healthcare. Known for a caring attitude and patient-centered approach, she focuses on ensuring every patient is heard and receives the best cardiovascular treatment possible.',
        'education' => ['Doctor of Medicine, Harvard University', 'Board Certified in Cardiology'],
        'experience' => ['15+ years in the medical field', 'Worked at HeartCare Hospital and Cardio Clinic'],
        'history' => [
            'Performed over 500 successful cardiac surgeries.',
            'Led a research team on heart disease prevention in 2018.',
            'Published 10+ papers on cardiovascular health.'
        ],
        'clinic_location' => 'Main Clinic: 123 Wellness Street, MedCity',
        'languages' => ['English', 'Spanish'],
        'availability' => ['Mon, Wed, Fri – 9:00 AM to 11:00 AM', 'Tue, Thu – 2:00 PM to 4:00 PM'],
        'rating' => 4.8,
        'total_reviews' => 120,
        'image' => 'alexa.png',
        'profile_color' => 'bg-blue-600'
    ],
    'Dr. Thea_Pediatrician' => [
        'name' => 'Dr. Thea',
        'full_name' => 'Dr. Thea - Pediatrician',
        'specialty' => 'Pediatrician',
        'email' => 'thea@healthcareportal.com',
        'phone' => '(555) 234-5678',
        'bio' => 'Dr. Thea is dedicated to children’s health, specializing in developmental care and pediatric wellness with a compassionate approach.',
        'education' => ['Doctor of Medicine, Stanford University', 'Board Certified in Pediatrics'],
        'experience' => ['10+ years in the medical field', 'Worked at Children’s Hospital and Family Clinic'],
        'history' => [
            'Treated over 2,000 pediatric patients with high satisfaction rates.',
            'Developed a community outreach program for child vaccinations.',
            'Awarded Pediatrician of the Year in 2020.'
        ],
        'clinic_location' => 'Main Clinic: 123 Wellness Street, MedCity',
        'languages' => ['English', 'Filipino'],
        'availability' => ['Mon – Fri – 10:00 AM to 12:00 PM', 'Sat – 9:00 AM to 11:00 AM'],
        'rating' => 4.9,
        'total_reviews' => 95,
        'image' => 'Thea.png',
        'profile_color' => 'bg-green-600'
    ],
    'Dr. Renelyn_Dermatologist' => [
        'name' => 'Dr. Renelyn',
        'full_name' => 'Dr. Renelyn - Dermatologist',
        'specialty' => 'Dermatologist',
        'email' => 'renelyn@healthcareportal.com',
        'phone' => '(555) 345-6789',
        'bio' => 'Dr. Renelyn specializes in skin care, offering treatments for acne, eczema, and cosmetic dermatology with a patient-centered approach.',
        'education' => ['Doctor of Medicine, UCLA Medical School', 'Board Certified in Dermatology'],
        'experience' => ['12+ years in the medical field', 'Worked at SkinCare Clinic and Wellness Center'],
        'history' => [
            'Treated over 1,500 patients with skin conditions.',
            'Pioneered a new laser treatment for acne scars in 2021.',
            'Regular speaker at dermatology conferences.'
        ],
        'clinic_location' => 'Main Clinic: 123 Wellness Street, MedCity',
        'languages' => ['English', 'Spanish'],
        'availability' => ['Tue, Thu – 11:00 AM to 1:00 PM', 'Sat – 1:00 PM to 3:00 PM'],
        'rating' => 4.7,
        'total_reviews' => 80,
        'image' => 'Renelyn.png',
        'profile_color' => 'bg-purple-600'
    ]
];

// Create a key to find the doctor in the array
$doctor_key = $doctor_name . '_' . $specialty;
$doctor = isset($doctors[$doctor_key]) ? $doctors[$doctor_key] : null;

if (!$doctor) {
    echo "<p>Doctor not found.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Doctor Profile - <?php echo htmlspecialchars($doctor['name']); ?> - Healthcare Portal</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #f0f4f8 0%, #e0e7ef 100%);
    }
    .profile-card {
      background: white;
      border-radius: 1rem;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    }
    .fade-in {
      animation: fadeIn 0.5s ease-in;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .btn-back {
      background: linear-gradient(45deg, #10B981, #059669);
      transition: all 0.3s ease;
    }
    .btn-back:hover {
      background: linear-gradient(45deg, #059669, #10B981);
      transform: scale(1.05);
      box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
    }
    .rating-stars {
      color: #FFD700;
    }
  </style>
</head>
<body class="min-h-screen">
  <!-- Navbar -->
  <header class="bg-white shadow-lg sticky top-0 z-50 backdrop-blur-sm">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
      <div class="flex items-center space-x-3">
        <img src="Images/Logo.png" alt="Healthcare Portal Logo" class="h-10 w-10">
        <h1 class="text-2xl font-bold text-teal-600">Healthcare Portal</h1>
      </div>
      <nav class="space-x-6">
        <a href="doctors.php" class="nav-link text-gray-700 hover:text-teal-600 text-lg font-medium transition">
          <i class="fas fa-arrow-left mr-2"></i>Back to Doctors
        </a>
      </nav>
    </div>
  </header>

  <!-- Doctor Profile Section -->
  <main class="container mx-auto px-4 py-12">
  <div class="profile-card p-10 max-w-5xl mx-auto fade-in">
    <h1 class="text-4xl font-extrabold text-center text-teal-700 mb-10">Meet <?php echo htmlspecialchars($doctor['name']); ?></h1>
    
    <!-- Top Profile -->
    <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
      <div class="relative w-36 h-36 rounded-full overflow-hidden border-4 <?php echo $doctor['profile_color']; ?> shadow-lg">
        <img src="Images/<?php echo $doctor['image']; ?>" alt="<?php echo htmlspecialchars($doctor['name']); ?>" class="w-full h-full object-cover">
      </div>

      <div class="flex-1">
        <h2 class="text-3xl font-semibold text-gray-800"><?php echo htmlspecialchars($doctor['full_name']); ?></h2>
        <p class="text-lg text-teal-600 mb-3 font-medium"><?php echo htmlspecialchars($doctor['specialty']); ?></p>

        <!-- Rating -->
        <div class="flex items-center text-yellow-400 text-xl mb-2">
          <?php
            $fullStars = floor($doctor['rating']);
            $halfStar = ($doctor['rating'] - $fullStars) >= 0.5;
            for ($i = 0; $i < $fullStars; $i++) echo '<i class="fas fa-star"></i>';
            if ($halfStar) echo '<i class="fas fa-star-half-alt"></i>';
            for ($i = $fullStars + $halfStar; $i < 5; $i++) echo '<i class="far fa-star"></i>';
          ?>
          <span class="text-gray-600 text-base ml-2">(<?php echo $doctor['total_reviews']; ?> reviews)</span>
        </div>

        <!-- Contact -->
        <div class="mt-2 space-y-1 text-sm text-gray-700">
          <p><i class="fas fa-envelope mr-2 text-teal-500"></i><?php echo htmlspecialchars($doctor['email']); ?></p>
          <p><i class="fas fa-phone mr-2 text-teal-500"></i><?php echo htmlspecialchars($doctor['phone']); ?></p>
        </div>
      </div>
    </div>

    <!-- Bio -->
    <div class="mt-10 p-6 rounded-lg bg-white border-l-4 border-teal-500 shadow">
      <h3 class="text-xl font-bold text-teal-700 mb-2">About <?php echo htmlspecialchars($doctor['name']); ?></h3>
      <p class="text-gray-700 leading-relaxed"><?php echo htmlspecialchars($doctor['bio']); ?></p>
    </div>

    <!-- Grid Info -->
    <div class="grid md:grid-cols-2 gap-6 mt-8">
      <!-- Availability -->
      <div class="bg-green-50 p-5 rounded-lg shadow">
        <h4 class="text-lg font-semibold text-green-700 mb-3"><i class="fas fa-calendar-alt mr-2"></i>Available Times</h4>
        <ul class="list-disc list-inside text-gray-700">
          <?php foreach ($doctor['availability'] as $slot): ?>
            <li><?php echo htmlspecialchars($slot); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <!-- Clinic Location -->
      <div class="bg-red-50 p-5 rounded-lg shadow">
        <h4 class="text-lg font-semibold text-red-700 mb-3"><i class="fas fa-map-marker-alt mr-2"></i>Clinic Location</h4>
        <p class="text-gray-700"><?php echo htmlspecialchars($doctor['clinic_location']); ?></p>
      </div>

      <!-- Education -->
      <div class="bg-yellow-50 p-5 rounded-lg shadow">
        <h4 class="text-lg font-semibold text-yellow-700 mb-3"><i class="fas fa-user-graduate mr-2"></i>Education</h4>
        <ul class="list-disc list-inside text-gray-700">
          <?php foreach ($doctor['education'] as $edu): ?>
            <li><?php echo htmlspecialchars($edu); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <!-- Experience -->
      <div class="bg-purple-50 p-5 rounded-lg shadow">
        <h4 class="text-lg font-semibold text-purple-700 mb-3"><i class="fas fa-briefcase-medical mr-2"></i>Experience</h4>
        <ul class="list-disc list-inside text-gray-700">
          <?php foreach ($doctor['experience'] as $exp): ?>
            <li><?php echo htmlspecialchars($exp); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <!-- Languages -->
      <div class="bg-indigo-50 p-5 rounded-lg shadow">
        <h4 class="text-lg font-semibold text-indigo-700 mb-3"><i class="fas fa-language mr-2"></i>Languages Spoken</h4>
        <p class="text-gray-700"><?php echo htmlspecialchars(implode(', ', $doctor['languages'])); ?></p>
      </div>

      <!-- Medical History -->
      <div class="bg-blue-50 p-5 rounded-lg shadow">
        <h4 class="text-lg font-semibold text-blue-700 mb-3"><i class="fas fa-notes-medical mr-2"></i>Medical Highlights</h4>
        <ul class="list-disc list-inside text-gray-700">
          <?php foreach ($doctor['history'] as $item): ?>
            <li><?php echo htmlspecialchars($item); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>

    <!-- CTA Buttons -->
    <div class="mt-10 text-center space-x-4">
      <a href="appointment.php?doctor=<?php echo urlencode($doctor['full_name']); ?>" class="btn-back px-6 py-2 text-white font-semibold rounded-lg">Book Appointment</a>
      <a href="doctors.php" class="btn-back px-6 py-2 text-white font-semibold rounded-lg">Back to Doctors</a>
    </div>
  </div>
</main>

  <!-- Footer -->
  <footer class="bg-teal-800 text-white py-8">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div>
          <h3 class="text-xl font-semibold mb-4">Healthcare Portal</h3>
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
        <p class="text-teal-100 text-base">© <?php echo date("Y"); ?> Healthcare Portal. All rights reserved.</p>
      </div>
    </div>
  </footer>
</body>
</html>