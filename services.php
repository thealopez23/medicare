<?php
session_start();
include('config.php');

// Set time zone for Philippines
date_default_timezone_set('Asia/Manila');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Our Services - Medicare</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #f0f4f8 0%, #e0e7ef 100%);
    }
    .service-card {
      background: white;
      border-radius: 1rem;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .service-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
    }
    .service-icon {
      background: linear-gradient(45deg, #10B981, #059669);
      transition: transform 0.3s ease;
    }
    .service-card:hover .service-icon {
      transform: scale(1.1);
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
    .hero-section {
      background: linear-gradient(rgba(16, 185, 129, 0.8), rgba(5, 150, 105, 0.8)), url('Images/hero-bg.jpg');
      background-size: cover;
      background-position: center;
    }
    .testimonial-card {
      background: white;
      border-radius: 1rem;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    }
    .carousel-container {
      position: relative;
      overflow: hidden;
    }
    .carousel {
      display: flex;
      transition: transform 0.5s ease-in-out;
    }
    .carousel-item {
      min-width: 100%;
    }
    .stat-number {
      font-size: 2.5rem;
      font-weight: 700;
      color: #10B981;
    }
  </style>
</head>
<body class="min-h-screen">
  <!-- Hero Section -->
  <section class="hero-section text-white py-20 text-center">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
      <h1 class="text-4xl md:text-5xl font-bold mb-4 fade-in">Discover Our Healthcare Services</h1>
      <p class="text-lg md:text-xl text-teal-100 mb-8 fade-in">Experience compassionate care with our expert medical services tailored to your needs.</p>
      <a href="#services" class="inline-block bg-white text-teal-600 px-8 py-3 rounded-lg font-medium hover:bg-teal-50 transition fade-in">Explore Services</a>
    </div>
  </section>

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

  <!-- Services Section -->
  <section class="mt-12 mb-16 px-4 sm:px-6 lg:px-8" id="services">
    <h3 class="text-3xl font-bold text-teal-600 mb-12 text-center fade-in">Our Services</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-8 max-w-5xl mx-auto">
      <!-- General Checkup -->
      <div class="service-card p-6 text-center fade-in">
        <div class="service-icon w-20 h-20 mx-auto mb-4 rounded-full flex items-center justify-center text-white text-3xl">
          <i class="fas fa-stethoscope"></i>
        </div>
        <h4 class="text-xl font-semibold text-gray-800 mb-2">General Checkup</h4>
        <p class="text-gray-600 text-base">Routine physical exams and consultations to monitor your overall health and detect early signs of illness.</p>
      </div>

      <!-- Radiology -->
      <div class="service-card p-6 text-center fade-in">
        <div class="service-icon w-20 h-20 mx-auto mb-4 rounded-full flex items-center justify-center text-white text-3xl">
          <i class="fas fa-x-ray"></i>
        </div>
        <h4 class="text-xl font-semibold text-gray-800 mb-2">Radiology</h4>
        <p class="text-gray-600 text-base">High-quality diagnostic imaging services including X-rays, ultrasounds, MRIs, and CT scans.</p>
      </div>

      <!-- Pharmacy -->
      <div class="service-card p-6 text-center fade-in">
        <div class="service-icon w-20 h-20 mx-auto mb-4 rounded-full flex items-center justify-center text-white text-3xl">
          <i class="fas fa-prescription-bottle-alt"></i>
        </div>
        <h4 class="text-xl font-semibold text-gray-800 mb-2">Pharmacy</h4>
        <p class="text-gray-600 text-base">Convenient access to prescription medications, over-the-counter drugs, and expert pharmacist advice.</p>
      </div>

      <!-- Emergency Care -->
      <div class="service-card p-6 text-center fade-in">
        <div class="service-icon w-20 h-20 mx-auto mb-4 rounded-full flex items-center justify-center text-white text-3xl">
          <i class="fas fa-ambulance"></i>
        </div>
        <h4 class="text-xl font-semibold text-gray-800 mb-2">Emergency Care</h4>
        <p class="text-gray-600 text-base">24/7 emergency response team equipped for urgent medical care, trauma, and life-threatening situations.</p>
      </div>
    </div>
  </section>

  <!-- Stats Section -->
  <section class="bg-white py-16">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
      <h3 class="text-3xl font-bold text-teal-600 mb-12 fade-in">Why Choose Us</h3>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
        <div class="fade-in">
          <p class="stat-number">10+</p>
          <p class="text-lg font-semibold text-gray-700">Years of Service</p>
        </div>
        <div class="fade-in">
          <p class="stat-number">50,000+</p>
          <p class="text-lg font-semibold text-gray-700">Patients Served</p>
        </div>
        <div class="fade-in">
          <p class="stat-number">100+</p>
          <p class="text-lg font-semibold text-gray-700">Expert Staff</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Testimonial Carousel -->
  <section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
      <h3 class="text-3xl font-bold text-teal-600 mb-12 text-center fade-in">What Our Patients Say</h3>
      <div class="carousel-container max-w-3xl mx-auto">
        <div class="carousel">
          <div class="carousel-item testimonial-card p-6 text-center">
            <p class="text-gray-600 italic mb-4">"The care I received was exceptional. The staff were professional and compassionate."</p>
            <p class="text-teal-600 font-semibold">Maria S.</p>
          </div>
          <div class="carousel-item testimonial-card p-6 text-center">
            <p class="text-gray-600 italic mb-4">"The radiology department was quick and efficient. Highly recommend!"</p>
            <p class="text-teal-600 font-semibold">John P.</p>
          </div>
          <div class="carousel-item testimonial-card p-6 text-center">
            <p class="text-gray-600 italic mb-4">"Emergency care saved my life. Thank you to the amazing team!"</p>
            <p class="text-teal-600 font-semibold">Anna L.</p>
          </div>
        </div>
        <div class="flex justify-center space-x-4 mt-6">
          <button class="carousel-prev bg-teal-600 text-white w-10 h-10 rounded-full flex items-center justify-center hover:bg-teal-700">
            <i class="fas fa-chevron-left"></i>
          </button>
          <button class="carousel-next bg-teal-600 text-white w-10 h-10 rounded-full flex items-center justify-center hover:bg-teal-700">
            <i class="fas fa-chevron-right"></i>
          </button>
        </div>
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

  <script>
    // Testimonial Carousel
    const carousel = document.querySelector('.carousel');
    const prevBtn = document.querySelector('.carousel-prev');
    const nextBtn = document.querySelector('.carousel-next');
    let currentIndex = 0;

    function updateCarousel() {
      carousel.style.transform = `translateX(-${currentIndex * 100}%)`;
    }

    prevBtn.addEventListener('click', () => {
      currentIndex = (currentIndex === 0) ? carousel.children.length - 1 : currentIndex - 1;
      updateCarousel();
    });

    nextBtn.addEventListener('click', () => {
      currentIndex = (currentIndex === carousel.children.length - 1) ? 0 : currentIndex + 1;
      updateCarousel();
    });

    // Auto-rotate every 5 seconds
    setInterval(() => {
      currentIndex = (currentIndex === carousel.children.length - 1) ? 0 : currentIndex + 1;
      updateCarousel();
    }, 5000);
  </script>
</body>
</html>
<?php
// Close the database connection if it was opened