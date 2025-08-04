<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'kgf_pharmaceuticals');

// Email Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'kgfpharmaceuticals@gmail.com');
define('SMTP_PASSWORD', 'your_app_password_here'); // Use App Password for Gmail
define('FROM_EMAIL', 'kgfpharmaceuticals@gmail.com');
define('FROM_NAME', 'KGF Pharmaceuticals');
define('TO_EMAIL', 'kgfpharmaceuticals@gmail.com');

// Site Configuration
define('SITE_NAME', 'KGF Pharmaceuticals');
define('SITE_URL', 'http://localhost');

// Security
define('CSRF_TOKEN_NAME', 'csrf_token');
session_start();

// Generate CSRF token if not exists
if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
    $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
}

// Database Connection
function getDBConnection() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch(PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        return false;
    }
}

// Utility Functions
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function validateCSRF($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

function sendEmail($to, $subject, $message, $fromEmail = FROM_EMAIL, $fromName = FROM_NAME) {
    // Using PHPMailer would be better, but for simplicity using mail() function
    // In production, use PHPMailer or similar library
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: $fromName <$fromEmail>" . "\r\n";
    $headers .= "Reply-To: $fromEmail" . "\r\n";
    
    return mail($to, $subject, $message, $headers);
}

function createTables() {
    $pdo = getDBConnection();
    if (!$pdo) return false;
    
    try {
        // Contact inquiries table
        $sql = "CREATE TABLE IF NOT EXISTS contact_inquiries (
            id INT AUTO_INCREMENT PRIMARY KEY,
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100) NOT NULL,
            email VARCHAR(255) NOT NULL,
            subject VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            ip_address VARCHAR(45),
            user_agent TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            status ENUM('new', 'read', 'replied') DEFAULT 'new'
        )";
        $pdo->exec($sql);
        
        // Products table
        $sql = "CREATE TABLE IF NOT EXISTS products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            category VARCHAR(100) NOT NULL,
            description TEXT,
            image_url VARCHAR(500),
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        $pdo->exec($sql);
        
        // Insert sample products if table is empty
        $count = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
        if ($count == 0) {
            $products = [
                ['ClearVision Eye Drops', 'Dry Eye Relief', 'Advanced lubricating eye drops that provide immediate relief for dry, irritated eyes. Formulated with natural tears and vitamins to nourish and protect.', 'fas fa-eye-dropper'],
                ['GlucoTreat Solution', 'Glaucoma Management', 'Advanced formulation for effective intraocular pressure reduction. Clinically proven to slow glaucoma progression and protect optic nerve health.', 'fas fa-eye'],
                ['RetinaGuard Capsules', 'Macular Degeneration', 'Nutritional supplement formulated with lutein, zeaxanthin, and essential vitamins to support retinal health and combat macular degeneration.', 'fas fa-low-vision'],
                ['AllerFree Eye Drops', 'Allergy Relief', 'Fast-acting antihistamine eye drops that provide relief from itching, redness, and swelling caused by seasonal allergies and environmental irritants.', 'fas fa-allergies'],
                ['AntiBac Ointment', 'Infection Treatment', 'Broad-spectrum antibiotic ointment for treating bacterial eye infections. Effective against conjunctivitis, blepharitis, and other ocular infections.', 'fas fa-infection'],
                ['VitreoShield Solution', 'Vitreous Support', 'Advanced formulation that supports vitreous health and reduces floaters. Helps maintain clarity and structural integrity of the vitreous humor.', 'fas fa-capsules']
            ];
            
            $stmt = $pdo->prepare("INSERT INTO products (name, category, description, image_url) VALUES (?, ?, ?, ?)");
            foreach ($products as $product) {
                $stmt->execute($product);
            }
        }
        
        return true;
    } catch(PDOException $e) {
        error_log("Table creation failed: " . $e->getMessage());
        return false;
    }
}

// Initialize database tables
createTables();
?>