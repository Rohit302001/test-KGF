<?php
require_once 'config.php';

// Simple authentication (in production, use proper authentication)
$admin_username = 'admin';
$admin_password = 'kgf@admin123'; // Change this password

session_start();

// Handle login
if (isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($username === $admin_username && $password === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $login_error = 'Invalid credentials';
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// Check if admin is logged in
$is_logged_in = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'];

if (!$is_logged_in) {
    // Show login form
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Login - KGF Pharmaceuticals</title>
        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0;
            }
            .login-container {
                background: white;
                padding: 2rem;
                border-radius: 10px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.3);
                width: 100%;
                max-width: 400px;
            }
            .logo-section {
                text-align: center;
                margin-bottom: 2rem;
            }
            .logo-icon {
                width: 60px;
                height: 60px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border-radius: 8px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: bold;
                font-size: 1.5rem;
                margin-bottom: 1rem;
            }
            h1 {
                margin: 0;
                color: #333;
                font-size: 1.5rem;
            }
            .form-group {
                margin-bottom: 1rem;
            }
            label {
                display: block;
                margin-bottom: 0.5rem;
                color: #555;
                font-weight: 500;
            }
            input[type="text"], input[type="password"] {
                width: 100%;
                padding: 0.75rem;
                border: 2px solid #e1e5e9;
                border-radius: 6px;
                font-size: 1rem;
                transition: border-color 0.3s;
                box-sizing: border-box;
            }
            input[type="text"]:focus, input[type="password"]:focus {
                outline: none;
                border-color: #667eea;
            }
            .btn {
                width: 100%;
                padding: 0.75rem;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                border: none;
                border-radius: 6px;
                font-size: 1rem;
                font-weight: 600;
                cursor: pointer;
                transition: transform 0.2s;
            }
            .btn:hover {
                transform: translateY(-2px);
            }
            .error {
                color: #dc3545;
                margin-top: 1rem;
                text-align: center;
                font-size: 0.9rem;
            }
        </style>
    </head>
    <body>
        <div class="login-container">
            <div class="logo-section">
                <div class="logo-icon">KGF</div>
                <h1>Admin Panel</h1>
                <p style="color: #666; margin: 0;">KGF Pharmaceuticals</p>
            </div>
            
            <form method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" name="login" class="btn">Login</button>
                
                <?php if (isset($login_error)): ?>
                    <div class="error"><?php echo htmlspecialchars($login_error); ?></div>
                <?php endif; ?>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Admin is logged in, show dashboard
$pdo = getDBConnection();
$page = $_GET['page'] ?? 'inquiries';

// Handle actions
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $id = (int)$_POST['id'];
    
    if ($action === 'mark_read' && $id > 0) {
        $stmt = $pdo->prepare("UPDATE contact_inquiries SET status = 'read' WHERE id = ?");
        $stmt->execute([$id]);
    } elseif ($action === 'mark_replied' && $id > 0) {
        $stmt = $pdo->prepare("UPDATE contact_inquiries SET status = 'replied' WHERE id = ?");
        $stmt->execute([$id]);
    } elseif ($action === 'delete' && $id > 0) {
        $stmt = $pdo->prepare("DELETE FROM contact_inquiries WHERE id = ?");
        $stmt->execute([$id]);
    }
}

