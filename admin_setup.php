<?php
require_once 'config.php';

try {
    // Check if role column exists
    $stmt = $pdo->query("PRAGMA table_info(users)");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN, 1);

    if (!in_array('role', $columns)) {
        // Add role column to users table
        $pdo->exec("ALTER TABLE users ADD COLUMN role TEXT DEFAULT 'user'");
        echo "Role column added to users table.<br>";
    } else {
        echo "Role column already exists.<br>";
    }

    // Insert default admin if doesn't exist
    $email = 'admin@aerobook.com';
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if (!$stmt->fetch()) {
        $password = password_hash('admin', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES ('System Admin', ?, ?, 'admin')");
        $stmt->execute([$email, $password]);
        echo "Default admin user created: admin@aerobook.com (password: admin)<br>";
    } else {
        // Make sure it has admin role
        $pdo->exec("UPDATE users SET role = 'admin' WHERE email = 'admin@aerobook.com'");
        echo "Default admin user already exists.<br>";
    }

    echo "<a href='login.php'>Go to Login</a>";

} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage();
}
?>
