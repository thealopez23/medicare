<?php
$host = '127.0.0.1';          
$dbname = 'healthcare_portal';     
$username = 'root';                
$password = '';                    
$charset = 'utf8mb4';              


$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";


$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        
    PDO::ATTR_EMULATE_PREPARES   => false,                   
];

try {
    
    $pdo = new PDO($dsn, $username, $password, $options);
    
    
} catch (PDOException $e) {
    
    error_log('Database connection failed: ' . $e->getMessage());  
    echo 'Connection failed. Please try again later.'; 
    exit(); 
}
?>
