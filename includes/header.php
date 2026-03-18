<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$base_url = '/Car-Rental-Agency/';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RideReserve - Car Rental Management System</title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS (optional, but requested. We will use CSS variables for custom styling) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $base_url ?>assets/css/style.css?v=<?= time() ?>">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

    <!-- Header / Navbar -->
    <nav class="navbar navbar-expand bg-white custom-navbar sticky-top">
        <div class="container-fluid px-5">
            <a class="navbar-brand fw-bold text-dark fs-4" href="<?= $base_url ?>index.php">RideReserve</a>

            <div class="navbar-collapse justify-content-center">
                <div class="nav-pill-container">
                    <ul class="navbar-nav align-items-center">
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>"
                                aria-current="page" href="<?= $base_url ?>index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : '' ?>"
                                href="<?= $base_url ?>about.php">About Us</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <form id="frontendSearchForm"
                    onsubmit="event.preventDefault(); window.filterCarsFrontend && window.filterCarsFrontend(this.search.value);"
                    class="d-flex align-items-center mb-0">
                    <div class="expandable-search" id="expandableSearch">
                        <input type="text" name="search" id="searchInput"
                            class="search-input"
                            placeholder="Search by model or number..."
                            onkeyup="window.filterCarsFrontend && window.filterCarsFrontend(this.value);"
                            autocomplete="off">
                        <button class="search-btn" type="button" id="searchToggleBtn">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                </form>

                <script>
                    document.getElementById('searchToggleBtn').addEventListener('click', function(e) {
                        const container = document.getElementById('expandableSearch');
                        const input = document.getElementById('searchInput');
                        
                        // If it's already open and has text, let the form submit or act as search
                        if (container.classList.contains('active') && input.value.trim() !== '') {
                            // act as submit
                            if(window.filterCarsFrontend) window.filterCarsFrontend(input.value);
                        } else {
                            // Toggle open/close
                            container.classList.toggle('active');
                            if(container.classList.contains('active')) {
                                input.focus();
                            }
                        }
                    });

                    // Close the search bar if user clicks outside of it
                    document.addEventListener('click', function(e) {
                        const container = document.getElementById('expandableSearch');
                        const input = document.getElementById('searchInput');
                        if (!container.contains(e.target)) {
                            // Only close if it's empty
                            if (input.value.trim() === '') {
                                container.classList.remove('active');
                            }
                        }
                    });
                </script>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['role'] === 'agency'): ?>
                        <a href="<?= $base_url ?>agency/add_car.php"
                            class="btn btn-outline-dark rounded-pill px-3 custom-btn-outline">Add Car</a>
                        <a href="<?= $base_url ?>agency/view_bookings.php"
                            class="btn btn-outline-dark rounded-pill px-3 custom-btn-outline">Bookings</a>
                    <?php elseif ($_SESSION['role'] === 'customer'): ?>
                        <a href="<?= $base_url ?>my_bookings.php"
                            class="btn btn-outline-dark rounded-pill px-3 custom-btn-outline">My Bookings</a>
                    <?php endif; ?>
                    <a href="<?= $base_url ?>api/logout.php"
                        class="btn btn-dark rounded-pill px-4 custom-btn-dark">Logout</a>
                <?php else: ?>
                    <a href="<?= $base_url ?>login.php" class="btn btn-dark rounded-pill px-4 custom-btn-dark">Login / Sign
                        Up</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>