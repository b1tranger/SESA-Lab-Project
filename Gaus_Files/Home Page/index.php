<!-- Home_Page(demo03) -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Hero - Connecting People</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- <link rel="stylesheet" type="text/css" href="index.css"> -->
    <link rel="stylesheet" type="text/css" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

    <!-- Navigation -->
    <nav>
        <!-- Desktop Navigation -->
        <div class="desktop-nav">
            <div class="desktop-nav-card">
                <ul>
                    <li><a href="#about" title="About"><i class="fa-solid fa-circle-info" style="font-size: 1.8rem;"></i></a></li>
                    <li><a href="#services" title="Services"><i class="fa-solid fa-list" style="font-size: 1.8rem;"></i></a></li>
                    <li><a href="#account" title="Services"><i class="fa-solid fa-inbox" style="font-size: 1.8rem;"></i></a></li>
                    <li><a href="#donation" title="Donation"><i class="fa-solid fa-hand-holding-medical"  style="font-size: 1.8rem;"></i></a></li>
                    <li><a href="#contact" title="Contact"><i class="fa-solid fa-envelope"  style="font-size: 1.8rem;"></i></a></li>
                </ul>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <div class="mobile-nav">
            <ul>
                    <li><a href="#about" title="About"><i class="fa-solid fa-circle-info" style="font-size: 1.8rem;"></i></a></li>
                    <li><a href="#services" title="Services"><i class="fa-solid fa-list" style="font-size: 1.8rem;"></i></a></li>
                    <li><a href="#account" title="Services"><i class="fa-solid fa-inbox" style="font-size: 1.8rem;"></i></a></li>
                    <li><a href="#donation" title="Donation"><i class="fa-solid fa-hand-holding-medical"  style="font-size: 1.8rem;"></i></a></li>
                    <li><a href="#contact" title="Contact"><i class="fa-solid fa-envelope"  style="font-size: 1.8rem;"></i></a></li>
                </ul>
        </div>
    </nav>

    <!-- Sticky Header -->
    <header id="sticky-header" class="sticky-header">
        <div class="container">
            <a href="#account" class="btn btn-blue">Join us</a>
            <a href="#services" class="btn btn-green">Services</a>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <!-- Section 1: Hero -->
        <section id="home" class="full-screen-section hero-bg">
            <div class="overlay"></div>
            <div class="content">
                <h1>Welcome to Support Hero</h1>
                <p>Connecting those in need with those who can help. A community-driven support system.</p>
                <div id="hero-buttons">
                    <a href="#account" class="btn btn-blue">Join Us</a>
                    <a href="#services" class="btn btn-green">Services</a>

                </div>
                <br><br><br><br><br><br><br>
                <div id="hero-buttons"><a href="#donation" class="btn btn-green"
                        style="background-color: rgb(139, 0, 253);">Support Our Cause</a></div>
            </div>
            <a href="#about" class="scroll-down-arrow">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white/80" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" style="width: 2.5rem; height: 2.5rem; color: rgba(255,255,255,0.8);">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </a>
        </section>

        <!-- Section 2: About -->
        <!-- <section id="about" class="full-screen-section" style="background-color: white;">
            <div class="section-content">

            </div>
        </section> -->

        <!-- Section 5: Services -->
        <section id="about" class="full-screen-section" style="background-color: #f9fafb;">
            <div class="section-content">
                <h2>About Our Mission</h2>
                <p>Support Hero is a platform designed to bridge the gap between consumers seeking services and
                    providers ready to offer them. Our unique model allows for direct compensation and community-driven
                    donations, creating a sustainable and supportive ecosystem.</p>
                <p>Whether you need help with daily tasks, professional services, or emergency support, our network of
                    vetted providers is here for you. Join us in building a stronger, more connected community.</p>
                <h2>How It Works</h2>
                <div class="grid-3">
                    <div class="card">
                        <div class="card-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg></div>
                        <h3>1. Request a Service</h3>
                        <p>Consumers post their needs, from simple errands to specialized tasks.</p>
                    </div>
                    <div class="card">
                        <div class="card-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.653-.124-1.28-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.653.124-1.28.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg></div>
                        <h3>2. Connect with a Provider</h3>
                        <p>Service providers accept requests that match their skills and availability.</p>
                    </div>
                    <div class="card">
                        <div class="card-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.085a2 2 0 00-1.736.93L5.5 8m7-3V5a2 2 0 00-2-2H7a2 2 0 00-2 2v5m0 0v5a2 2 0 002 2h2.5" />
                            </svg></div>
                        <h3>3. Compensate & Donate</h3>
                        <p>Providers are compensated, and consumers can donate to support the community.</p>
                    </div>
                </div>
            </div>
        </section>


        <!-- Section 6: Backend Demo -->
        <section id="services" class="full-screen-section">
            <div class="section-content">
                <h2>Admin & Backend Operations (Demo)</h2>
                <div class="grid-3">
                    <div class="card">
                        <div class="card-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2" />
                            </svg></div>
                        <h3>User Management</h3>
                        <p>Admin panel to manage consumers, service providers, and visitor feedback.</p>
                    </div>
                    <div class="card">
                        <div class="card-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg></div>
                        <h3>Service Management</h3>
                        <p>Tools for admins to oversee service requests, bounties, and provider ranks.</p>
                    </div>
                    <div class="card">
                        <div class="card-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg></div>
                        <h3>Financials</h3>
                        <p>Manage direct compensations and the flow of donations through the system.</p>
                    </div>
                </div>
            </div>
        </section>


        <!-- Section 3: Accounts -->
        <section id="account" class="full-screen-section" style="background-color: white;">
            <div class="section-content">
                <h2>Get involved</h2>
                <div class="grid-3">
                    <div class="card">
                        <div class="card-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2" />
                            </svg></div>
                        <!-- <h3>Consumer Account</h3> -->
                        <a href="../Registration_Login/registration_form.php" class="btn btn-green">Register as a
                            Consumer</a>
                        <p>As a consumer, you can request for services or accept an existing offer. They are our core
                            users.</p>
                        <p>Already registered? Go to <a href="../Registration_Login/login.php">Login</a></p>
                    </div>
                    <div class="card">
                        <div class="card-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg></div>
                        <!-- <h3>Provider Account</h3> -->
                        <a href="../Registration_Login/registration_form.php" class="btn btn-blue">Register as a
                            Provider</a>
                        <p>As a provider, you can accept a request or create an offer for the consumers. They areour
                            main driving force.</p>
                        <p>Already registered? Go to <a href="../Registration_Login/login.php">Login</a></p>
                    </div>
                </div>
            </div>
        </section>


        <!-- Section 4: Accounts -->
        <section id="donation" class="full-screen-section" style="background-color: #f9fafb;">
            <div class="section-content">
                <h2>Support Our Community</h2>
                <p>Your donations help us maintain the platform, support our providers, and ensure that help is
                    available
                    to everyone, regardless of their ability to pay. Every contribution makes a difference.</p>
                <p>Help us build a stronger, more connected community by making a contribution today.</p>
                <a href="#payment-link" class="btn btn-blue" style="margin-top: 1.5rem;">Become a Donator</a>
            </div>
        </section>





        <!-- Section 7: Contact -->
        <section id="contact" class="full-screen-section">
            <div class="section-content">
                <h2>Contact Us</h2>

            </div>
        </section>


        <a href="#home" class="go-to-top-button" title="Go to top">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                style="width: 1.5rem; height: 1.5rem;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
            </svg>
        </a>

    </main>

    <!-- Combined JS -->
    <script>
        // JavaScript for sticky header AND go-to-top button
        document.addEventListener('DOMContentLoaded', function () {
            const stickyHeader = document.getElementById('sticky-header');
            const goToTopBtn = document.querySelector('.go-to-top-button'); // Get the button
            const heroSection = document.getElementById('home');

            // Function to toggle sticky elements
            const toggleStickyElements = () => {
                const scrollPosition = window.scrollY;
                const heroHeight = heroSection.offsetHeight;

                // Toggle Sticky Header
                if (scrollPosition > heroHeight) {
                    stickyHeader.classList.add('show');
                } else {
                    stickyHeader.classList.remove('show');
                }

                // Toggle Go to Top Button
                if (goToTopBtn) { // Check if the button was found
                    if (scrollPosition > heroHeight) {
                        goToTopBtn.classList.add('show'); // Use 'show' to match style.css
                    } else {
                        goToTopBtn.classList.remove('show');
                    }
                }
            };

            // Listen for scroll events
            window.addEventListener('scroll', toggleStickyElements);
        });
    </script>

</body>

</html>

<!-- <script>
        // JavaScript for the sticky header
        document.addEventListener('DOMContentLoaded', function () {
            const stickyHeader = document.getElementById('sticky-header');
            const heroSection = document.getElementById('home');

            // Function to toggle the sticky header
            const toggleStickyHeader = () => {
                // The offsetHeight gives the full height of the hero section
                if (window.scrollY > heroSection.offsetHeight) {
                    stickyHeader.classList.add('show');
                } else {
                    stickyHeader.classList.remove('show');
                }
            };

            // Listen for scroll events
            window.addEventListener('scroll', toggleStickyHeader);
        });
    </script> -->

</body>

</html>