<!-- Home_Page(demo03) -->

<?php

include("../connection.php");

session_start();

// Check if the user is logged in and store the state in a variable
$is_logged_in = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

if ($is_logged_in) {
    // Grab user info if they are logged in
    $username = $_SESSION['username'];
    $user_type = $_SESSION['user_type'];
} else {
    $user_type = 'visitor';
    $username = 'visitor';
}


// comments

$comment_error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_comment'])) {
    // if ($is_logged_in) {
    $comment_text = trim($_POST['comment_text']);
    $comment_subject = "Homepage Comment"; // Default subject

    if (!empty($comment_text)) {
        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO comments (username, subject, comment_text, date_posted) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $username, $comment_subject, $comment_text);

        if ($stmt->execute()) {
            // Redirect to prevent form resubmission
            header("Location: index.php#comments");
            exit;
        } else {
            $comment_error = "Error: Could not post comment.";
        }
        $stmt->close();
    } else {
        $comment_error = "Comment cannot be empty.";
    }
    // } else {
    // $comment_error = "You must be logged in to comment.";
    // }
}
?>



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

    <style>
        .comment-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 2.5rem;
            background-color: white;
            padding: 1.5rem;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            text-align: left;
        }

        .comment-form textarea {
            width: 100%;
            min-height: 100px;
            padding: 0.75rem 1rem;
            border: 1px solid #d1d5db;
            /* Slightly darker border */
            border-radius: 0.5rem;
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            line-height: 1.6;
            box-sizing: border-box;
            /* Ensures padding doesn't break layout */
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .comment-form textarea:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
        }

        .comment-form button {
            align-self: flex-start;
            /* Button doesn't stretch full-width */
        }

        /* Style for the comment text in the list */
        .comment-text-content {
            font-size: 1rem;
            color: #1f2937;
            line-height: 1.6;
            margin-top: 0.5rem;
            margin-bottom: 0.25rem;
            /* This is key for showing newlines entered by user */
            white-space: pre-wrap;
            /* Ensures long text wraps */
            word-break: break-word;
        }
    </style>

</head>

