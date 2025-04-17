<?php
require_once __DIR__ . '/../config/database.php';

// Check if event_id is present
if (!isset($_GET['event_id']) || empty($_GET['event_id'])) {
    die("Invalid event ID.");
}

$event_id = intval($_GET['event_id']);

// Fetch event details
$query = "SELECT event_name, event_date, location FROM events WHERE event_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

if (!$event) {
    die("Event not found.");
}

include __DIR__ . '/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Book Event - <?= htmlspecialchars($event['event_name']); ?></title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <!-- SweetAlert2 for Popups -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">Book Your Spot for <?= htmlspecialchars($event['event_name']); ?></h2>
    <p class="text-center">
        <strong>Date:</strong> <?= date("F j, Y", strtotime($event['event_date'])); ?> | 
        <strong>Location:</strong> <?= htmlspecialchars($event['location']); ?>
    </p>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <form id="bookingForm">
                <input type="hidden" name="event_id" value="<?= $event_id; ?>">

                <div class="mb-3">
                    <label for="customer_name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                </div>

                <div class="mb-3">
                    <label for="customer_email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="customer_email" name="customer_email" required>
                </div>

                <div class="mb-3">
                    <label for="num_participants" class="form-label">Number of Participants</label>
                    <input type="number" class="form-control" id="num_participants" name="num_participants" min="1" value="1" required>
                </div>

                <button type="submit" class="btn btn-success w-100">Confirm Booking</button>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript for handling the form submission and pop-up message -->
<script>
$(document).ready(function() {
    $("#bookingForm").submit(function(event) {
        event.preventDefault(); // Prevent default form submission

        $.ajax({
            url: "process_booking.php",
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function(response) {
                console.log("Response:", response); // Debugging line

                if (response.status === "success") {
                    Swal.fire({
                        title: "Success!",
                        text: response.message,
                        icon: "success",
                        confirmButtonText: "OK"
                    }).then(() => {
                        window.location.href = "events.php"; // Redirect after success
                    });
                } else {
                    Swal.fire({
                        title: "Error!",
                        text: response.message,
                        icon: "error",
                        confirmButtonText: "Try Again"
                    });
                }
            },
            error: function(xhr, status, error) {
                console.log("XHR:", xhr); // Debugging line
                console.log("Status:", status);
                console.log("Error:", error);
                Swal.fire({
                    title: "Oops!",
                    text: "An unexpected error occurred. Please try again.",
                    icon: "error",
                    confirmButtonText: "OK"
                });
            }
        });
    });
});

</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php include __DIR__ . '/footer.php'; ?>
