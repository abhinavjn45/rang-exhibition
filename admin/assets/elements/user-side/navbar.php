<nav class="floating-nav">
    <div class="nav-container">
        <ul class="nav-menu d-none d-lg-flex" id="navMenu">
            <li>
                <a href="<?= get_site_option('site_url') ?>">Home</a>
            </li>
            <li>
                <a href="<?= get_site_option('site_url') ?>gallery/">Gallery</a></li>
            </li>
            <li>
                <a href="<?= get_site_option('site_url') ?>cities/">Cities</a>
            </li>
            <li>
                <a href="<?= get_site_option('site_url') ?>about-us/">About Us</a>
            </li>
            <li>
                <a href="<?= get_site_option('site_url') ?>contact-us/">Contact Us</a>
            </li>
        </ul>
        <a href="<?= get_site_option('site_url') ?>" class="nav-logo">
            <img src="<?= get_site_option('dashboard_url') ?>assets/uploads/images/logos/<?= get_site_option('logo') ?>" alt="Event Logo" class="nav-logo-img">
        </a>
        <div class="nav-cta d-none d-lg-flex">
            <a href="<?= get_site_option('site_url') ?>exhibitor-registration/" class="u-btn-primary" id="desktop-cta">Register Now</a> 
            <a href="<?= get_site_option('site_url') ?>get-free-quote/" class="u-btn-secondary">Get Free Quote</a>
        </div>
        
        <button class="mobile-toggle d-lg-none" id="mobileToggle"> <i class="fas fa-bars"></i> </button>
    </div>
    
    <!-- Mobile Menu -->
    <ul class="nav-menu mobile-menu d-lg-none" id="mobileMenu">
        <li>
            <a href="<?= get_site_option('site_url') ?>">Home</a>
        </li>
        <li>
            <a href="<?= get_site_option('site_url') ?>gallery/">Gallery</a></li>
        </li>
        <li>
            <a href="<?= get_site_option('site_url') ?>cities/">Cities</a>
        </li>
        <li>
            <a href="<?= get_site_option('site_url') ?>about-us/">About Us</a>
        </li>
        <li>
            <a href="<?= get_site_option('site_url') ?>contact-us/">Contact Us</a>
        </li>
        <li>
            <div class="nav-cta">
                <a href="<?= get_site_option('site_url') ?>exhibitor-registration/" class="u-btn-primary" id="mobile-cta">Register Here</a> 
                <a href="<?= get_site_option('site_url') ?>get-free-quote/" class="u-btn-secondary">Get Free Quote</a>
            </div>
        </li>
    </ul>
</nav>