<body>

    <!-- Navigation -->
    <nav>
        <!-- Desktop Navigation -->
        <div class="desktop-nav">
            <div class="desktop-nav-card">
                <ul>
                    <li><a href="#about" title="About"><i class="fa-solid fa-circle-info"
                                style="font-size: 1.8rem;"></i></a></li>
                    <li><a href="#services" title="Services"><i class="fa-solid fa-list"
                                style="font-size: 1.8rem;"></i></a></li>
                    <?php if (!$is_logged_in): ?>
                        <li><a href="#account" title="Accounts"><i class="fa-solid fa-inbox"
                                    style="font-size: 1.8rem;"></i></a>
                        </li>
                    <?php else: ?>
                        <li><a href="#transactions" title="Transactions"><i class="fa-solid fa-receipt"
                                    style="font-size: 1.8rem;"></i></a>
                        </li>
                    <?php endif ?>
                    <li><a href="#donation" title="Donation"><i class="fa-solid fa-hand-holding-medical"
                                style="font-size: 1.8rem;"></i></a></li>
                    <li><a href="#contact" title="Contact"><i class="fa-solid fa-envelope"
                                style="font-size: 1.8rem;"></i></a></li>
                </ul>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <div class="mobile-nav">
            <ul>
                <li><a href="#about" title="About"><i class="fa-solid fa-circle-info"
                            style="font-size: 1.8rem;"></i></a></li>
                <li><a href="#services" title="Services"><i class="fa-solid fa-list" style="font-size: 1.8rem;"></i></a>
                </li>
                <?php if (!$is_logged_in): ?>
                    <li><a href="#account" title="Accounts"><i class="fa-solid fa-inbox" style="font-size: 1.8rem;"></i></a>
                    </li>
                <?php else: ?>
                    <li><a href="#transactions" title="Transactions"><i class="fa-solid fa-receipt"
                                style="font-size: 1.8rem;"></i></a>
                    </li>
                <?php endif ?>
                <li><a href="#donation" title="Donation"><i class="fa-solid fa-hand-holding-medical"
                            style="font-size: 1.8rem;"></i></a></li>
                <li><a href="#contact" title="Contact"><i class="fa-solid fa-envelope"
                            style="font-size: 1.8rem;"></i></a></li>
            </ul>
        </div>
    </nav>

    <!-- Sticky Header -->
    <header id="sticky-header" class="sticky-header">
        <div class="container">
            <?php if ($user_type == 'admin'): ?>
                <a href="admin.php" class="btn btn-blue" style="margin-right:auto;">Go to Dashboard</a>

                <span class="welcome-text">Welcome <?php echo $user_type; ?>, <?php echo $username; ?>!</span>

                <a href="../Registration_Login/logout.php" class="btn btn-red">Logout</a>
            <?php elseif ($user_type == 'provider' || $user_type == 'consumer'): ?>
                <a href="profile.php" class="btn btn-blue" style="margin-right:auto;">Go to Profile</a>

                <span class="welcome-text">Welcome <?php echo $user_type; ?>, <?php echo $username; ?>!</span>

                <a href="../Registration_Login/logout.php" class="btn btn-red">Logout</a>
            <?php else: ?>
                <!-- ORIGINAL: Show this if user is not logged in -->
                <a href="#account" class="btn btn-blue">Join us</a>
                <a href="#services" class="btn btn-green">Services</a>
            <?php endif; ?>
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
                    <?php if ($user_type == 'admin'): ?>
                        <a href="admin.php" class="btn btn-blue">Dashboard</a>
                    <?php elseif ($user_type == 'consumer' || $user_type == 'provider'): ?>
                        <a href="profile.php" class="btn btn-blue">Profile</a>
                    <?php else: ?>
                        <a href="#account" class="btn btn-blue">Join Us</a>
                    <?php endif; ?>
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


        <!-- Section 3: Service List -->
        <section id="services" class="full-screen-section">
            <div class="section-content">
                <h2>Services</h2>

                <?php
                include("../connection.php");

                // MODIFIED: Updated SQL query to get all the new fields.
                // I am ASSUMING your columns are named 'id', 'service_type', 'username', 'deadline', 'compensation'
                $sql = "SELECT service_id, service_name, details, service_type, username, deadline, compensation FROM service LIMIT 6";
                $result = mysqli_query($conn, $sql);
                ?>

                <div class="service-list-container">

                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        // Loop through each service from the database
                        while ($row = mysqli_fetch_assoc($result)) {

                            // 1. Get all the new data
                            $service_id = $row['service_id'];
                            $service_name = htmlspecialchars($row['service_name']);
                            $service_desc = htmlspecialchars($row['details']);
                            $service_type = htmlspecialchars($row['service_type']);
                            $username = htmlspecialchars($row['username']);
                            $deadline = htmlspecialchars(date("d M, Y", strtotime($row['deadline']))); // Format the date
                            $compensation = htmlspecialchars($row['compensation']);

                            // 2. Truncate the description to keep the list item small
                            $details_snippet = substr($service_desc, 0, 100);
                            if (strlen($service_desc) > 100) {
                                $details_snippet .= '...'; // Add ellipsis if text is cut
                            }

                            // 3. MODIFIED: This is the new HTML template for each list item
                            echo '
                        <div class="service-list-item">
                            <div class="service-info">
                                <h3>' . $service_name . '</h3>
                                <p><strong>Provider:</strong> ' . $username . ' | <strong>Type:</strong> ' . $service_type . '</p>
                                <p><strong>Compensation:</strong> ' . $compensation . ' | <strong>Deadline:</strong> ' . $deadline . '</p>
                                <p class="service-desc-snippet">' . $details_snippet . '</p>
                            </div>
                            <div class="service-action">
                                <a href="../Services/service_details.php?id=' . $service_id . '" class="btn btn-blue btn-small">View</a>
                            </div>
                        </div>
                        ';
                        }
                    } else {
                        // This message shows if the database table is empty
                        echo '<p style="padding: 2rem;">No services are currently available.</p>';
                    }
                    ?>

                </div>
                <div style="margin-top: 2.5rem; display: flex; flex-wrap: wrap; justify-content: center; gap: 1rem;">
                    <?php if ($is_logged_in): ?>
                        <a href="../Services/request_offer.php" class="btn btn-blue">Create a Service</a>
                        <a href="../Services/service.php" class="btn btn-green">See Full list</a>
                    <?php else: ?>
                        <a href="../Registration_Login/login.php" class="btn btn-blue">Log in to Create
                            Service</a>
                        <a href="../Registration_Login/login.php" class="btn btn-green">Log in to see Full list</a>

                    <?php endif; ?>


                </div>

            </div>
        </section>

        <?php if (!$is_logged_in): ?>
            <!-- Section 4: Accounts -->
            <section id="account" class="full-screen-section" style="background-color: white;">
                <div class="section-content">
                    <h2>Get involved</h2>

                    <div class="card">
                        <div class="card-icon">
                            <i class="fa-regular fa-user" style="font-size:2.5rem;font-weight:bold;"></i>
                        </div>
                        <a href="../Registration_Login/login.php" class="btn btn-blue">User Login</a>
                        <p>Create services as a consumer or a provider. Help out the community by accepting requests and
                            donating!</p>
                        <p>Don&apos;t have an account? <a href="../Registration_Login/registration_form.php">Create
                                Account?</a></p>
                    </div>
                </div>
                </div>
            </section>
        <?php endif ?>

        <!-- Section 6: Transactions -->
        <section id="transactions" class="full-screen-section" style="background-color: #f9fafb;">
            <div class="section-content">
                <h2>Recent Transactions</h2>

                <?php
                // We assume $conn is still open from the previous section
                // If not, you'd add: include("../connection.php");
                
                // New SQL query to get recent transactions and the service name
                $sql_trans = "SELECT 
                                t.transaction_id, 
                                t.user_id,  
                                t.amount, 
                                t.report
                            FROM 
                                transactions t 
                            
                            ORDER BY 
                                t.transaction_id DESC 
                            LIMIT 5";

                $result_trans = mysqli_query($conn, $sql_trans);
                $receiver = 'System';
                $date = 'random date';
                ?>

                <div class="service-list-container">

                    <?php
                    if (mysqli_num_rows($result_trans) > 0) {
                        // Loop through each transaction from the database
                        while ($row = mysqli_fetch_assoc($result_trans)) {

                            // 1. Get all the transaction data
                            $sender = htmlspecialchars($row['user_id']);
                            // $receiver = htmlspecialchars($row['receiver_username']);
                            $amount = htmlspecialchars($row['amount']);
                            $report = htmlspecialchars($row['report']);
                            // $date = htmlspecialchars(date("d M, Y, g:i a", strtotime($row['created_at']))); // Format the date and time
                    
                            // Check if service_name is NULL (e.g., general donation)
                            // $service_name = $row['service_name'] ? htmlspecialchars($row['service_name']) : 'General Donation';
                    

                            // 2. This is the new HTML template for each transaction item
                            echo '
                        <div class="service-list-item">
                        <div class="service-info" style="margin-right: 0;"> 
                        <h3>' . $amount . ' BDT Transferred</h3>
                                <p><strong>From:</strong> ' . $sender . ' | <strong>To:</strong> ' . $receiver . '</p>
                                <p><strong>Summary:</strong> ' . $report . ' | <strong>Date:</strong> ' . $date . '</p>
                            </div>
                            
                        </div>
                        ';
                        }
                    } else {
                        // This message shows if the transactions table is empty
                        echo '<p style="padding: 2rem;">No recent transactions found.</p>';
                    }
                    ?>

                </div>

                <?php if ($user_type == 'admin' || $user_type == 'provider' || $user_type == 'consumer'): ?>
                    <div style="margin-top: 2.5rem; display: flex; flex-wrap: wrap; justify-content: center; gap: 1rem;">
                        <a href="../Services/transactions.php" class="btn btn-green">See Full List</a>
                    </div>
                <?php else: ?>
                    <div style="margin-top: 2.5rem; display: flex; flex-wrap: wrap; justify-content: center; gap: 1rem;">
                        <a href="../Registration_Login/login.php" class="btn btn-green">Log in to see Full List</a>
                    </div>
                <?php endif ?>
            </div>
        </section>


        <!-- Section 5: Donations -->
        <section id="donation" class="full-screen-section" style="background-color: #f9fafb;">
            <div class="section-content">
                <h2>Become a Donor !</h2>
                <p>Your donations help us maintain the platform, support our providers, and ensure that help is
                    available
                    to everyone, regardless of their ability to pay. Every contribution makes a difference.</p>
                <p>Help us build a stronger, more connected community by making a contribution today.</p>
                <?php if ($is_logged_in): ?>
                    <a href="../Services/add_balance.php" class="btn btn-blue" style="margin-top: 1.5rem;">Add to
                        Balance</a>
                    <br>
                    <a href="../Services/donation.php" class="btn btn-green" style="margin-top: 1.5rem;">Support us</a>
                <?php else: ?>
                    <a href="../Registration_Login/login.php" class="btn btn-blue">Log in to Donate</a>

                <?php endif; ?>


            </div>
        </section>



        <!-- Comments -->

        <section id="comments" class="full-screen-section">
            <div class="section-content">
                <h2>Post a Comment</h2>


                <form action="index.php#comments" method="POST" class="comment-form">
                    <label for="comment_text" style="display:none;">Your Comment</label>
                    <textarea id="comment_text" name="comment_text" rows="4"
                        placeholder="Write your comment, <?php echo htmlspecialchars($user_type); ?>..."
                        required></textarea>
                    <?php if ($comment_error): ?>
                        <p style="color: red;"><?php echo $comment_error; ?></p>
                    <?php endif; ?>
                    <button type="submit" name="submit_comment" class="btn btn-blue">Post Comment</button>
                </form>


                <?php
                // Fetch last 5 comments
                $sql_comments = "SELECT username, comment_text, date_posted FROM comments ORDER BY date_posted DESC LIMIT 5";
                $result_comments = mysqli_query($conn, $sql_comments);
                ?>

                <div class="service-list-container">
                    <?php
                    if (mysqli_num_rows($result_comments) > 0) {
                        while ($row = mysqli_fetch_assoc($result_comments)) {
                            $comment_user = htmlspecialchars($row['username']);
                            $comment_text = htmlspecialchars($row['comment_text']);
                            $comment_date = htmlspecialchars(date("d M, Y, g:i a", strtotime($row['date_posted'])));

                            echo '
                            <div class="service-list-item">
                                <div class="service-info" style="margin-right: 0;">
                                    <h3>' . $comment_user . ' <span style="font-weight: 400; font-size: 0.875rem; color: #6b7280;">' . $comment_date . '</span></h3>
                                    <p class="comment-text-content">' . $comment_text . '</p>
                                </div>
                            </div>
                            ';
                        }
                    } else {
                        echo '<p style="padding: 2rem; text-align: center; color: #6b7280;">No comments yet. Be the first to post!</p>';
                    }
                    ?>
                </div>


                <div style="margin-top: 2.5rem; display: flex; flex-wrap: wrap; justify-content: center; gap: 1rem;">
                    <a href="../Services/comments.php" class="btn btn-green">See All Comments</a>
                </div>






            </div>
        </section>

        <!-- Section 7: Contact -->
        <section id="contact" class="full-screen-section">
            <div class="section-content">
                <h2>Contact Us</h2>

                <!-- ADDED: New Footer Content -->
                <footer class="site-footer">
                    <div class="footer-nav">
                        <div class="footer-links">
                            <a href="mailto:gsmurady123@gmail.com">Email</a>
                            <a href="#about">About</a>
                        </div>
                        <div class="social-links">
                            <a href="https://github.com/b1tranger/SESA-Lab-Project" target="_blank" title="GitHub"><i
                                    class="fab fa-github"></i></a>
                            <a href="https://www.linkedin.com/in/gaus-saraf-0471b81a4/" target="_blank"
                                title="LinkedIn"><i class="fab fa-linkedin"></i></a>
                        </div>
                    </div>
                    <p class="footer-motto">
                        "Connecting those in need with those who can help. A community-driven support system."
                    </p>
                </footer>
                <!-- END: New Footer Content -->

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