<?php
session_start();

// Include the database connection file
include('config.php');

// Initialize error message variable
$error_message = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        // Prepare SQL query to check user credentials
        $query = "SELECT id, full_name, role, password FROM users WHERE email = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$email]);

        $user = $stmt->fetch();

        if ($user) {
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Store user session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $email;
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];

                // Redirect based on role
                if ($user['role'] == 1) { // Admin
                    header('Location: admin_dashboard.php');
                } else if ($user['role'] == 2) { // Doctor
                    header('Location: doctor_dashboard.php');
                } else { // Patient
                    header('Location: patient-dashboard.php');
                }
                exit();
            } else {
                $error_message = 'Incorrect password.';
            }
        } else {
            $error_message = 'No account found with that email address.';
        }
    }
}

function redirectBasedOnRole($role) {
    if ($role == 1) {
        header('Location: admin_dashboard.php');
    } else if ($role == 2) {
        header('Location: doctor_dashboard.php');
    } else if ($role == 3) {
        header('Location: profile.php');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Login - Healthcare Portal</title>
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
        .login-section {
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
        .btn-signin {
            background-color: #10B981;
            color: white;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.5);
            transition: all 0.3s ease;
        }
        .btn-signin:hover {
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.7);
            transform: scale(1.05);
        }
        a:focus, button:focus, input:focus, select:focus {
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
            <nav class="hidden md:flex space-x-8 text-gray-800 font-medium text-lg">
                <a class="nav-link hover:text-green-600" href="index.php">Home</a>
                <a class="nav-link hover:text-green-600" href="index.php#services">Services</a>
                <a class="nav-link hover:text-green-600" href="index.php#doctors">Doctors</a>
                <a class="nav-link hover:text-green-600" href="index.php#appointments">Appointments</a>
                <a class="nav-link hover:text-green-600" href="index.php#contact">Contact</a>
            </nav>
            <button aria-label="Toggle menu" class="md:hidden text-gray-800 focus:outline-none focus:ring-2 focus:ring-green-600" id="mobile-menu-button">
                <i class="fas fa-bars fa-lg"></i>
            </button>
        </div>
        <nav class="hidden md:hidden bg-white border-t border-gray-200" id="mobile-menu">
            <a class="block px-6 py-3 text-gray-800 hover:bg-green-50 hover:text-green-600 transition" href="index.php">Home</a>
            <a class="block px-6 py-3 text-gray-800 hover:bg-green-50 hover:text-green-600 transition" href="index.php#services">Services</a>
            <a class="block px-6 py-3 text-gray-800 hover:bg-green-50 hover:text-green-600 transition" href="index.php#doctors">Doctors</a>
            <a class="block px-6 py-3 text-gray-800 hover:bg-green-50 hover:text-green-600 transition" href="index.php#appointments">Appointments</a>
            <a class="block px-6 py-3 text-gray-800 hover:bg-green-50 hover:text-green-600 transition" href="index.php#contact">Contact</a>
        </nav>
    </header>

    <!-- Main Login Form -->
    <main class="flex-grow flex justify-center items-center py-12">
        <section class="login-section max-w-lg mx-auto p-8 md:p-12">
            <h2 class="text-4xl font-semibold text-readable mb-8 text-center">
                Welcome Back
            </h2>
            <div class="mb-8">
                <img alt="Healthcare professional" class="rounded-xl shadow-lg w-full max-w-md h-48 object-cover mx-auto" src="Images/medical.png"/>
            </div>
            <p class="text-center text-gray-600 text-lg mb-8">
                Sign in to access your healthcare dashboard and manage your appointments.
            </p>
            <?php if ($error_message): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 text-center">
                    <p><?php echo htmlspecialchars($error_message); ?></p>
                </div>
            <?php endif; ?>
            <form action="login.php" method="POST" class="space-y-6" novalidate>
                <div>
                    <label for="email" class="block text-gray-800 font-medium mb-2 text-lg">Email Address</label>
                    <div class="relative">
                        <input autocomplete="email" id="email" name="email" placeholder="you@example.com" required type="email"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 text-lg focus:outline-none focus:ring-2 focus:ring-green-600"/>
                        <span class="absolute inset-y-0 right-4 flex items-center text-green-600">
                            <i class="fas fa-envelope"></i>
                        </span>
                    </div>
                </div>
                <div>
                    <label for="password" class="block text-gray-800 font-medium mb-2 text-lg">Password</label>
                    <div class="relative">
                        <input autocomplete="current-password" id="password" name="password" placeholder="Enter your password" required type="password"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 text-lg focus:outline-none focus:ring-2 focus:ring-green-600"/>
                        <span class="absolute inset-y-0 right-4 flex items-center text-green-600 cursor-pointer" id="togglePassword" title="Show/Hide Password">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>
                <div class="flex justify-center">
                    <label class="inline-flex items-center text-lg text-gray-700">
                        <input class="form-checkbox h-5 w-5 text-green-600 rounded" id="remember_me" name="remember_me" type="checkbox"/>
                        <span class="ml-2 select-none">Remember me</span>
                    </label>
                </div>
                <div class="text-center">
                    <button type="submit"
                            class="btn-signin px-10 py-4 rounded-lg font-semibold text-lg">
                        Sign In
                    </button>
                </div>
            </form>
            <p class="mt-6 text-center text-gray-600 text-lg">
                Don't have an account?
                <a href="signup.php" class="font-semibold text-green-600 hover:text-green-700">Sign up</a>
            </p>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12 mt-auto">
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

        // Mobile Menu Toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Toggle Password Visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        togglePassword.addEventListener('click', () => {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            togglePassword.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
        });

        // Smooth Scroll for Anchor Links
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
