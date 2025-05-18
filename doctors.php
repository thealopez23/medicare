<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Book an Appointment</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 font-sans leading-relaxed tracking-wide">

  <!-- Navbar -->
  <header class="bg-white shadow">
    <div class="container mx-auto px-6 py-4 flex justify-between items-center">
      <h1 class="text-xl font-bold text-blue-700">Healthcare Portal</h1>
      <nav class="space-x-4">
        <a href="index.php" class="text-gray-700 hover:text-blue-600 transition">Back</a>
      </nav>
    </div>
  </header>

  <!-- Doctor Section -->
  <section class="mt-12 mb-16 px-6" id="doctors">
    <h3 class="text-3xl font-semibold text-blue-700 mb-10 text-center">Meet Our Doctors</h3>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">

      <!-- Doctor Card -->
      <div class="bg-white rounded-xl shadow-md p-4 text-center">
        <img src="Images/alexa.png" alt="Dr. Alexa" class="w-full h-64 object-cover rounded-md mb-4">
        <h4 class="text-xl font-semibold text-gray-800">Dr. Alexa</h4>
        <p class="text-blue-600 font-medium">Cardiologist</p>
        <p class="text-gray-600 mt-2">Expert in heart health and cardiovascular diseases with 15 years of experience.</p>
        <div class="mt-4 text-sm text-left">
          <p class="font-semibold text-gray-700">Available Times:</p>
          <ul class="list-disc list-inside text-gray-600">
            <li>Mon, Wed, Fri – 9:00 AM to 12:00 PM</li>
            <li>Tue, Thu – 2:00 PM to 5:00 PM</li>
          </ul>
        </div>
      </div>

      <!-- Doctor Card -->
      <div class="bg-white rounded-xl shadow-md p-4 text-center">
        <img src="Images/Thea.png" alt="Dr. Thea" class="w-full h-64 object-cover rounded-md mb-4">
        <h4 class="text-xl font-semibold text-gray-800">Dr. Thea</h4>
        <p class="text-blue-600 font-medium">Pediatrician</p>
        <p class="text-gray-600 mt-2">Caring for children’s health and wellness with a gentle approach.</p>
        <div class="mt-4 text-sm text-left">
          <p class="font-semibold text-gray-700">Available Times:</p>
          <ul class="list-disc list-inside text-gray-600">
            <li>Mon – Fri – 10:00 AM to 1:00 PM</li>
            <li>Sat – 9:00 AM to 12:00 PM</li>
          </ul>
        </div>
      </div>

      <!-- Doctor Card -->
      <div class="bg-white rounded-xl shadow-md p-4 text-center">
        <img src="Images/Renelyn.png" alt="Dr. Renelyn" class="w-full h-64 object-cover rounded-md mb-4">
        <h4 class="text-xl font-semibold text-gray-800">Dr. Renelyn</h4>
        <p class="text-blue-600 font-medium">Dermatologist</p>
        <p class="text-gray-600 mt-2">Specialist in skin care and treatment of skin conditions.</p>
        <div class="mt-4 text-sm text-left">
          <p class="font-semibold text-gray-700">Available Times:</p>
          <ul class="list-disc list-inside text-gray-600">
            <li>Tue, Thu – 11:00 AM to 2:00 PM</li>
            <li>Sat – 1:00 PM to 4:00 PM</li>
          </ul>
        </div>
      </div>

    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-white border-t py-4 text-center text-gray-600">
    &copy; <?php echo date("Y"); ?> Healthcare Portal. All rights reserved.
  </footer>

</body>
</html>
