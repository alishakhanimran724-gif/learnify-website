<?php
// ===== UPDATED HEADER COMPONENT =====
// File: includes/header.php

// Fetch boards for dropdown
if (!isset($pdo)) {
    include 'config/db.php';
}
$boards = $pdo->query("SELECT * FROM boards ORDER BY board_name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBoard - Your Learning Journey Starts Here</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
   
    
</head>
<body>
    <header class="header">
        <div class="nav-container">
            <a href="index.php" class="logo"> Learnify
            </a>
            <nav>
                <ul class="nav-links" id="navLinks">
                    <li><a href="index.php">Home</a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle">
                            Boards <i class="fas fa-chevron-down"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <?php foreach($boards as $board): ?>
                                <li>
                                    <a href="board.php?id=<?= $board['id'] ?>&name=<?= urlencode($board['board_name']) ?>">
                                        
                                        <?= htmlspecialchars($board['board_name']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <li><a href="careers.php">Career</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
                <div class="mobile-menu" id="mobileMenu">
                    <i class="fas fa-bars"></i>
                </div>
            </nav>
        </div>
    </header>

    <script>
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenu = document.getElementById('mobileMenu');
            const navLinks = document.getElementById('navLinks');
            
            if (mobileMenu && navLinks) {
                mobileMenu.addEventListener('click', function() {
                    navLinks.classList.toggle('active');
                    
                    // Change hamburger to X
                    const icon = this.querySelector('i');
                    if (navLinks.classList.contains('active')) {
                        icon.classList.remove('fa-bars');
                        icon.classList.add('fa-times');
                    } else {
                        icon.classList.remove('fa-times');
                        icon.classList.add('fa-bars');
                    }
                });
            }

            // Close mobile menu when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.nav-container')) {
                    navLinks.classList.remove('active');
                    const icon = mobileMenu.querySelector('i');
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            });

            // Handle dropdown clicks on mobile
            const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
            dropdownToggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    if (window.innerWidth <= 768) {
                        e.preventDefault();
                        const dropdown = this.closest('.dropdown');
                        const menu = dropdown.querySelector('.dropdown-menu');
                        
                        // Toggle display
                        if (menu.style.display === 'block') {
                            menu.style.display = 'none';
                        } else {
                            menu.style.display = 'block';
                        }
                    }
                });
            });
        });
    </script>