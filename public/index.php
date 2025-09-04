<?php
// Start session for any future functionality
session_start();

// Set security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
?>
<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Larsen8ors Gæstebog</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;
    600;700&display=swap" rel="stylesheet">
    <style>
        /* Minimal inline styles for critical rendering */
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow-x: hidden;
            font-family: 'Inter', sans-serif;
        }
        .app-container {
            display: flex;
            min-height: 100vh;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar Navigation -->
        <nav class="sidebar" aria-label="Hovednavigation">
            <a href="index.php" class="nav-icon active" aria-current="page">
                <img src="Billeder/home.png" alt="Forside" class="nav-image">
                <span class="sr-only">Forside</span>
            </a>
            <a href="entries.php" class="nav-icon">
                <img src="Billeder/guestbook.png" alt="Gæstebog" class="nav-image">
                <span class="sr-only">Gæstebog</span>
            </a>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <div class="content-wrapper" style="display: flex; padding: 0 40px 20px 0; gap: 20px; align-items: flex-start; margin: -10px 0 0 0;">
                <!-- Left Side - Image and Social Icons -->
                <div style="flex: 1; display: flex; flex-direction: column; position: relative; height: 100%; min-height: 0; padding: 20px; box-sizing: border-box; background-color: var(--light-gray);">
                    <div style="height: 100%; display: flex; justify-content: center; align-items: flex-start;">
                        <img src="Billeder/skyscraper.png" alt="Skyskraber"
                             style="max-height: 78vh; width: auto; object-fit: contain; margin-top: 20px;">
                    </div>

                    <!-- Social Media Icons - Positioned at bottom left -->
                    <div style="position: absolute; bottom: 125px; left: 340px; display: flex; gap: 20px;">
                        <a href="https://www.facebook.com/Larsen8or"
                           target="_blank" rel="noopener noreferrer">
                            <img src="Billeder/facebook.png" alt="Facebook"
                                 style="width: 40px; height: 40px;">
                        </a>
                        <a href="https://www.linkedin.com/in/larsen8or/"
                           target="_blank" rel="noopener noreferrer">
                            <img src="Billeder/linkedin.png" alt="LinkedIn"
                                 style="width: 40px; height: 40px;">
                        </a>
                    </div>
                </div>

                <!-- Right Side - Content -->
                <div style="flex: 1; padding: 20px 0 20px 40px; white-space: nowrap; margin-right: 40px;">
                        <h1 style="font-size: 2rem; color: var(--primary-color); margin: -20px 0 1.5rem 0; line-height: 1.2; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            Velkommen til Larsen8ors gæstebog
                        </h1>

                        <div class="welcome-section" style="margin-bottom: 4rem; padding-bottom: 40px; white-space: nowrap;">
                            <p style="font-size: 1.1rem; color: var(--text-dark);">Efterlad gerne en besked i min
                                gæstebog.</p>
                        </div>

                        <!-- Material Design CTA Button -->
                        <a href="entries.php#guestbook-form" class="mdc-button mdc-button--raised"
                           style="
                            align-self: flex-start;
                            background-color: var(--primary-color);
                            color: white;
                            padding: 1.6rem 4rem;
                            margin-top: 0;
                            border-radius: 4px;
                            text-decoration: none;
                            font-weight: 500;
                            font-size: 1rem;
                            text-transform: uppercase;
                            letter-spacing: 1px;
                            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                            transition: all 0.2s ease;
                        ">
                            Skriv indlæg
                        </a>
                    </div>
                </div>

            </div>
        </main>
    </div>
</body>
</html>
