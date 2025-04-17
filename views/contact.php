<?php
$pageTitle = "Contact Us - Shopping Mall";
include __DIR__ . '/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        /* Sticky Footer CSS */
        html, body {
            height: 100%;
        }
        body {
            display: flex;
            flex-direction: column;
        }
        main {
            flex: 1;
        }
        .contact-section { padding: 40px 0; }
        .contact-card { padding: 20px; background: #f8f9fa; border-radius: 10px; }
        .btn-custom { width: 100%; }
        iframe { width: 100%; height: 300px; border: 0; border-radius: 10px; }
    </style>
</head>
<body>

<!-- Contact Section -->
<main>
    <div class="container contact-section">
        <h2 class="text-center mb-4">Get in Touch</h2>
        <p class="text-center">We’re here to help! Reach out for any inquiries.</p>

        <div class="row g-4">
            <!-- Contact Details -->
            <div class="col-md-6">
                <div class="contact-card">
                    <h5>Contact Information</h5>
                    <p><strong>Address:</strong> 123 Shopping Mall Ave, City, Country</p>
                    <p><strong>Phone:</strong> <a href="tel:+1234567890">+1 234 567 890</a></p>
                    <p><strong>Email:</strong> <a href="mailto:support@shoppingmall.com">support@shoppingmall.com</a></p>
                </div>
            </div>

            <!-- Map -->
            <div class="col-md-6">
                <div class="contact-card">
                    <h5>Find Us</h5>
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3151.8354345093677!2d144.95373531531892!3d-37.81627974202165!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad642af0f11fd81%3A0xf0727c4b9b0174d3!2sShopping+Mall!5e0!3m2!1sen!2s!4v1621234567890!5m2!1sen!2s"></iframe>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="mt-4">
            <h4>Send Us a Message</h4>
            <form id="contactForm">
                <div class="mb-3">
                    <label for="name" class="form-label">Your Name</label>
                    <input type="text" class="form-control" id="name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Your Email</label>
                    <input type="email" class="form-control" id="email" required>
                </div>
                <div class="mb-3">
                    <label for="message" class="form-label">Your Message</label>
                    <textarea class="form-control" id="message" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-custom">Send Message</button>
            </form>
        </div>

        <!-- Feedback Form -->
        <div class="mt-4">
            <h4>Feedback & Complaints</h4>
            <form id="feedbackForm">
                <div class="mb-3">
                    <label for="feedback" class="form-label">Your Feedback</label>
                    <textarea class="form-control" id="feedback" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-warning btn-custom">Submit Feedback</button>
            </form>
        </div>

        <!-- FAQ Section -->
        <div class="mt-5">
            <h4>Frequently Asked Questions</h4>
            <div class="accordion" id="faqAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                            What are your opening hours?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">We are open from 10 AM - 10 PM every day.</div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                            Is parking available?
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">Yes, we offer free parking for all visitors.</div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                            Do you have free Wi-Fi?
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">Yes, free Wi-Fi is available throughout the mall.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- JavaScript for Forms -->
<script>
    document.getElementById("contactForm").addEventListener("submit", function(event) {
        event.preventDefault();

        Swal.fire({
            title: "Message Sent!",
            text: "Thank you for reaching out. We will get back to you soon.",
            icon: "success",
            confirmButtonText: "OK"
        }).then(() => {
            document.getElementById("contactForm").reset();
        });
    });

    document.getElementById("feedbackForm").addEventListener("submit", function(event) {
        event.preventDefault();

        Swal.fire({
            title: "Feedback Submitted!",
            text: "Thank you for your valuable feedback. We appreciate it!",
            icon: "success",
            confirmButtonText: "OK"
        }).then(() => {
            document.getElementById("feedbackForm").reset();
        });
    });
</script>

<!-- SweetAlert2 for Popups -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Bootstrap Script -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php include __DIR__ . '/footer.php'; ?>
