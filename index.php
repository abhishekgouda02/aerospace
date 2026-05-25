<?php
require_once 'config.php';

// Fetch some popular destinations for the dropdowns
$stmt = $pdo->query("SELECT DISTINCT origin FROM flights");
$origins = $stmt->fetchAll(PDO::FETCH_COLUMN);

$stmt = $pdo->query("SELECT DISTINCT destination FROM flights");
$destinations = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AeroBook - Modern Flight Booking</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .hero-section {
            text-align: center;
            margin-bottom: 3rem;
            animation: slideUp 0.8s ease forwards;
        }
        .hero-section h1 {
            font-size: 3.5rem;
            background: linear-gradient(to right, #60a5fa, #3b82f6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
        }
        .hero-section p {
            font-size: 1.2rem;
            color: var(--text-muted);
            max-width: 600px;
            margin: 0 auto;
        }
        .search-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            align-items: end;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="navbar-brand">✈️ AeroBook</a>
        <div class="nav-links">
            <?php if (isLoggedIn()): ?>
                <?php if (isAdmin()): ?>
                    <a href="admin_dashboard.php" style="color: var(--primary-color);">Admin Panel</a>
                <?php endif; ?>
                <a href="dashboard.php">Dashboard</a>
                <a href="logout.php" class="btn-outline">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php" class="btn-outline">Register</a>
            <?php endif; ?>
        </div>
    </nav>

    <main>
        <div class="hero-section">
            <h1>Discover The World</h1>
            <p>Book your next adventure with our premium flight booking experience. Fast, secure, and beautiful.</p>
        </div>

        <div class="glass-card large">
            <h2>Find Your Flight</h2>
            <form action="search.php" method="GET" class="search-form">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="origin">From</label>
                    <select name="origin" id="origin" class="form-control" required>
                        <option value="">Select Origin</option>
                        <?php foreach($origins as $orig): ?>
                            <option value="<?= htmlspecialchars($orig) ?>"><?= htmlspecialchars($orig) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="destination">To</label>
                    <select name="destination" id="destination" class="form-control" required>
                        <option value="">Select Destination</option>
                        <?php foreach($destinations as $dest): ?>
                            <option value="<?= htmlspecialchars($dest) ?>"><?= htmlspecialchars($dest) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label for="date">Departure Date (Optional)</label>
                    <input type="date" name="date" id="date" class="form-control">
                </div>

                <div>
                    <button type="submit" class="btn">Search Flights</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
