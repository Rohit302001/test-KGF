<?php
require_once 'config.php';

// Get products from database
$pdo = getDBConnection();
$products = [];
if ($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE is_active = 1 ORDER BY name");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KGF Pharmaceuticals - Leading the Future of Healthcare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Design System Variables */
        :root {
            /* Colors (HSL) */
            --background: 0 0% 100%;
            --foreground: 220 26% 14%;
            --card: 0 0% 100%;
            --card-foreground: 220 26% 14%;
            --primary: 217 91% 60%;
            --primary-foreground: 0 0% 100%;
            --primary-light: 217 91% 95%;
            --secondary: 220 14% 96%;
            --secondary-foreground: 220 26% 14%;
            --muted: 220 14% 96%;
            --muted-foreground: 220 13% 46%;
            --accent: 142 76% 36%;
            --accent-foreground: 0 0% 100%;
            --accent-light: 142 76% 95%;
            --border: 220 13% 91%;
            --input: 220 13% 91%;
            --ring: 217 91% 60%;
            
            /* Gradients */
            --gradient-medical: linear-gradient(135deg, hsl(217 91% 60%), hsl(217 91% 70%));
            --gradient-hero: linear-gradient(135deg, hsl(217 91% 60%) 0%, hsl(142 76% 36%) 100%);
            
            /* Shadows */
            --shadow-medical: 0 4px 20px -2px hsl(217 91% 60% / 0.1);
            --shadow-card: 0 2px 10px -2px hsl(220 26% 14% / 0.1);
            
            --radius: 0.5rem;
        }

        /* Dark mode */
        @media (prefers-color-scheme: dark) {
            :root {
                --background: 222.2 84% 4.9%;
                --foreground: 210 40% 98%;
                --card: 222.2 84% 4.9%;
                --card-foreground: 210 40% 98%;
                --primary: 210 40% 98%;
                --primary-foreground: 222.2 47.4% 11.2%;
                --secondary: 217.2 32.6% 17.5%;
                --secondary-foreground: 210 40% 98%;
                --muted: 217.2 32.6% 17.5%;
                --muted-foreground: 215 20.2% 65.1%;
                --accent: 217.2 32.6% 17.5%;
                --accent-foreground: 210 40% 98%;
                --border: 217.2 32.6% 17.5%;
                --input: 217.2 32.6% 17.5%;
                --ring: 212.7 26.8% 83.9%;
            }
        }

        /* Base styles */
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: hsl(var(--background));
            color: hsl(var(--foreground));
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Page container */
        .page {
            display: none;
            min-height: calc(100vh - 200px);
        }
        
        .page.active {
            display: block;
        }

        /* Utility classes */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius);
            text-decoration: none;
            font-weight: 600;
            text-align: center;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            white-space: nowrap;
        }

        .btn-primary {
            background: var(--gradient-medical);
            color: hsl(var(--primary-foreground));
            box-shadow: var(--shadow-medical);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px -5px hsl(217 91% 60% / 0.2);
        }

        .btn-secondary {
            background: hsl(var(--primary-foreground));
            color: hsl(var(--primary));
            border: 2px solid hsl(var(--primary-foreground));
        }

        .btn-secondary:hover {
            background: hsl(var(--primary-foreground) / 0.9);
        }

        .btn-outline {
            background: transparent;
            color: hsl(var(--primary-foreground));
            border: 2px solid hsl(var(--primary-foreground));
        }

        .btn-outline:hover {
            background: hsl(var(--primary-foreground));
            color: hsl(var(--primary));
        }

        .btn-lg {
            padding: 1rem 2rem;
            font-size: 1.125rem;
        }

        .card {
            background: hsl(var(--card));
            border: 1px solid hsl(var(--border));
            border-radius: var(--radius);
            box-shadow: var(--shadow-card);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: var(--shadow-medical);
        }

        .grid {
            display: grid;
            gap: 2rem;
        }

        .grid-2 { grid-template-columns: repeat(2, 1fr); }
        .grid-3 { grid-template-columns: repeat(3, 1fr); }
        .grid-4 { grid-template-columns: repeat(4, 1fr); }

        /* Responsive grid adjustments */
        @media (max-width: 768px) {
            .grid-2, .grid-3, .grid-4 {
                grid-template-columns: 1fr;
            }
        }

        @media (min-width: 769px) and (max-width: 1024px) {
            .grid-3 { grid-template-columns: repeat(2, 1fr); }
            .grid-4 { grid-template-columns: repeat(2, 1fr); }
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes bounce {
            0%, 20%, 53%, 80%, 100% { transform: translate3d(0,0,0); }
            40%, 43% { transform: translate3d(0,-30px,0); }
            70% { transform: translate3d(0,-15px,0); }
            90% { transform: translate3d(0,-4px,0); }
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .animate-fade-in { animation: fadeIn 1s ease-out; }
        .animate-bounce { animation: bounce 2s infinite; }
        .animate-pulse { animation: pulse 2s infinite; }

        /* Component styles */
        .header {
            background: hsl(var(--card));
            box-shadow: var(--shadow-card);
            position: sticky;
            top: 0;
            z-index: 50;
            padding: 1rem 0;
            transition: all 0.3s ease;
        }

        .scrolled {
            background: hsl(var(--card) / 0.95);
            backdrop-filter: blur(10px);
        }

        .nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo-icon {
            width: 2.5rem;
            height: 2.5rem;
            background: hsl(var(--primary));
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            color: hsl(var(--primary-foreground));
            font-weight: bold;
            font-size: 1.125rem;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            list-style: none;
        }

        .nav-links a {
            color: hsl(var(--foreground));
            text-decoration: none;
            transition: color 0.3s ease;
            font-weight: 500;
        }

        .nav-links a:hover {
            color: hsl(var(--primary));
        }

        /* Mobile menu button */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: hsl(var(--foreground));
            padding: 0.5rem;
        }

        /* Mobile menu */
        .mobile-menu {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: hsl(var(--card));
            z-index: 100;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transform: translateX(100%);
            transition: transform 0.3s ease;
        }

        .mobile-menu.active {
            transform: translateX(0);
        }

        .mobile-menu-links {
            list-style: none;
            text-align: center;
        }

        .mobile-menu-links li {
            margin: 1.5rem 0;
        }

        .mobile-menu-links a {
            font-size: 1.5rem;
            font-weight: 600;
            color: hsl(var(--foreground));
            text-decoration: none;
        }

        .close-menu {
            position: absolute;
            top: 2rem;
            right: 2rem;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: hsl(var(--foreground));
        }

        @media (max-width: 768px) {
            .nav-links, .nav .btn {
                display: none;
            }
            
            .mobile-menu-btn {
                display: block;
            }
        }

        .hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: var(--gradient-hero);
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2)), url(https://kgfpharmaceuticals.com/hero-medical.jpg);
            background-size: cover;
            background-position: center;
            opacity: 0.3;
        }

        .hero-content {
            position: relative;
            z-index: 10;
            max-width: 48rem;
            padding: 0 1rem;
        }

        .hero h1 {
            font-size: clamp(2.5rem, 5vw, 4.5rem);
            font-weight: bold;
            color: hsl(var(--primary-foreground));
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .hero .accent {
            color: hsl(var(--accent-foreground));
        }

        .hero p {
            font-size: clamp(1.125rem, 2vw, 1.5rem);
            color: hsl(var(--primary-foreground) / 0.9);
            margin-bottom: 2rem;
        }

        .hero-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        @media (max-width: 480px) {
            .hero-buttons {
                flex-direction: column;
            }
            
            .hero-buttons .btn {
                width: 100%;
            }
        }

        .scroll-indicator {
            position: absolute;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
        }

        .scroll-mouse {
            width: 1.5rem;
            height: 2.5rem;
            border: 2px solid hsl(var(--primary-foreground));
            border-radius: 1.25rem;
            display: flex;
            justify-content: center;
        }

        .scroll-dot {
            width: 0.25rem;
            height: 0.75rem;
            background: hsl(var(--primary-foreground));
            border-radius: 0.125rem;
            margin-top: 0.5rem;
        }

        .section {
            padding: 5rem 0;
        }

        .section-bg {
            background: hsl(var(--secondary));
        }

        .section-title {
            text-align: center;
            margin-bottom: 4rem;
            padding: 0 1rem;
        }

        .section-title h2 {
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: bold;
            color: hsl(var(--foreground));
            margin-bottom: 1.5rem;
        }

        .section-title p {
            font-size: clamp(1rem, 2vw, 1.25rem);
            color: hsl(var(--muted-foreground));
            max-width: 48rem;
            margin: 0 auto;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        .stat-card {
            background: hsl(var(--primary-light));
            border: none;
            padding: 1.5rem;
            text-align: center;
            border-radius: var(--radius);
        }

        .stat-number {
            font-size: 1.875rem;
            font-weight: bold;
            color: hsl(var(--primary));
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.875rem;
            color: hsl(var(--muted-foreground));
            font-weight: 500;
        }

        .feature-list {
            list-style: none;
            margin: 1.5rem 0;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .feature-dot {
            width: 1.5rem;
            height: 1.5rem;
            background: hsl(var(--accent));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 0.25rem;
        }

        .feature-dot::after {
            content: '';
            width: 0.5rem;
            height: 0.5rem;
            background: hsl(var(--accent-foreground));
            border-radius: 50%;
        }

        .service-card {
            background: hsl(var(--card));
            border: 1px solid hsl(var(--border));
            border-radius: var(--radius);
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            height: 100%;
        }

        .service-card:hover {
            box-shadow: var(--shadow-medical);
            transform: translateY(-5px);
        }

        .service-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .service-title {
            font-size: 1.25rem;
            font-weight: bold;
            color: hsl(var(--foreground));
            margin-bottom: 1rem;
        }

        .service-desc {
            color: hsl(var(--muted-foreground));
            font-size: 0.95rem;
        }

        .quality-step {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .step-number {
            width: 2rem;
            height: 2rem;
            background: hsl(var(--accent));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: hsl(var(--accent-foreground));
            font-weight: bold;
            flex-shrink: 0;
        }

        .cert-card {
            background: hsl(var(--accent-light));
            border: 1px solid hsl(var(--accent) / 0.2);
            padding: 1.5rem;
            text-align: center;
            border-radius: var(--radius);
            transition: box-shadow 0.3s ease;
            height: 100%;
        }

        .cert-card:hover {
            box-shadow: var(--shadow-card);
        }

        .cert-name {
            font-weight: bold;
            color: hsl(var(--accent));
            font-size: 1.125rem;
            margin-bottom: 0.5rem;
        }

        .cert-desc {
            font-size: 0.875rem;
            color: hsl(var(--muted-foreground));
        }

        .contact-card {
            background: hsl(var(--card));
            border: 1px solid hsl(var(--border));
            border-radius: var(--radius);
            padding: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .contact-icon {
            width: 3rem;
            height: 3rem;
            background: hsl(var(--primary));
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .contact-form {
            background: hsl(var(--card));
            border: 1px solid hsl(var(--border));
            border-radius: var(--radius);
            padding: 2rem;
            box-shadow: var(--shadow-card);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: hsl(var(--foreground));
            margin-bottom: 0.5rem;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid hsl(var(--input));
            border-radius: var(--radius);
            font-size: 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: hsl(var(--primary));
            box-shadow: 0 0 0 2px hsl(var(--primary) / 0.2);
        }

        .form-textarea {
            resize: none;
            min-height: 8rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        @media (max-width: 480px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        .footer {
            background: hsl(var(--foreground));
            color: hsl(var(--background));
            padding: 3rem 0 1rem;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
            margin-bottom: 2rem;
        }

        @media (max-width: 768px) {
            .footer-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .footer-grid {
                grid-template-columns: 1fr;
            }
        }

        .footer-logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .footer-logo-icon {
            width: 2.5rem;
            height: 2.5rem;
            background: var(--gradient-medical);
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            color: hsl(var(--primary-foreground));
            font-weight: bold;
        }

        .footer h4 {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .footer ul {
            list-style: none;
        }

        .footer li {
            margin-bottom: 0.5rem;
        }

        .footer a {
            color: hsl(var(--background) / 0.8);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer a:hover {
            color: hsl(var(--background));
        }

        .footer-bottom {
            border-top: 1px solid hsl(var(--background) / 0.2);
            padding-top: 2rem;
            text-align: center;
            color: hsl(var(--background) / 0.6);
            font-size: 0.875rem;
        }
        
        /* Product Page Styles */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .product-card {
            background: hsl(var(--card));
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow-card);
            transition: all 0.3s ease;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-medical);
        }
        
        .product-image {
            height: 200px;
            background-color: hsl(var(--muted));
            display: flex;
            align-items: center;
            justify-content: center;
            color: hsl(var(--muted-foreground));
            font-size: 3rem;
        }
        
        .product-info {
            padding: 1.5rem;
        }
        
        .product-title {
            font-size: 1.25rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .product-category {
            color: hsl(var(--primary));
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }
        
        .product-description {
            color: hsl(var(--muted-foreground));
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
        }
        
        .policy-content {
            max-width: 800px;
            margin: 0 auto;
            background: hsl(var(--card));
            border-radius: var(--radius);
            padding: 2rem;
            box-shadow: var(--shadow-card);
        }
        
        .policy-content h3 {
            font-size: 1.5rem;
            margin: 2rem 0 1rem;
            color: hsl(var(--primary));
        }
        
        .policy-content p {
            margin-bottom: 1rem;
            line-height: 1.8;
        }
        
        .policy-content ul {
            margin: 1rem 0 1rem 2rem;
        }
        
        .policy-content li {
            margin-bottom: 0.5rem;
        }

        /* Form alerts */
        .alert {
            padding: 1rem;
            border-radius: var(--radius);
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .alert-success {
            background: hsl(var(--accent-light));
            color: hsl(var(--accent));
            border: 1px solid hsl(var(--accent) / 0.3);
        }

        .alert-error {
            background: #fee;
            color: #c53030;
            border: 1px solid #fed7d7;
        }

        /* Responsive text sizes */
        @media (max-width: 768px) {
            .container { padding: 0 1rem; }
            .section { padding: 3rem 0; }
            .hero-buttons { justify-content: center; }
            
            .section-title {
                margin-bottom: 2rem;
            }
        }

        /* iPad specific adjustments */
        @media (min-width: 769px) and (max-width: 1024px) {
            .service-card, .cert-card {
                padding: 1.5rem;
            }
            
            .hero-content {
                max-width: 80%;
            }
        }

        /* Mobile specific adjustments */
        @media (max-width: 480px) {
            .hero {
                min-height: 90vh;
            }
            
            .hero h1 {
                font-size: 2.2rem;
            }
            
            .hero p {
                font-size: 1.1rem;
            }
            
            .section-title h2 {
                font-size: 1.8rem;
            }
            
            .section-title p {
                font-size: 1rem;
            }
            
            .contact-card {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="nav">
                <div class="logo">
                    <div class="logo-icon">KGF</div>
                    <div>
                        <h1 style="font-size: 1.25rem; font-weight: bold; margin: 0;">KGF Pharmaceuticals</h1>
                        <p style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); margin: 0;">Healthcare Excellence</p>
                    </div>
                </div>
                
                <ul class="nav-links">
                    <li><a href="#" data-page="home-page">Home</a></li>
                    <li><a href="#" data-page="about-page">About</a></li>
                    <li><a href="#" data-page="services-page">Services</a></li>
                    <li><a href="#" data-page="products-page">Products</a></li>
                    <li><a href="#" data-page="contact-page">Contact</a></li>
                </ul>
                
                <a href="#" data-page="contact-page" class="btn btn-primary">Get in Touch</a>
                
                <button class="mobile-menu-btn">
                    <i class="fas fa-bars"></i>
                </button>
            </nav>
        </div>
    </header>
    
    <!-- Mobile Menu -->
    <div class="mobile-menu">
        <button class="close-menu">
            <i class="fas fa-times"></i>
        </button>
        <ul class="mobile-menu-links">
            <li><a href="#" data-page="home-page">Home</a></li>
            <li><a href="#" data-page="about-page">About</a></li>
            <li><a href="#" data-page="services-page">Services</a></li>
            <li><a href="#" data-page="products-page">Products</a></li>
            <li><a href="#" data-page="contact-page">Contact</a></li>
            <li><a href="#" data-page="contact-page" class="btn btn-primary">Get in Touch</a></li>
        </ul>
    </div>

    <!-- Home Page -->
    <div id="home-page" class="page active">
        <!-- Hero Section -->
        <section class="hero">
            <div class="container">
                <div class="hero-content animate-fade-in">
                    <h1>Leading the Future of <span class="accent">Healthcare</span></h1>
                    <p>KGF Pharmaceuticals is committed to developing innovative medications and healthcare solutions that improve lives worldwide.</p>
                    <div class="hero-buttons">
                        <a href="#" data-page="products-page" class="btn btn-secondary btn-lg">Our Products</a>
                        <a href="#" data-page="about-page" class="btn btn-outline btn-lg">Learn More</a>
                    </div>
                </div>
            </div>
            
            <div class="scroll-indicator animate-bounce">
                <div class="scroll-mouse">
                    <div class="scroll-dot animate-pulse"></div>
                </div>
            </div>
        </section>

        <!-- About Preview Section -->
        <section class="section">
            <div class="container">
                <div class="section-title">
                    <h2>About KGF Pharmaceuticals</h2>
                    <p>Founded with a vision to revolutionize healthcare, we combine cutting-edge research with compassionate care to deliver pharmaceutical solutions that make a real difference.</p>
                </div>

                <div class="grid grid-2" style="align-items: center; margin-bottom: 4rem;">
                    <div>
                        <h3 style="font-size: clamp(1.5rem, 3vw, 1.875rem); font-weight: bold; margin-bottom: 1.5rem;">Our Mission</h3>
                        <p style="font-size: 1.125rem; color: hsl(var(--muted-foreground)); margin-bottom: 1.5rem;">
                            To develop, manufacture, and distribute high-quality pharmaceutical products that address 
                            unmet medical needs and improve patient outcomes worldwide. We are committed to scientific 
                            excellence, regulatory compliance, and ethical business practices.
                        </p>
                        <a href="#" data-page="about-page" class="btn btn-primary">Learn More About Us</a>
                    </div>
                    
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-number">15+</div>
                            <div class="stat-label">Years of Excellence</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number"><?php echo count($products); ?>+</div>
                            <div class="stat-label">Healthcare Products</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">100K+</div>
                            <div class="stat-label">Lives Improved</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">25+</div>
                            <div class="stat-label">Countries Served</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Preview Section -->
        <section class="section section-bg">
            <div class="container">
                <div class="section-title">
                    <h2>Our Core Services</h2>
                    <p>Comprehensive pharmaceutical solutions from research and development to global distribution, ensuring quality healthcare reaches those who need it most.</p>
                </div>

                <div class="grid grid-3">
                    <div class="service-card">
                        <div class="service-icon">🔬</div>
                        <h3 class="service-title">Drug Development</h3>
                        <p class="service-desc">From concept to clinical trials, we develop innovative pharmaceutical solutions using cutting-edge research methodologies.</p>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">🏭</div>
                        <h3 class="service-title">Manufacturing</h3>
                        <p class="service-desc">State-of-the-art manufacturing facilities ensuring highest quality standards and regulatory compliance.</p>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">✅</div>
                        <h3 class="service-title">Quality Assurance</h3>
                        <p class="service-desc">Comprehensive quality control and assurance programs meeting international pharmaceutical standards.</p>
                    </div>
                </div>
                <div style="text-align: center; margin-top: 3rem;">
                    <a href="#" data-page="services-page" class="btn btn-outline">View All Services</a>
                </div>
            </div>
        </section>
    </div>

    <!-- About Page -->
    <div id="about-page" class="page">
        <section class="hero" style="min-height: 50vh;">
            <div class="container">
                <div class="hero-content animate-fade-in">
                    <h1>About KGF Pharmaceuticals</h1>
                    <p>Learn about our mission, values, and commitment to healthcare excellence.</p>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <div class="grid grid-2" style="align-items: center; margin-bottom: 4rem;">
                    <div>
                        <h3 style="font-size: clamp(1.5rem, 3vw, 1.875rem); font-weight: bold; margin-bottom: 1.5rem;">Our Mission</h3>
                        <p style="font-size: 1.125rem; color: hsl(var(--muted-foreground)); margin-bottom: 1.5rem;">
                            To develop, manufacture, and distribute high-quality pharmaceutical products that address 
                            unmet medical needs and improve patient outcomes worldwide. We are committed to scientific 
                            excellence, regulatory compliance, and ethical business practices.
                        </p>
                        <ul class="feature-list">
                            <li class="feature-item">
                                <div class="feature-dot"></div>
                                <span style="font-weight: 500;">Research & Development Excellence</span>
                            </li>
                            <li class="feature-item">
                                <div class="feature-dot"></div>
                                <span style="font-weight: 500;">Global Quality Standards</span>
                            </li>
                            <li class="feature-item">
                                <div class="feature-dot"></div>
                                <span style="font-weight: 500;">Patient-Centric Approach</span>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-number">15+</div>
                            <div class="stat-label">Years of Excellence</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number"><?php echo count($products); ?>+</div>
                            <div class="stat-label">Healthcare Products</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">100K+</div>
                            <div class="stat-label">Lives Improved</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">25+</div>
                            <div class="stat-label">Countries Served</div>
                        </div>
                    </div>
                </div>
                
                <div class="section-title">
                    <h2>Our Quality Commitment</h2>
                    <p>We adhere to the highest international standards and maintain rigorous quality control processes throughout our operations.</p>
                </div>

                <div class="grid grid-2" style="align-items: center;">
                    <div>
                        <h3 style="font-size: clamp(1.5rem, 3vw, 1.875rem); font-weight: bold; margin-bottom: 1.5rem;">Our Quality Promise</h3>
                        <div class="quality-step">
                            <div class="step-number">1</div>
                            <div>
                                <h4 style="font-weight: 600; margin-bottom: 0.25rem;">Rigorous Testing</h4>
                                <p style="color: hsl(var(--muted-foreground));">Every batch undergoes comprehensive testing for purity, potency, and safety.</p>
                            </div>
                        </div>
                        <div class="quality-step">
                            <div class="step-number">2</div>
                            <div>
                                <h4 style="font-weight: 600; margin-bottom: 0.25rem;">Regulatory Compliance</h4>
                                <p style="color: hsl(var(--muted-foreground));">Full compliance with international pharmaceutical regulations and guidelines.</p>
                            </div>
                        </div>
                        <div class="quality-step">
                            <div class="step-number">3</div>
                            <div>
                                <h4 style="font-weight: 600; margin-bottom: 0.25rem;">Continuous Monitoring</h4>
                                <p style="color: hsl(var(--muted-foreground));">Real-time monitoring and documentation of all manufacturing processes.</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-2">
                        <div class="cert-card">
                            <h4 class="cert-name">WHO-GMP</h4>
                            <p class="cert-desc">World Health Organization Good Manufacturing Practices</p>
                        </div>
                        <div class="cert-card">
                            <h4 class="cert-name">ISO 9001</h4>
                            <p class="cert-desc">Quality Management Systems</p>
                        </div>
                        <div class="cert-card">
                            <h4 class="cert-name">ISO 13485</h4>
                            <p class="cert-desc">Medical Device Quality Management</p>
                        </div>
                        <div class="cert-card">
                            <h4 class="cert-name">FDA Compliance</h4>
                            <p class="cert-desc">U.S. Food and Drug Administration Standards</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Services Page -->
    <div id="services-page" class="page">
        <section class="hero" style="min-height: 50vh;">
            <div class="container">
                <div class="hero-content animate-fade-in">
                    <h1>Our Services</h1>
                    <p>Comprehensive pharmaceutical solutions to meet your healthcare needs.</p>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <div class="section-title">
                    <h2>Our Core Services</h2>
                    <p>Comprehensive pharmaceutical solutions from research and development to global distribution, ensuring quality healthcare reaches those who need it most.</p>
                </div>

                <div class="grid grid-3">
                    <div class="service-card">
                        <div class="service-icon">🔬</div>
                        <h3 class="service-title">Drug Development</h3>
                        <p class="service-desc">From concept to clinical trials, we develop innovative pharmaceutical solutions using cutting-edge research methodologies.</p>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">🏭</div>
                        <h3 class="service-title">Manufacturing</h3>
                        <p class="service-desc">State-of-the-art manufacturing facilities ensuring highest quality standards and regulatory compliance.</p>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">✅</div>
                        <h3 class="service-title">Quality Assurance</h3>
                        <p class="service-desc">Comprehensive quality control and assurance programs meeting international pharmaceutical standards.</p>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">📋</div>
                        <h3 class="service-title">Regulatory Affairs</h3>
                        <p class="service-desc">Expert regulatory guidance and support for drug approvals across global markets.</p>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">🧪</div>
                        <h3 class="service-title">Clinical Research</h3>
                        <p class="service-desc">Conducting clinical trials and research studies to validate safety and efficacy of our products.</p>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">🚚</div>
                        <h3 class="service-title">Distribution</h3>
                        <p class="service-desc">Global distribution network ensuring timely delivery of medications to healthcare providers.</p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Products Page -->
    <div id="products-page" class="page">
        <section class="hero" style="min-height: 50vh;">
            <div class="container">
                <div class="hero-content animate-fade-in">
                    <h1>Ophthalmology Products</h1>
                    <p>Innovative solutions for eye care and vision health.</p>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <div class="section-title">
                    <h2>Our Ophthalmology Solutions</h2>
                    <p>Specializing in advanced ophthalmic medications and treatments designed to improve vision and eye health.</p>
                </div>

                <div class="product-grid">
                    <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <i class="<?php echo htmlspecialchars($product['image_url']); ?>"></i>
                        </div>
                        <div class="product-info">
                            <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <div class="product-category"><?php echo htmlspecialchars($product['category']); ?></div>
                            <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                            <a href="#" class="btn btn-outline">Learn More</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </div>

    <!-- Contact Page -->
    <div id="contact-page" class="page">
        <section class="hero" style="min-height: 50vh;">
            <div class="container">
                <div class="hero-content animate-fade-in">
                    <h1>Contact Us</h1>
                    <p>Reach out to our team for inquiries, partnerships, or support.</p>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <div class="section-title">
                    <h2>Get in Touch</h2>
                    <p>Ready to partner with us or learn more about our pharmaceutical solutions? Contact our team of experts today.</p>
                </div>

                <div class="grid grid-2">
                    <div>
                        <h3 style="font-size: clamp(1.3rem, 3vw, 1.5rem); font-weight: bold; margin-bottom: 1.5rem;">Contact Information</h3>
                        
                        <div class="contact-card">
                            <div class="contact-icon">📍</div>
                            <div>
                                <h4 style="font-weight: 600; margin-bottom: 0.25rem;">Office Address</h4>
                                <p style="color: hsl(var(--muted-foreground)); margin: 0;">
                                    E-Block, Shop No. 6,<br>
                                    Near Shindi Sweets, Opposite Bustand,<br>
                                    Baddi, District Solan, H.P. - 173205
                                </p>
                            </div>
                        </div>
                        <div style="margin-top: 0.75rem; margin-bottom: 2rem;">
                            <iframe
                                src="https://www.google.com/maps?q=E-Block,+Shop+No.+6,+Near+Shindi+Sweets,+Opposite+Bustand,+Baddi,+District+Solan,+HP+173205&output=embed"
                                width="100%"
                                height="250"
                                style="border:0; border-radius: 8px;"
                                allowfullscreen=""
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>

                        <div class="contact-card">
                            <div class="contact-icon">📞</div>
                            <div>
                                <h4 style="font-weight: 600; margin-bottom: 0.25rem;">Phone</h4>
                                <p style="color: hsl(var(--muted-foreground)); margin: 0;">
                                    <a href="tel:+919216226227" style="color: inherit; text-decoration: none;">+91-9216226227</a><br>
                                    <a href="tel:+919906253881" style="color: inherit; text-decoration: none;">+91-9906253881</a>
                                </p>
                            </div>
                        </div>

                        <div class="contact-card">
                            <div class="contact-icon">✉️</div>
                            <div>
                                <h4 style="font-weight: 600; margin-bottom: 0.25rem;">Email</h4>
                                <p style="color: hsl(var(--muted-foreground)); margin: 0;">
                                    <a href="mailto:kgfpharmaceuticals@gmail.com" style="color: inherit; text-decoration: none;">
                                        kgfpharmaceuticals@gmail.com
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="contact-form">
                        <h3 style="font-size: clamp(1.3rem, 3vw, 1.5rem); font-weight: bold; margin-bottom: 1.5rem;">Send us a Message</h3>
                        
                        <div id="form-messages"></div>
                        
                        <form id="contact-form">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION[CSRF_TOKEN_NAME]; ?>">
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">First Name</label>
                                    <input type="text" name="first_name" class="form-input" placeholder="Enter your first name" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" name="last_name" class="form-input" placeholder="Enter your last name" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-input" placeholder="Enter your email address" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Subject</label>
                                <input type="text" name="subject" class="form-input" placeholder="What's this about?" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Message</label>
                                <textarea name="message" class="form-input form-textarea" placeholder="Tell us more about your inquiry..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary" style="width: 100%;">
                                <span class="btn-text">Send Message</span>
                                <span class="btn-loading" style="display: none;">Sending...</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Privacy Policy Page -->
    <div id="privacy-page" class="page">
        <section class="hero" style="min-height: 50vh;">
            <div class="container">
                <div class="hero-content animate-fade-in">
                    <h1>Privacy Policy</h1>
                    <p>How we collect, use, and protect your personal information.</p>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <div class="policy-content">
                    <h2>Privacy Policy</h2>
                    <p>Last updated: <?php echo date('F j, Y'); ?></p>
                    
                    <p>KGF Pharmaceuticals ("us", "we", or "our") operates this website (the "Service").</p>
                    
                    <p>This page informs you of our policies regarding the collection, use, and disclosure of personal data when you use our Service and the choices you have associated with that data.</p>
                    
                    <h3>Information Collection and Use</h3>
                    <p>We collect several different types of information for various purposes to provide and improve our Service to you.</p>
                    
                    <h3>Types of Data Collected</h3>
                    <h4>Personal Data</h4>
                    <p>While using our Service, we may ask you to provide us with certain personally identifiable information that can be used to contact or identify you ("Personal Data"). Personally identifiable information may include, but is not limited to:</p>
                    <ul>
                        <li>Email address</li>
                        <li>First name and last name</li>
                        <li>Phone number</li>
                        <li>Address, State, Province, ZIP/Postal code, City</li>
                        <li>Cookies and Usage Data</li>
                    </ul>
                    
                    <h4>Usage Data</h4>
                    <p>We may also collect information on how the Service is accessed and used ("Usage Data"). This Usage Data may include information such as your computer's Internet Protocol address (e.g. IP address), browser type, browser version, the pages of our Service that you visit, the time and date of your visit, the time spent on those pages, unique device identifiers and other diagnostic data.</p>
                    
                    <h3>Use of Data</h3>
                    <p>KGF Pharmaceuticals uses the collected data for various purposes:</p>
                    <ul>
                        <li>To provide and maintain our Service</li>
                        <li>To notify you about changes to our Service</li>
                        <li>To allow you to participate in interactive features of our Service when you choose to do so</li>
                        <li>To provide customer support</li>
                        <li>To gather analysis or valuable information so that we can improve our Service</li>
                        <li>To monitor the usage of our Service</li>
                        <li>To detect, prevent and address technical issues</li>
                    </ul>
                    
                    <h3>Security of Data</h3>
                    <p>The security of your data is important to us, but remember that no method of transmission over the Internet, or method of electronic storage is 100% secure. While we strive to use commercially acceptable means to protect your Personal Data, we cannot guarantee its absolute security.</p>
                    
                    <h3>Changes to This Privacy Policy</h3>
                    <p>We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
                    <p>We will let you know via email and/or a prominent notice on our Service, prior to the change becoming effective and update the "effective date" at the top of this Privacy Policy.</p>
                    <p>You are advised to review this Privacy Policy periodically for any changes. Changes to this Privacy Policy are effective when they are posted on this page.</p>
                    
                    <h3>Contact Us</h3>
                    <p>If you have any questions about this Privacy Policy, please contact us:</p>
                    <ul>
                        <li>By email: kgfpharmaceuticals@gmail.com</li>
                        <li>By phone number: +91-9216226227</li>
                    </ul>
                </div>
            </div>
        </section>
    </div>

    <!-- Terms of Service Page -->
    <div id="terms-page" class="page">
        <section class="hero" style="min-height: 50vh;">
            <div class="container">
                <div class="hero-content animate-fade-in">
                    <h1>Terms of Service</h1>
                    <p>Legal terms governing the use of our website and services.</p>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <div class="policy-content">
                    <h2>Terms of Service</h2>
                    <p>Last updated: <?php echo date('F j, Y'); ?></p>
                    
                    <p>Please read these Terms of Service ("Terms", "Terms of Service") carefully before using this website (the "Service") operated by KGF Pharmaceuticals ("us", "we", or "our").</p>
                    
                    <p>Your access to and use of the Service is conditioned on your acceptance of and compliance with these Terms. These Terms apply to all visitors, users and others who access or use the Service.</p>
                    
                    <p>By accessing or using the Service you agree to be bound by these Terms. If you disagree with any part of the terms then you may not access the Service.</p>
                    
                    <h3>Accounts</h3>
                    <p>When you create an account with us, you must provide us information that is accurate, complete, and current at all times. Failure to do so constitutes a breach of the Terms, which may result in immediate termination of your account on our Service.</p>
                    
                    <p>You are responsible for safeguarding the password that you use to access the Service and for any activities or actions under your password, whether your password is with our Service or a third-party service.</p>
                    
                    <p>You agree not to disclose your password to any third party. You must notify us immediately upon becoming aware of any breach of security or unauthorized use of your account.</p>
                    
                    <h3>Intellectual Property</h3>
                    <p>The Service and its original content, features and functionality are and will remain the exclusive property of KGF Pharmaceuticals and its licensors. The Service is protected by copyright, trademark, and other laws of both the India and foreign countries. Our trademarks and trade dress may not be used in connection with any product or service without the prior written consent of KGF Pharmaceuticals.</p>
                    
                    <h3>Links To Other Web Sites</h3>
                    <p>Our Service may contain links to third-party web sites or services that are not owned or controlled by KGF Pharmaceuticals.</p>
                    
                    <p>KGF Pharmaceuticals has no control over, and assumes no responsibility for, the content, privacy policies, or practices of any third party web sites or services. You further acknowledge and agree that KGF Pharmaceuticals shall not be responsible or liable, directly or indirectly, for any damage or loss caused or alleged to be caused by or in connection with use of or reliance on any such content, goods or services available on or through any such web sites or services.</p>
                    
                    <p>We strongly advise you to read the terms and conditions and privacy policies of any third-party web sites or services that you visit.</p>
                    
                    <h3>Termination</h3>
                    <p>We may terminate or suspend your access immediately, without prior notice or liability, for any reason whatsoever, including without limitation if you breach the Terms.</p>
                    
                    <p>Upon termination, your right to use the Service will immediately cease.</p>
                    
                    <h3>Limitation of Liability</h3>
                    <p>In no event shall KGF Pharmaceuticals, nor its directors, employees, partners, agents, suppliers, or affiliates, be liable for any indirect, incidental, special, consequential or punitive damages, including without limitation, loss of profits, data, use, goodwill, or other intangible losses, resulting from (i) your access to or use of or inability to access or use the Service; (ii) any conduct or content of any third party on the Service; (iii) any content obtained from the Service; and (iv) unauthorized access, use or alteration of your transmissions or content, whether based on warranty, contract, tort (including negligence) or any other legal theory, whether or not we have been informed of the possibility of such damage, and even if a remedy set forth herein is found to have failed of its essential purpose.</p>
                    
                    <h3>Governing Law</h3>
                    <p>These Terms shall be governed and construed in accordance with the laws of India, without regard to its conflict of law provisions.</p>
                    
                    <p>Our failure to enforce any right or provision of these Terms will not be considered a waiver of those rights. If any provision of these Terms is held to be invalid or unenforceable by a court, the remaining provisions of these Terms will remain in effect. These Terms constitute the entire agreement between us regarding our Service, and supersede and replace any prior agreements we might have between us regarding the Service.</p>
                    
                    <h3>Changes</h3>
                    <p>We reserve the right, at our sole discretion, to modify or replace these Terms at any time. If a revision is material we will try to provide at least 30 days notice prior to any new terms taking effect. What constitutes a material change will be determined at our sole discretion.</p>
                    
                    <p>By continuing to access or use our Service after those revisions become effective, you agree to be bound by the revised terms. If you do not agree to the new terms, please stop using the Service.</p>
                    
                    <h3>Contact Us</h3>
                    <p>If you have any questions about these Terms, please contact us:</p>
                    <ul>
                        <li>By email: kgfpharmaceuticals@gmail.com</li>
                        <li>By phone number: +91-9216226227</li>
                    </ul>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <div class="footer-logo">
                        <div class="footer-logo-icon">KGF</div>
                        <span style="font-size: 1.25rem; font-weight: bold;">KGF Pharmaceuticals</span>
                    </div>
                    <p style="color: hsl(var(--background) / 0.8);">
                        Leading the future of healthcare with innovative pharmaceutical solutions 
                        and unwavering commitment to quality.
                    </p>
                </div>

                <div>
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="#" data-page="home-page">Home</a></li>
                        <li><a href="#" data-page="about-page">About Us</a></li>
                        <li><a href="#" data-page="services-page">Services</a></li>
                        <li><a href="#" data-page="products-page">Products</a></li>
                    </ul>
                </div>

                <div>
                    <h4>Legal</h4>
                    <ul>
                        <li><a href="#" data-page="privacy-page">Privacy Policy</a></li>
                        <li><a href="#" data-page="terms-page">Terms of Service</a></li>
                    </ul>
                </div>

                <div>
                    <h4>Contact Info</h4>
                    <div style="color: hsl(var(--background) / 0.8);">
                        <p>📍 Baddi, District Solan, HP</p>
                        <p>📞  <a href="tel:+919216226227">+91-9216226227</a>,<a href="tel:+919906253881">+91-9906253881</a>
                         <p>✉️ <a href="mailto:kgfpharmaceuticals@gmail.com">kgfpharmaceuticals@gmail.com</a></p>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p>© <?php echo date('Y'); ?> KGF Pharmaceuticals. All rights reserved. | 
                    <a href="#" data-page="privacy-page" style="color: inherit;">Privacy Policy</a> | 
                    <a href="#" data-page="terms-page" style="color: inherit;">Terms of Service</a>
                </p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu functionality
        const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
        const closeMenuBtn = document.querySelector('.close-menu');
        const mobileMenu = document.querySelector('.mobile-menu');
        
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
        
        closeMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.remove('active');
            document.body.style.overflow = '';
        });
        
        // Close menu when clicking on links
        document.querySelectorAll('.mobile-menu-links a').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.remove('active');
                document.body.style.overflow = '';
            });
        });

        // Page navigation functionality
        function showPage(pageId) {
            // Hide all pages
            document.querySelectorAll('.page').forEach(page => {
                page.classList.remove('active');
            });
            
            // Show the requested page
            document.getElementById(pageId).classList.add('active');
            
            // Scroll to top
            window.scrollTo(0, 0);
            
            // Close mobile menu if open
            mobileMenu.classList.remove('active');
            document.body.style.overflow = '';
        }
        
        // Set up page navigation
        document.querySelectorAll('[data-page]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                showPage(this.getAttribute('data-page'));
            });
        });
        
        // Enhanced form submission with AJAX
        document.getElementById('contact-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const btnText = submitBtn.querySelector('.btn-text');
            const btnLoading = submitBtn.querySelector('.btn-loading');
            const messagesDiv = document.getElementById('form-messages');
            
            // Show loading state
            submitBtn.disabled = true;
            btnText.style.display = 'none';
            btnLoading.style.display = 'inline';
            
            // Clear previous messages
            messagesDiv.innerHTML = '';
            
            fetch('contact_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messagesDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                    form.reset();
                } else {
                    messagesDiv.innerHTML = `<div class="alert alert-error">${data.message}</div>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                messagesDiv.innerHTML = '<div class="alert alert-error">An error occurred. Please try again later.</div>';
            })
            .finally(() => {
                // Reset button state
                submitBtn.disabled = false;
                btnText.style.display = 'inline';
                btnLoading.style.display = 'none';
                
                // Scroll to messages
                messagesDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            });
        });

        // Add scroll effect to header
        window.addEventListener('scroll', function() {
            const header = document.querySelector('.header');
            if (window.scrollY > 100) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
        
        // Add touch support for iOS devices
        document.querySelectorAll('.btn').forEach(btn => {
            btn.addEventListener('touchstart', function() {
                this.classList.add('active');
            });
            
            btn.addEventListener('touchend', function() {
                this.classList.remove('active');
            });
        });
        
        // Show home page by default
        showPage('home-page');
    </script>
</body>
</html>