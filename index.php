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
        return 'patient-dashboard.php';
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
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <title>Healthcare Portal</title>
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
        .hero-img {
            transition: transform 0.5s ease;
        }
        .hero-img:hover {
            transform: scale(1.05);
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
        .hero-section {
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
        .btn-appointment {
            background-color: #10B981;
            color: white;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.5);
            transition: all 0.3s ease;
        }
        .btn-appointment:hover {
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.7);
            transform: scale(1.05);
        }
        #mobile-menu {
            transform: translateX(100%);
            transition: transform 0.3s ease-in-out;
        }
        #mobile-menu.open {
            transform: translateX(0);
        }
        .fab-appointment {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 50;
            background-color: #10B981;
            color: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }
        .fab-appointment:hover {
            transform: scale(1.1);
        }
        a:focus, button:focus {
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
                <img alt="Healthcare Portal Logo" class="w-14 h-14" src="Images/Logo.png" />
                <h1 class="text-3xl font-bold text-green-600">Healthcare Portal</h1>
            </div>
            <nav class="hidden md:flex space-x-8 text-gray-800 font-medium text-lg">
                <a class="nav-link hover:text-green-600" href="services.php">Services</a>
                <a class="nav-link hover:text-green-600" href="doctors.php">Doctors</a>
                <a class="nav-link hover:text-green-600" href="appointment.php">Appointments</a>
                <a class="nav-link hover:text-green-600" href="contact.php">Contact</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a class="nav-link hover:text-green-600" href="<?php echo getDashboardLink(); ?>">
                        <?php echo getDashboardText(); ?>
                    </a>
                    <a class="nav-link text-red-600 hover:text-red-800" href="logout.php">Logout</a>
                <?php else: ?>
                    <a class="nav-link hover:text-green-600" href="login.php">Login</a>
                <?php endif; ?>
            </nav>
            <button aria-label="Toggle menu" class="md:hidden text-gray-800 focus:outline-none" id="mobile-menu-button">
                <i class="fas fa-bars fa-2x"></i>
            </button>
        </div>
        <nav class="fixed inset-y-0 right-0 bg-white/95 shadow-lg md:hidden w-3/4 max-w-sm" id="mobile-menu">
            <div class="px-6 py-6 space-y-3">
                <button aria-label="Close menu" class="mb-4 text-gray-800 focus:outline-none" id="close-menu-button">
                    <i class="fas fa-times fa-lg"></i>
                </button>
                <a class="block px-4 py-3 text-gray-800 hover:bg-green-50 hover:text-green-600 rounded-lg text-lg" href="services.php">Services</a>
                <a class="block px-4 py-3 text-gray-800 hover:bg-green-50 hover:text-green-600 rounded-lg text-lg" href="doctors.php">Doctors</a>
                <a class="block px-4 py-3 text-gray-800 hover:bg-green-50 hover:text-green-600 rounded-lg text-lg" href="appointment.php">Appointments</a>
                <a class="block px-4 py-3 text-gray-800 hover:bg-green-50 hover:text-green-600 rounded-lg text-lg" href="contact.php">Contact</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a class="block px-4 py-3 text-gray-800 hover:bg-green-50 hover:text-green-600 rounded-lg text-lg" href="<?php echo getDashboardLink(); ?>">
                        <?php echo getDashboardText(); ?>
                    </a>
                    <a class="block px-4 py-3 text-red-600 hover:bg-red-50 hover:text-red-800 rounded-lg text-lg" href="logout.php">Logout</a>
                <?php else: ?>
                    <a class="block px-4 py-3 text-gray-800 hover:bg-green-50 hover:text-green-600 rounded-lg text-lg" href="login.php">Login</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main class="flex-grow container mx-auto px-6 py-12">
        <!-- Hero Section -->
        <section class="hero-section primary-bg rounded-2xl shadow-2xl p-8 md:p-16 mb-12 flex flex-col md:flex-row items-center">
            <div class="md:w-1/2 mb-8 md:mb-0">
                <h2 class="text-4xl md:text-5xl font-extrabold mb-6 leading-tight text-readable">Your Health, Our Priority</h2>
                <p class="text-lg mb-8 opacity-90 text-readable leading-relaxed">Access world-class healthcare services, book appointments with expert doctors, and manage your wellness journey seamlessly.</p>
                <a class="inline-block btn-appointment px-10 py-4 rounded-full font-semibold text-lg" href="appointment.php">
                    Book an Appointment
                </a>
            </div>
            <div class="md:w-1/2 flex justify-center">
                <img alt="Healthcare professional" class="hero-img rounded-xl shadow-lg" src="Images/medical.png" width="400" height="300"/>
            </div>
        </section>
    </main>

    <!-- Floating Action Button -->
    <a href="appointment.php" class="fab-appointment p-4 rounded-full" aria-label="Quick book appointment">
        <i class="fas fa-calendar-plus fa-2x"></i>
    </a>

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
                        <li><a class="hover:text-green-300 transition text-lg" href="appointment.php inhalers">Appointments</a></li>
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
                            <input type="email" placeholder="Enter your email" class="px-4 py-3 rounded-l-lg text-gray-800 focus:outline-none w-full text-lg" aria-label="Newsletter email input">
                            <button class="bg-green-600 px-4 py-3 rounded-r-lg hover:bg-green-700 text-lg">Subscribe</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-8 pt-8 border-t border-gray-700 text-center">
                <p class="text-lg">© 2025 Healthcare Portal. All rights reserved.</p>
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

        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const closeMenuButton = document.getElementById('close-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('open');
            mobileMenu.classList.toggle('hidden');
        });

        closeMenuButton?.addEventListener('click', () => {
            mobileMenu.classList.toggle('open');
            mobileMenu.classList.toggle('hidden');
        });

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