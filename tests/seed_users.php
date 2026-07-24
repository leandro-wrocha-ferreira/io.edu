<?php

$host = getenv('DB_HOSTNAME') ?: 'localhost';
$username = getenv('DB_USERNAME') ?: 'inverta';
$password = getenv('DB_PASSWORD') ?: 'inverta';
$database = getenv('DB_DATABASE') ?: 'inverta';

try {
    $pdo = new PDO("mysql:host={$host};dbname={$database}", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Insert admin user (role_id = 1 for admin)
    $stmt = $pdo->prepare("
        INSERT IGNORE INTO users (name, email, password, role_id, created_at)
        VALUES (?, ?, ?, ?, ?)
    ");

    $admin_password = password_hash('admin123', PASSWORD_BCRYPT);
    $stmt->execute(['Admin User', 'admin@example.com', $admin_password, 1, date('Y-m-d H:i:s')]);

    $student_password = password_hash('student123', PASSWORD_BCRYPT);
    $stmt->execute(['Student User', 'student@example.com', $student_password, 2, date('Y-m-d H:i:s')]);

    echo "Test users created successfully!\n";
    echo "Admin: admin@example.com / admin123\n";
    echo "Student: student@example.com / student123\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
