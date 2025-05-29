<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'Technician Portal - AirProtech'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Link to your main CSS file if you have one -->
    <link rel="stylesheet" href="/assets/css/main.css"> 
    <?php
    // Output any additional styles passed from the specific view
    if (!empty($additionalStyles)) {
        echo $additionalStyles;
    }
    ?>
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        .navbar-technician {
            background-color: #343a40; /* Dark navbar for technician */
            color: #ffffff;
        }
        .navbar-technician .navbar-brand,
        .navbar-technician .nav-link {
            color: #ffffff;
        }
        .navbar-technician .nav-link:hover {
            color: #adb5bd;
        }
        .content-wrapper {
            flex: 1;
            padding-top: 20px; /* Add padding to prevent content from hiding behind a fixed navbar if you add one */
        }
        .footer {
            background-color: #e9ecef;
            padding: 1rem 0;
            text-align: center;
            font-size: 0.9rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-technician">
            <div class="container-fluid">
                <a class="navbar-brand" href="/technician/dashboard">
                    <img src="/assets/images/logo-placeholder.png" alt="AirProtech Logo" style="height: 30px; margin-right: 10px;"> <!-- Replace with your actual logo -->
                    AirProtech Technician
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#technicianNavbar" aria-controls="technicianNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon" style="color:white;"></span>
                </button>
                <div class="collapse navbar-collapse" id="technicianNavbar">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($activeTab === 'dashboard') ? 'active' : ''; ?>" href="/technician/dashboard"><i class="fas fa-tachometer-alt me-1"></i>Dashboard</a>
                        </li>
                        <!-- Add other technician-specific nav items here -->
                        <!-- Example:
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($activeTab === 'profile') ? 'active' : ''; ?>" href="/technician/profile"><i class="fas fa-user me-1"></i>Profile</a>
                        </li>
                         -->
                        <li class="nav-item">
                            <a class="nav-link" href="/logout"><i class="fas fa-sign-out-alt me-1"></i>Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="content-wrapper container-fluid">
        <?php
        // Display flash messages
        if (isset($_SESSION['flash_message'])) {
            $message = $_SESSION['flash_message'];
            $type = $_SESSION['flash_type'] ?? 'info'; // Default to info if type not set
            echo "<div class='alert alert-{$type} alert-dismissible fade show' role='alert'>
                    {$message}
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                  </div>";
            unset($_SESSION['flash_message']);
            unset($_SESSION['flash_type']);
        }
        ?>
        <?php echo $content; // Main content from the specific view ?>
    </main>

    <footer class="footer mt-auto">
        <div class="container">
            <span>&copy; <?php echo date("Y"); ?> AirProtech Cooling Systems. All rights reserved.</span>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/assets/js/utility/toast-notifications.js"></script>
    <!-- Link to your global JS file if you have one -->
    <!-- <script src="/assets/js/main.js"></script> -->
</body>
</html> 