<?php
session_start();
include('config.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get user information
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
$stmt->execute([':user_id' => $user_id]);
$user = $stmt->fetch();

// Check if user exists and is a patient (role = 3)
if (!$user || $user['role'] != 3) {
    header('Location: index.php');
    exit();
}

// Clear form data if it was submitted
if (isset($_SESSION['form_submitted'])) {
    unset($_SESSION['form_submitted']);
    // $user['full_name'] = '';
    // $user['email'] = '';
    // $user['phone'] = '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Book an Appointment - Medicare</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-color: #f0f0f0; /* Fallback color */
        }
        .primary-bg {
            background-color: #10B981;
        }
        .nav-link {
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            transform: translateY(-2px);
        }
        #preloader {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: #10B981;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .appointment-section {
            background: rgba(255, 255, 255, 0.95); /* More opaque for readability */
            backdrop-filter: blur(8px);
            border-radius: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        .text-readable {
            color: #1E40AF; /* Deep blue for readability */
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }
        .background-slideshow {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            opacity: 1;
            transition: opacity 1s ease-in-out;
            background-size: cover;
            background-position: center;
        }
        .background-slideshow::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.3));
        }
        .background-slideshow.fade {
            opacity: 0;
        }
        .btn-submit {
            background-color: #10B981;
            color: white;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.5);
            transition: all 0.3s ease;
        }
        .btn-submit:hover {
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.7);
            transform: scale(1.05);
        }
        a:focus, button:focus, input:focus, select:focus, textarea:focus {
            outline: 3px solid #10B981;
            outline-offset: 2px;
        }
        .flatpickr-day.disabled {
            background-color: #f3f4f6 !important;
            color: #9ca3af !important;
            cursor: not-allowed !important;
        }
        .flatpickr-day.disabled:hover {
            background-color: #f3f4f6 !important;
            color: #9ca3af !important;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <!-- Background Slideshow -->
    <div id="background-slideshow" class="background-slideshow"></div>

    <!-- Preloader -->
    <div id="preloader">
        <div class="flex flex-col items-center">
            <img src="Images/Logo.png" alt="Loading Logo" class="w-16 h-16 animate-pulse mb-4"/>
            <div class="animate-spin rounded-full h-12 w-12 border-t-4 border-white"></div>
        </div>
    </div>

    <!-- Header -->
    <header class="bg-white/90 shadow-lg sticky top-0 z-50 backdrop-blur-sm">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <img alt="Medicare Logo" class="w-14 h-14" src="Images/Logo.png"/>
                <h1 class="text-3xl font-bold text-green-600">Medicare</h1>
            </div>
            <nav class="flex space-x-8 text-gray-800 font-medium text-lg">
                <a class="nav-link hover:text-green-600" href="index.php">Home</a>
                <a class="nav-link hover:text-green-600" href="patient-dashboard.php">Dashboard</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a class="nav-link text-red-600 hover:text-red-800" href="logout.php">Logout</a>
                <?php else: ?>
                    <a class="nav-link hover:text-green-600" href="login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Appointment Booking Section -->
    <section class="appointment-section max-w-5xl mx-auto my-12 p-8 md:p-12">
        <h3 class="text-4xl font-semibold text-readable mb-8 text-center">
            Book an Appointment
        </h3>
        <div class="mb-8">
            <img alt="Healthcare professional" class="rounded-xl shadow-lg w-full h-48 object-cover mx-auto" src="Images/medical.png"/>
        </div>
        <?php if (isset($_GET['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6" role="alert">
                <span class="block sm:inline">Appointment booked successfully!</span>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6" role="alert">
                <span class="block sm:inline">Error booking appointment. Please try again.</span>
            </div>
        <?php endif; ?>
        <form action="submit_appointment.php" method="POST" class="space-y-6" id="appointment-form">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="fullName" class="block text-gray-800 font-medium mb-2 text-lg">Full Name</label>
                    <input readonly type="text" id="fullName" name="fullName" value="<?php echo isset($_SESSION['form_submitted']) ? '' : htmlspecialchars($user['full_name']); ?>" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 text-lg focus:outline-none focus:ring-2 focus:ring-green-600"/>
                </div>
                <div>
                    <label for="email" class="block text-gray-800 font-medium mb-2 text-lg">Email Address</label>
                    <input readonly type="email" id="email" name="email" value="<?php echo isset($_SESSION['form_submitted']) ? '' : htmlspecialchars($user['email']); ?>" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 text-lg focus:outline-none focus:ring-2 focus:ring-green-600"/>
                </div>
                <div>
                    <label for="phone" class="block text-gray-800 font-medium mb-2 text-lg">Phone Number</label>
                    <input readonly type="tel" id="phone" name="phone" value="<?php echo isset($_SESSION['form_submitted']) ? '' : htmlspecialchars($user['phone']); ?>" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 text-lg focus:outline-none focus:ring-2 focus:ring-green-600"/>
                </div>
                <div>
                    <label for="doctor" class="block text-gray-800 font-medium mb-2 text-lg">Select Doctor</label>
                    <select id="doctor" name="doctor" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 text-lg focus:outline-none focus:ring-2 focus:ring-green-600">
                        <option disabled selected value="">Select a doctor</option>
                        <option value="Dr. Alexa - Cardiologist">Dr. Alexa - Cardiologist</option>
                        <option value="Dr. Thea - Pediatrician">Dr. Thea - Pediatrician</option>
                        <option value="Dr. Renelyn - Dermatologist">Dr. Renelyn - Dermatologist</option>
                    </select>
                </div>
                <div>
                    <label for="date" class="block text-gray-800 font-medium mb-2 text-lg">Preferred Date</label>
                    <input type="text" id="date" name="date" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 text-lg focus:outline-none focus:ring-2 focus:ring-green-600"/>
                </div>
                <div>
                    <label for="time" class="block text-gray-800 font-medium mb-2 text-lg">Preferred Time</label>
                    <select id="time" name="time" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 text-lg focus:outline-none focus:ring-2 focus:ring-green-600">
                        <option value="" disabled selected>Select a time</option>
                    </select>
                </div>
            </div>
            <div>
                <label for="message" class="block text-gray-800 font-medium mb-2 text-lg">Additional Notes</label>
                <textarea id="message" name="message" rows="4" placeholder="Any specific concerns or requests"
                          class="w-full border border-gray-300 rounded-lg px-4 py-3 text-lg focus:outline-none focus:ring-2 focus:ring-green-600"></textarea>
            </div>
            <div class="text-center">
                <button type="submit"
                        class="btn-submit px-10 py-4 rounded-lg font-semibold text-lg">
                    Submit Appointment
                </button>
            </div>
        </form>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-2xl font-semibold mb-4">Medicare</h3>
                    <p class="text-gray-300 text-lg leading-relaxed">Providing quality healthcare services with compassion and expertise.</p>
                </div>
                <div>
                    <h3 class="text-2xl font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-3">
                        <li><a class="hover:text-green-300 transition text-lg" href="services.php">Services</a></li>
                        <li><a class="hover:text-green-300 transition text-lg" href="doctors.php">Doctors</a></li>
                        <li><a class="hover:text-green-300 transition text-lg" href="appointment.php">Appointments</a></li>
                        <li><a class="hover:text-green-300 transition text-lg" href="contact.php">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-2xl font-semibold mb-4">Stay Connected</h3>
                    <div class="flex space-x-4 mb-4">
                        <a href="#" class="hover:text-green-300 transition text-xl"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="hover:text-green-300 transition text-xl"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="hover:text-green-300 transition text-xl"><i class="fab fa-instagram"></i></a>
                    </div>
                    <div>
                        <p class="mb-2 text-lg">Subscribe to our newsletter</p>
                        <div class="flex">
                            <input type="email" placeholder="Enter your email" class="px-4 py-3 rounded-l-lg text-gray-800 focus:outline-none w-full text-lg" aria-label="Newsletter email input"/>
                            <button class="bg-green-600 px-4 py-3 rounded-r-lg hover:bg-green-700 text-lg">Subscribe</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-8 pt-8 border-t border-gray-700 text-center">
                <p class="text-lg">© <?php echo date("Y"); ?> Medicare. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Background Slideshow
        const images = [
            'https://images.unsplash.com/photo-1576091160550-2173dba999ef?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80', // Doctor with stethoscope
            'https://images.unsplash.com/photo-1579684453423-8ac3b6f19e98?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80', // Medical equipment
            'https://images.unsplash.com/photo-1519494026892-80bbdcdf8b18?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80', // Hospital corridor
            'https://images.unsplash.com/photo-1584982751601-97dcc096659c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80', // Doctor and patient
            'https://images.unsplash.com/photo-1585435557343-3b0929fb0483?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80', // Medical research
            'https://images.unsplash.com/photo-1624727828489-a1e03b79bba8?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80', // Healthcare technology
            'https://images.unsplash.com/photo-1586771107445-3b3b1f17a1d7?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80', // Nurse with patient
            'https://images.unsplash.com/photo-1599043513900-ed6fe6d7ab6c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80' // Medical consultation
        ];
        let currentImageIndex = 0;
        const slideshow = document.getElementById('background-slideshow');

        // Preload images to reduce flicker
        images.forEach(src => {
            const img = new Image();
            img.src = src;
        });

        function changeBackgroundImage() {
            slideshow.classList.add('fade');
            setTimeout(() => {
                slideshow.style.backgroundImage = `url(${images[currentImageIndex]})`;
                slideshow.classList.remove('fade');
            }, 1000); // Match transition duration
            currentImageIndex = (currentImageIndex + 1) % images.length;
        }

        // Initial background
        slideshow.style.backgroundImage = `url(${images[0]})`;
        // Change image every 3 seconds
        setInterval(changeBackgroundImage, 3000);

        // Preloader
        window.addEventListener('load', () => {
            document.getElementById('preloader').style.display = 'none';
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Flatpickr and Appointment Logic
        let calendar;
        const doctorSelect = document.getElementById('doctor');
        const dateInput = document.getElementById('date');
        const timeSelect = document.getElementById('time');

        // Initialize calendar
        calendar = flatpickr(dateInput, {
            dateFormat: "Y-m-d",
            minDate: "today",
            disableMobile: "true",
            onChange: function(selectedDates, dateStr) {
                checkAvailability(dateStr);
                updateTimeSlots(dateStr);
            }
        });

        // Check doctor availability when doctor is selected
        doctorSelect.addEventListener('change', function() {
            const doctor = this.value;
            if (!doctor) return;

            // Clear time slots
            timeSelect.innerHTML = '<option value="" disabled selected>Select a time</option>';

            // Fetch all booked dates for the selected doctor
            fetch(`check_availability.php?doctor=${encodeURIComponent(doctor)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('Error:', data.error);
                        return;
                    }

                    // Clear existing calendar
                    calendar.clear();

                    // Disable booked dates
                    if (data.bookedDates && data.bookedDates.length > 0) {
                        calendar.set('disable', data.bookedDates);
                    }

                    // Update time slots if a date is selected
                    if (dateInput.value) {
                        updateTimeSlots(dateInput.value);
                    }
                })
                .catch(error => console.error('Error:', error));
        });

        // Function to update available time slots
        async function updateTimeSlots(date) {
            const doctor = doctorSelect.value;
            if (!doctor || !date) return;

            try {
                const response = await fetch(`get_available_times.php?doctor=${encodeURIComponent(doctor)}&date=${date}`);
                const data = await response.json();
                
                // Clear existing options
                timeSelect.innerHTML = '<option value="" disabled selected>Select a time</option>';

                if (!data.available) {
                    const option = document.createElement('option');
                    option.disabled = true;
                    option.textContent = data.message;
                    timeSelect.appendChild(option);
                    return;
                }

                // Add available time slots
                data.times.forEach(time => {
                    const option = document.createElement('option');
                    option.value = time;
                    option.textContent = time;
                    timeSelect.appendChild(option);
                });
            } catch (error) {
                console.error('Error:', error);
            }
        }

        // Function to check specific date availability
        async function checkAvailability(date) {
            const doctor = doctorSelect.value;
            if (!doctor || !date) return;

            try {
                const response = await fetch(`check_availability.php?doctor=${encodeURIComponent(doctor)}&date=${date}`);
                const data = await response.json();
                
                if (data.error) {
                    console.error('Error checking availability:', data.error);
                    return;
                }

                if (!data.available) {
                    alert('This doctor is already booked for the selected date. Please choose another date.');
                    dateInput.value = '';
                    timeSelect.innerHTML = '<option value="" disabled selected>Select a time</option>';
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }
    </script>
</body>
</html>