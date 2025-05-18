<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Book an Appointment</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-50 font-sans leading-relaxed tracking-wide">

  <!-- Navbar -->
  <header class="bg-white shadow">
    <div class="container mx-auto px-6 py-4 flex justify-between items-center">
      <h1 class="text-xl font-bold text-blue-700">Healthcare Portal</h1>
      <nav class="space-x-4">
        <a href="index.php" class="hover:text-blue-600 transition">Back</a>
      
      </nav>
    </div>
  </header>

  <!-- Services Section -->
  <section class="mb-16 mt-12 px-6" id="services">
    <h3 class="text-3xl font-bold text-blue-700 mb-12 text-center">
      Our Services
    </h3>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-10 max-w-5xl mx-auto">

  <!-- General Checkup -->
  <div class="bg-white rounded-2xl shadow-lg p-6 text-center hover:shadow-2xl transition duration-300">
    <img src="Images/general-checkup.png" alt="General Checkup" class="w-24 h-24 mx-auto mb-4 object-cover rounded-full shadow-md">
    <h4 class="text-xl font-semibold text-gray-800 mb-2">General Checkup</h4>
    <p class="text-gray-600 text-sm">
      Routine physical exams and consultations to monitor your overall health and detect early signs of illness.
    </p>
  </div>

  <!-- Radiology -->
  <div class="bg-white rounded-2xl shadow-lg p-6 text-center hover:shadow-2xl transition duration-300">
    <img src="Images/radiology.png" alt="Radiology" class="w-24 h-24 mx-auto mb-4 object-cover rounded-full shadow-md">
    <h4 class="text-xl font-semibold text-gray-800 mb-2">Radiology</h4>
    <p class="text-gray-600 text-sm">
      High-quality diagnostic imaging services including X-rays, ultrasounds, MRIs, and CT scans.
    </p>
  </div>

  <!-- Pharmacy -->
  <div class="bg-white rounded-2xl shadow-lg p-6 text-center hover:shadow-2xl transition duration-300">
    <img src="Images/pharmacy.png" alt="Pharmacy" class="w-24 h-24 mx-auto mb-4 object-cover rounded-full shadow-md">
    <h4 class="text-xl font-semibold text-gray-800 mb-2">Pharmacy</h4>
    <p class="text-gray-600 text-sm">
      Convenient access to prescription medications, over-the-counter drugs, and expert pharmacist advice.
    </p>
  </div>

  <!-- Emergency Care -->
  <div class="bg-white rounded-2xl shadow-lg p-6 text-center hover:shadow-2xl transition duration-300">
    <img src="Images/emergency-care.png" alt="Emergency Care" class="w-24 h-24 mx-auto mb-4 object-cover rounded-full shadow-md">
    <h4 class="text-xl font-semibold text-gray-800 mb-2">Emergency Care</h4>
    <p class="text-gray-600 text-sm">
      24/7 emergency response team equipped for urgent medical care, trauma, and life-threatening situations.
    </p>
  </div>

</div>
  </section>

  <!-- Footer -->
  <footer class="bg-white border-t mt-12 py-6 text-center text-gray-600">
    &copy; <?php echo date("Y"); ?> Healthcare Portal. All rights reserved.
  </footer>

</body>
</html>
