<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'];
    $user_id = $_SESSION['user_id'];

    try {
        $pdo->beginTransaction();

        // Check if booking belongs to user and is currently booked
        $stmt = $pdo->prepare("SELECT flight_id FROM bookings WHERE id = ? AND user_id = ? AND status = 'Booked'");
        $stmt->execute([$booking_id, $user_id]);
        $booking = $stmt->fetch();

        if ($booking) {
            // Update booking status
            $cancelStmt = $pdo->prepare("UPDATE bookings SET status = 'Cancelled' WHERE id = ?");
            $cancelStmt->execute([$booking_id]);

            // Increment available seats on the flight
            $restoreStmt = $pdo->prepare("UPDATE flights SET available_seats = available_seats + 1 WHERE id = ?");
            $restoreStmt->execute([$booking['flight_id']]);

            $pdo->commit();
            $_SESSION['flash_success'] = "Ticket cancelled successfully.";
        } else {
            $pdo->rollBack();
            $_SESSION['flash_error'] = "Invalid booking or already cancelled.";
        }

        redirect('dashboard.php');

    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['flash_error'] = "Cancellation failed: " . $e->getMessage();
        redirect('dashboard.php');
    }
} else {
    redirect('dashboard.php');
}
?>
