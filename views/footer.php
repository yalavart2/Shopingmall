<!-- Footer -->
<footer class="footer bg-dark text-white text-center py-4 mt-5">
    <div class="container">
        <div class="row">
            <!-- Quick Links -->
            <div class="col-md-3 footer-section">
                <h5 class="footer-title">Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="home.php">Home</a></li>
                    <li><a href="events.php">Events</a></li>
                    <li><a href="dining.php">Dining</a></li>
                    <li><a href="offers.php">Offers</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                </ul>
            </div>

            <!-- Social Media -->
            <div class="col-md-3 footer-section">
                <h5 class="footer-title">Follow Us</h5>
                <div class="social-icons">
                    <img src="uploads/facebook.png" alt="Facebook" title="Follow us on Facebook" class="social-icon">
                    <img src="uploads/twitter.png" alt="Twitter" title="Follow us on Twitter" class="social-icon">
                    <img src="uploads/instagram.png" alt="Instagram" title="Follow us on Instagram" class="social-icon">
                </div>
            </div>

            <!-- Contact Info -->
            <div class="col-md-3 footer-section">
                <h5 class="footer-title">Contact Us</h5>
                <p>Email: <a href="mailto:support@shoppingmall.com">support@shoppingmall.com</a></p>
                <p>Phone: <a href="tel:+1234567890">+123 456 7890</a></p>
                <p>Location: 123 Mall Street, City, Country</p>
            </div>

            <!-- Newsletter Subscription -->
            <div class="col-md-3 footer-section">
                <h5 class="footer-title">Newsletter</h5>
                <form id="newsletterForm">
                    <div class="input-group">
                        <input type="email" id="newsletterEmail" class="form-control" placeholder="Enter your email" required>
                        <button class="btn btn-success" type="submit">Subscribe</button>
                    </div>
                </form>
            </div>
        </div>

        <hr class="footer-divider">

        <!-- Privacy Policy & Terms -->
        <p class="footer-text">
            <a href="#">Privacy Policy</a> | 
            <a href="#">Terms of Service</a>
        </p>

        <p class="footer-text">&copy; <?php echo date('Y'); ?> Shopping Mall. All Rights Reserved.</p>
    </div>
</footer>

<!-- Bootstrap Script -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- FontAwesome -->
<script src="https://kit.fontawesome.com/YOUR-FONT-AWESOME-KEY.js" crossorigin="anonymous"></script>

<!-- SweetAlert2 for Popups -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Custom JavaScript for Newsletter -->
<script>
    document.getElementById('newsletterForm').addEventListener('submit', function(event) {
        event.preventDefault();
        var email = document.getElementById('newsletterEmail').value;

        if (email.trim() === '') {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please enter a valid email address!'
            });
            return;
        }

        // Simulating AJAX request
        setTimeout(() => {
            Swal.fire({
                icon: 'success',
                title: 'Subscribed!',
                text: 'Thank you for subscribing to our newsletter.',
                confirmButtonColor: '#28a745'
            });
        }, 500);

        // Clear input after submission
        document.getElementById('newsletterEmail').value = '';
    });
</script>

<!-- Custom Footer CSS -->
<style>
    .footer {
        font-size: 16px;
    }

    .footer-section {
        margin-bottom: 15px;
    }

    .footer-title {
        font-weight: bold;
        margin-bottom: 10px;
    }

    .footer-section ul {
        padding: 0;
    }

    .footer-section ul li {
        list-style: none;
        margin: 5px 0;
    }

    .footer-section ul li a {
        color: #ccc;
        text-decoration: none;
        transition: color 0.3s ease-in-out;
    }

    .footer-section ul li a:hover {
        color: #f8f9fa;
        text-decoration: underline;
    }

    .footer-divider {
        background-color: rgba(255, 255, 255, 0.2);
        height: 1px;
        width: 100%;
        margin: 15px 0;
    }

    .footer-text {
        font-size: 14px;
        margin-top: 5px;
    }

    .social-icons img {
        width: 35px;
        height: 35px;
        margin: 0 10px;
        transition: transform 0.3s ease-in-out;
    }

    .social-icons img:hover {
        transform: scale(1.1);
    }

    .input-group input {
        border-radius: 5px 0 0 5px;
    }

    .input-group button {
        border-radius: 0 5px 5px 0;
    }

    @media (max-width: 768px) {
        .footer-section {
            text-align: center;
            margin-bottom: 20px;
        }
        .social-icons {
            margin-top: 10px;
        }
    }
</style>
