<?php
require_once 'config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

$flights = $pdo->query("SELECT * FROM flights ORDER BY departure_time DESC")->fetchAll();
$flash_success = $_SESSION['flash_success'] ?? '';
$flash_error = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Flights - AeroBook Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
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
        .action-btns form { display: inline; }
        .action-btns .btn { padding: 0.3rem 0.6rem; font-size: 0.8rem; width: auto; }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="navbar-brand">✈️ AeroBook Admin</a>
        <div class="nav-links">
            <a href="index.php" class="btn-outline">Main Site</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <main>
        <div class="glass-card large" style="max-width: 1200px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2>Manage Flights</h2>
                <a href="admin_flight_action.php" class="btn" style="width: auto;">+ Add New Flight</a>
            </div>
            
            <div class="admin-nav">
                <a href="admin_dashboard.php">Overview</a>
                <a href="admin_flights.php" class="active">Manage Flights</a>
                <a href="admin_users.php">Manage Users</a>
                <a href="admin_bookings.php">View Bookings</a>
                <a href="admin_reports.php">Reports</a>
            </div>

            <?php if ($flash_error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($flash_error) ?></div>
            <?php endif; ?>
            
            <?php if ($flash_success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($flash_success) ?></div>
            <?php endif; ?>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Flight No.</th>
                            <th>Route</th>
                            <th>Departure</th>
                            <th>Price</th>
                            <th>Seats (Avail/Total)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($flights as $f): ?>
                            <tr>
                                <td><?= $f['id'] ?></td>
                                <td><strong><?= htmlspecialchars($f['flight_number']) ?></strong></td>
                                <td><?= htmlspecialchars($f['origin']) ?> → <?= htmlspecialchars($f['destination']) ?></td>
                                <td><?= date('M d, Y H:i', strtotime($f['departure_time'])) ?></td>
                                <td>$<?= number_format($f['price'], 2) ?></td>
                                <td><?= $f['available_seats'] ?> / <?= $f['total_seats'] ?></td>
                                <td class="action-btns">
                                    <a href="admin_flight_action.php?id=<?= $f['id'] ?>" class="btn" style="background:#f59e0b;">Edit</a>
                                    <form action="admin_flight_action.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this flight?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $f['id'] ?>">
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
