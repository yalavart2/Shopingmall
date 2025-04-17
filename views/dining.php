<?php
session_start();
require_once __DIR__ . '/../config/database.php';

$pageTitle = "Dining - Shopping Mall";
include __DIR__ . '/header.php';

$diningOptions = [];
$errorMessage = '';

try {
    $query = "SELECT * FROM dining ORDER BY dining_name ASC";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("SQL Error: " . $conn->error);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $diningOptions[] = $row;
    }
} catch (Exception $e) {
    $errorMessage = "Unable to fetch dining options. Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle); ?></title>

    <!-- Bootstrap & FontAwesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/YOUR-FONT-AWESOME-KEY.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            padding-top: 50px;
        }
        .dining-card {
            border-radius: 10px;
            transition: transform 0.3s ease-in-out;
        }
        .dining-card:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center">Dining Options</h2>
    <p class="text-center">Taste delicious food from different cuisines.</p>

    <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($errorMessage); ?></div>
    <?php endif; ?>

    <div class="row">
        <?php if (!empty($diningOptions)): ?>
            <?php foreach ($diningOptions as $dining): ?>
                <div class="col-md-4">
                    <div class="card shadow-sm dining-card">
                        <img src="<?= htmlspecialchars($dining['image_path']); ?>" class="card-img-top" alt="<?= htmlspecialchars($dining['dining_name']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($dining['dining_name']); ?></h5>
                            <p class="card-text"><?= htmlspecialchars($dining['description']); ?></p>
                            <p><strong>Cuisine:</strong> <?= htmlspecialchars($dining['cuisine']); ?></p>
                            <p><strong>Location:</strong> <?= htmlspecialchars($dining['location']); ?></p>
                            <p><strong>Price Range:</strong> $<?= number_format($dining['price'], 2); ?></p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bookingModal" 
                                    data-diningid="<?= $dining['dining_id']; ?>" 
                                    data-diningname="<?= htmlspecialchars($dining['dining_name']); ?>">
                                Book a Table
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center">No dining options available at the moment.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Booking Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookingModalLabel">Book a Table</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="bookingForm">
                    <input type="hidden" name="dining_id" id="dining_id">
                    <div class="mb-3">
                        <label for="customer_name" class="form-label">Your Name</label>
                        <input type="text" class="form-control" name="customer_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="customer_email" class="form-label">Your Email</label>
                        <input type="email" class="form-control" name="customer_email" required>
                    </div>
                    <div class="mb-3">
                        <label for="tables_required" class="form-label">Number of Tables</label>
                        <input type="number" class="form-control" name="tables_required" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="reservation_date" class="form-label">Reservation Date & Time</label>
                        <input type="datetime-local" class="form-control" name="reservation_date" required>
                    </div>
                    <button type="submit" class="btn btn-success">Confirm Booking</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    var bookingModal = document.getElementById('bookingModal');
    bookingModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var diningId = button.getAttribute('data-diningid');
        var diningName = button.getAttribute('data-diningname');

        document.getElementById('bookingModalLabel').innerText = "Book a Table at " + diningName;
        document.getElementById('dining_id').value = diningId;
    });

    document.getElementById("bookingForm").addEventListener("submit", function(event) {
        event.preventDefault();
        var formData = new FormData(this);

        fetch("book_table.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            Swal.fire({
                title: data.success ? "Success!" : "Error!",
                text: data.message,
                icon: data.success ? "success" : "error",
            });
            if (data.success) {
                document.getElementById("bookingForm").reset();
            }
        });
    });
</script>

</body>
</html>

<?php include __DIR__ . '/footer.php'; ?>
