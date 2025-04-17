<?php
$pageTitle = "About Us - Shopping Mall";

// API Mode: Return JSON if requested
if (isset($_GET['api']) && $_GET['api'] == '1') {
    header("Content-Type: application/json");
    $aboutData = [
        "success" => true,
        "title" => "About Us",
        "history" => "Founded in 2005, our shopping mall has evolved into a premier shopping and entertainment destination. We feature global and local brands, diverse dining options, and state-of-the-art entertainment facilities. Our commitment is to provide a seamless and enjoyable shopping experience, blending modern architecture with sustainability initiatives.",
        "mission" => "To deliver an exceptional shopping and entertainment experience for all.",
        "vision" => "To be the most preferred shopping destination in the region, offering world-class services and products.",
        "management" => "Our leadership team consists of industry experts dedicated to ensuring an outstanding experience for visitors, retailers, and staff.",
        "contact" => [
            "email" => "info@shoppingmall.com",
            "phone" => "+123-456-7890",
            "address" => "123 Mall Street, City, Country"
        ],
        "careers" => "Looking for a rewarding career? Explore opportunities in retail, management, and customer service. Visit our Careers page for more details.",
        "services" => [
            "Ample Parking & Valet Service",
            "Free High-Speed Wi-Fi",
            "Wheelchair Accessibility",
            "Dedicated Customer Service Desk",
            "Family Lounge & Baby Changing Stations",
            "Lost & Found Assistance"
        ],
        "sustainability" => "Our mall leads sustainability efforts with eco-friendly initiatives, energy-efficient lighting, and waste reduction programs. We integrate rainwater harvesting, solar-powered lighting, and recycling programs for a greener future.",
        "virtualTour" => "Experience our mall from anywhere with our interactive virtual tour. (Coming Soon)"
    ];
    echo json_encode($aboutData);
    exit;
}

include __DIR__ . '/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/YOUR-FONT-AWESOME-KEY.js" crossorigin="anonymous"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .container { max-width: 1000px; margin-top: 50px; padding: 20px; }
        .section-title { font-weight: 700; border-bottom: 3px solid #007bff; display: inline-block; padding-bottom: 5px; }
        .info-box { background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
        .icon { font-size: 25px; color: #007bff; margin-right: 10px; }
        .footer { background-color: #343a40; color: white; padding: 20px 0; text-align: center; margin-top: 50px; }
        .footer a { color: #f8f9fa; text-decoration: none; margin: 0 10px; }
        .footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center section-title" id="about-title">Loading...</h2>

    <div class="info-box">
        <h4><i class="fas fa-history icon"></i> Our History</h4>
        <p id="about-history"></p>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="info-box">
                <h4><i class="fas fa-bullseye icon"></i> Mission</h4>
                <p id="about-mission"></p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="info-box">
                <h4><i class="fas fa-eye icon"></i> Vision</h4>
                <p id="about-vision"></p>
            </div>
        </div>
    </div>

    <div class="info-box">
        <h4><i class="fas fa-users icon"></i> Our Management</h4>
        <p id="about-management"></p>
    </div>

    <div class="info-box">
        <h4><i class="fas fa-envelope icon"></i> Contact Information</h4>
        <p><strong>Email:</strong> <span id="contact-email"></span></p>
        <p><strong>Phone:</strong> <span id="contact-phone"></span></p>
        <p><strong>Address:</strong> <span id="contact-address"></span></p>
    </div>

    <div class="info-box">
        <h4><i class="fas fa-briefcase icon"></i> Careers</h4>
        <p id="about-careers"></p>
    </div>

    <div class="info-box">
        <h4><i class="fas fa-concierge-bell icon"></i> Services Offered</h4>
        <ul id="services-list"></ul>
    </div>

    <div class="info-box">
        <h4><i class="fas fa-leaf icon"></i> Sustainability Initiatives</h4>
        <p id="about-sustainability"></p>
    </div>

    <div class="info-box">
        <h4><i class="fas fa-vr-cardboard icon"></i> Virtual Tour</h4>
        <p id="about-virtual-tour"></p>
    </div>
</div>

<!-- JavaScript to Fetch About Us Data from the API -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        fetch("about.php?api=1")
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById("about-title").textContent = data.title;
                    document.getElementById("about-history").textContent = data.history;
                    document.getElementById("about-vision").textContent = data.vision;
                    document.getElementById("about-mission").textContent = data.mission;
                    document.getElementById("about-management").textContent = data.management;
                    document.getElementById("contact-email").textContent = data.contact.email;
                    document.getElementById("contact-phone").textContent = data.contact.phone;
                    document.getElementById("contact-address").textContent = data.contact.address;
                    document.getElementById("about-careers").textContent = data.careers;
                    document.getElementById("about-sustainability").textContent = data.sustainability;
                    document.getElementById("about-virtual-tour").textContent = data.virtualTour;

                    let servicesList = document.getElementById("services-list");
                    servicesList.innerHTML = "";
                    data.services.forEach(service => {
                        let listItem = document.createElement("li");
                        listItem.textContent = service;
                        servicesList.appendChild(listItem);
                    });
                } else {
                    document.getElementById("about-title").textContent = "Error Loading Data";
                }
            })
            .catch(error => {
                console.error("Error fetching About Us data:", error);
                document.getElementById("about-title").textContent = "Error";
            });
    });
</script>

<?php include __DIR__ . '/footer.php'; ?>
