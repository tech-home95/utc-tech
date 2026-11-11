<?php
/**
 * Universal Technology Corporation (UTC) - Header
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';

$appRoot = getAppRoot();
$currentUser = getCurrentUser();
$pageTitle = $pageTitle ?? 'Universal Technology Corporation (UTC) | Top IT Training Institute';
?>
<!DOCTYPE html>
<html lang="bn" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitize($pageTitle) ?></title>
    <meta name="description" content="Universal Technology Corporation (UTC) - Feni's Leading IT Training Institute. Programming, Web Development, Flutter, Python, Networking & Cybersecurity.">
    <link rel="icon" type="image/png" href="<?= $appRoot ?>assets/images/utc-logo.png">
    <link rel="apple-touch-icon" href="<?= $appRoot ?>assets/images/utc-logo.png">

    <!-- Pre-apply Dark Mode to avoid FOUC -->
    <script>
        if (localStorage.getItem('utc_theme') === 'dark' || (!('utc_theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Tailwind CSS with Typography & Forms via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        utc: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                            dark: '#0b1329',
                            darkcard: '#131e3a',
                            gold: '#f59e0b',
                            cyan: '#06b6d4',
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Hind Siliguri', 'sans-serif'],
                        bengali: ['Hind Siliguri', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Three.js for 3D elements -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $appRoot ?>assets/css/style.css">
</head>
<body class="bg-slate-50 text-slate-800 dark:bg-[#080d1a] dark:text-slate-100 min-h-screen flex flex-col selection:bg-blue-600 selection:text-white transition-colors duration-300">
