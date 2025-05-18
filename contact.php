<?php
session_start();

function getDashboardLink() {
    if (!isset($_SESSION['user_id'])) {
        return 'login.php';
    }
    
    $role = $_SESSION['role'];
    if ($role == 1) {
        return 'admin_dashboard.php';
    } else if ($role == 2) {
        return 'doctor_dashboard.php';
    } else if ($role == 3) {
        return 'profile.php';
    }
    return 'index.php';
}

function getDashboardText() {
    if (!isset($_SESSION['user_id'])) {
        return 'Login';
    }
    
    $role = $_SESSION['role'];
    if ($role == 1) {
        return 'Admin Dashboard';
    } else if ($role == 2) {
        return 'Doctor Dashboard';
    } else if ($role == 3) {
        return 'Patient Dashboard';
    }
    return 'Dashboard';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Contact Us - Healthcare Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet"/>
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
        .contact-section {
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
        .btn-send {
            background-color: #10B981;
            color: white;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.5);
            transition: all 0.3s ease;
        }
        .btn-send:hover {
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.7);
            transform: scale(1.05);
        }
        a:focus, button:focus, input:focus, textarea:focus {
            outline: 3px solid #10B981;
            outline-offset: 2px;
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
                <img alt="Healthcare Portal Logo" class="w-14 h-14" src="Images/Logo.png"/>
                <h1 class="text-3xl font-bold text-green-600">Healthcare Portal</h1>
            </div>
            <nav class="flex space-x-8 text-gray-800 font-medium text-lg">
                <a class="nav-link hover:text-green-600" href="index.php">Back</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a class="nav-link hover:text-green-600" href="<?php echo getDashboardLink(); ?>">
                        <?php echo getDashboardText(); ?>
                    </a>
                    <a class="nav-link text-red-600 hover:text-red-800" href="logout.php">Logout</a>
                <?php else: ?>
                    <a class="nav-link hover:text-green-600" href="login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Contact Section -->
    <section class="contact-section max-w-4xl mx-auto my-12 p-8 md:p-12" id="contact">
        <h3 class="text-4xl font-semibold text-readable mb-8 text-center">
            Contact Us
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <!-- Contact Info -->
            <div>
                <div class="mb-8">
                    <img alt="Healthcare professional" class="rounded-xl shadow-lg w-full h-48 object-cover" src="Images/clinic.png"/>
                </div>
                <h4 class="text-2xl font-semibold text-readable mb-4">Our Location</h4>
                <p class="text-gray-700 text-lg mb-4 leading-relaxed">
                    123 Health St.<br>
                    San Pablo, Laguna<br>
                    Philippines
                </p>
                <h4 class="text-2xl font-semibold text-readable mb-4 mt-6">Phone & Email</h4>
                <p class="text-gray-700 text-lg mb-2">
                    Phone:
                    <a class="text-green-600 hover:text-green-700" href="tel:+63491234567">+63 49 123 4567</a>
                </p>
                <p class="text-gray-700 text-lg">
                    Email:
                    <a class="text-green-600 hover:text-green-700" href="mailto:info@healthcareportal.com">info@healthcareportal.com</a>
                </p>
            </div>

            <!-- Contact Form -->
            <div>
                <h4 class="text-2xl font-semibold text-readable mb-6">Send Us a Message</h4>
                <form action="submit_contact.php" method="POST" class="space-y-6" id="contact-form">
                    <div>
                        <label for="contactName" class="block text-gray-800 font-medium mb-2 text-lg">Name</label>
                        <input type="text" id="contactName" name="contactName" placeholder="Your name" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 text-lg focus:outline-none focus:ring-2 focus:ring-green-600"/>
                    </div>
                    <div>
                        <label for="contactEmail" class="block text-gray-800 font-medium mb-2 text-lg">Email</label>
                        <input type="email" id="contactEmail" name="contactEmail" placeholder="you@example.com" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 text-lg focus:outline-none focus:ring-2 focus:ring-green-600"/>
                    </div>
                    <div>
                        <label for="contactMessage" class="block text-gray-800 font-medium mb-2 text-lg">Message</label>
                        <textarea id="contactMessage" name="contactMessage" rows="5" placeholder="Write your message here" required
                                  class="w-full border border-gray-300 rounded-lg px-4 py-3 text-lg focus:outline-none focus:ring-2 focus:ring-green-600"></textarea>
                    </div>
                    <div class="text-center">
                        <button type="submit"
                                class="btn-send px-10 py-4 rounded-lg font-semibold text-lg">
                            Send Message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-2xl font-semibold mb-4">Healthcare Portal</h3>
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
                <p class="text-lg">© <?php echo date("Y"); ?> Healthcare Portal. All rights reserved.</p>
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
    </script>
</body>
</html>