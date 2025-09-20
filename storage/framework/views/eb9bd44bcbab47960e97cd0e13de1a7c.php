<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="<?php echo e(asset('css/home.css')); ?>">
    <title>Document</title>
</head>
<body>
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-brand">
                <div class="footer-logo">
                    <span class="footer-logo-icon"><img src="<?php echo e(asset('img/white.png')); ?>" alt=""></span>
                    <span>Ghorfa</span>
                </div>
                <p>Discover thoughtfully curated living spaces and connect with roommates who feel like home.</p>
                <a href="///" class="footer-cta-button">Start exploring</a>
                <div class="social-links">
                    <a href="///" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="///" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="///" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="///" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="footer-columns">
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="<?php echo e(route('home')); ?>">Home</a></li>
                        <li><a href="<?php echo e(route('search')); ?>">Search</a></li>
                        <li><a href="///">Search by Map</a></li>
                        <li><a href="<?php echo e(route('profileInfo')); ?>">Profile</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="///">FAQ</a></li>
                        <li><a href="///">Terms of Service</a></li>
                        <li><a href="///">Privacy Policy</a></li>
                        <li><a href="///">Help Center</a></li>
                    </ul>
                </div>
                <div class="footer-section footer-contact">
                    <h4>Contact</h4>
                    <p><i class="fas fa-envelope"></i> ghorfa@gmail.com</p>
                    <p><i class="fas fa-phone"></i> +961 81 920 211</p>
                    <p><i class="fas fa-map-marker-alt"></i> Beirut</p>
                </div>
                <div class="footer-section footer-subscribe">
                    <h4>Stay Updated</h4>
                    <p>Get curated room matches and community updates delivered to your inbox.</p>
                    <form class="footer-newsletter" action="#" method="get">
                        <input type="email" name="newsletter_email" placeholder="Email address" aria-label="Email address">
                        <button type="submit">Join</button>
                    </form>
                    <small>No spam. Unsubscribe anytime.</small>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 Ghorfa. All rights reserved.</p>
            <div class="footer-bottom-links">
                <a href="///">Privacy Policy</a>
                <a href="///">Terms of Service</a>
                <a href="///">Support</a>
            </div>
        </div>
    </footer>
</body>
</html>
<?php /**PATH C:\Ghorfa-Project\resources\views/partials/footer.blade.php ENDPATH**/ ?>