<?php
// Start session and include required files
session_start();
require_once 'includes/db.php';
require_once 'includes/emoji_functions.php';

// Set CSRF token if not already set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['csrf_token'])) {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('CSRF token validation failed');
    }

    $name = trim($_POST['name'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $ip = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    // Validate input
    if (empty($name) || empty($message)) {
        $error = 'Begge felter skal udfyldes';
    } elseif (strlen($name) > 100) {
        $error = 'Navnet må maksimalt være 100 tegn';
    } elseif (strlen($message) > 1000) {
        $error = 'Beskeden må maksimalt være 1000 tegn';
    } else {
        // Save to database
        $stmt = $pdo->prepare("INSERT INTO entries (name, message,
                     ip_address, user_agent) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$name, $message, $ip, $user_agent])) {
            // Set cookie with user's name
            setcookie('guestbook_name', $name, time() + (86400 * 30), "/");
            // 30 days

            // Redirect to prevent form resubmission
            header('Location: ' . $_SERVER['PHP_SELF'] . '?success=1');
            exit;
        } else {
            $error = 'Der opstod en fejl under indsendelsen. Prøv igen senere.';
        }
    }
}

// First, check if the is_approved column exists
$columnExists = false;
try {
    $test = $pdo->query("SELECT is_approved FROM entries LIMIT 1");
    $columnExists = true;
} catch (PDOException $e) {
    // Column doesn't exist, we'll handle this case
    $columnExists = false;
}

// Get all entries (filter by is_approved if the column exists)
$query = "SELECT * FROM entries";
if ($columnExists) {
    $query .= " WHERE is_approved = 1";
}
$query .= " ORDER BY created_at DESC";

