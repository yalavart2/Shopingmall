<?php
// Ensure database connection is included
require_once __DIR__ . '/../config/database.php';

// Set page title
$pageTitle = "Events - Shopping Mall";

// Fetch events from the database
$events = [];
$errorMessage = '';

try {
    $query = "SELECT event_id, event_name, event_date, location, description, image_path FROM events ORDER BY event_date ASC";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        throw new Exception("SQL Error: " . $conn->error);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
} catch (Exception $e) {
    $errorMessage = "Unable to fetch events. Error: " . $e->getMessage();
}

include __DIR__ . '/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>

    <!-- Bootstrap & FontAwesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/YOUR-FONT-AWESOME-KEY.js" crossorigin="anonymous"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            padding-top: 50px;
        }
        .card {
            transition: transform 0.2s;
        }
        .card:hover {
            transform: scale(1.03);
        }
        .event-img {
            height: 200px;
            object-fit: cover;
        }
    </style>
</head>
<body>

<!-- Events Section -->
<div class="container">
    <h2 class="text-center mb-4">Upcoming Events</h2>
    
    <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($errorMessage); ?></div>
    <?php endif; ?>

    <div class="row">
        <?php if (!empty($events)): ?>
            <?php foreach ($events as $event): ?>
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <img src="uploads/<?= !empty($event['image_path']) ? htmlspecialchars($event['image_path']) : 'event_placeholder.jpg'; ?>" class="card-img-top event-img" alt="Event Image">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($event['event_name']); ?></h5>
                            <p class="card-text"><strong>Date:</strong> <?= date("F j, Y", strtotime($event['event_date'])); ?></p>
                            <p class="card-text"><strong>Location:</strong> <?= htmlspecialchars($event['location']); ?></p>
                            <p class="card-text"><?= htmlspecialchars($event['description']); ?></p>
                            <a href="book_event.php?event_id=<?= $event['event_id']; ?>" class="btn btn-primary">Book Now</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center">No upcoming events at the moment.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php include __DIR__ . '/footer.php'; ?>