// Get statistics
$stats = [];
if ($pdo) {
    $stats['total_inquiries'] = $pdo->query("SELECT COUNT(*) FROM contact_inquiries")->fetchColumn();
    $stats['new_inquiries'] = $pdo->query("SELECT COUNT(*) FROM contact_inquiries WHERE status = 'new'")->fetchColumn();
    $stats['read_inquiries'] = $pdo->query("SELECT COUNT(*) FROM contact_inquiries WHERE status = 'read'")->fetchColumn();
    $stats['replied_inquiries'] = $pdo->query("SELECT COUNT(*) FROM contact_inquiries WHERE status = 'replied'")->fetchColumn();
    $stats['total_products'] = $pdo->query("SELECT COUNT(*) FROM products WHERE is_active = 1")->fetchColumn();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - KGF Pharmaceuticals</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .logo-icon {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.2);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .nav-tabs {
            display: flex;
            gap: 1rem;
        }
        
        .nav-tab {
            padding: 0.5rem 1rem;
            background: rgba(255,255,255,0.1);
            border: none;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        
        .nav-tab:hover, .nav-tab.active {
            background: rgba(255,255,255,0.2);
        }
        
        .logout-btn {
            padding: 0.5rem 1rem;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.3);
            color: white;
            border-radius: 6px;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        
        .logout-btn:hover {
            background: rgba(255,255,255,0.2);
        }
        
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }
        
        .content-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .card-header {
            background: #f8f9fa;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e9ecef;
            font-weight: 600;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th, .table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }
        
        .table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-new {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-read {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .status-replied {
            background: #d4edda;
            color: #155724;
        }
        
        .btn {
            padding: 0.25rem 0.75rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8rem;
            margin: 0 0.25rem;
            transition: opacity 0.3s;
        }
        
        .btn:hover {
            opacity: 0.8;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .message-preview {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 0 1rem;
            }
            
            .header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .nav-tabs {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .table {
                font-size: 0.9rem;
            }
            
            .table th, .table td {
                padding: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <div class="logo-icon">KGF</div>
            <div>
                <h1>Admin Dashboard</h1>
                <p style="font-size: 0.9rem; opacity: 0.9;">KGF Pharmaceuticals</p>
            </div>
        </div>
        
        <div class="nav-tabs">
            <a href="?page=inquiries" class="nav-tab <?php echo $page === 'inquiries' ? 'active' : ''; ?>">
                <i class="fas fa-envelope"></i> Inquiries
            </a>
            <a href="?page=products" class="nav-tab <?php echo $page === 'products' ? 'active' : ''; ?>">
                <i class="fas fa-pills"></i> Products
            </a>
        </div>
        
        <a href="?logout=1" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
    
    <div class="container">
        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total_inquiries']; ?></div>
                <div class="stat-label">Total Inquiries</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['new_inquiries']; ?></div>
                <div class="stat-label">New Inquiries</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['read_inquiries']; ?></div>
                <div class="stat-label">Read Inquiries</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['replied_inquiries']; ?></div>
                <div class="stat-label">Replied</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total_products']; ?></div>
                <div class="stat-label">Active Products</div>
            </div>
        </div>
        
        <?php if ($page === 'inquiries'): ?>
            <!-- Contact Inquiries -->
            <div class="content-card">
                <div class="card-header">
                    <i class="fas fa-envelope"></i> Contact Inquiries
                </div>
                
                <?php
                $stmt = $pdo->prepare("SELECT * FROM contact_inquiries ORDER BY created_at DESC LIMIT 50");
                $stmt->execute();
                $inquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>
                
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inquiries as $inquiry): ?>
                        <tr>
                            <td>#<?php echo $inquiry['id']; ?></td>
                            <td><?php echo htmlspecialchars($inquiry['first_name'] . ' ' . $inquiry['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($inquiry['email']); ?></td>
                            <td><?php echo htmlspecialchars($inquiry['subject']); ?></td>
                            <td class="message-preview" title="<?php echo htmlspecialchars($inquiry['message']); ?>">
                                <?php echo htmlspecialchars($inquiry['message']); ?>
                            </td>
                            <td><?php echo date('M j, Y H:i', strtotime($inquiry['created_at'])); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $inquiry['status']; ?>">
                                    <?php echo ucfirst($inquiry['status']); ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="id" value="<?php echo $inquiry['id']; ?>">
                                    <?php if ($inquiry['status'] === 'new'): ?>
                                        <button type="submit" name="action" value="mark_read" class="btn btn-primary">Mark Read</button>
                                    <?php endif; ?>
                                    <?php if ($inquiry['status'] !== 'replied'): ?>
                                        <button type="submit" name="action" value="mark_replied" class="btn btn-success">Mark Replied</button>
                                    <?php endif; ?>
                                    <button type="submit" name="action" value="delete" class="btn btn-danger" 
                                            onclick="return confirm('Are you sure you want to delete this inquiry?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($inquiries)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; color: #666; padding: 2rem;">
                                No inquiries found.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
        <?php elseif ($page === 'products'): ?>
            <!-- Products -->
            <div class="content-card">
                <div class="card-header">
                    <i class="fas fa-pills"></i> Products
                </div>
                
                <?php
                $stmt = $pdo->prepare("SELECT * FROM products ORDER BY name");
                $stmt->execute();
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>
                
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo $product['id']; ?></td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo htmlspecialchars($product['category']); ?></td>
                            <td class="message-preview" title="<?php echo htmlspecialchars($product['description']); ?>">
                                <?php echo htmlspecialchars($product['description']); ?>
                            </td>
                            <td>
                                <span class="status-badge <?php echo $product['is_active'] ? 'status-replied' : 'status-new'; ?>">
                                    <?php echo $product['is_active'] ? 'Active' : 'Inactive'; ?>
                                </span>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($product['created_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>