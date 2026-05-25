<?php
require_once 'config.php';

try {
    // 1. Create Users Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // 2. Create Flights Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS flights (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        flight_number TEXT NOT NULL,
        origin TEXT NOT NULL,
        destination TEXT NOT NULL,
        departure_time DATETIME NOT NULL,
        arrival_time DATETIME NOT NULL,
        price REAL NOT NULL,
        total_seats INTEGER NOT NULL,
        available_seats INTEGER NOT NULL
    )");

    // 3. Create Bookings Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS bookings (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        flight_id INTEGER NOT NULL,
        booking_date DATETIME DEFAULT CURRENT_TIMESTAMP,
        status TEXT DEFAULT 'Booked',
        FOREIGN KEY(user_id) REFERENCES users(id),
        FOREIGN KEY(flight_id) REFERENCES flights(id)
    )");

    // Insert Dummy Flights if empty
    $stmt = $pdo->query("SELECT COUNT(*) FROM flights");
    if ($stmt->fetchColumn() == 0) {
        $flights = [
            ['FL101', 'New York', 'London', '2026-06-10 08:00:00', '2026-06-10 20:00:00', 450.00, 150, 150],
            ['FL102', 'London', 'New York', '2026-06-15 10:00:00', '2026-06-15 14:00:00', 500.00, 150, 150],
            ['FL201', 'Los Angeles', 'Tokyo', '2026-06-12 11:30:00', '2026-06-13 15:00:00', 800.00, 200, 200],
            ['FL301', 'Dubai', 'Paris', '2026-06-14 09:00:00', '2026-06-14 13:45:00', 350.00, 120, 120],
            ['FL401', 'Sydney', 'Singapore', '2026-06-20 07:00:00', '2026-06-20 15:30:00', 600.00, 180, 180],
            ['FL501', 'New York', 'Paris', '2026-06-11 18:00:00', '2026-06-12 07:00:00', 550.00, 160, 160]
        ];

        $insertStmt = $pdo->prepare("INSERT INTO flights (flight_number, origin, destination, departure_time, arrival_time, price, total_seats, available_seats) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        foreach ($flights as $flight) {
            $insertStmt->execute($flight);
        }
        echo "Database schema created and dummy flights inserted successfully.<br>";
    } else {
        echo "Database schema already exists and is populated.<br>";
    }
    
    echo "<a href='index.php'>Go to Homepage</a>";

} catch (PDOException $e) {
    echo "Setup failed: " . $e->getMessage();
}
?>
