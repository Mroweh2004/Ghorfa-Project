<footer class="footer">
    <div class="footer-content">
        <div class="footer-brand">
            <div class="footer-logo">
                <span class="footer-logo-icon"><img src="{{ asset('img/white.png') }}" alt="Ghorfa"></span>
                <span>Ghorfa</span>
            </div>
            <p>Find rooms and roommates in Lebanon—search, message landlords, and move in with confidence.</p>
            <a href="{{ route('search') }}" class="footer-cta-button">Browse listings</a>
            <div class="social-links">
                <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
        <div class="footer-columns">
            <div class="footer-section">
                <h4>Explore</h4>
                <ul>
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="{{ route('search') }}">Search</a></li>
                    <li><a href="{{ route('map') }}">Map</a></li>
                    @auth
                        <li><a href="{{ route('profileInfo') }}">Profile</a></li>
                    @else
                        <li><a href="{{ route('login') }}">Log in</a></li>
                    @endauth
                </ul>
            </div>
            <div class="footer-section">
                <h4>Support</h4>
                <ul>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Terms</a></li>
                    <li><a href="#">Privacy</a></li>
                    <li><a href="#">Help</a></li>
                </ul>
            </div>
            <div class="footer-section footer-contact">
                <h4>Contact</h4>
                <p><i class="fas fa-envelope" aria-hidden="true"></i> <a href="mailto:ghorfa@gmail.com">ghorfa@gmail.com</a></p>
                <p><i class="fas fa-phone" aria-hidden="true"></i> <a href="tel:+96181920211">+961 81 920 211</a></p>
                <p><i class="fas fa-map-marker-alt" aria-hidden="true"></i> Beirut, Lebanon</p>
            </div>
            <div class="footer-section footer-subscribe">
                <h4>Updates</h4>
                <p>Occasional tips and new listings—no clutter.</p>
                <form class="footer-newsletter" action="#" method="get">
                    <input type="email" name="newsletter_email" placeholder="Your email" autocomplete="email" aria-label="Email for newsletter">
                    <button type="submit">Subscribe</button>
                </form>
                <small>Unsubscribe anytime.</small>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; {{ date('Y') }} Ghorfa. All rights reserved.</p>
        <div class="footer-bottom-links">
            <a href="#">Privacy</a>
            <a href="#">Terms</a>
            <a href="#">Support</a>
        </div>
    </div>
</footer>
