# KGF Pharmaceuticals Website

A complete pharmaceutical company website with PHP backend, featuring contact management, product catalog, and admin panel.

## Features

### Frontend
- **Responsive Design**: Modern, mobile-first design that works on all devices
- **Single Page Application**: Smooth navigation between pages without reload
- **Professional UI**: Medical-themed design with gradients and animations
- **Accessibility**: WCAG compliant with proper contrast and keyboard navigation
- **Performance**: Optimized CSS and JavaScript for fast loading

### Backend
- **Contact Form Processing**: Secure form handling with validation
- **Email Notifications**: Automatic email sending to both admin and users
- **Database Storage**: MySQL database for storing inquiries and products
- **Admin Panel**: Complete dashboard for managing inquiries and viewing statistics
- **Security Features**: CSRF protection, input sanitization, and SQL injection prevention
- **Dynamic Content**: Products loaded from database

## File Structure

```
/
├── index.php              # Main website file (PHP)
├── config.php             # Database and email configuration
├── contact_handler.php    # Contact form processing
├── admin.php             # Admin panel
├── README.md             # This file
└── test.html             # Original HTML file (backup)
```

## Setup Instructions

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- SMTP server access (for email functionality)

### Installation

1. **Clone/Download Files**
   ```bash
   # Place all files in your web server directory
   # e.g., /var/www/html/ or htdocs/
   ```

2. **Database Setup**
   ```sql
   # Create database
   CREATE DATABASE kgf_pharmaceuticals;
   
   # The tables will be created automatically when you first run the website
   ```

3. **Configure Database & Email**
   
   Edit `config.php` and update these settings:
   
   ```php
   // Database Configuration
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_db_username');
   define('DB_PASS', 'your_db_password');
   define('DB_NAME', 'kgf_pharmaceuticals');
   
   // Email Configuration
   define('SMTP_USERNAME', 'your_email@gmail.com');
   define('SMTP_PASSWORD', 'your_app_password');
   define('FROM_EMAIL', 'your_email@gmail.com');
   define('TO_EMAIL', 'admin@kgfpharmaceuticals.com');
   ```

4. **Set File Permissions**
   ```bash
   chmod 644 *.php
   chmod 755 directory_name
   ```

5. **Test Installation**
   - Visit your website URL
   - Check if pages load correctly
   - Test contact form submission
   - Verify database tables are created

## Admin Panel

### Access
- URL: `your-domain.com/admin.php`
- Username: `admin`
- Password: `kgf@admin123` (Change this in `admin.php`)

### Features
- **Dashboard**: Overview statistics of inquiries and products
- **Inquiries Management**: View, mark as read/replied, delete inquiries
- **Products View**: See all products in the database
- **Responsive Design**: Works on mobile and desktop

## Email Configuration

### Gmail Setup
1. Enable 2-Factor Authentication on your Gmail account
2. Generate an App Password:
   - Go to Google Account Settings
   - Security → 2-Step Verification → App passwords
   - Generate password for "Mail"
3. Use the generated password in `config.php`

### Other Email Providers
Update SMTP settings in `config.php`:
```php
define('SMTP_HOST', 'your-smtp-server.com');
define('SMTP_PORT', 587); // or 465 for SSL
```

## Database Schema

### contact_inquiries
- `id` (INT, PRIMARY KEY, AUTO_INCREMENT)
- `first_name` (VARCHAR 100)
- `last_name` (VARCHAR 100)
- `email` (VARCHAR 255)
- `subject` (VARCHAR 255)
- `message` (TEXT)
- `ip_address` (VARCHAR 45)
- `user_agent` (TEXT)
- `created_at` (TIMESTAMP)
- `status` (ENUM: 'new', 'read', 'replied')

### products
- `id` (INT, PRIMARY KEY, AUTO_INCREMENT)
- `name` (VARCHAR 255)
- `category` (VARCHAR 100)
- `description` (TEXT)
- `image_url` (VARCHAR 500)
- `is_active` (BOOLEAN)
- `created_at` (TIMESTAMP)
- `updated_at` (TIMESTAMP)

## Security Features

### Implemented
- **CSRF Protection**: All forms include CSRF tokens
- **Input Sanitization**: All user inputs are sanitized
- **SQL Injection Prevention**: Using prepared statements
- **XSS Protection**: HTML special characters escaped
- **Session Security**: Secure session handling

### Recommendations
- Use HTTPS in production
- Change default admin credentials
- Regular database backups
- Keep PHP and MySQL updated
- Use strong passwords

## Customization

### Adding New Products
1. Access the database directly or create an admin interface
2. Insert into `products` table:
   ```sql
   INSERT INTO products (name, category, description, image_url) 
   VALUES ('Product Name', 'Category', 'Description', 'fas fa-icon');
   ```

### Modifying Design
- Edit CSS in `index.php` within the `<style>` tags
- Modify HTML structure in the same file
- Colors are defined in CSS custom properties for easy theming

### Email Templates
- Edit email templates in `contact_handler.php`
- Modify the `$emailMessage` and `$confirmationMessage` variables

## Troubleshooting

### Common Issues

1. **Database Connection Failed**
   - Check database credentials in `config.php`
   - Ensure MySQL is running
   - Verify database exists

2. **Emails Not Sending**
   - Check SMTP credentials
   - Verify firewall allows SMTP connections
   - Check PHP mail configuration

3. **Contact Form Not Working**
   - Check browser console for JavaScript errors
   - Verify `contact_handler.php` is accessible
   - Check file permissions

4. **Admin Panel Access Issues**
   - Verify credentials in `admin.php`
   - Check session configuration
   - Clear browser cache/cookies

### Debug Mode
Add this to `config.php` for debugging:
```php
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

## Production Deployment

### Before Going Live
1. **Security Checklist**
   - Change all default passwords
   - Enable HTTPS
   - Set proper file permissions
   - Disable PHP error display
   - Configure proper backup strategy

2. **Performance Optimization**
   - Enable PHP OPcache
   - Configure web server caching
   - Optimize database queries
   - Compress CSS/JS if needed

3. **Monitoring**
   - Set up error logging
   - Monitor database performance
   - Regular security updates

## Support

For issues or questions:
- Email: kgfpharmaceuticals@gmail.com
- Phone: +91-9216226227, +91-9906253881

## License

This project is created for KGF Pharmaceuticals. All rights reserved.

---

**Note**: This is a complete pharmaceutical website solution with both frontend and backend functionality. The system is designed to be secure, scalable, and easy to maintain.