<?php
require_once 'config.php';

if (!isLoggedIn()) {
    // Store intended flight in session and redirect to login
    if (isset($_POST['flight_id'])) {
        $_SESSION['pending_flight_id'] = $_POST['flight_id'];
    }
    redirect('login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $flight_id = $_POST['flight_id'];
    $user_id = $_SESSION['user_id'];

    try {
        $pdo->beginTransaction();

        // Check if user already booked this flight and it's active
        $checkStmt = $pdo->prepare("SELECT id FROM bookings WHERE user_id = ? AND flight_id = ? AND status = 'Booked'");
        $checkStmt->execute([$user_id, $flight_id]);
        if ($checkStmt->fetch()) {
            $_SESSION['flash_error'] = "You have already booked this flight.";
            $pdo->rollBack();
            redirect('dashboard.php');
        }

        // Lock row implicitly with update and check seats
        $updateStmt = $pdo->prepare("UPDATE flights SET available_seats = available_seats - 1 WHERE id = ? AND available_seats > 0");
        $updateStmt->execute([$flight_id]);
        
        if ($updateStmt->rowCount() > 0) {
            // Seat booked successfully, create booking record
            $bookStmt = $pdo->prepare("INSERT INTO bookings (user_id, flight_id, status) VALUES (?, ?, 'Booked')");
            $bookStmt->execute([$user_id, $flight_id]);
            
            $pdo->commit();
            $_SESSION['flash_success'] = "Flight booked successfully!";
        } else {
            $pdo->rollBack();
            $_SESSION['flash_error'] = "Sorry, this flight is fully booked.";
        }
        
        redirect('dashboard.php');

    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['flash_error'] = "Booking failed: " . $e->getMessage();
        redirect('index.php');
    }
} else {
    redirect('index.php');
}
?>