$stmt = $pdo->query($query);
$entries = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gæstebogsindlæg - Larsen8ors Gæstebog</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/guestbook.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="app-container">
        <!-- Sidebar Navigation -->
        <nav class="sidebar" aria-label="Hovednavigation">
            <a href="index.php" class="nav-icon">
                <img src="Billeder/home.png" alt="Forside" class="nav-image">
                <span class="sr-only">Forside</span>
            </a>
            <a href="entries.php" class="nav-icon active" aria-current="page">
                <img src="Billeder/guestbook.png" alt="Gæstebog" class="nav-image">
                <span class="sr-only">Gæstebog</span>
            </a>
        </nav>

        <!-- Main Content -->
        <main class="main-content" style="background-color: var(--off-white);">
            <div class="content-wrapper" style="display: flex; padding: 2rem; gap: 2rem; align-items: flex-start; max-width: var(--content-max-width); margin: 0 auto; width: 100%;">
                <!-- Left Side - Image and Social Icons -->
                <div style="flex: 0 0 400px; display: flex; flex-direction: column; align-items: center; gap: 2rem;">
                    <div style="width: 100%; display: flex; justify-content: center;">
                        <img src="Billeder/skyscraper.png" alt="Skyskraber" style="max-width: 100%; height: auto; object-fit: contain;">
                    </div>

                    <!-- Social Media Icons Removed -->
                </div>

                <!-- Right Side - Content -->
                <div style="flex: 1; padding: 15px 0 0 0; white-space: normal; width: 100%;">
                    <h1 style="font-size: 2rem; color: var(--primary-color); margin: -20px 0 1.5rem 0; line-height: 1.2; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        Gæstebogsindlæg
                    </h1>

                    <div class="welcome-section" style="margin-bottom: 2rem; white-space: normal;">
                        <p style="font-size: 1.1rem; color: var(--text-dark);">Skriv en besked i min gæstebog.</p>
                    </div>

                    <!-- Guestbook Form -->
                    <div style="margin: 0 0 3rem 0; padding-right: 2rem; white-space: normal; width: 100%;">
                        <?php if (isset($error)): ?>
                            <div style="color: #dc3545; margin-bottom: 1rem; padding: 0.75rem 1rem; background-color: #f8d7da; border-radius: 4px;">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_GET['success'])): ?>
                            <div style="color: #0f5132; margin-bottom: 1rem; padding: 0.75rem 1rem; background-color: #d1e7dd; border-radius: 4px;">
                                Tak for din besked! Den vil blive vist når den er godkendt.
                            </div>
                        <?php endif; ?>

                        <form id="guestbook-form" method="POST" style="width: 100%;">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                            <div style="margin-bottom: 1.5rem;">
                                <label for="name" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-dark);">Dit navn</label>
                                <input type="text" id="name" name="name" required
                                       style="width: 100%; max-width: 100%; padding: 0.75rem; border: 1px solid #ced4da; border-radius: 4px; font-size: 1rem;"
                                       value="<?php echo isset($_COOKIE['guestbook_name']) ? htmlspecialchars($_COOKIE['guestbook_name']) : ''; ?>">
                            </div>

                            <div style="margin-bottom: 1.5rem;">
                                <label for="message" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-dark);">Din besked</label>
                                <textarea id="message" name="message" required
                                          style="width: 100%; max-width: 100%; min-height: 150px; padding: 0.75rem; border: 1px solid #ced4da; border-radius: 4px; font-size: 1rem; font-family: 'Inter', sans-serif;"
                                          placeholder="Skriv din besked her..."></textarea>
                            </div>

                            <button type="submit" style="
                                background-color: var(--primary-color);
                                color: white;
                                padding: 1rem 2.5rem;
                                border: none;
                                border-radius: 4px;
                                font-size: 1rem;
                                font-weight: 500;
                                text-transform: uppercase;
                                letter-spacing: 1px;
                                cursor: pointer;
                                transition: background-color 0.2s ease;
                            ">
                                Indsend besked
                            </button>
                        </form>
                    </div>

                    <!-- Guestbook Entries -->
                    <div style="margin: 0 0 3rem 0; padding-right: 2rem; white-space: normal; width: 100%;">
                        <h2 style="font-size: 1.5rem; color: var(--primary-color); margin-bottom: 1.5rem;">Seneste indlæg</h2>

                        <?php if (!empty($entries)): ?>
                            <div style="display: grid; gap: 1.5rem; width: 100%;">
                                <?php foreach ($entries as $entry): ?>
                                    <div style="background: white; border-radius: 8px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1); width: 100%;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; padding-bottom: 0.75rem; border-bottom: 1px solid #eee;">
                                            <strong style="color: var(--primary-color); font-size: 1.1rem;">
                                                <?php echo htmlspecialchars($entry['name']); ?>
                                            </strong>
                                            <span style="color: #6c757d; font-size: 0.9rem;">
                                                <?php echo date('d/m/Y H:i', strtotime($entry['created_at'])); ?>
                                            </span>
                                        </div>
                                        <div style="color: #333; line-height: 1.6;">
                                            <?php 
                                            // Convert text smileys to emojis before displaying
                                            $message = htmlspecialchars($entry['message']);
                                            $message = convertSmileysToEmojis($message);
                                            echo nl2br($message); 
                                            ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p style="color: #6c757d; font-style: italic;">Ingen indlæg endnu. Vær den første til at skrive!</p>
                        <?php endif; ?>
                    </div>
            </div>

            <!-- Footer -->
            <footer class="footer">
                <div class="footer-content">
                    <div class="social-links">
                        <a href="https://www.facebook.com/Larsen8or"
                           target="_blank" rel="noopener noreferrer"
                           class="social-button">
                            <img src="Billeder/facebook.png" alt="Facebook"
                                 class="social-icon">
                        </a>
                        <a href="https://www.linkedin.com/in/larsen8or/"
                           target="_blank" rel="noopener noreferrer"
                           class="social-button">
                            <img src="Billeder/linkedin.png" alt="LinkedIn"
                                 class="social-icon">
                        </a>
                    </div>
                    </div>
            </footer>
        </main>
    </div>

    <script>
        // Simple JavaScript for animations and interactivity
        document.addEventListener('DOMContentLoaded', function() {
            // Add animation to entries when they come into view
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, {
                threshold: 0.1
            });

            // Observe all entries
            document.querySelectorAll('.entry').forEach(entry => {
                observer.observe(entry);
            });
        });
    </script>
</body>
</html>
