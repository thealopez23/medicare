<?php
include('config.php');

// Doctor and admin seed data
$accounts = [
    [
        'full_name' => 'Dr. Alexa - Cardiologist',
        'age' => 40,
        'address' => '123 Heart Ave',
        'birthday' => '1984-01-01',
        'email' => 'alexa@healthcare.com',
        'password' => 'doctor123',
        'phone' => '0912345678',
        'role' => 2
    ],
    [
        'full_name' => 'Dr. Thea - Pediatrician',
        'age' => 38,
        'address' => '456 Kids St',
        'birthday' => '1986-02-02',
        'email' => 'thea@healthcare.com',
        'password' => 'doctor123',
        'phone' => '0923456789',
        'role' => 2
    ],
    [
        'full_name' => 'Dr. Renelyn - Dermatologist',
        'age' => 35,
        'address' => '789 Skin Blvd',
        'birthday' => '1989-03-03',
        'email' => 'renelyn@healthcare.com',
        'password' => 'doctor123',
        'phone' => '0934567890',
        'role' => 2
    ],
    [
        'full_name' => 'Admin',
        'age' => 30,
        'address' => 'Admin HQ',
        'birthday' => '1994-04-04',
        'email' => 'admin@healthcare.com',
        'password' => 'admin123',
        'phone' => '0999999999',
        'role' => 1
    ],
];

foreach ($accounts as $acc) {
    // Check if account exists by email
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = :email');
    $stmt->execute([':email' => $acc['email']]);
    if ($stmt->fetchColumn() == 0) {
        // Hash password
        $hashed_password = password_hash($acc['password'], PASSWORD_DEFAULT);
        // Insert account
        $sql = 'INSERT INTO users (full_name, role, age, address, birthday, email, password, phone) VALUES (:full_name, :role, :age, :address, :birthday, :email, :password, :phone)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':full_name' => $acc['full_name'],
            ':role' => $acc['role'],
            ':age' => $acc['age'],
            ':address' => $acc['address'],
            ':birthday' => $acc['birthday'],
            ':email' => $acc['email'],
            ':password' => $hashed_password,
            ':phone' => $acc['phone'],
        ]);
        echo "Seeded: {$acc['full_name']}<br>\n";
    } else {
        echo "Already exists: {$acc['full_name']}<br>\n";
    }
}

echo '<br>Seeding complete.';

// Seed doctors into doctors table
echo '<br>Seeding doctors table...<br>';
foreach ($accounts as $acc) {
    if ($acc['role'] == 2) { // Only process doctors
        // Get user_id from users table
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email');
        $stmt->execute([':email' => $acc['email']]);
        $user_id = $stmt->fetchColumn();
        if ($user_id) {
            // Check if doctor already exists in doctors table
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM doctors WHERE user_id = :user_id');
            $stmt->execute([':user_id' => $user_id]);
            if ($stmt->fetchColumn() == 0) {
                // Insert into doctors table
                $sql = 'INSERT INTO doctors (name, user_id) VALUES (:name, :user_id)';
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':name' => $acc['full_name'],
                    ':user_id' => $user_id
                ]);
                echo "Seeded doctor: {$acc['full_name']}<br>\n";
            } else {
                echo "Doctor already exists: {$acc['full_name']}<br>\n";
            }
        } else {
            echo "User not found for doctor: {$acc['full_name']}<br>\n";
        }
    }
}

echo '<br>Doctors seeding complete.'; 