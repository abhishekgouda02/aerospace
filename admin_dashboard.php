<?php
require_once 'config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

// Get Stats
$stats = [];
$stats['users'] = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$stats['flights'] = $pdo->query("SELECT COUNT(*) FROM flights")->fetchColumn();
$stats['bookings'] = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();

// Calculate Revenue (Only Booked tickets)
$revenueQuery = $pdo->query("
    SELECT SUM(f.price) 
    FROM bookings b 
    JOIN flights f ON b.flight_id = f.id 
    WHERE b.status = 'Booked'
");
$stats['revenue'] = $revenueQuery->fetchColumn() ?: 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - AeroBook</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(255,255,255,0.1);
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
        }
        .stat-card h3 {
            color: var(--text-muted);
            font-size: 0.9rem;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }
        .stat-card .value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        .admin-nav {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        .admin-nav a {
            padding: 0.5rem 1rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 6px;
            color: #fff;
            text-decoration: none;
        }
        .admin-nav a:hover, .admin-nav a.active {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="navbar-brand">✈️ AeroBook Admin</a>
        <div class="nav-links">
            <span style="color: var(--text-color); margin-right: 1rem;">Admin: <?= htmlspecialchars($_SESSION['user_name']) ?></span>
            <a href="index.php" class="btn-outline">Main Site</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <main>
        <div class="glass-card large" style="max-width: 1200px;">
            <h2>Admin Dashboard</h2>
            
            <div class="admin-nav">
                <a href="admin_dashboard.php" class="active">Overview</a>
                <a href="admin_flights.php">Manage Flights</a>
                <a href="admin_users.php">Manage Users</a>
                <a href="admin_bookings.php">View Bookings</a>
                <a href="admin_reports.php">Reports</a>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Users</h3>
                    <div class="value"><?= $stats['users'] ?></div>
                </div>
                <div class="stat-card">
                    <h3>Total Flights</h3>
                    <div class="value"><?= $stats['flights'] ?></div>
                </div>
                <div class="stat-card">
                    <h3>Total Bookings</h3>
                    <div class="value"><?= $stats['bookings'] ?></div>
                </div>
                <div class="stat-card">
                    <h3>Total Revenue</h3>
                    <div class="value">$<?= number_format($stats['revenue'], 2) ?></div>
                </div>
            </div>
            
            <div class="alert alert-success">
                Welcome to the admin panel. Use the navigation above to manage the system.
            </div>
        </div>
    </main>
</body>
</html>
